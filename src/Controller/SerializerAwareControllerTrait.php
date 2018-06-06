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
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
trait SerializerAwareControllerTrait
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param mixed  $object
     * @param string $version
     * @param array  $groups
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

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $version
     * @param array  $groups
     *
     * @return SerializationContext
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
