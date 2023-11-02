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

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Vocento\MicroserviceBundle\Controller\SerializerAwareControllerTrait;
use Vocento\MicroserviceBundle\Controller\SerializerAwareInterface;

/**
 * Class SerializerAwareControllerTraitTest.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 *
 * @covers \Vocento\MicroserviceBundle\Controller\SerializerAwareControllerTrait
 *
 * @internal
 */
final class SerializerAwareControllerTraitTest extends TestCase
{
    public function testWhenCallingSerializeWithoutSerializerShouldReturnNull(): void
    {
        /** @var SerializerAwareInterface $trait */
        $trait = $this->createTraitStub();
        $data = $trait->serialize(['test' => '1'], 'v1', ['group1', 'group2']);

        self::assertNull($data);
    }

    /**
     * Create trait stub.
     */
    private function createTraitStub(): MockObject
    {
        return $this->getMockForTrait(SerializerAwareControllerTrait::class);
    }

    /**
     * @dataProvider provideWhenCallingSerializeWithSerializerShouldReturnJsonCases
     *
     * @param object|array<string, string>|scalar $object
     * @param array<string>                       $groups
     */
    public function testWhenCallingSerializeWithSerializerShouldReturnJson(
        $object,
        string $version,
        array $groups
    ): void {
        $serializer = $this->createSerializerMock();
        $serializer->expects(self::once())
            ->method('serialize');

        /** @var SerializerAwareInterface $trait */
        $trait = $this->createTraitStub();

        /** @var SerializerInterface $serializer */
        $trait->setSerializer($serializer);
        $trait->serialize($object, $version, $groups);
    }

    private function createSerializerMock(): MockObject
    {
        $serializerMock = $this->createMock(SerializerInterface::class);
        $serializerMock->method('serialize')
            ->willReturn('');

        return $serializerMock;
    }

    /**
     * @return array<array{object|array<string, string>|scalar, string, list<string>}>
     */
    public function provideWhenCallingSerializeWithSerializerShouldReturnJsonCases(): iterable
    {
        return [
            [(object) ['test' => '1'], 'v1', ['group1', 'group2']],
            [['test' => '1'], 'v1', ['group1', 'group2']],
            ['1', 'v1', ['group1', 'group2']],
        ];
    }
}
