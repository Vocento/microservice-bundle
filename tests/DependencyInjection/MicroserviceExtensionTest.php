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

namespace Vocento\MicroserviceBundle\Tests\DependencyInjection;

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
    public function testAfterLoadingTheCorrectParametersHasBeenSet($configuration, $expectation): void
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
    public function getConfigurations(): array
    {
        $testCases = [];

        /*
         * Case 0
         * Repeated version with all majors and latest
         */
        $testCases[] = [
            [
                'name' => 'test',
                'commit-id' => '96c122f',
                'versions' => [
                    'list' => ['v1', 'v3', 'v1', 'v2'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.commit_id' => '96c122f',
                    'microservice.debug' => false,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v3',
                    'microservice.versions.list' => ['v1', 'v2', 'v3'],
                ],
            ],
        ];

        /*
         * Case 1
         * Repeated versions with all majors and current version defined
         */
        $testCases[] = [
            [
                'name' => 'test',
                'commit-id' => 'unknown',
                'versions' => [
                    'list' => ['v3', 'v1', 'v2', 'v2'],
                    'current' => 'v2',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.commit_id' => 'unknown',
                    'microservice.debug' => false,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v2',
                    'microservice.versions.list' => ['v1', 'v2', 'v3'],
                ],
            ],
        ];

        /*
         * Case 2
         * Single version and current version defined
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
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v1',
                    'microservice.versions.list' => ['v1'],
                ],
            ],
        ];

        /*
         * Case 3
         * Deeper version and current set as latest
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v1.1.2'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v1.1.2',
                    'microservice.versions.list' => ['v1.1.2'],
                ],
            ],
        ];

        /*
         * Case 4
         * Repeated versions and current set as latest
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v1.1.2', 'v1.0.0', 'v1.0.0', 'v1.0.0'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v1.1.2',
                    'microservice.versions.list' => ['v1.0.0', 'v1.1.2'],
                ],
            ],
        ];

        /*
         * Case 5
         * Unstable versions and current version set as latest that should result in a stable current version
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v1.1.2', 'v2.0-alpha', 'v1.0.0', 'v2.0-beta', 'v1.0.0', 'v1.0.0'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v1.1.2',
                    'microservice.versions.list' => ['v1.0.0', 'v1.1.2', 'v2.0-alpha', 'v2.0-beta'],
                ],
            ],
        ];

        /*
         * Case 6
         * Unstable versions and current version defined with unstable version
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v1.1.2', 'v2.0-alpha', 'v1.0.0', 'v2.0-beta', 'v1.0.0', 'v1.0.0'],
                    'current' => 'v2.0-beta',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v2.0-beta',
                    'microservice.versions.list' => ['v1.0.0', 'v1.1.2', 'v2.0-alpha', 'v2.0-beta'],
                ],
            ],
        ];

        /*
         * Case 7
         * Unstable versions and current version set as latest
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'versions' => [
                    'list' => ['v2.0-alpha', 'v2.0-beta'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => true,
                    'microservice.versions.current' => 'v2.0-beta',
                    'microservice.versions.list' => ['v2.0-alpha', 'v2.0-beta'],
                ],
            ],
        ];

        /*
         * Case 8
         * Manage exceptions is disabled
         */
        $testCases[] = [
            [
                'name' => 'test',
                'debug' => true,
                'manage_exceptions' => false,
                'versions' => [
                    'list' => ['v1.0', 'v1.1'],
                    'current' => 'latest',
                ],
            ],
            [
                'parameters' => [
                    'microservice.name' => 'test',
                    'microservice.debug' => true,
                    'microservice.manage_exceptions' => false,
                    'microservice.versions.current' => 'v1.1',
                    'microservice.versions.list' => ['v1.0', 'v1.1'],
                ],
            ],
        ];

        return $testCases;
    }

    /**
     * @inheritDoc
     */
    protected function getContainerExtensions(): array
    {
        return [
            new MicroserviceExtension(['controllers', 'listeners']),
        ];
    }
}
