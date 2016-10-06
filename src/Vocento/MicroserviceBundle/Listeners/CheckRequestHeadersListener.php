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

use AppBundle\RequestId;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
final class CheckRequestHeadersListener
{
    /** @var string */
    private $serviceName;

    /**
     * CheckRequestIdOrCreateListener constructor.
     * @param string $serviceName
     */
    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Set service-name header
        $event->getRequest()->headers->set('service-name', $this->serviceName);

        // Set request-id header
        if (null === $requestId = $event->getRequest()->headers->get('request-id', null)) {
            $event->getRequest()->headers->set('request-id', RequestId::create());
        }
    }
}
