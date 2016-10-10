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

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Vocento\RequestId;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
final class SetResponseHeadersListener
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
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Set service-name header
        $event->getRequest()->headers->set('service-name', $this->serviceName);

        // Set request-id header
        if (null === $event->getResponse()->headers->get('request-id')) {
            if (null === $event->getRequest()->headers->get('request-id')) {
                $event->getRequest()->headers->set('request-id', RequestId::create());
            }
            $event->getResponse()->headers->set('request-id', $event->getRequest()->headers->get('request-id'));
        }
    }
}
