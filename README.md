[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mediact/testing-suite/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mediact/testing-suite/?branch=master)

# MediaCT Testing Suite

This package serves as an umbrella package for several of MediaCT's testing 
packages.

# Installation

```
composer require mediact/testing-suite --dev
```

# Usage

The testing suite can be run through the GrumPHP command.

```
vendor/bin/grumphp run
```

The testing suite is also automatically run at each git commit using a git
commit hook.

# Components

The following components are part of the testing suite.

## Coding style validation (PHPCS)

The coding style is validated using PHPCS and uses the 
[MediaCT Coding Standard](https://github.com/mediact/coding-standard).

During the installation of the testing suite a file called `phpcs.xml` is added to
the root of the repository which refers to the coding standard. To make
adjustments to the coding standard this file can be edited and committed.

Depending on the composer type of the project an other standard will be used:

- `magento-module`: [MediaCT Coding Standard Magento1](https://github.com/mediact/coding-standard-magento1)
- `magento2-module`: [MediaCT Coding Standard Magento2](https://github.com/mediact/coding-standard-magento2)

### Overriding the type

The type for a project can be overridden in the composer.json `config` node by 
adding `testing-suite-type` to the configuration.
This will allow the use of standards for a different type.
The allowed values for this node are:
- magento1
- magento2
- default

The configurations looks like the following:
```json
{
  "config": {
    "mediact-testing-suite": {
      "type": "magento2"
    }
  }
}
```

This can be helpful when development is done in the `app/code` folder and force 
the testing suite to automatically select the correct standards.

## Coding complexity validation (PHPMD)

The complexity of the code is validated using PHPMD. A file called `phpmd.xml`
is added during the installation of the testing suite.

## Static code analysis (PHPStan)

Static code analysis is executed using PHPStan. A file called `phpstan.neon`
is added during the installation of the testing suite.

## Unit tests (PHPUnit)

Unit tests are executed using PHPUnit. A file called `phpunit.xml` is added
during the installation of the testing suite.

The unit tests are expected to be in a directory called `tests`. The code is
expected to be in a directory called `src`.

## Bitbucket Pipelines

When the project is hosted on Bitbucket a 
[Pipelines](https://bitbucket.org/product/features/pipelines) script will be
installed. The scripts supports a callback that will be called before 
`composer install` is executed. This callback can be used to add credentials
to composer. To enable the callback go to **Bitbucket Settings > Pipelines >
Environment Variables** and add an environment variable called 
`COMPOSER_PRE_INSTALL_CALLBACK`.

Example to add basic authentication for repo.example.com:

```
composer config --global http-basic.repo.example.com $YOUR_USER $YOUR_PASSWORD
```

## ESLint
Javascript linting for Magento 1 and Magento 2 projects is executed using 
[ESLint](https://eslint.org/). Two files called `.eslintrc.json` and 
`.eslintignore` are added to the root of the repository which contains the 
coding standards and files excluded from analysis.

A third file called `package.json` is added which contains the needed npm 
modules. Run `npm install` after the installation in order to enable ESLint:

```shell
npm install
```

# Integration with PHPStorm

When the testing suite is installed in a PHPStorm environment it automatically
configures PHPStorm to use the correct coding style. 

To enable PHPCS and PHPMD inspections in PHPStorm the correct binaries need
to be configured. This is a global setting in PHPStorm and can therefore not
be configured by the testing suite.

The recommended way to get the correct binaries is by installing the MediaCT 
Coding Standard globally.

```
composer global require mediact/coding-standard
```

The package will be installed in the home directory of composer. The location
of this directory can be found using the following command:

```
composer global config home
```

Open PHPStorm and go to __Settings > Languages & Frameworks > PHP > Code Sniffer__.
 
Choose "Local" for the development environment and fill in the full path to
`<composer_home_directory>/vendor/bin/phpcs`.

Then go to __Settings > Languages & Frameworks > PHP > Mess Detector__.

Choose "Local" for the development environment and fill in the full path to
`<composer_home_directory>/vendor/bin/phpmd`.

After these adjustments the coding style and complexity will be validated
while typing in PHPStorm.

To enable ESLint open PHPStorm and go to 
__Settings > Languages & Frameworks > Javascript > Code Quality Tools > ESLint__.

Enable ESLint by checking `Enabled`. Then set the `Node interpreter` 
to `Project` and `Configuration file` to `Automatic Search`.

