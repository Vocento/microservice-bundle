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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\CheckRequestHeadersListener;

/**
 * Class CheckRequestHeadersListenerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Listeners\CheckRequestHeadersListener
 *
 * @internal
 */
final class CheckRequestHeadersListenerTest extends TestCase
{
    /** @var string */
    private $serviceName;

    /** @var CheckRequestHeadersListener */
    private $listener;

    public function testShouldAddServiceNameHeaderIfIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        self::assertArrayHasKey('service-name', $event->getRequest()->headers->all());
        self::assertSame($this->serviceName, $event->getRequest()->headers->get('service-name'));
    }

    private function createResponseEvent(bool $isMasterRequest = true): ExceptionEvent
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $requestType = $isMasterRequest ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST;

        return new ExceptionEvent($kernel, $request, $requestType, new \RuntimeException());
    }

    public function testShouldNotAddServiceNameHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        self::assertArrayNotHasKey('service-name', $event->getRequest()->headers->all());
    }

    public function testShouldAddRequestIdHeaderIfNotPresentAndIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        self::assertArrayHasKey('request-id', $event->getRequest()->headers->all());
        self::assertNotEmpty($event->getRequest()->headers->get('request-id'));
    }

    public function testShouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        self::assertArrayNotHasKey('request-id', $event->getRequest()->headers->all());
    }

    protected function setUp(): void
    {
        $this->serviceName = 'test-service-name';
        $this->listener = new CheckRequestHeadersListener($this->serviceName);
    }
}
