<?php

namespace App\Central\EventSubscriber;

use App\Central\Exception\MaintenanceModeException;
use App\Central\Repository\TenantRepository;
use App\Central\Services\TenantService;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TenantSubscriber implements EventSubscriberInterface
{
    /**
     * @param TenantService $tenantService
     * @param TenantRepository $tenantRepository
     */
    public function __construct(
        private readonly TenantService $tenantService,
        private readonly TenantRepository $tenantRepository
    ) {
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 500]
            ],
            KernelEvents::EXCEPTION => [
                ['onKernelException', 500]
            ]
        ];
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof MaintenanceModeException) {
            $response = new JsonResponse([
                'message' => $exception->getMessage()
            ], $exception->getCode());
            $event->setResponse($response);
        }
    }

    /**
     * @param RequestEvent $event
     * @return void
     * @throws Exception|MaintenanceModeException
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->switchTenant($event->getRequest());
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception|MaintenanceModeException
     */
    private function switchTenant(Request $request): void
    {
        $parts = explode('.', $request->getHost());
        if (count($parts) > 2) {
            $tenant = $this->tenantRepository->findOneBy(['subDomain' => $parts[0]]);
            if ($tenant->isMaintenance())
            {
                $message = 'Tenant - '. $tenant->getConfig('name') .' is in maintenance mode';
                throw new MaintenanceModeException($message, 503);
            }
            $this->tenantService->switchTenant($tenant);
            $request->attributes->set('tenant', $tenant);
        }
    }
}
