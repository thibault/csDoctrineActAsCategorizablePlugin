<?php

class CategoryRefClassGenerator extends Doctrine_Record_Generator
{
  protected $_options = array(
                          'className'     => 'Category%CLASS%',
                          'options'       => 'Categories',
                          'foreignAlias'   =>  null,
                          'generateFiles' => true,
                          'generatePath'  => false,
                          'table'         => false,
                          'pluginTable'   => false,
                          'children'      => array(),
                          'options'       => array(),
                          'cascadeDelete' => true,
                          'appLevelDelete'=> false
                          );
  /**
   * __construct
   *
   * @param string $options
   * @return void
   */
  public function __construct($options)
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  public function initOptions()
  {
    $builderOptions = ProjectConfiguration::getActive()->getPluginConfiguration('sfDoctrinePlugin')->getModelBuilderOptions();
    $this->setOption('builderOptions', $builderOptions);

    if (false === $this->_options['generatePath'])
    {
      $this->_options['generatePath'] = sfConfig::get('sf_lib_dir').DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'doctrine';
    }
  }

  public function buildRelation()
  {
    $this->buildForeignRelation($this->_options['alias']);
    $this->buildLocalRelation($this->_options['foreignAlias']);
  }

  public function getRelationLocalKey()
  {
    return Doctrine_Inflector::tableize($this->_options['table']->getComponentName()).'_id';
  }

  public function setTableDefinition()
  {
    $this->hasColumn('category_id', 'integer', null, array(
      'type' => 'integer',
      'primary' => 'true'
    ));

    $this->hasColumn($this->getRelationLocalKey(), 'integer', null, array(
      'type' => 'integer',
      'primary' => 'true'
    ));
  }

  public function setUp()
  {
    $this->hasOne('Category', array(
      'local' => 'category_id',
      'foreign' => 'id'
    ));

    $this->hasOne($this->_options['table']->getComponentName(), array(
      'local' => $this->getRelationLocalKey(),
      'foreign' => 'id'
    ));
  }
}
