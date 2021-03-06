<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginCategoryTable extends Doctrine_Table
{
	public function getOrCreateCategory($name)
	{
		$category = $this->findOneByName($name);
		if($category)
		{
			return $category;
		}
		else
		{
			$category = new Category();
			$category['name'] = $name;
			$category->save();
			return $category;
		}
	}
	public function getCategory($category)
	{
		if(is_string($category))
		{
			$category = $this->findOneByName($category);
		}
		return $category;
	}
	public function getCategories($category_id)
	{
		$parent = $this->findOneById($category_id);
		$children = $parent->getSubCategories();
		if(!$children)
		{
			$children = new Doctrine_Collection('Category');
		}
		$children->add($parent);	
		return $children;
	}
	public function getRootByClassName($name)
	{
		$q = $this->createQuery()->where('level = ?', 0)->addWhere('name = ?', $name);
		return $q->fetchOne();
	}
	public function getCategoryTree($name)
	{
		$root = $this->getRootByClassName($name);
		return $root ? $this->getTree($root->getId()) : $this->getTree();
	}
}