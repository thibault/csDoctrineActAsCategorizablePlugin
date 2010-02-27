<?php

if (!isset($app))
{
  $app = 'frontend';
}

$projectPath = dirname(__FILE__).'/../fixtures/project';
require_once($projectPath.'/config/ProjectConfiguration.class.php');

require_once(dirname(__FILE__).'/cleanup.php');
$configuration = ProjectConfiguration::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

new sfDatabaseManager($configuration);
