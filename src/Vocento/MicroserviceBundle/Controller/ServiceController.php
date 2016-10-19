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
class ServiceController extends AbstractController
{
    /** @var string */
    private $serviceName;

    /** @var array */
    private $versions;

    /**
     * MicroserviceController constructor.
     *
     * @param string $serviceName
     * @param array $versions
     * @param string $currentVersion
     */
    public function __construct($serviceName, array $versions, $currentVersion)
    {
        parent::__construct($currentVersion);

        $this->serviceName = $serviceName;
        $this->versions = $versions;
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
        return new JsonResponse(array('version' => $this->getVersion()));
    }

    /**
     * @return JsonResponse
     */
    public function versionsAction()
    {
        return new JsonResponse(array('versions' => $this->versions, 'current' => $this->getVersion()));
    }
}
