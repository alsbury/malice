<?php

namespace Alsbury\Malice\Component\Fixtures;

use Alsbury\Malice\Component\Fixtures\FixtureConfig;
use Hautelook\AliceBundle\Finder\FixturesFinder;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Takes a FixtureConfig object and generates a list of
 * fixture files to load.
 *
 * Class FixtureConfigResolver
 * @package Alsbury\Malice
 */
class FixtureConfigResolver
{
    /**
     * @var \AppKernel $kernel
     */
    protected $kernel;

    /**
     * hautelook_alice.bundle_resolver
     * @var BundlesResolverInterface $bundlesResolver
     */
    protected $bundlesResolver;

    /**
     * hautelook_alice.doctrine.orm.fixtures_finder
     * @var FixturesFinder $fixturesFinder
     */
    protected $fixturesFinder;

    /**
     * FixtureConfigResolver constructor.
     * @param \AppKernel $kernel
     * @param BundlesResolverInterface $bundlesResolver
     * @param FixturesFinder $fixturesFinder
     */
    public function __construct(
        \AppKernel $kernel,
        BundlesResolverInterface $bundlesResolver,
        FixturesFinder $fixturesFinder
    ) {
        $this->kernel = $kernel;
        $this->bundlesResolver = $bundlesResolver;
        $this->fixturesFinder = $fixturesFinder;
    }

    /**
     * Takes a fixture config object and returns a list of fixture files
     *
     * @param FixtureConfig $fixtureConfig
     * @return array
     */
    public function getFixtures($fixtureConfig)
    {
        $fixtures = [];

        // Apply fixture config
        if (null !== $fixtureConfig->getFixtures())
        {
            $fixtures = $fixtureConfig->getFixtures();
        }

        // Apply bundle config
        if (null !== $fixtureConfig->getBundles()) {
            $fixtures = array_merge($fixtures, $this->resolveBundles($fixtureConfig->getBundles()));
        }

        if (count($fixtures) == 0) {
            $fixtures = $this->resolveBundles(array_keys($this->kernel->getBundles()));
        }

        return $fixtures;
    }

    /**
     * @param array $bundles
     * @return array
     */
    private function resolveBundles($bundles)
    {
        return $this->fixturesFinder->getFixtures($this->kernel, $this->bundlesResolver->resolveBundles(new Application($this->kernel), $bundles), 'test');
    }

}