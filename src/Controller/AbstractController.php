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
 * Class AbstractController.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @deprecated since version 5.1, use Vocento\MicroserviceBundle\Controller\AbstractMicroserviceController instead.
 */
abstract class AbstractController
{
    /** @var string */
    private $version;

    /** @var int<0, max> */
    private $sharedMaxAge = 0;

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

    /**
     * @throws \InvalidArgumentException when $sharedMaxAge is lower than zero
     */
    public function setSharedMaxAge(int $sharedMaxAge): void
    {
        if ($sharedMaxAge < 0) {
            throw new \InvalidArgumentException('Service shared max age must be zero or greater');
        }

        $this->sharedMaxAge = $sharedMaxAge;
    }

    /**
     * @throws \InvalidArgumentException when $version does not match version format pattern
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
     * @param mixed                $data    The response data
     * @param array<string, mixed> $headers
     */
    public function getJsonResponse(
        $data,
        int $status = 200,
        array $headers = [],
        ?int $sharedMaxAge = null
    ): JsonResponse {
        $response = new JsonResponse($data, $status, $headers);
        $response->setEncodingOptions($response->getEncodingOptions() | \JSON_PRESERVE_ZERO_FRACTION);
        $response->setSharedMaxAge($sharedMaxAge ?? $this->sharedMaxAge);

        return $response;
    }

    public function getSharedMaxAge(): int
    {
        return $this->sharedMaxAge;
    }

    public function getJsonProblemResponse(int $status, string $detail): JsonResponse
    {
        $data = [
            'status' => $status,
            'title' => JsonResponse::$statusTexts[$status] ?? 'Unknown Error',
            'message' => $detail,
        ];

        return new JsonResponse($data, $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}
