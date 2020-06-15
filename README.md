# Magento 2 Recipe for Deployer

A recipe (tasklist) for deploying simple Magento 2 projects with the help of [deployer](https://deployer.org/).

## Overview

[Deployer tool](deployer.org) user so called recipes: sets of commands implemented in PHP - for executing specific tasks. One of the possible tasks is [deployment of Magento 2](https://devdocs.magento.com/guides/v2.4/config-guide/deployment/) with minimal downtime.       

## Installation and requirements

In order to be able to use the recipe [deployer must be installed](https://deployer.org/docs/installation.html) either directly or with composer.

Supported versions:
 - Magento: `>2.2`
 - PHP: `>7.0`
 - [n98-magerun2](https://github.com/netz98/n98-magerun2) (recommended): `>2.0`

The configuration (`hosts.yml`) and execution file (`deploy.php`) are projects specific and should be added to the project VCS.

It's possible to add this recipe with composer but you might also want to add some more tasks or change existing ones 
in the recipe (`magento.php`) so it might make sense to add that file to VCS and make it project-specific as well. 

## Usage

After setting up the tool can be used like this (in project root):

`vendor/bin/dep deploy test`

In case something happened the task remains locked and can be unlocked as follows:

`vendor/bin/dep deploy:unlock test`

Subtasks can also be executed separately, for example:

`vendor/bin/dep magento:db:dump test`

## Implementation details

The recipe's main task (`deploy`) is implemented similar to the official [Magento 2 Pipeline deployment](https://devdocs.magento.com/guides/v2.4/config-guide/deployment/pipeline/technical-details.html).

The main differences here are:
 - project is build on the same server (1)
 - it requires the DB connection (2)
 
Both of them are made for simplification: there is no need for a special build server (1) and there is no need to maintain `config.php` (2)  

## Authors

Oleh Kravets [oleh@openstream.ch](oleh@openstream.ch)

Website: [openstream.ch](https://www.openstream.ch/)

## License

MIT License (see: [License file](LICENSE))
