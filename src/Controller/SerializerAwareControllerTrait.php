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

namespace Vocento\MicroserviceBundle\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Trait SerializerAwareControllerTrait.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
trait SerializerAwareControllerTrait
{
    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param object|array|scalar $object
     * @param array<string>       $groups
     */
    public function serialize($object, string $version, array $groups = []): ?string
    {
        if (!$this->serializer) {
            return null;
        }

        $context = $this->createContext($version, $groups);

        return $this->serializer->serialize($object, 'json', $context);
    }

    public function getSerializer(): ?SerializerInterface
    {
        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array<string> $groups
     */
    protected function createContext(string $version, array $groups): SerializationContext
    {
        foreach ($groups as $index => $group) {
            $majorVersion = \strtok($version, '.');
            $groups[$index] = $majorVersion.'.'.$group;
        }

        $context = SerializationContext::create();
        $context->setGroups($groups);

        return $context;
    }
}
