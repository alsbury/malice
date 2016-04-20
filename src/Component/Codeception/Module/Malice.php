<?php

namespace Alsbury\Malice\Component\Codeception\Module;

use Alsbury\Malice\Component\Codeception\TestCaseInterface;
use Alsbury\Malice\Component\Fixtures\FixtureConfig;
use Codeception\Module as CodeceptionModule;
use Codeception\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\FixturesExecutor;
use Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\DependencyInjection\Container;

class Malice extends CodeceptionModule
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var FixturesFinder
     */
    protected $fixturesFinder;

    /**
     * @var Loader
     */
    protected $fixturesLoader;

    /**
     * @var FixturesExecutor
     */
    protected $fixturesExecutor;

    /**
     * @var \AppKernel
     */
    protected $kernel;

    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    /**
     * @var mixed
     */
    protected $schemaMetaData;

    /**
     * @var Container
     */
    protected $container;

    /**
     * Array of fixture files to load
     * @var array
     */
    protected $fixtures;

    /**
     * @var BundlesResolverInterface
     */
    protected $bundleResolver;

    protected $config = [
        'append' => false
    ];

    public function _initialize()
    {
        /** @var CodeceptionModule\Symfony2 $module */
        $module = $this->getModule('Symfony2');

        /** @var \AppKernel $kernel */
        $this->kernel = $module->kernel;
        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->fixturesFinder = $this->container->get('hautelook_alice.doctrine.orm.fixtures_finder');
        $this->fixturesLoader = $this->container->get('hautelook_alice.fixtures.loader');
        $this->fixturesExecutor = $this->container->get('hautelook_alice.doctrine.executor.fixtures_executor');
        $this->bundleResolver = $this->container->get('hautelook_alice.bundle_resolver');
        $this->schemaTool = new SchemaTool($this->entityManager);
    }

    public function _before(TestCase $test)
    {
        codecept_debug("Malice running before");
        if ($this->config['append'] === false) {
            $this->emptyDatabase();
        }

        if (in_array(TestCaseInterface::class, class_implements(get_class($test->getTestClass())))) {
            codecept_debug("Test implements TestCaseInterface");
            if ($this->fixtures === null) {
                /** @var FixtureConfig $fixtureConfig */
                $configResolver = $this->container->get('alsbury.malice.fixture_config_resolver');
                $fixtureConfig = $test->getTestClass()->_getFixtureConfig();
                $this->fixtures = $configResolver->getFixtures($fixtureConfig === null ? new FixtureConfig() : $fixtureConfig);
            }
            $this->loadFixtures($this->fixtures);
        }
    }

    public function loadFixtures($fixtures)
    {
        codecept_debug("Loading fixtures");
        $this->fixturesExecutor
            ->execute($this->entityManager, $this->fixturesLoader, $fixtures, $this->config['append'], null);
    }

    public function getSchemaClasses()
    {
        if (!$this->schemaMetaData) {
            $this->schemaMetaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        }
        return $this->schemaMetaData;
    }

    public function createSchema()
    {
        codecept_debug("Creating schema");
        $this->schemaTool->createSchema($this->getSchemaClasses());
    }

    public function dropSchema()
    {
        codecept_debug("Dropping schema");
        $this->schemaTool->dropSchema($this->getSchemaClasses());
    }

    public function emptyDatabase()
    {
        codecept_debug("Empty database");
        $this->dropSchema();
        $this->createSchema();
    }

    public function getContainer()
    {
        return $this->getModule('Symfony2')->kernel->getContainer();
    }

}
