<?php

namespace Module\ResetPassword;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ResetPasswordBundle extends AbstractBundle
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
            'orm' => [
                'mappings' => [
                    'ResetPassword' => [
                        'is_bundle' => false,
                        'dir' => __DIR__ . '/../src/Entity',
                        'prefix' => 'Module\ResetPassword\Entity',
                        'alias' => 'ResetPassword',
                    ],
                ],
            ],
        ]);
    }
}
