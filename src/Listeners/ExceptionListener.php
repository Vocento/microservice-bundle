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

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
final class ExceptionListener
{
    /** @var bool */
    private $debug = false;

    /** @var bool */
    private $manageExceptions = true;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param bool $debug
     * @param bool $manageExceptions
     * @param LoggerInterface $logger
     */
    public function __construct($debug = false, $manageExceptions = true, LoggerInterface $logger = null)
    {
        $this->debug = $debug;
        $this->manageExceptions = $manageExceptions;
        $this->logger = $logger;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (false === $this->debug && true === $this->manageExceptions) {
            $this->logException($event->getException());

            $response = new Response();

            if ($event->getException() instanceof HttpExceptionInterface) {
                /** @var HttpExceptionInterface $exception */
                $exception = $event->getException();
                $response->headers->add($exception->getHeaders());

                $response->setStatusCode($exception->getStatusCode());
                $response->headers->set('X-Status-Code', $exception->getStatusCode());
            } else {
                $response->setStatusCode(500);
                $response->headers->set('X-Status-Code', 500);
            }

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     */
    protected function logException(\Exception $exception)
    {
        $message = sprintf(
            'Uncaught PHP Exception %s: "%s" at %s line %s',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );

        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, array('exception' => $exception));
            } else {
                $this->logger->error($message, array('exception' => $exception));
            }
        }
    }
}
