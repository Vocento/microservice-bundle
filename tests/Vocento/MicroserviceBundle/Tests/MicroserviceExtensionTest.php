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

namespace Vocento\MicroserviceBundle\Tests;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Vocento\MicroserviceBundle\DependencyInjection\MicroserviceExtension;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class MicroserviceExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @dataProvider getConfigurations
     *
     * @param $configuration
     * @param $expectation
     */
    public function testAfterLoadingTheCorrectParametersHasBeenSet($configuration, $expectation)
    {
        $this->load($configuration);

        // Check parameters exist
        foreach ($expectation['parameters'] as $parameterName => $expectedParameterValue) {
            $this->assertContainerBuilderHasParameter($parameterName, $expectedParameterValue);
        }

        // Check controller exists
        $this->assertContainerBuilderHasService('vocento.service.controller');

        // Check listeners exist
        $this->assertContainerBuilderHasService('vocento.microservice.check_request_id_or_create.listener');
        $this->assertContainerBuilderHasService('vocento.microservice.exception.listener');
        $this->assertContainerBuilderHasService('vocento.microservice.set_response_request_id_header.listener');
    }

    /**
     * @return array
     */
    public function getConfigurations()
    {
        $testCases = [];

        /**
         * Case 1
         */
        $testCases[] = [
            [
                'name' => 'test',
                'versions' => [
                    'list' => ['v3', 'v1', 'v2'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => false,
                    'microservice.versions.current' => 'v3',
                    'microservice.versions.list' => ['v1', 'v2', 'v3'],
                ],
            ],
        ];

        /**
         * Case 2
         */
        $testCases[] = [
            [
                'name' => 'test',
                'versions' => [
                    'list' => ['v3', 'v1', 'v2'],
                    'current' => 'v2',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => false,
                    'microservice.versions.current' => 'v2',
                    'microservice.versions.list' => ['v1', 'v2', 'v3'],
                ],
            ],
        ];

        /**
         * Case 3
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v1'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.versions.current' => 'v1',
                    'microservice.versions.list' => ['v1'],
                ],
            ],
        ];

        return $testCases;
    }

    /**
     * @inheritDoc
     */
    protected function getContainerExtensions()
    {
        return [
            new MicroserviceExtension(['controllers', 'listeners']),
        ];
    }
}
