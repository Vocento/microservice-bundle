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
     *
     * @return string
     */
    public function serializeObject($object, string $version, array $groups = []): ?string
    {
        if ($this->serializer) {
            return $this->getSerializer()->serialize($object, 'json', $this->createContext($version, $groups));
        }

        return null;
    }

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

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
