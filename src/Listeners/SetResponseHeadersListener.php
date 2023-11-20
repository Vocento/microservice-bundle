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

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Vocento\RequestId;

/**
 * Class SetResponseHeadersListener.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
final class SetResponseHeadersListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (\method_exists($event, 'isMainRequest')) {
            $isMainRequest = $event->isMainRequest();
        } else {
            $isMainRequest = $event->isMasterRequest();
        }

        if (!$isMainRequest) {
            return;
        }

        $response = $event->getResponse();

        if (null !== $response->headers->get('request-id')) {
            return;
        }

        $request = $event->getRequest();
        $requestId = $request->headers->get('request-id', RequestId::create()->getId());

        $request->headers->set('request-id', $requestId);
        $response->headers->set('request-id', $requestId);
    }
}
