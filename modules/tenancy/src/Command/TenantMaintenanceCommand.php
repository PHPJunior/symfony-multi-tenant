<?php

namespace Module\Tenancy\Command;

use Module\Tenancy\Repository\TenantRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:tenant-maintenance',
    description: 'Set Tenant Maintenance Mode',
    aliases: ['tenant:maintenance'],
    hidden: false,
)]
class TenantMaintenanceCommand extends Command
{
    /**
     * @param TenantRepository $tenantRepository
     */
    public function __construct(
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
            ->addArgument('mode', InputArgument::REQUIRED, 'up|down')
            ->addUsage('app:tenant-maintenance 1 up|down');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenantId = $input->getArgument('tenantId');
        $tenant = $this->tenantRepository->find($tenantId);

        if (!$tenant) {
            $output->writeln('Tenant not found');
            return Command::FAILURE;
        }

        $mode = $input->getArgument('mode');
        if (!in_array($mode, ['up', 'down'])) {
            $output->writeln('Invalid mode');
            return Command::FAILURE;
        }

        $tenant->setMaintenance($mode === 'down');
        $this->tenantRepository->save($tenant);
        $output->writeln(sprintf('Tenant %s maintenance mode set to %s', $tenant->getConfig('name'), $mode));
        return Command::SUCCESS;
    }
}
