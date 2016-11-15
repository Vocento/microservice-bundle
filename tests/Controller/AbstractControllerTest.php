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

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class AbstractControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     * @dataProvider invalidVersions
     */
    public function invalidVersionShouldThrowException($version)
    {
        $this->getMockForAbstractClass('Vocento\MicroserviceBundle\Controller\AbstractController', [$version]);
    }

    /**
     * @test
     *
     * @dataProvider validVersions
     */
    public function validVersionShouldReturnVersion($version, $majorVersion)
    {
        $controller = $this->getMockForAbstractClass('Vocento\MicroserviceBundle\Controller\AbstractController', [$version]);
        $this->assertEquals($version, $controller->getVersion());
    }

    /**
     * @test
     * @dataProvider validVersions
     */
    public function majorVersionShouldReturnMajorVersion($version, $majorVersion)
    {
        $controller = $this->getMockForAbstractClass('Vocento\MicroserviceBundle\Controller\AbstractController', [$version]);
        $this->assertEquals($majorVersion, $controller->getMajorVersion());
    }

    /**
     * @return array
     */
    public function invalidVersions()
    {
        return [
            [''],
            [' '],
            ['a'],
            ['a123'],
            ['1'],
            ['12'],
            ['123'],
            ['1.2'],
            ['1.2.3'],
            ['v1.'],
            ['v1.2.'],
            ['v1..2'],
            ['v1.2.'],
            ['v1.2..3'],
            ['v1.2.3.'],
            ['v1.2.3.4'],
        ];
    }

    /**
     * @return array
     */
    public function validVersions()
    {
        return [
            ['v1', 'v1'],
            ['v2.0', 'v2'],
            ['v3.1.0', 'v3'],
            ['v40.12.0', 'v40'],
            ['v500.123.1', 'v500'],
            ['v6000.1234.12', 'v6000'],
            ['v70000.12345.123', 'v70000'],
            ['v800000.123456.1234', 'v800000'],
            ['v9000000.1234567.12345', 'v9000000'],
        ];
    }
}
