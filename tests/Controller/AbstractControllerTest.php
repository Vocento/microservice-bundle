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

declare(strict_types=1);

namespace Vocento\MicroserviceBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vocento\MicroserviceBundle\Controller\AbstractMicroserviceController;

/**
 * Class AbstractControllerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Controller\AbstractController
 * @covers \Vocento\MicroserviceBundle\Controller\AbstractMicroserviceController
 *
 * @internal
 */
final class AbstractControllerTest extends TestCase
{
    /**
     * @dataProvider provideInvalidVersionShouldThrowExceptionCases
     */
    public function testInvalidVersionShouldThrowException(string $version): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getMockForAbstractClass(AbstractMicroserviceController::class, [0, $version]);
    }

    /**
     * @dataProvider validVersions
     */
    public function testValidVersionShouldReturnVersion(string $version): void
    {
        /** @var AbstractMicroserviceController $controller */
        $controller = $this->getMockForAbstractClass(AbstractMicroserviceController::class, [0, $version]);

        self::assertSame($version, $controller->getVersion());
    }

    public function testSharedMaxAgeShouldReturnValue(): void
    {
        /** @var AbstractMicroserviceController $controller */
        $controller = $this->getMockForAbstractClass(AbstractMicroserviceController::class, [300, 'v1']);

        $method = (new \ReflectionObject($controller))->getMethod('getSharedMaxAge');
        $method->setAccessible(true);

        self::assertSame(300, $method->invoke($controller));
    }

    /**
     * @dataProvider validVersions
     */
    public function testMajorVersionShouldReturnMajorVersion(string $version, string $majorVersion): void
    {
        /** @var AbstractMicroserviceController $controller */
        $controller = $this->getMockForAbstractClass(AbstractMicroserviceController::class, [0, $version]);

        self::assertSame($majorVersion, $controller->getMajorVersion());
    }

    public function testShouldCreateJsonResponse(): void
    {
        $data = ['data' => 'data'];

        /** @var AbstractMicroserviceController $controller */
        $controller = $this->getMockForAbstractClass(AbstractMicroserviceController::class, [300, 'v1']);

        $container = $this->createMock(ContainerInterface::class);
        $controller->setContainer($container);

        $controllerResponse = $controller->getJsonResponse($data, 201);

        self::assertJsonStringEqualsJsonString(\json_encode($data), (string) $controllerResponse->getContent());
        self::assertSame(201, $controllerResponse->getStatusCode());
        self::assertSame(300, $controllerResponse->getMaxAge());
    }

    /**
     * @return \Generator<array{string}>
     */
    public function provideInvalidVersionShouldThrowExceptionCases(): iterable
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
     * @return \Generator<array{string, string}>
     */
    public function validVersions(): iterable
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
