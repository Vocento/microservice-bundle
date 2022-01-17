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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vocento\MicroserviceBundle\Controller\SerializerAwareControllerTrait;

/**
 * Class SerializerAwareControllerTraitTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Controller\SerializerAwareControllerTrait
 */
class SerializerAwareControllerTraitTest extends TestCase
{
    public function testWhenCallingSerializeWithoutSerializerShouldReturnNull(): void
    {
        /** @var SerializerAwareControllerTrait $trait */
        $trait = $this->createTraitStub();

        static::assertNull($trait->serializeObject(['test' => '1'], 'v1', ['group1', 'group2']));
    }

    /**
     * Create trait stub.
     *
     * @return MockObject|SerializerAwareControllerTrait
     */
    private function createTraitStub(): MockObject
    {
        return $this->getMockForTrait(SerializerAwareControllerTrait::class);
    }

    /**
     * @dataProvider serializationCases
     *
     * @param object|array|scalar $object
     */
    public function testWhenCallingSerializeWithSerializerShouldReturnJson(
        $object,
        string $version,
        array $groups
    ): void {
        $serializer = $this->createSerializerMock();
        $serializer->expects($this->once())->method('serialize');

        $trait = $this->createTraitStub();
        $trait->setSerializer($serializer);

        $trait->serializeObject($object, $version, $groups);
    }

    /**
     * @return MockObject|SerializerInterface
     */
    private function createSerializerMock(): MockObject
    {
        $serializerMock = $this->createMock(SerializerInterface::class);
        $serializerMock->method('serialize')
            ->willReturn('');

        return $serializerMock;
    }

    public function serializationCases(): array
    {
        return [
            [['test' => '1'], 'v1', ['group1', 'group2']],
        ];
    }
}
