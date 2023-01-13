<?php

namespace App\Central\EventListener;

use App\Central\Repository\TenantRepository;
use Doctrine\ORM\EntityManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Cache\ItemInterface;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
#[AsEventListener(event: 'kernel.controller', method: 'onKernelController', priority: 10)]
class TenantListener
{
    /**
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     * @param TenantRepository $tenantRepository
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly EntityManager $entityManager,
        private readonly TenantRepository $tenantRepository
    ) {
    }

    /**
     * @param RequestEvent $event
     * @return void
     * @throws InvalidArgumentException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($this->container->getParameter('hostname') === $request->getHost()) {
            return;
        }

        $subDomain = $request->getHost();
        $subDomain = explode('.', $subDomain)[0];

        $cache = new FilesystemAdapter();
        $tenant = $cache->get($subDomain, function (ItemInterface $item) use ($subDomain) {
            $item->expiresAfter(3600);
            return $this->tenantRepository->findOneBy(['subDomain' => $subDomain]);
        });

        if (!$tenant) {
            throw new NotFoundHttpException('Tenant not found');
        }

        $request->attributes->set('tenant', $tenant);
        $connection = $this->entityManager->getConnection();
        $connection->changeDatabase($tenant->getDbname());
    }

    /**
     * @param ControllerEvent $event
     *
     * @return void
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->getRequest()->attributes->has('tenant')) {
            throw new NotFoundHttpException('Tenant not found');
        }
    }
}
