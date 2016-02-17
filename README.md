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
            
# Requirements

* Symfony 2 or 3
* Codeception 2.1+ with Symfony 2 module
* hautelook/alice-bundle
