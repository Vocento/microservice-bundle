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
     * @param $serviceName
     * @param array $versions
     * @param $currentVersion
     */
    public function invalidServiceControllerArgumentsShouldThrowException($serviceName, array $versions, $currentVersion)
    {
        new ServiceController($serviceName, $versions, $currentVersion);
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     */
    public function serviceActionShouldReturnJsonResponse($serviceName, array $versions, $currentVersion)
    {
        $controller = $this->createController($serviceName, $versions, $currentVersion);
        $response = $controller->serviceAction();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals(json_encode(['service' => ['current' => $currentVersion, 'name' => $serviceName, 'versions' => $versions]]), $response->getContent());
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     */
    public function nameActionShouldReturnJsonResponse($serviceName, array $versions, $currentVersion)
    {
        $controller = $this->createController($serviceName, $versions, $currentVersion);
        $response = $controller->nameAction();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals(json_encode(['service' => ['name' => $serviceName]]), $response->getContent());
    }

    /**
     * @param $serviceName
     * @param array $versions
     * @param $currentVersion
     *
     * @return ServiceController
     */
    private function createController($serviceName, array $versions, $currentVersion)
    {
        return new ServiceController($serviceName, $versions, $currentVersion);
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     */
    public function versionsActionShouldReturnJsonResponse($serviceName, array $versions, $currentVersion)
    {
        $controller = $this->createController($serviceName, $versions, $currentVersion);
        $response = $controller->versionsAction();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals(json_encode(['service' => ['current' => $currentVersion, 'versions' => $versions]]), $response->getContent());
    }

    /**
     * @test
     * @dataProvider validConstructorArguments
     */
    public function currentVersionActionShouldReturnJsonResponse($serviceName, array $versions, $currentVersion)
    {
        $controller = $this->createController($serviceName, $versions, $currentVersion);
        $response = $controller->currentVersionAction();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals(json_encode(['service' => ['version' => $currentVersion]]), $response->getContent());
    }

    /**
     * @return array
     */
    public function invalidConstructorArguments()
    {
        return [
            ['', [], ''],
            ['', [], 'v1'],
            ['', ['v1'], 'v1'],
            ['name', [], ''],
            ['name', [], 'v1'],
            ['name', ['v1'], ''],
            ['name', ['v'], 'v1'],
            ['name', ['v1'], 'v'],
        ];
    }

    /**
     * @return array
     */
    public function validConstructorArguments()
    {
        return [
            ['name', ['v1', 'v2', 'v3'], 'v1'],
            ['name', ['v1', 'v2', 'v3'], 'v2'],
            ['name', ['v1', 'v2', 'v3'], 'v3'],
        ];
    }
}
