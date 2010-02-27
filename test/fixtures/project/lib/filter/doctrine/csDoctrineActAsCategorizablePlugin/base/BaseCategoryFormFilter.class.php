<?php

/**
 * Category filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseCategoryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'    => new sfWidgetFormFilterInput(),
      'root_id' => new sfWidgetFormFilterInput(),
      'lft'     => new sfWidgetFormFilterInput(),
      'rgt'     => new sfWidgetFormFilterInput(),
      'level'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'    => new sfValidatorPass(array('required' => false)),
      'root_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('category_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Category';
  }

  public function getFields()
  {
    return array(
      'id'      => 'Number',
      'name'    => 'Text',
      'root_id' => 'Number',
      'lft'     => 'Number',
      'rgt'     => 'Number',
      'level'   => 'Number',
    );
  }
}
