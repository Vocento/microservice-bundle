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

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
abstract class AbstractMicroserviceBundle implements BundleInterface
{
    use ContainerAwareTrait;

    /** @var string */
    protected $path;

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function shutdown(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension(): ExtensionInterface
    {
    }

    /**
     * @inheritDoc
     */
    public function getParent(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getNamespace(): string
    {
        $class = \get_class($this);

        return \substr($class, 0, \strrpos($class, '\\'));
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName());
        }

        return $this->path;
    }
}
