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

use Assert\Assertion;

/**
 * @author Ariel Ferrandini <aferrandini@vocento.com>
 */
abstract class AbstractController
{
    /** @var string */
    private $version;

    /**
     * AbstractController constructor.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->setVersion($version);
    }

    /**
     * @param $version
     */
    private function setVersion($version)
    {
        Assertion::regex($version, '/v[0-9][\.0-9]{0,2}/');

        $this->version = $version;
    }

    /**
     * @return string
     */
    protected function getVersion()
    {
        return $this->version;
    }
}
