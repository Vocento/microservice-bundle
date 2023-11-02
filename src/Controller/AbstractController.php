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

namespace Vocento\MicroserviceBundle\Controller;

use Assert\Assertion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as FrameworkAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AbstractController.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @deprecated since version 5.2, use Vocento\MicroserviceBundle\Controller\AbstractMicroserviceController instead.
 */
abstract class AbstractController extends FrameworkAbstractController
{
    /** @var string */
    protected $version;

    /** @var int */
    protected $sharedMaxAge = 0;

    /**
     * AbstractController constructor.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $sharedMaxAge, string $version)
    {
        $this->setSharedMaxAge($sharedMaxAge);
        $this->setVersion($version);
    }

    protected function setSharedMaxAge(int $sharedMaxAge): void
    {
        if ($sharedMaxAge > 0) {
            $this->sharedMaxAge = $sharedMaxAge;
        }
    }

    protected function getSharedMaxAge(): int
    {
        return $this->sharedMaxAge;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function setVersion(string $version): void
    {
        Assertion::regex($version, '/^v(\d+\.)?(\d+\.)?(\d+)$/');

        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getMajorVersion(): string
    {
        $token = \strtok($this->version, '.');

        if (false === $token) {
            return '';
        }

        return $token;
    }

    /**
     * @param mixed                $data    The response data
     * @param array<string, mixed> $headers
     */
    public function getJsonResponse($data, int $status = 200, array $headers = []): JsonResponse
    {
        return $this->json($data, $status, $headers)
            ->setSharedMaxAge($this->sharedMaxAge);
    }
}
