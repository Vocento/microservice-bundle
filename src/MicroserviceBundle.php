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

namespace Vocento\MicroserviceBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Vocento\MicroserviceBundle\DependencyInjection\MicroserviceExtension;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class MicroserviceBundle extends AbstractMicroserviceBundle
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'MicroserviceBundle';
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension(): ExtensionInterface
    {
        return new MicroserviceExtension(['controllers', 'listeners']);
    }
}
