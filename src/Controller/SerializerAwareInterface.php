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

namespace Vocento\MicroserviceBundle\Controller;

use JMS\Serializer\SerializerInterface;

interface SerializerAwareInterface
{
    /**
     * @param object|array|scalar $object
     * @param array<string>       $groups
     */
    public function serialize($object, string $version, array $groups = []): ?string;

    public function getSerializer(): ?SerializerInterface;

    public function setSerializer(SerializerInterface $serializer): void;
}
