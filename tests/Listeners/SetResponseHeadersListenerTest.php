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
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Vocento\MicroserviceBundle\Listeners\SetResponseHeadersListener;

/**
 * Class SetResponseHeadersListenerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Listeners\SetResponseHeadersListener
 */
class SetResponseHeadersListenerTest extends TestCase
{
    /** @var SetResponseHeadersListener */
    private $listener;

    public function testShouldAddRequestIdIfIsMaster(): void
    {
        $event = $this->createFilteredResponseEvent();

        $this->listener->onKernelResponse($event);

        static::assertArrayHasKey('request-id', $event->getResponse()->headers->all());
        static::assertNotEmpty($event->getResponse()->headers->get('request-id'));
    }

    private function createFilteredResponseEvent(bool $isMasterRequest = true, bool $addRequestId = true)
    {
        /** @var MockObject|HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        if ($addRequestId) {
            $request->headers->add(['request-id' => Uuid::uuid4()]);
        }

        $response = new Response();
        $requestType = $isMasterRequest ? HttpKernelInterface::MASTER_REQUEST : HttpKernelInterface::SUB_REQUEST;
        $eventClass = \class_exists('\\Symfony\\Component\\HttpKernel\\Event\\ResponseEvent')
            ? '\\Symfony\\Component\\HttpKernel\\Event\\ResponseEvent'
            : '\\Symfony\\Component\\HttpKernel\\Event\\FilterResponseEvent';

        return new $eventClass($kernel, $request, $requestType, $response);
    }

    public function testShouldNotAddRequestIdHeaderIfIsNotMaster(): void
    {
        $event = $this->createFilteredResponseEvent(false);

        $this->listener->onKernelResponse($event);

        static::assertArrayNotHasKey('request-id', $event->getResponse()->headers->all());
    }

    public function testShouldCreateRequestIdWhenDoesNotExistsInRequest(): void
    {
        $event = $this->createFilteredResponseEvent(true, false);

        $this->listener->onKernelResponse($event);

        static::assertArrayHasKey('request-id', $event->getResponse()->headers->all());
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->listener = new SetResponseHeadersListener();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->listener = null;
    }
}
