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
     * @inheritDoc
     */
    protected function getContainerExtensions()
    {
        return array(
            new MicroserviceExtension(array('controllers', 'listeners'))
        );
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set()
    {
        $this->load(array(
            'name' => 'test',
            'versions' => array(
                'list' => array('v3', 'v1', 'v2'),
                'current' => 'latest'
            )
        ));

        // Check parameters exist
        $this->assertContainerBuilderHasParameter('microservice.name', 'test');
        $this->assertContainerBuilderHasParameter('microservice.versions.current', 'v3');
        $this->assertContainerBuilderHasParameter('microservice.versions.list', array('v1', 'v2', 'v3'));

        // Check controller exists
        $this->assertContainerBuilderHasService('vocento.microservice.controller');

        // Check listeners exist
        $this->assertContainerBuilderHasService('vocento.microservice.check_request_id_or_create.listener');
        $this->assertContainerBuilderHasService('vocento.microservice.exception.listener');
        $this->assertContainerBuilderHasService('vocento.microservice.set_response_request_id_header.listener');
    }
}
