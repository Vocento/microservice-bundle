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

namespace Vocento\MicroserviceBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Vocento\MicroserviceBundle\Controller\ServiceController;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class ServiceControllerTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidConstructorArguments
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @return ServiceController
     *
     * @throws \Assert\AssertionFailedException
     */
    public function invalidServiceControllerArgumentsShouldThrowException(
        $serviceName,
        $commitId,
        array $versions,
        $currentVersion
    ): ServiceController {
        new ServiceController($serviceName, $commitId, $versions, $currentVersion);
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @throws \Assert\AssertionFailedException
     */
    public function serviceActionShouldReturnJsonResponse(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $commitId, $versions, $currentVersion);
        $response = $controller->serviceAction();

        $this->assertEquals(
            \json_encode(
                [
                    'service' => [
                        'current' => $currentVersion,
                        'name' => $serviceName,
                        'commit-id' => $commitId,
                        'versions' => $versions,
                    ],
                ]
            ),
            $response->getContent()
        );
    }

    /**
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @return ServiceController
     *
     * @throws \Assert\AssertionFailedException
     */
    private function createController(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ): ServiceController {
        return new ServiceController($serviceName, $commitId, $versions, $currentVersion);
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @throws \Assert\AssertionFailedException
     */
    public function nameActionShouldReturnJsonResponse(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $commitId, $versions, $currentVersion);
        $response = $controller->nameAction();

        $this->assertEquals(\json_encode(['service' => ['name' => $serviceName]]), $response->getContent());
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @throws \Assert\AssertionFailedException
     */
    public function versionsActionShouldReturnJsonResponse(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $commitId, $versions, $currentVersion);
        $response = $controller->versionsAction();

        $this->assertEquals(
            \json_encode(['service' => ['current' => $currentVersion, 'versions' => $versions]]),
            $response->getContent()
        );
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     *
     * @param string $serviceName
     * @param string $commitId
     * @param array  $versions
     * @param string $currentVersion
     *
     * @throws \Assert\AssertionFailedException
     */
    public function currentVersionActionShouldReturnJsonResponse(
        string $serviceName,
        string $commitId,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $commitId, $versions, $currentVersion);
        $response = $controller->currentVersionAction();

        $this->assertEquals(\json_encode(['service' => ['version' => $currentVersion]]), $response->getContent());
    }

    /**
     * @return \Generator
     */
    public function invalidConstructorArguments(): \Generator
    {
        yield ['',     '', [],     ''];
        yield ['',     '', [],     'v1'];
        yield ['',     '', ['v1'], 'v1'];
        yield ['name', '', [],     ''];
        yield ['name', '', [],     'v1'];
        yield ['name', '', ['v1'], ''];
        yield ['name', '', ['v'],  'v1'];
        yield ['name', '', ['v1'], 'v'];
    }

    /**
     * @return \Generator
     */
    public function validConstructorArguments(): \Generator
    {
        yield ['name', 'unknown', ['v1', 'v2', 'v3'], 'v1'];
        yield ['name', '71121dd', ['v1', 'v2', 'v3'], 'v2'];
        yield ['name', '71121dd', ['v1', 'v2', 'v3'], 'v3'];
    }
}
