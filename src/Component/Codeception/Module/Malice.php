<?php

namespace Alsbury\Malice\Component\Codeception\Module;

use Alsbury\Malice\Component\Codeception\TestCaseInterface;
use Alsbury\Malice\Fixtures\FixtureConfig;
use Codeception\Module as CodeceptionModule;
use Codeception\TestCase;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Tools\SchemaTool;
use Hautelook\AliceBundle\Alice\DataFixtures\Loader;
use Hautelook\AliceBundle\Doctrine\DataFixtures\Executor\FixturesExecutor;
use Hautelook\AliceBundle\Doctrine\Finder\FixturesFinder;
use Hautelook\AliceBundle\Resolver\BundlesResolverInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;

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

    protected $classes;

    protected $fixtureConfig;

    /**
     * @var BundlesResolverInterface
     */
    protected $bundleResolver;

    protected $config = [
        'drop_create' => true
    ];

    public function _initialize()
    {
        /** @var CodeceptionModule\Symfony2 $module */
        $module = $this->getModule('Symfony2');

        /** @var \AppKernel $kernel */
        $this->setKernel($module->kernel);

        $this->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'));
        $this->setFixturesFinder($this->getContainer()->get('hautelook_alice.doctrine.orm.fixtures_finder'));
        $this->setFixturesLoader($this->getContainer()->get('hautelook_alice.fixtures.loader'));
        $this->setFixturesExecutor($this->getContainer()->get('hautelook_alice.doctrine.executor.fixtures_executor'));
        $this->setBundleResolver($this->getContainer()->get('hautelook_alice.bundle_resolver'));
        $this->setSchemaTool(new SchemaTool($this->getEntityManager()));
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return Malice
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return FixturesFinder
     */
    public function getFixturesFinder()
    {
        return $this->fixturesFinder;
    }

    /**
     * @param FixturesFinder $fixturesFinder
     * @return Malice
     */
    public function setFixturesFinder($fixturesFinder)
    {
        $this->fixturesFinder = $fixturesFinder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFixturesLoader()
    {
        return $this->fixturesLoader;
    }

    /**
     * @param mixed $fixturesLoader
     * @return Malice
     */
    public function setFixturesLoader($fixturesLoader)
    {
        $this->fixturesLoader = $fixturesLoader;
        return $this;
    }

    /**
     * @return FixturesExecutor
     */
    public function getFixturesExecutor()
    {
        return $this->fixturesExecutor;
    }

    /**
     * @param FixturesExecutor $fixturesExecutor
     * @return Malice
     */
    public function setFixturesExecutor($fixturesExecutor)
    {
        $this->fixturesExecutor = $fixturesExecutor;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * @param mixed $kernel
     * @return Malice
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
        return $this;
    }

    /**
     * @return SchemaTool
     */
    public function getSchemaTool()
    {
        return $this->schemaTool;
    }

    /**
     * @param SchemaTool $schemaTool
     * @return Malice
     */
    public function setSchemaTool($schemaTool)
    {
        $this->schemaTool = $schemaTool;
        return $this;
    }

    /**
     * @return BundlesResolverInterface
     */
    public function getBundleResolver()
    {
        return $this->bundleResolver;
    }

    /**
     * @param BundlesResolverInterface $bundleResolver
     * @return Malice
     */
    public function setBundleResolver($bundleResolver)
    {
        $this->bundleResolver = $bundleResolver;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFixtureConfig()
    {
        return $this->fixtureConfig;
    }

    /**
     * @param mixed $fixtureConfig
     * @return Malice
     */
    public function setFixtureConfig($fixtureConfig)
    {
        $this->fixtureConfig = $fixtureConfig;
        return $this;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     * @throws \Codeception\Exception\ModuleException
     */
    public function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    public function _before(TestCase $test)
    {
        if ($test instanceof TestCaseInterface) {
            /** @var FixtureConfig $fixtureConfig */
            $fixtureConfig = $test->getFixtureConfig();
            $configResolver = $this->getContainer()->get('alsbury.malice.fixture_config_resolver');
            $fixtures = $configResolver->getFixtures($fixtureConfig);
//            print_r($fixtures); die();
            $this->loadFixtures($fixtures);
        }

//        if ($this->config['drop_create']) {
//            $this->emptyDatabase();
//        }
    }

    public function _after(TestCase $test)
    {

    }

    public function loadFixtures($fixtures)
    {
        $this->getFixturesExecutor()
            ->execute($this->getEntityManager(), $this->getFixturesLoader(), $fixtures, false, null);
    }

    public function executeFixtureCommand()
    {
        $container = $this->getContainer();
        $kernel = $container->get('kernel');
        $app = new Application($kernel);

        $input = new StringInput('hautelook_alice:doctrine:fixtures:load');
        $input->setInteractive(false);
        $output = new StreamOutput(fopen('php://memory', 'w'));

        $app->doRun($input, $output);

        $em = $container->get('doctrine.orm.entity_manager');
        $em->flush();
    }

    public function getSchemaClasses()
    {
        if (!$this->classes) {
            $this->classes = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        }
        return $this->classes;
    }

    public function createSchema()
    {
        $this->getSchemaTool()->createSchema($this->getSchemaClasses());
    }

    public function dropSchema()
    {
        $this->getSchemaTool()->dropSchema($this->getSchemaClasses());
    }

    public function emptyDatabase()
    {
        $this->dropSchema();
        $this->createSchema();
    }
}