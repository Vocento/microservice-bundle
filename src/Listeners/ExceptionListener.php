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

namespace Vocento\MicroserviceBundle\Listeners;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
final class ExceptionListener
{
    /** @var bool */
    private $debug;

    /** @var bool */
    private $manageExceptions;

    /** @var LoggerInterface|null */
    private $logger;

    public function __construct(bool $debug = false, bool $manageExceptions = true, LoggerInterface $logger = null)
    {
        $this->debug = $debug;
        $this->manageExceptions = $manageExceptions;
        $this->logger = $logger;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->debug || !$this->manageExceptions) {
            return;
        }

        if (\method_exists($event, 'getThrowable')) {
            $exception = $event->getThrowable();
        } else {
            $exception = $event->getException();
        }

        $this->logException($exception);

        $response = new Response('', 500);

        if ($exception instanceof HttpExceptionInterface) {
            $headers = $exception->getHeaders();
            $statusCode = $exception->getStatusCode();

            $response->headers->add($headers);
            $response->setStatusCode($statusCode);
        }

        $event->setResponse($response);
        $event->stopPropagation();
    }

    /**
     * Logs an exception.
     *
     * @param \Throwable $exception The \Exception instance
     */
    private function logException(\Throwable $exception): void
    {
        if (null === $this->logger) {
            return;
        }

        $message = \sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            \get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        $context = [
            'exception' => $exception,
        ];

        if (!($exception instanceof HttpExceptionInterface)) {
            $this->logger->error($message, $context);

            return;
        }

        if ($exception->getStatusCode() < Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->error($message, $context);
        }

        $this->logger->critical($message, $context);
    }
}
