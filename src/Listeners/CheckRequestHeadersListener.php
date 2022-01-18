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

namespace Vocento\MicroserviceBundle\Listeners;

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

    public function onKernelRequest($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $eventRequest = $event->getRequest();

        $eventRequest->headers->set('service-name', $this->serviceName);

        $requestId = $eventRequest->headers->get('request-id');

        if (null === $requestId) {
            $eventRequest->headers->set('request-id', RequestId::create());
        }
    }
}
