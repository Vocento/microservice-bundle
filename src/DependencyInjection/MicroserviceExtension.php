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

namespace Vocento\MicroserviceBundle\DependencyInjection;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class MicroserviceExtension.
 *
 * @author Arquitectura <arquitectura@vocento.com>
 */
class MicroserviceExtension extends Extension
{
    /**
     * @param array<array<string, mixed>> $configs
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config/services'));

        $loader->load('controllers.yml');
        $loader->load('listeners.yml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $versions = $this->normalizeVersions($config['versions']['list']);
        $currentVersion = $this->getCurrentVersion($config['versions']['current'], $versions);

        $container->setParameter('microservice.name', $config['name']);
        $container->setParameter('microservice.debug', (bool) $config['debug']);
        $container->setParameter('microservice.manage_exceptions', (bool) $config['manage_exceptions']);
        $container->setParameter('microservice.code_version', $config['code_version']);
        $container->setParameter('microservice.versions.list', $versions);
        $container->setParameter('microservice.versions.current', $currentVersion);
    }

    /**
     * @param string[] $versions
     *
     * @return string[]
     */
    private function normalizeVersions(array $versions): array
    {
        // Sort versions
        $versions = Semver::sort($versions);

        $versionParser = new VersionParser();
        $versionsCount = \count($versions);

        $removedVersionsKey = [];

        foreach ($versions as $i => $iValue) {
            for ($j = $i + 1; $j < $versionsCount; ++$j) {
                if ($i === $j || \in_array($j, $removedVersionsKey, true)) {
                    continue;
                }

                if (Comparator::equalTo(
                    $versionParser->normalize($iValue),
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
     * @param string[] $versions
     */
    private function getCurrentVersion(string $currentVersion, array $versions): string
    {
        if ('latest' !== \strtolower($currentVersion) || 0 === \count($versions)) {
            return $currentVersion;
        }

        $versions = Semver::rsort($versions);

        foreach ($versions as $version) {
            if ('stable' === VersionParser::parseStability($version)) {
                return $version;
            }
        }

        return \array_shift($versions);
    }
}
