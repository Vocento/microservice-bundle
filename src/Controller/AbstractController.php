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
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
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
     * @param int    $sharedMaxAge
     * @param string $version
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(int $sharedMaxAge, string $version)
    {
        $this->setSharedMaxAge($sharedMaxAge);
        $this->setVersion($version);
    }

    /**
     * @param int $sharedMaxAge
     */
    private function setSharedMaxAge($sharedMaxAge): void
    {
        if (\is_int($sharedMaxAge) && $sharedMaxAge > 0) {
            $this->sharedMaxAge = $sharedMaxAge;
        }
    }

    /**
     * @param string $version
     *
     * @throws \Assert\AssertionFailedException
     */
    private function setVersion(string $version): void
    {
        Assertion::regex($version, '/^v(\d+\.)?(\d+\.)?(\d+)$/');

        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getMajorVersion(): string
    {
        return \strtok($this->version, '.');
    }

    /**
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @param int   $sharedMaxAge
     *
     * @return JsonResponse
     */
    public function getJsonResponse(
        array $data,
        int $status = 200,
        array $headers = [],
        int $sharedMaxAge = 0
    ): JsonResponse {
        $response = JsonResponse::create($data, $status, $headers);
        $response->setSharedMaxAge($sharedMaxAge);

        return $response;
    }

    /**
     * @return int
     */
    protected function getSharedMaxAge(): int
    {
        return $this->sharedMaxAge;
    }
}
