<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginCategory extends BaseCategory
{
	public function getSubCategories()
	{
		return $this->getNode()->getDescendants();
	}
	public function setSubCategory($category)
	{
		if(is_string($category))
		{
			$category = $this->getTable()->getOrCreateCategory($category);
		}
		elseif($category instanceof Category)
		{
			//Do Nothing, category is good.
		}
		else
		{
			throw new sfException("Parameters passed to setSubCategory must be an instance of Category or type string");
		}
		if($category->getNode()->getLevel())
		{
			//Category exists in DB, move it to the new location
			$category->getNode()->moveAsLastChildOf($this);
		}
		else
		{
			$this->getNode()->addChild($category);	
		}
		$this->refresh();
	}
	public function getObjects($table)
	{
		$q = Doctrine::getTable($table)->createQuery()->where('category_id = ?', $this->getId());
		return $q->execute();
	}
	public function getParentId()
  {
    if (!$this->getNode()->isValidNode() || $this->getNode()->isRoot())
    {      
      return null;
    }
    
    $parent = $this->getNode()->getParent();
    
    return $parent['id'];
  }
  
  public function getIndentedName()
  {
    return str_repeat('- ',$this['level']).$this['name'];
  }
	public function getParentCategory()
	{
		$parent = $this->getNode()->getParent();
		return $parent == null || $parent->getNode()->isRoot() ? null : $parent;
	}
	public function setParentCategory($category)
	{
		$category = $this->getTable()->getCategory($category);
		if($this->getNode()->getLevel())
		{
			//This category already exists in DB, move it to the new location
			$this->getNode()->moveAsLastChildOf($category);
		}
		else
		{
			$category->getNode()->addChild($this);	
		}
		
		$this->refresh();
	}
	public function secureSave()
	{
		if(!$this->getInvoker()->getNode()->getRootValue() && !$this->getInvoker()->getTable()->getTree()->findRoot($this->getInvoker()->getId()))
		{
			throw new sfException('Categories must be attached to a specific object tree.  use the "addCategory" method called form your object.');
		}
		$ret = $this->save();
		$this->refresh();
		return $ret;
	}
}