<?php

namespace Module\Tenancy\Command;

use Doctrine\DBAL\Exception;
use Module\Tenancy\Entity\Tenant;
use Module\Tenancy\Repository\TenantRepository;
use Module\Tenancy\Services\TenantService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'app:tenant-create',
    description: 'Create Tenant',
    aliases: ['tenant:create'],
    hidden: false
)]
class TenantCreateCommand extends Command
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
        $helper = $this->getHelper('question');
        $subdomain = $helper->ask($input, $output, new Question('Subdomain: '));
        $name = $helper->ask($input, $output, new Question('Name: '));
        $address = $helper->ask($input, $output, new Question('Address: '));
        $phone = $helper->ask($input, $output, new Question('Phone: '));
        $email = $helper->ask($input, $output, new Question('Email: '));
        $password = $helper->ask($input, $output, new Question('Password: '));

        $tenant = new Tenant();
        $tenant->setSubDomain($subdomain);
        $tenant->setData([
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'dbname' => 'tenancy_' . uniqid(),
            'maintenance' => 0,
        ]);
        $this->tenantRepository->save($tenant);
        $this->tenantService->switchTenant($tenant);

        $command = $this->getApplication()->find('doctrine:database:create');
        $command->run($input, $output);

        $command = $this->getApplication()->find('app:tenant-migrate');
        $arguments = [
            'tenantId' => $tenant->getId(),
        ];
        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        $this->tenantService->createUser($email, $password);

        return Command::SUCCESS;
    }
}
