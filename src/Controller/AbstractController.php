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

    /**
     * AbstractController constructor.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->setVersion($version);
    }

    /**
     * @param $version
     */
    private function setVersion($version)
    {
        Assertion::regex($version, '/^v(\d+\.)?(\d+\.)?(\d+)$/');

        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getMajorVersion()
    {
        return strtok($this->version, '.');
    }

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $sharedMaxAge
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getJsonResponse(array $data, $status = 200, array $headers = [], $sharedMaxAge = 0)
    {
        return JsonResponse::create($data, $status, $headers)
            ->setSharedMaxAge($sharedMaxAge);
    }
}
