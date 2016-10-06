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

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class MicroserviceController
{
    /** @var string */
    private $serviceName;

    /** @var array */
    private $versions;

    /** @var string */
    private $currentVersion;

    /**
     * MicroserviceController constructor.
     * @param $serviceName
     * @param array $versions
     * @param $currentVersion
     */
    public function __construct($serviceName, array $versions, $currentVersion)
    {
        $this->serviceName = $serviceName;
        $this->versions = $versions;
        $this->currentVersion = $currentVersion;
    }

    /**
     * @return JsonResponse
     */
    public function nameAction()
    {
        return new JsonResponse(array('name' => $this->serviceName));
    }

    /**
     * @return JsonResponse
     */
    public function currentVersionAction()
    {
        return new JsonResponse(array('version' => $this->currentVersion));
    }

    /**
     * @return JsonResponse
     */
    public function versionsAction()
    {
        return new JsonResponse(array('versions' => $this->versions, 'current' => $this->currentVersion));
    }
}
