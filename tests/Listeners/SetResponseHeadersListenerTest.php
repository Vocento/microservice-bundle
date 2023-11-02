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

namespace Vocento\MicroserviceBundle\Tests\Listeners;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\SetResponseHeadersListener;

/**
 * Class SetResponseHeadersListenerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Listeners\SetResponseHeadersListener
 *
 * @internal
 */
final class SetResponseHeadersListenerTest extends TestCase
{
    /** @var SetResponseHeadersListener */
    private $listener;

    public function testShouldAddRequestIdIfIsMaster(): void
    {
        $event = $this->createFilteredResponseEvent();

        $this->listener->onKernelResponse($event);

        self::assertArrayHasKey('request-id', $event->getResponse()->headers->all());
        self::assertNotEmpty($event->getResponse()->headers->get('request-id'));
    }

    private function createFilteredResponseEvent(bool $isMasterRequest = true, bool $addRequestId = true): ResponseEvent
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        if ($addRequestId) {
            $request->headers->add(['request-id' => Uuid::uuid4()]);
        }

        $response = new Response();
        $requestType = $isMasterRequest ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST;

        return new ResponseEvent($kernel, $request, $requestType, $response);
    }

    public function testShouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createFilteredResponseEvent(false);

        $this->listener->onKernelResponse($event);

        self::assertArrayNotHasKey('request-id', $event->getResponse()->headers->all());
    }

    public function testShouldCreateRequestIdWhenDoesNotExistsInRequest(): void
    {
        $event = $this->createFilteredResponseEvent(true, false);

        $this->listener->onKernelResponse($event);

        self::assertArrayHasKey('request-id', $event->getResponse()->headers->all());
    }

    protected function setUp(): void
    {
        $this->listener = new SetResponseHeadersListener();
    }
}
