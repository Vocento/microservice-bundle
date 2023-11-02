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
use Vocento\MicroserviceBundle\Controller\ServiceController;

/**
 * Class ServiceControllerTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Controller\ServiceController
 *
 * @internal
 */
final class ServiceControllerTest extends TestCase
{
    /**
     * @dataProvider provideInvalidServiceControllerArgumentsShouldThrowExceptionCases
     *
     * @param list<string> $versions
     */
    public function testInvalidServiceControllerArgumentsShouldThrowException(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        new ServiceController($serviceName, $codeVersion, $versions, $currentVersion);
    }

    /**
     * @dataProvider validConstructorArguments
     *
     * @param list<string> $versions
     */
    public function testServiceActionShouldReturnJsonResponse(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $codeVersion, $versions, $currentVersion);
        $response = $controller->serviceAction();

        self::assertSame(
            \json_encode(
                [
                    'service' => [
                        'current' => $currentVersion,
                        'name' => $serviceName,
                        'code' => $codeVersion,
                        'versions' => $versions,
                    ],
                ]
            ),
            $response->getContent()
        );
    }

    /**
     * @param list<string> $versions
     */
    private function createController(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): ServiceController {
        return new ServiceController($serviceName, $codeVersion, $versions, $currentVersion);
    }

    /**
     * @dataProvider validConstructorArguments
     *
     * @param list<string> $versions
     */
    public function testNameActionShouldReturnJsonResponse(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $codeVersion, $versions, $currentVersion);
        $response = $controller->nameAction();

        self::assertSame(\json_encode(['service' => ['name' => $serviceName]]), $response->getContent());
    }

    /**
     * @dataProvider validConstructorArguments
     *
     * @param list<string> $versions
     */
    public function testVersionsActionShouldReturnJsonResponse(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $codeVersion, $versions, $currentVersion);
        $response = $controller->versionsAction();

        self::assertSame(
            \json_encode(['service' => ['current' => $currentVersion, 'versions' => $versions]]),
            $response->getContent()
        );
    }

    /**
     * @dataProvider validConstructorArguments
     *
     * @param list<string> $versions
     */
    public function testCurrentVersionActionShouldReturnJsonResponse(
        string $serviceName,
        string $codeVersion,
        array $versions,
        string $currentVersion
    ): void {
        $controller = $this->createController($serviceName, $codeVersion, $versions, $currentVersion);
        $response = $controller->currentVersionAction();

        self::assertSame(\json_encode(['service' => ['version' => $currentVersion]]), $response->getContent());
    }

    /**
     * @return \Generator<array{string, string, list<string>, string}>
     */
    public function provideInvalidServiceControllerArgumentsShouldThrowExceptionCases(): iterable
    {
        yield ['', '', [], ''];
        yield ['', '', [], 'v1'];
        yield ['', '', ['v1'], 'v1'];
        yield ['name', '', [], ''];
        yield ['name', '', [], 'v1'];
        yield ['name', '', ['v1'], ''];
        yield ['name', '', ['v'], 'v1'];
        yield ['name', '', ['v1'], 'v'];
    }

    /**
     * @return \Generator<array{string, string, list<string>, string}>
     */
    public function validConstructorArguments(): iterable
    {
        yield ['name', 'unknown', ['v1', 'v2', 'v3'], 'v1'];
        yield ['name', '71121dd', ['v1', 'v2', 'v3'], 'v2'];
        yield ['name', '71121dd', ['v1', 'v2', 'v3'], 'v3'];
    }
}
