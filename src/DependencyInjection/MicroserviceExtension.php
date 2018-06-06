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
    private $configFiles;

    /**
     * MicroserviceExtension constructor.
     *
     * @param array $configFiles
     */
    public function __construct(array $configFiles = [])
    {
        $this->configFiles = $configFiles;
    }

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(
                __DIR__
                .\DIRECTORY_SEPARATOR
                .'..'
                .\DIRECTORY_SEPARATOR
                .'Resources'
                .\DIRECTORY_SEPARATOR
                .'config'
                .\DIRECTORY_SEPARATOR
                .'services'
            )
        );

        foreach ($this->configFiles as $file) {
            $loader->load($file.'.yml');
        }

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('microservice.name', $config['name']);
        $container->setParameter('microservice.debug', ($config['debug'] ? true : false));
        $container->setParameter('microservice.manage_exceptions', ($config['manage_exceptions'] ? true : false));
        $container->setParameter('microservice.code_version', $config['code_version']);

        $versions = $this->normalizeVersions($config['versions']['list']);
        $container->setParameter('microservice.versions.list', $versions);
        $container->setParameter(
            'microservice.versions.current',
            $this->getCurrentVersion($config['versions']['current'], $versions)
        );
    }

    /**
     * @param ConfigurationInterface $configuration
     * @param array                  $config
     *
     * @return array
     */
    protected function processConfiguration(ConfigurationInterface $configuration, array $config): array
    {
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $config);
    }

    /**
     * @param array $versions
     *
     * @return array
     */
    private function normalizeVersions(array $versions): array
    {
        // Sort versions
        $versions = Semver::sort($versions);

        $versionParser = new VersionParser();
        $versionsCount = \count($versions);

        $removedVersionsKey = [];

        for ($i = 0; $i < $versionsCount; ++$i) {
            for ($j = $i + 1; $j < $versionsCount; ++$j) {
                if ($i === $j || \in_array($j, $removedVersionsKey, true)) {
                    continue;
                }

                if (Comparator::equalTo(
                    $versionParser->normalize($versions[$i]),
                    $versionParser->normalize($versions[$j])
                )) {
                    $removedVersionsKey[] = $j;
                }
            }
        }

        foreach ($removedVersionsKey as $key) {
            unset($versions[$key]);
        }

        return \array_values($versions);
    }

    /**
     * @param string $currentVersion
     * @param array  $versions
     *
     * @return string
     */
    private function getCurrentVersion($currentVersion, array $versions): string
    {
        if ('latest' === \strtolower($currentVersion) && \count($versions)) {
            $versions = Semver::rsort($versions);
            foreach ($versions as $version) {
                if ('stable' === VersionParser::parseStability($version)) {
                    return $version;
                }
            }

            return \array_shift($versions);
        }

        return $currentVersion;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return 'http://example.org/schema/dic/'.$this->getAlias();
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return 'microservice';
    }

    /**
     * @inheritDoc
     */
    public function getXsdValidationBasePath(): string
    {
        return 'http://example.org/schema/dic/'.$this->getAlias();
    }
}
