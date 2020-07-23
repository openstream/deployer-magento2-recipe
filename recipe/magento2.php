<?php
/**
 * @author      Oleh Kravets <oleh@openstream.ch>
 * @copyright   Copyright (c) 2020 Openstream Internet Solutions  (https://www.openstream.ch)
 * @license     MIT License
 */

namespace Deployer;

require_once __DIR__ . '/../../deployer/deployer/recipe/common.php';

// (Default) Configuration.
// All values can be overwritten in an inventory file. For example in hosts.yml

set('shared_files', [
    'app/etc/env.php',
]);

set('shared_dirs', [
    'var',
    'pub/sitemaps',
    'pub/media'
]);
set('writable_dirs', [
    'var',
    'pub/media'
]);

set('clear_paths', [
    'var/view_preprocessed',
    'var/page_cache',
]);

//----------------------------------------------------------------------------------------------------------------------
// Custom task definitions.
desc('Compile magento di');
task('magento:compile', function () {
    run("{{bin/php}} {{release_path}}/bin/magento setup:di:compile");
    run('cd {{release_path}} && {{bin/composer}} dump-autoload -o');
});

desc('Deploy assets');
task('magento:deploy:static-content', function () {
    if (has('locales') && (get('locales'))) {
        run("{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy {{locales}} -f ");
    }
});

desc('Enable all modules');
task('magento:deploy:enable-modules', function () {
    run("{{bin/php}} {{release_path}}/bin/magento module:enable --all");
});

desc('Dump database');
task('magento:db:dump', function () {
    if (has('dump_db') && get('dump_db')) {
        run("cd {{deploy_path}} && {{bin/magerun2}} db:dump --force ");
    }
});

desc('Enable maintenance mode');
task('magento:maintenance:enable', function () {
    run("if [ -d $(echo {{deploy_path}}/current) ]; then {{bin/php}} {{deploy_path}}/current/bin/magento maintenance:enable; fi");
});

desc('Disable maintenance mode');
task('magento:maintenance:disable', function () {
    run("if [ -d $(echo {{deploy_path}}/current) ]; then {{bin/php}} {{deploy_path}}/current/bin/magento maintenance:disable; fi");
});

desc('Upgrade magento database');
task('magento:upgrade:db', function () {
    run("{{bin/php}} {{release_path}}/bin/magento setup:upgrade --keep-generated");
});

desc('Flush Magento Cache');
task('magento:cache:flush', function () {
    run("{{bin/php}} {{release_path}}/bin/magento cache:flush");
});

desc('Deploy Shared env.php');
task('magento:deploy:env', function () {
    run("rm -rf {{release_path}}/app/etc/env.php && ln -s {{deploy_path}}/shared/app/etc/env.php {{release_path}}/app/etc/env.php");
});

//----------------------------------------------------------------------------------------------------------------------
// Main task definition, includes subtasks in the proper order.
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:writable',
    'magento:deploy:enable-modules',
    'magento:compile',
    'magento:deploy:env',
    'magento:deploy:static-content',
    'deploy:shared',
    'magento:db:dump',
    // downtime starts here
    'magento:maintenance:enable',
    'deploy:clear_paths',
    'deploy:symlink',
    'magento:upgrade:db',
    'magento:cache:flush',
    'magento:maintenance:disable',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// To not leave the website in maintenance mode if something went wrong
after('deploy:failed', 'magento:maintenance:disable');
