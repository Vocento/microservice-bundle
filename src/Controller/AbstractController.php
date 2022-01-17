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

namespace Vocento\MicroserviceBundle\Controller;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AbstractController.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
abstract class AbstractController
{
    /** @var string */
    private $version;

    /** @var int */
    private $sharedMaxAge = 0;

    /**
     * AbstractController constructor.
     *
     * @throws AssertionFailedException
     */
    public function __construct(int $sharedMaxAge, string $version)
    {
        $this->setSharedMaxAge($sharedMaxAge);
        $this->setVersion($version);
    }

    private function setSharedMaxAge(int $sharedMaxAge): void
    {
        if ($sharedMaxAge > 0) {
            $this->sharedMaxAge = $sharedMaxAge;
        }
    }

    /**
     * @throws AssertionFailedException
     */
    private function setVersion(string $version): void
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
     * @param mixed                $data
     * @param array<string, mixed> $headers
     */
    public function getJsonResponse(
        $data,
        int $status = 200,
        array $headers = [],
        int $sharedMaxAge = 0
    ): JsonResponse {
        $response = new JsonResponse($data, $status, $headers);
        $response->setEncodingOptions($response->getEncodingOptions() | \JSON_PRESERVE_ZERO_FRACTION);
        $response->setSharedMaxAge($sharedMaxAge);

        return $response;
    }

    protected function getSharedMaxAge(): int
    {
        return $this->sharedMaxAge;
    }
}
