<?php
/**
 * @author      Oleh Kravets <oleh@openstream.ch>
 * @copyright   Copyright (c) 2020 Openstream Internet Solutions  (https://www.openstream.ch)
 * @license     MIT License
 */

/**
 * Sample execution file
 */
namespace Deployer;

require __DIR__ . '/recipe/magento2.php';

$settings = [
    'default_timeout' => 3600, // prevent breaking because of timeout
    'application'     => 'APP_NAME',
    'bin/magerun2'    => '~/n98-magerun2',
    'repository'      => '{PATH_TO_GIT_REPOSITORY}',
    'exclude_theme'    => 'Magento/luma'
];

foreach ($settings as $key => $value) {
    set($key, $value);
}

inventory('hosts.yml');