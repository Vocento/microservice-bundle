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
    private $commitId;

    /** @var string */
    private $serviceName;

    /** @var array */
    private $versions;

    /**
     * ServiceController constructor.
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @throws \Assert\AssertionFailedException
     */
    public function __construct(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ) {
        parent::__construct(0, $currentVersion);

        $this->setServiceName($serviceName);
        $this->setVersions($versions);
        $this->setCommitId($commitId);
    }

    /**
     * @param string $serviceName
     */
    private function setServiceName(string $serviceName): void
    {
        \Assert\that($serviceName)
            ->string()
            ->notBlank();

        $this->serviceName = $serviceName;
    }

    /**
     * @param array $versions
     *
     * @throws \Assert\AssertionFailedException
     */
    private function setVersions(array $versions): void
    {
        Assertion::isArray($versions);
        Assertion::greaterOrEqualThan(\count($versions), 1);

        $this->versions = [];

        foreach ($versions as $version) {
            Assertion::regex($version, '/^v(\d+\.)?(\d+\.)?(\d+)$/');
            $this->versions[] = $version;
        }
    }

    /**
     * @param string $commitId
     */
    private function setCommitId(string $commitId): void
    {
        \Assert\that($commitId)
            ->string()
            ->notBlank();

        $this->commitId = $commitId;
    }

    /**
     * @return JsonResponse
     */
    public function serviceAction(): JsonResponse
    {
        return new JsonResponse(
            [
                'service' => [
                    'current' => $this->getVersion(),
                    'name' => $this->serviceName,
                    'commit-id' => $this->commitId,
                    'versions' => $this->versions,
                ],
            ]
        );
    }

    /**
     * @return JsonResponse
     */
    public function nameAction(): JsonResponse
    {
        return new JsonResponse(['service' => ['name' => $this->serviceName]]);
    }

    /**
     * @return JsonResponse
     */
    public function currentVersionAction(): JsonResponse
    {
        return new JsonResponse(['service' => ['version' => $this->getVersion()]]);
    }

    /**
     * @return JsonResponse
     */
    public function versionsAction(): JsonResponse
    {
        return new JsonResponse(['service' => ['current' => $this->getVersion(), 'versions' => $this->versions]]);
    }
}
