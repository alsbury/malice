<?php

namespace Alsbury\Malice\Component\Fixtures;

class FixtureConfig
{
    /**
     * @var array
     */
    protected $bundles;

    /**
     * @var array
     */
    protected $fixtures;

    /**
     * FixtureConfig constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['bundles'])) {
            $this->setBundles($config['bundles']);
        }

        if (isset($config['fixtures'])) {
            $this->setFixtures($config['fixtures']);
        }
    }

    /**
     * @return array
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param array $bundles
     * @return FixtureConfig
     */
    public function setBundles($bundles)
    {
        $this->bundles = $bundles;
        return $this;
    }

    /**
     * @return array
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * @param array $fixtures
     * @return FixtureConfig
     */
    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
        return $this;
    }

}