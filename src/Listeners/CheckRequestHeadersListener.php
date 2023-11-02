<?php
/*
 * This file is part of the Vocento Software.
 *
 * (c) Vocento S.A., <desarrollo.dts@vocento.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Vocento\MicroserviceBundle\Listeners;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Vocento\RequestId;

/**
 * Class CheckRequestHeadersListener.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
final class CheckRequestHeadersListener
{
    /** @var string */
    private $serviceName;

    /**
     * CheckRequestIdOrCreateListener constructor.
     */
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (\method_exists($event, 'isMainRequest')) {
            $isMainRequest = $event->isMainRequest();
        } else {
            $isMainRequest = $event->isMasterRequest();
        }

        if (!$isMainRequest) {
            return;
        }

        $request = $event->getRequest();

        if (null !== $request->headers->get('request-id')) {
            return;
        }

        $request->headers->set('request-id', RequestId::create()->getId());
        $request->headers->set('service-name', $this->serviceName);
    }
}
