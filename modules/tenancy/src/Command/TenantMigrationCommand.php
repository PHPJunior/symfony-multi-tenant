<?php

namespace Module\Tenancy\Command;

use Doctrine\DBAL\Exception;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Module\Tenancy\Repository\TenantRepository;
use Module\Tenancy\Services\TenantService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AsCommand(
    name: 'app:tenant-migrate',
    description: 'Run migration for Tenant',
    aliases: ['tenant:migrate'],
    hidden: false
)]
class TenantMigrationCommand extends Command
{
    /**
     * @param ContainerInterface $container
     * @param TenantService $tenantService
     * @param TenantRepository $tenantRepository
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly TenantService $tenantService,
        private readonly TenantRepository $tenantRepository
    )
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('tenantId', InputArgument::REQUIRED, 'Tenant ID')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS) or alias (first, prev, next, latest) to migrate to.', 'latest')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the migration as a dry run.')
            ->addOption('query-time', null, InputOption::VALUE_NONE, 'Time all the queries individually.')
            ->addOption('allow-no-migration', null, InputOption::VALUE_NONE, 'Do not throw an exception when no changes are detected.');
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenantId = $input->getArgument('tenantId');
        $tenant = $this->tenantRepository->find($tenantId);

        if (!$tenant) {
            $output->writeln('Tenant not found');
            return Command::FAILURE;
        }

        $connection = $this->tenantService->getConnection();
        $this->tenantService->switchTenant($tenant);

        $configuration = new Configuration();
        $configuration->addMigrationsDirectory('Tenant\Migrations', $this->container->getParameter('tenant_migrations_path'));
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName('doctrine_migration_versions');

        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        $dependencyFactory = DependencyFactory::fromConnection(
            new ExistingConfiguration($configuration),
            new ExistingConnection($connection)
        );

        $newInput = new ArrayInput([
            'version' => $input->getArgument('version'),
            '--dry-run' => $input->getOption('dry-run'),
            '--query-time' => $input->getOption('query-time'),
            '--allow-no-migration' => $input->getOption('allow-no-migration'),
        ]);

        $command = new MigrateCommand($dependencyFactory);
        $command->run($newInput, $output);

        return Command::SUCCESS;
    }
}
