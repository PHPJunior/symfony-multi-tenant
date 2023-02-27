<?php

namespace Module\Tenancy\Command;

use Doctrine\DBAL\Exception;
use Module\Tenancy\Repository\TenantRepository;
use Module\Tenancy\Services\TenantService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:tenant-create-user',
    description: 'Create Tenant User',
    aliases: ['tenant:create:user'],
    hidden: false
)]
class TenantCreateUserCommand extends Command
{
    /**
     * @param TenantRepository $tenantRepository
     * @param TenantService $tenantService
     */
    public function __construct(
        private readonly TenantRepository $tenantRepository,
        private readonly TenantService $tenantService,
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
            ->addUsage('app:tenant-create-user 1');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     * @throws ExceptionInterface
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenantId = $input->getArgument('tenantId');
        $tenant = $this->tenantRepository->find($tenantId);

        if (!$tenant) {
            $output->writeln('Tenant not found');
            return Command::FAILURE;
        }

        $helper = $this->getHelper('question');
        $email = $helper->ask($input, $output, new Question('Email: '));
        $password = $helper->ask($input, $output, new Question('Password: '));

        $this->tenantService->switchTenant($tenant);
        $this->tenantService->createUser($email, $password);

        return Command::SUCCESS;
    }
}
