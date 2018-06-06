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
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\CheckRequestHeadersListener;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class CheckRequestHeadersListenerTest extends TestCase
{
    /** @var string */
    private $serviceName;

    /** @var CheckRequestHeadersListener */
    private $listener;

    /**
     * @test
     */
    public function shouldAddServiceNameHeaderIfIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        $this->assertArrayHasKey('service-name', $event->getRequest()->headers->all());
        $this->assertEquals($this->serviceName, $event->getRequest()->headers->get('service-name'));
    }

    /**
     * @param bool $master
     *
     * @return GetResponseEvent
     */
    private function createResponseEvent(bool $master = true): GetResponseEvent
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = new Request();

        return new GetResponseEvent($kernel, $request, ($master ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST));
    }

    /**
     * @test
     */
    public function shouldNotAddServiceNameHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        $this->assertArrayNotHasKey('service-name', $event->getRequest()->headers->all());
    }

    /**
     * @test
     */
    public function shouldAddRequestIdHeaderIfNotPresentAndIsMaster(): void
    {
        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        $this->assertArrayHasKey('request-id', $event->getRequest()->headers->all());
        $this->assertNotEmpty($event->getRequest()->headers->get('request-id'));
    }

    /**
     * @test
     */
    public function shouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createResponseEvent(false);

        $this->listener->onKernelRequest($event);

        $this->assertArrayNotHasKey('request-id', $event->getRequest()->headers->all());
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->serviceName = 'test-service-name';
        $this->listener = new CheckRequestHeadersListener($this->serviceName);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->listener = null;
    }
}
