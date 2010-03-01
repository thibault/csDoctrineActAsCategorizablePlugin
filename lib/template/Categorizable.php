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
  protected $_options = array();

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

    // We manage categories as a virtual field
    // Usual doctrine association magic is simulated in the listener
    $this->getInvoker()->hasAccessorMutator('Categories', 'getCategories', 'setCategories');
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
      ->leftJoin('c.CategoryObject co')
      ->addwhere('co.categorized_model = ?', get_class($this->getInvoker()))
      ->addWhere('co.categorized_id = ?', $this->getInvoker()->getId());

    return $q;
  }

  /**
   * Return a collection of categories the invoker belongs to
   * This will be the default accessor for the Categories relation
   *
   * @return Doctrine_Collection
   **/
  public function getCategories()
  {
    if (!$this->getInvoker()->hasMappedValue('_categories'))
    {
      $categories = $this->getCategoriesQuery()->execute();
      $this->setCategories($categories);
    }
    else
    {
      $categories = $this->getInvoker()->get('_categories');
    }

    return $categories;
  }

  /**
   * Custom mutator for categories
   **/
  public function setCategories(Doctrine_Collection $categories = null)
  {
    $this->getInvoker()->mapValue('_categories', $categories);
  }
}
