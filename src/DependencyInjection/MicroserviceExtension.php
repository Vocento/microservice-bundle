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

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
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
     *
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

        $container->setParameter('microservice.name', $config['name']);
        $container->setParameter('microservice.debug', ($config['debug'] ? true : false));

        $versions = $this->normalizeVersions($config['versions']['list']);
        $container->setParameter('microservice.versions.list', $versions);
        $container->setParameter('microservice.versions.current', $this->getCurrentVersion($config['versions']['current'], $versions));
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param array $config
     *
     * @return array
     */
    protected function processConfiguration(ConfigurationInterface $configuration, array $config)
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $config);
    }

    /**
     * @param array $versions
     *
     * @return array
     */
    private function normalizeVersions(array $versions)
    {
        // Sort versions
        $versions = Semver::sort($versions);

        $versionParser = new VersionParser();
        $versionsCount = count($versions);
        $removeKeys = [];

        for ($i = 0; $i < $versionsCount; $i++) {
            $version1 = $versionParser->normalize($versions[$i]);

            for ($j = $i + 1; $j < $versionsCount; $j++) {
                if (in_array($j, $removeKeys)) {
                    continue;
                }

                if (Comparator::equalTo($version1, $versionParser->normalize($versions[$j], true))) {
                    $removeKeys[] = $j;
                }
            }
        }

        foreach ($removeKeys as $key) {
            unset($versions[$key]);
        }

        return array_values($versions);
    }

    /**
     * @param string $currentVersion
     * @param array $versions
     *
     * @return string
     */
    private function getCurrentVersion($currentVersion, array $versions)
    {
        if ('latest' === strtolower($currentVersion) && count($versions)) {
            $versions = Semver::rsort($versions);
            foreach ($versions as $version) {
                if ('stable' === VersionParser::parseStability($version)) {
                    return $version;
                }
            }

            return array_shift($versions);
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
