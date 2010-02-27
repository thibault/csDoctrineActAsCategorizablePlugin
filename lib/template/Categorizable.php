<?php

/**
 * Categorizable template
 *
 * Add categories to your model
 *
 * @package csDoctrineActAsCategorizablePlugin
 * @subpackage template
 * @author Brent Shaffer, Centre{source} (initial version)
 * @author Thibault Jouannic <thibault@jouannic.fr> (1.4 branch)
 **/
class Doctrine_Template_Categorizable extends Doctrine_Template
{
  /**
   * Array of Categorizable options
   */
  protected $_options = array('model'   => 'Category',
                              'alias'   => 'Categories',
                              'foreignAlias'   =>  null,
                              'refClass' => 'CategoryObject',
                              'local' => 'categorized_id',
                              'foreign' => 'category_id',
                              'root'        =>  null,
  );


  /**
   * Constructor for Categorizable Template
   *
   * @param array $options
   * @return void
   */
  public function __construct(array $options = array())
  {
    parent::__construct($options);
  }

  /**
   * Add the listener
   **/
  public function setTableDefinition()
  {
    $this->addListener(new Doctrine_Template_Listener_Categorizable($this->_options));
  }

  /**
   * Initialize the category relationship
   **/
  public function setUp()
  {
    $relation = sprintf('%s as %s', $this->_options['model'], $this->_options['alias']);

    $this->hasMany($relation, array(
      'refClass' => $this->_options['refClass'],
      'local' => $this->_options['local'],
      'foreign' => $this->_options['foreign']
    ));
  }

  /**
   * Generate a query to fetch all the categories the invoker belongs to
   *
   * @return Doctrine_Query
   **/
  public function getCategoriesQuery()
  {
    $q = Doctrine_Query::create()
      ->select('c.*')
      ->from('Category c')
      ->andwhere('c.categorized_type = ?', get_class($this->getInvoker()))
      ->andWhere('c.categorized_id = ?', $this->getInvoker()->getId());

    return $q;
  }

  /**
   * Return a collection of categories the invoker belongs to
   *
   * @return Doctrine_Collection
   **/
  public function getCategories()
  {
    return $this->getCategoriesQuery()->execute();
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
