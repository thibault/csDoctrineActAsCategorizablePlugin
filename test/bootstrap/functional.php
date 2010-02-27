<?php

if (!isset($app))
{
  $app = 'frontend';
}

if (isset($_SERVER['SYMFONY'])
{
  $sfDir = $_SERVER['SYMFONY'];
}
else
{
  $sfDir = dirname(__FILE__).'/../../../lib/vendor/symfony/lib';
}
require_once $sfDir.'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

function csDoctrineActAsCategorizable_cleanup()
{
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/cache');
  sfToolkit::clearDirectory(dirname(__FILE__).'/../fixtures/project/log');
}
csDoctrineActAsCategorizable_cleanup();
register_shutdown_function('csDoctrineActAsCategorizable_cleanup');

require_once dirname(__FILE__).'/../fixtures/project/config/ProjectConfiguration.class.php';
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

new sfDatabaseManager($configuration);
