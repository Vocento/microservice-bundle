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

namespace Vocento\MicroserviceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
class MicroserviceExtension implements ExtensionInterface
{
    /** @var array */
    private $configFiles = array();

    /**
     * MicroserviceExtension constructor.
     * @param array $configFiles
     */
    public function __construct(array $configFiles = array())
    {
        $this->configFiles = $configFiles;
    }

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'services'
            )
        );

        foreach ($this->configFiles as $file) {
            $loader->load($file.'.yml');
        }

        $config = $this->processConfiguration(new Configuration(), $configs);
        $config['versions']['list'] = $this->sortVersions($config['versions']['list']);

        $container->setParameter('microservice.name', $config['name']);
        $container->setParameter('microservice.versions.current',$this->getCurrentVersion($config['versions']['current'], $config['versions']['list']));
        $container->setParameter('microservice.versions.list', $this->sortVersions($config['versions']['list']));
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param array $config
     * @return array
     */
    protected function processConfiguration(ConfigurationInterface $configuration, array $config)
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $config);
    }

    /**
     * @param array $versions
     * @return array
     */
    private function sortVersions(array $versions)
    {
        usort($versions, function ($version1, $version2) {
            if ($version1 === $version2) {
                return 0;
            }

            return version_compare($version1, $version2, '<') ? -1 : 1;
        });

        return $versions;
    }

    /**
     * @param string $currentVersion
     * @param array $versions
     * @return string
     */
    private function getCurrentVersion($currentVersion, array $versions)
    {
        if ('latest' === strtolower($currentVersion) && count($versions)) {
            return array_pop($versions);
        }

        return $currentVersion;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return 'http://example.org/schema/dic/'.$this->getAlias();
    }

    /**
     * @inheritDoc
     */
    public function getAlias()
    {
        return 'microservice';
    }

    /**
     * @inheritDoc
     */
    public function getXsdValidationBasePath()
    {
        return 'http://example.org/schema/dic/'.$this->getAlias();
    }
}
