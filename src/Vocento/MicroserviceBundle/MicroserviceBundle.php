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

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Vocento\MicroserviceBundle\DependencyInjection\Compiler\RoutingCompilerPass;
use Vocento\MicroserviceBundle\DependencyInjection\MicroserviceExtension;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class MicroserviceBundle implements BundleInterface
{
    use ContainerAwareTrait;

    /** @var string */
    protected $path;

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }

    /**
     * @inheritDoc
     */
    public function shutdown()
    {
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        return new MicroserviceExtension(array('controllers', 'listeners'));
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'MicroserviceBundle';
    }

    /**
     * @inheritDoc
     */
    public function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = dirname($reflected->getFileName());
        }

        return $this->path;
    }
}
