<?php

/**
 * BaseCategoryObject
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $category_id
 * @property string $categorized_model
 * @property integer $categorized_id
 * @property Category $Category
 * 
 * @method integer        getCategoryId()        Returns the current record's "category_id" value
 * @method string         getCategorizedModel()  Returns the current record's "categorized_model" value
 * @method integer        getCategorizedId()     Returns the current record's "categorized_id" value
 * @method Category       getCategory()          Returns the current record's "Category" value
 * @method CategoryObject setCategoryId()        Sets the current record's "category_id" value
 * @method CategoryObject setCategorizedModel()  Sets the current record's "categorized_model" value
 * @method CategoryObject setCategorizedId()     Sets the current record's "categorized_id" value
 * @method CategoryObject setCategory()          Sets the current record's "Category" value
 * 
 * @package    ##PROJECT_NAME##
 * @subpackage model
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseCategoryObject extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('category_object');
        $this->hasColumn('category_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('categorized_model', 'string', 50, array(
             'type' => 'string',
             'primary' => true,
             'length' => '50',
             ));
        $this->hasColumn('categorized_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));


        $this->index('Categorized', array(
             'fields' => 
             array(
              0 => 'categorized_model',
              1 => 'categorized_id',
             ),
             ));

        $this->setAttribute(Doctrine_Core::ATTR_EXPORT, Doctrine_Core::EXPORT_ALL ^ Doctrine_Core::EXPORT_CONSTRAINTS);

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Category', array(
             'local' => 'category_id',
             'foreign' => 'id'));
    }
}