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
 * Class AbstractControllerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Controller\AbstractController
 */
class AbstractControllerTest extends TestCase
{
    /**
     * @dataProvider invalidVersions
     */
    public function testInvalidVersionShouldThrowException(string $version): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(AbstractController::class, [0, $version]);
    }

    /**
     * @dataProvider validVersions
     */
    public function testValidVersionShouldReturnVersion(string $version): void
    {
        /** @var AbstractController $controller */
        $controller = $this->getMockForAbstractClass(AbstractController::class, [0, $version]);

        static::assertEquals($version, $controller->getVersion());
    }

    public function testSharedMaxAgeShouldReturnValue(): void
    {
        /** @var AbstractController $controller */
        $controller = $this->getMockForAbstractClass(AbstractController::class, [300, 'v1']);

        $method = (new \ReflectionObject($controller))->getMethod('getSharedMaxAge');
        $method->setAccessible(true);

        static::assertEquals(300, $method->invoke($controller));
    }

    /**
     * @dataProvider validVersions
     */
    public function testMajorVersionShouldReturnMajorVersion(string $version, string $majorVersion): void
    {
        /** @var AbstractController $controller */
        $controller = $this->getMockForAbstractClass(AbstractController::class, [0, $version]);

        static::assertEquals($majorVersion, $controller->getMajorVersion());
    }

    public function testShouldCreateJsonResponse(): void
    {
        $data = ['data' => 'data'];
        $status = 201;
        $headers = [];
        $sharedMaxAge = 100;

        $response = new JsonResponse($data, $status, $headers);
        $response->setSharedMaxAge($sharedMaxAge);

        /** @var AbstractController $controller */
        $controller = $this->getMockForAbstractClass(AbstractController::class, [0, 'v1']);

        $controllerResponse = $controller->getJsonResponse($data, $status, $headers, $sharedMaxAge);

        static::assertEquals($response->getContent(), $controllerResponse->getContent());
        static::assertEquals($response->getStatusCode(), $controllerResponse->getStatusCode());
        static::assertEquals($response->headers->all(), $controllerResponse->headers->all());
        static::assertEquals($response->getMaxAge(), $controllerResponse->getMaxAge());
    }

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
