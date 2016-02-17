# Malice Bundle

Codeception module for working with Alice/Faker fixtures in conjunction with AliceBundle.

This is an early prototype. Feedback is welcome.

# Installation:

    composer require alsbury/malice

## Configure Codeception Test Suites

Add module to codeception test suite YAML file:

    class_name: FunctionalTester
    modules:
       enabled:
        - Symfony2:
            app_path: '../../../app'
            var_path: '../../../var'
            environment: 'test'
        - \Component\Registration\Tests\Helper\Functional
        - \Alsbury\Malice\Component\Codeception\Module\Malice:
            drop_create: true
    
Example test that loads fixtures defined in MyBundle:

    <?php
    
    use Alsbury\Malice\Component\Codeception\TestCaseInterface;
    use Alsbury\Malice\Component\Fixtures\FixtureConfig;
    
    class AccountCreatorTest extends \Codeception\TestCase\Test implements TestCaseInterface
    {
        
        public function testNothing()
        {
            $this->assertEquals(true, true);
        }
    
        /**
         * @return FixtureConfig
         */
        public function getFixtureConfig()
        {
            return new FixtureConfig(['bundles' => [
                'MyBundle'
            ]]);
        }
    }            
       
Setup fixtures per the `hautelook/alice-bundle` spec. If your test implements `TestCaseInterface`, Malice bundle will
attempt to load fixtures defined in FixtureConfig. If however you provide an empty config (new FixtureConfig(), all fixtures in all bundles
will be loaded. Fixture environments are not currently supported. 
            
# Requirements

* Symfony 2 or 3
* Codeception 2.1+ with Symfony 2 module
* hautelook/alice-bundle
