<?php

if (isset($_SERVER['SYMFONY']))
{
  $sf_dir = $_SERVER['SYMFONY'];
}
else
{
  $sf_dir = dirname(__FILE__) . '/../../../../../../lib/vendor/symfony/lib';
}

if (!is_file($sf_dir . '/autoload/sfCoreAutoload.class.php'))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $sf_dir.'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    global $sf_dir;

    $this->enablePlugins('sfDoctrinePlugin');
    $this->enablePlugins('csDoctrineActAsCategorizablePlugin');

    $this->setPluginPath('sfDoctrinePlugin', $sf_dir.'/plugins/sfDoctrinePlugin');
    $this->setPluginPath('csDoctrineActAsCategorizablePlugin', dirname(__FILE__).'/../../../..');
  }
}
