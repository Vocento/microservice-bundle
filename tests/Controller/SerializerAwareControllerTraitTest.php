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

use JMS\Serializer\SerializerInterface;
use Vocento\MicroserviceBundle\Controller\SerializerAwareControllerTrait;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class SerializerAwareControllerTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function whenCallingSerializeWithoutSerializerShouldReturnNull()
    {
        $trait = $this->createTraitStub();

        $this->assertNull($trait->serializeObject(['test' => '1'], 'v1', ['group1', 'group2']));
    }

    /**
     * Create trait stub
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createTraitStub()
    {
        $trait = $this->getMockForTrait(SerializerAwareControllerTrait::class);

        return $trait;
    }

    /**
     * @test
     * @dataProvider serializationCases
     */
    public function whenCallingSerializeWithSerializerShouldReturnJson($object, $version, $groups)
    {
        $serializer = $this->createSerializerMock();
        $serializer->expects($this->once())->method('serialize');

        $trait = $this->createTraitStub();
        $trait->setSerializer($serializer);

        $trait->serializeObject($object, $version, $groups);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createSerializerMock()
    {
        return $this->getMockBuilder(SerializerInterface::class)
            ->getMock();
    }

    /**
     * @return array
     */
    public function serializationCases()
    {
        return [
            [['test' => '1'], 'v1', ['group1', 'group2']],
        ];
    }
}
