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

namespace Vocento\MicroserviceBundle\Tests\Listeners;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\CheckRequestHeadersListener;

/**
 * Class CheckRequestHeadersListenerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Listeners\CheckRequestHeadersListener
 */
class CheckRequestHeadersListenerTest extends TestCase
{
    /** @var string */
    private $serviceName;

    /** @var CheckRequestHeadersListener */
    private $listener;

    public function testShouldAddServiceNameHeaderIfIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        static::assertArrayHasKey('service-name', $event->getRequest()->headers->all());
        static::assertEquals($this->serviceName, $event->getRequest()->headers->get('service-name'));
    }

    private function createResponseEvent(bool $isMasterRequest = true)
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();
        $requestType = $isMasterRequest ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST;
        $eventClass = \class_exists('\\Symfony\\Component\\HttpKernel\\Event\\RequestEvent')
            ? '\\Symfony\\Component\\HttpKernel\\Event\\RequestEvent'
            : '\\Symfony\\Component\\HttpKernel\\Event\\GetResponseForExceptionEvent';

        return new $eventClass($kernel, $request, $requestType, new \RuntimeException());
    }

    public function testShouldNotAddServiceNameHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        static::assertArrayNotHasKey('service-name', $event->getRequest()->headers->all());
    }

    public function testShouldAddRequestIdHeaderIfNotPresentAndIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        static::assertArrayHasKey('request-id', $event->getRequest()->headers->all());
        static::assertNotEmpty($event->getRequest()->headers->get('request-id'));
    }

    public function testShouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        static::assertArrayNotHasKey('request-id', $event->getRequest()->headers->all());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->serviceName = 'test-service-name';
        $this->listener = new CheckRequestHeadersListener($this->serviceName);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->listener = null;
    }
}
