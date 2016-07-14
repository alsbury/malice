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
    
Example test that loads fixtures defined in a bundle, specified by Doctrine annotations:

    <?php
    
    use Alsbury\Malice\Component\Annotation\Fixture;
    
    /**
     * @Fixture("MyBundle:Foo.yml")
     * @Fixture("MyBundle:Bar.yml")
     * @Fixture("AnotherBundle:AnotherFoo.yml")
     */
    class AccountCreatorTest extends \Codeception\TestCase\Test
    {
        /**
         * @Fixture("MyBundle:MethodSpecificFoo.yml")
         */
        public function testNothing()
        {
            $this->assertEquals(true, true);
        }
    
        public function testSomeOtherNothing()
        {
            $this->assertEquals(true,true);
        }
    }            
       
Setup fixtures per the `hautelook/alice-bundle` spec. Fixtures definition files `Foo.yml` are expected
to be in the directory `DataFixtures/ORM` in the specific bundle directory. Each fixture needs to have
its own annotation as noted in the sample above, containing the bundle name, followed by a colon, then the
fixture yaml file `MyBundle:Foo.yml`. Fixtures annotations for the class will apply to all methods in that
class. Fixture annotations for a method will be added to the class fixtures for that specific test only.

Fixture environments are not currently supported. 
            
# Requirements

* Symfony 2 or 3
* Codeception 2.1+ with Symfony 2 module
* hautelook/alice-bundle
