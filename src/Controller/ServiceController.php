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
 * Class ServiceController.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
final class ServiceController extends AbstractController
{
    /** @var string */
    private $codeVersion;

    /** @var string */
    private $serviceName;

    /** @var string[] */
    private $versions;

    /**
     * @param string[] $versions
     *
     * @throws AssertionFailedException
     */
    public function __construct(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ) {
        parent::__construct(0, $currentVersion);

        $this->setServiceName($serviceName);
        $this->setVersions($versions);
        $this->setCodeVersion($codeVersion);
    }

    private function setServiceName(string $serviceName): void
    {
        Assertion::notBlank($serviceName);

        $this->serviceName = $serviceName;
    }

    /**
     * @param string[] $versions
     *
     * @throws AssertionFailedException
     */
    private function setVersions(array $versions): void
    {
        Assertion::greaterOrEqualThan(\count($versions), 1);
        Assertion::allRegex($versions, '/^v(\d+\.)?(\d+\.)?(\d+)$/');

        $this->versions = $versions;
    }

    private function setCodeVersion(string $codeVersion): void
    {
        Assertion::notBlank($codeVersion);

        $this->codeVersion = $codeVersion;
    }

    public function serviceAction(): JsonResponse
    {
        return new JsonResponse([
            'service' => [
                'current' => $this->getVersion(),
                'name' => $this->serviceName,
                'code' => $this->codeVersion,
                'versions' => $this->versions,
            ],
        ]);
    }

    public function nameAction(): JsonResponse
    {
        return new JsonResponse([
            'service' => [
                'name' => $this->serviceName,
            ],
        ]);
    }

    public function currentVersionAction(): JsonResponse
    {
        return new JsonResponse([
            'service' => [
                'version' => $this->getVersion(),
            ],
        ]);
    }

    public function versionsAction(): JsonResponse
    {
        return new JsonResponse([
            'service' => [
                'current' => $this->getVersion(),
                'versions' => $this->versions,
            ],
        ]);
    }
}
