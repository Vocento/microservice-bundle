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
use Symfony\Component\HttpFoundation\JsonResponse;
use Vocento\MicroserviceBundle\Controller\AbstractController;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class AbstractControllerTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidVersions
     *
     * @param string $version
     */
    public function invalidVersionShouldThrowException(string $version): void
    {
        $this->getMockForAbstractClass(AbstractController::class, [0, $version]);
    }

    /**
     * @test
     * @dataProvider validVersions
     *
     * @param string $version
     */
    public function validVersionShouldReturnVersion(string $version): void
    {
        $controller = $this->getMockForAbstractClass(
            AbstractController::class,
            [0, $version]
        );
        $this->assertEquals($version, $controller->getVersion());
    }

    /**
     * @test
     */
    public function sharedMaxAgeShouldReturnValue(): void
    {
        $controller = $this->getMockForAbstractClass(
            AbstractController::class,
            [300, 'v1']
        );

        $method = (new \ReflectionObject($controller))->getMethod('getSharedMaxAge');
        $method->setAccessible(true);
        $this->assertEquals(300, $method->invoke($controller));
    }

    /**
     * @test
     * @dataProvider validVersions
     *
     * @param string $version
     * @param string $majorVersion
     */
    public function majorVersionShouldReturnMajorVersion(string $version, string $majorVersion): void
    {
        $controller = $this->getMockForAbstractClass(
            AbstractController::class,
            [0, $version]
        );
        $this->assertEquals($majorVersion, $controller->getMajorVersion());
    }

    /**
     * @test
     */
    public function shouldCreateJsonResponse(): void
    {
        $data = ['data' => 'data'];
        $status = 201;
        $headers = [];
        $sharedMaxAge = 100;

        $response = new JsonResponse($data, $status, $headers);
        $response->setSharedMaxAge($sharedMaxAge);

        $controller = $this->getMockForAbstractClass(
            AbstractController::class,
            [0, 'v1']
        );

        $controllerResponse = $controller->getJsonResponse($data, $status, $headers, $sharedMaxAge);
        $this->assertEquals($response->getContent(), $controllerResponse->getContent());
        $this->assertEquals($response->getStatusCode(), $controllerResponse->getStatusCode());
        $this->assertEquals($response->headers->all(), $controllerResponse->headers->all());
        $this->assertEquals($response->getMaxAge(), $controllerResponse->getMaxAge());
    }

    /**
     * @return \Generator
     */
    public function invalidVersions(): \Generator
    {
        yield [''];
        yield [' '];
        yield ['a'];
        yield ['a123'];
        yield ['1'];
        yield ['12'];
        yield ['123'];
        yield ['1.2'];
        yield ['1.2.3'];
        yield ['v1.'];
        yield ['v1.2.'];
        yield ['v1..2'];
        yield ['v1.2.'];
        yield ['v1.2..3'];
        yield ['v1.2.3.'];
        yield ['v1.2.3.4'];
    }

    /**
     * @return array
     */
    public function validVersions(): \Generator
    {
        yield ['v1', 'v1'];
        yield ['v2.0', 'v2'];
        yield ['v3.1.0', 'v3'];
        yield ['v40.12.0', 'v40'];
        yield ['v500.123.1', 'v500'];
        yield ['v6000.1234.12', 'v6000'];
        yield ['v70000.12345.123', 'v70000'];
        yield ['v800000.123456.1234', 'v800000'];
        yield ['v9000000.1234567.12345', 'v9000000'];
    }
}
