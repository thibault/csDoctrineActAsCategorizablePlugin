<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @author Brent Shaffer
 */
class PluginCategoryTable extends Doctrine_Table
{
  /**
   * Get a category by name, and create it if it doens'nt exists already
   **/
  public function getOrCreateCategory($name)
  {
    $category = $this->findOneByName($name);
    if (!$category)
    {
      $category = new Category();
      $category['name'] = $name;
      $category->save();
    }

    return $category;
  }

  /**
   * Get a category by name
   **/
  public function getCategory($name)
  {
    if (!is_string($name))
    {
      throw new sfException('You must pass a string as an argument');
    }

    $category = $this->findOneByName($name);
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
