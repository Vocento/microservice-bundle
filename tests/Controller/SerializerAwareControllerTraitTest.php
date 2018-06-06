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
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class SerializerAwareControllerTraitTest extends TestCase
{
    /**
     * @test
     */
    public function whenCallingSerializeWithoutSerializerShouldReturnNull(): void
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
     * @test
     * @dataProvider serializationCases
     *
     * @param mixed  $object
     * @param string $version
     * @param array  $groups
     */
    public function whenCallingSerializeWithSerializerShouldReturnJson($object, string $version, array $groups): void
    {
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
        $serializerMock->expects(static::any())
            ->method('serialize')
            ->willReturn('');

        return $serializerMock;
    }

    /**
     * @return array
     */
    public function serializationCases(): array
    {
        return [
            [['test' => '1'], 'v1', ['group1', 'group2']],
        ];
    }
}
