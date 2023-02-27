<?php

namespace Module\Tenancy;

use Module\Tenancy\Doctrine\DBAL\TenantConnection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class TenancyBundle extends AbstractBundle
{
    /**
     * @return string
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @param array $config
     * @param ContainerConfigurator $container
     * @param ContainerBuilder $builder
     * @return void
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/../config/services.yaml');
    }

    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder      $builder
     *
     * @return void
     */
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $this->prependDoctrineConfig($container, $builder);
        $this->prependDoctrineMigrationsConfig($container, $builder);
    }

    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder      $builder
     *
     * @return void
     */
    private function prependDoctrineConfig(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('doctrine')) {
            return;
        }

        $container->extension('doctrine', [
            'dbal' => [
                'wrapper_class' => TenantConnection::class,
            ],
            'orm' => [
                'mappings' => [
                    'Tenancy' => [
                        'is_bundle' => false,
                        'dir' => __DIR__ . '/../src/Entity',
                        'prefix' => 'Module\Tenancy\Entity',
                        'alias' => 'Tenancy',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param ContainerConfigurator $container
     * @param ContainerBuilder      $builder
     *
     * @return void
     */
    private function prependDoctrineMigrationsConfig(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('doctrine_migrations')) {
            return;
        }

        $container->extension('doctrine_migrations', [
            'migrations_paths' => [
                'Module\Tenancy\Migrations' => __DIR__ . '/../migrations',
            ],
        ]);
    }
}
