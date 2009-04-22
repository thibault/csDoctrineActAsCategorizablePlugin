<?php

// 
//  Categorizable.php
//  csActAsCategorizablePlugin
//  
//  Created by Brent Shaffer on 2009-01-29.
//  Copyright 2008 Centre{source}. Al9 rights reserved.
// 

class Doctrine_Template_Categorizable extends Doctrine_Template
{    
  /**
   * Array of Categorizable options
   */  
  protected $_options = array('columns' => array(
																'category_id' =>  array(
																	'name' 		=> 'category_id',
																	'type' 		=> 'integer',
																	'length'	=>  4,
																	'model'		=> 'Category',
		                              'alias'   =>  null,
		                              'foreignAlias'   =>  null,
		                              'options' =>  array()),
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
  }


  public function setup()
  {
		foreach($this->_options['columns'] as $key => $options)
		{
			$this->hasOne($options['model'], array('local' => $options['name'],
			                                 		'foreign' => 'id'));
			
			$relName = $this->getInvoker()->getTable()->getOption('name');
			$relName = $options['foreignAlias'] ? $relName . ' AS '.$options['foreignAlias'] : $relName;
			$relOptions = array('local' => 'id', 'foreign' => $options['name']);
			Doctrine::getTable('Category')->bind(array($relName, $relOptions), Doctrine_Relation::MANY);
		}
  }

	public function createRootTableProxy()
	{
		$root_category = new Category();
		$root_category['name'] = $this->_options['root'] ? $this->_options['root'] : get_class($this->getInvoker()).'_Root';
		$root_category->save();
		$root_category->getTable()->getTree()->createRoot($root_category);
		return $root_category;
	}
	
  /**
   * Set table definition for categorizable behavior
   *
   * @return void
   * @author Brent Shaffer
   */
  public function setTableDefinition()
  {
		foreach ($this->_options['columns'] as $key => $options) {

	    $name = $options['name'];

			if ($options['alias'])
	    {
	      $name .= ' as ' . $options['alias'];
	    }
			
	    $this->hasColumn($name, $options['type'], $options['length'], $options['options']);
		}
		
    $this->addListener(new Doctrine_Template_Listener_Categorizable($this->_options));
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
