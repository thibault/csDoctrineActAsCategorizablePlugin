<?php

//
//  Categorizable.php
//  csActAsCategorizablePlugin
//
//  Created by Brent Shaffer on 2009-01-29.
//  Copyright 2008 Centre{source}. Al9 rights reserved.

class Doctrine_Template_Categorizable extends Doctrine_Template
{
  /**
   * Array of Categorizable options
   */
  protected $_options = array('category' => array(
                                'model'		=> 'Category',
                                'alias'   => 'Categories',
                                'foreignAlias'   =>  null,
                                'refClass' => null,
                                'local' => null,
                                'foreign' => 'category_id',
                              ),
															'root'				=>  null,
	);


  /**
   * Constructor for Categorizable Template
   *
   * @param array $options
   * @return void
   * @author Brent Shaffer
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);

    $name = Doctrine_Inflector::tableize($this->getInvoker()->getTable()->getOption('name'));

    if (null === $this->_options['local'])
    {
      $this->_options['local'] = $name.'_id';
    }

    if (null === $this->_options['refClass'])
    {
      $this->_options['refClass'] = 'Category'.$this->getInvoker()->getTable()->getOption('name');
    }

    /*if (null === $this->_options['foreignAlias'])
    {
      $this->_options['foreignAlias'] =
    }*/
  }

  /**
   * Set table definition for categorizable behavior
   *
   * @return void
   * @author Brent Shaffer
   */
  public function setTableDefinition()
  {
    $this->addListener(new Doctrine_Template_Listener_Categorizable($this->_options));
  }

  public function setup()
  {
    $categoryOptions = $this->_options['category'];
    $relation = sprintf('%s as %s', $categoryOptions['model'], $categoryOptions['alias']);

    $this->hasMany($relation, array(
      'refClass' => $options['refClass'],
      'local' => $options['local'],
      'foreign' => $options['foreign']
    ));
  }

	public function createRootTableProxy()
	{
		$root_category = new Category();
		$root_category['name'] = $this->_options['root'] ? $this->_options['root'] : get_class($this->getInvoker()).'_Root';
		$root_category->save();
		$root_category->getTable()->getTree()->createRoot($root_category);
		return $root_category;
	}

	public function addCategoryTableProxy($category)
	{
		if(!$category->getNode()->getRootValue());
		{
			$root_name = $this->_options['root'] ? $this->_options['root'] : get_class($this->getInvoker()).'_Root';
			$this_root = Doctrine::getTable('Category')->getRootByClassName($root_name);

			if(!$this_root)
			{
				$this_root = $this->createRootTableProxy();
			}
			$this_root->getNode()->addChild($category);
			$category->refresh();
		}
	}
	public function addCategory($category)
	{
		$this->addCategoryTableProxy($category);
		$this->setCategory($category);
	}
	public function removeCategoryTableProxy($category)
	{
		$category->getNode()->deleteNode();
	}
	public function getCategoryTreeTableProxy()
	{
		$name = $this->_options['root'] ? $this->_options['root'] : get_class($this->getInvoker()).'_Root';
		return Doctrine::getTable('Category')->getCategoryTree($name);
	}
	public function getCategoriesQueryTableProxy()
	{
		$tree = $this->getCategoryTreeTableProxy();
		$root = $tree->fetchRoot();
		$q = Doctrine::getTable('Category')->createQuery();
		if($root)
		{
			$q->where('root_id = ? AND level != 0', $root->getId());
		}
		return $q;
	}
	public function findAllByCategoryTableProxy($name)
	{
		$category = Doctrine::getTable('Category')->getCategory($name);
		return $this->getInvoker()->getTable()->createQuery()->where('category_id = ?', $category->getId())->execute();
	}
	public function findAllByCategorySlugTableProxy($slug)
	{
		$category = Doctrine::getTable('Category')->getCategory($slug);
		return $this->getInvoker()->getTable()->createQuery()->where('category_id = ?', $category->getId())->execute();
	}
	public function getCategoriesTableProxy()
	{
		return $this->getCategoriesQueryTableProxy()->execute();
	}
	public function setCategory($category)
	{
		if($category instanceof Category)
		{
			$category->secureSave();
			return $this->getInvoker()->setCategory($category);
		}
		elseif(is_string($category))
		{
			$category->secureSave();
			$category = Doctrine::getTable('Category')->getOrCreateCategory($category);
			return $this->getInvoker()->setCategory($category);
		}
		throw new sfException("Parameters passed to setSubCategory must be an instance of Category or type string");
	}
}
