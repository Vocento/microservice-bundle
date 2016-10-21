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
final class ServiceController extends AbstractController
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

        $this->setServiceName($serviceName);
        $this->setVersions($versions);
    }

    /**
     * @param $serviceName
     */
    private function setServiceName($serviceName)
    {
        \Assert\that($serviceName)
            ->string()
            ->notBlank();

        $this->serviceName = $serviceName;
    }

    /**
     * @param array $versions
     */
    private function setVersions(array $versions)
    {
        Assertion::isArray($versions);
        Assertion::greaterOrEqualThan(count($versions), 1);

        $this->versions = [];

        foreach ($versions as $version) {
            Assertion::regex($version, '/^v(\d+\.)?(\d+\.)?(\d+)$/');
            $this->versions[] = $version;
        }
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
