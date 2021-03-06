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
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\SetResponseHeadersListener;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class SetResponseHeadersListenerTest extends TestCase
{
    /** @var string */
    private $serviceName;

    /** @var SetResponseHeadersListener */
    private $listener;

    /**
     * @test
     */
    public function shouldAddRequestIdIfIsMaster(): void
    {
        $event = $this->createFilteredResponseEvent();

        $this->listener->onKernelResponse($event);

        $this->assertArrayHasKey('request-id', $event->getResponse()->headers->all());
        $this->assertNotEmpty($event->getResponse()->headers->get('request-id'));
    }

    /**
     * @param bool $master
     * @param bool $addRequestId
     *
     * @return FilterResponseEvent
     */
    private function createFilteredResponseEvent(bool $master = true, bool $addRequestId = true): FilterResponseEvent
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = new Request();

        if ($addRequestId) {
            $request->headers->add(['request-id' => Uuid::uuid4()]);
        }

        $response = new Response();

        return new FilterResponseEvent(
            $kernel,
            $request,
            ($master ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST),
            $response
        );
    }

    /**
     * @test
     */
    public function shouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createFilteredResponseEvent(false);

        $this->listener->onKernelResponse($event);

        $this->assertArrayNotHasKey('request-id', $event->getResponse()->headers->all());
    }

    /**
     * @test
     */
    public function shouldCreateRequestIdWhenDoesNotExistsInRequest(): void
    {
        $event = $this->createFilteredResponseEvent(true, false);

        $this->listener->onKernelResponse($event);

        $this->assertArrayHasKey('request-id', $event->getResponse()->headers->all());
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->serviceName = 'test-service-name';
        $this->listener = new SetResponseHeadersListener();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->listener = null;
    }
}
