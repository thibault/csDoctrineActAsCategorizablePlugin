<?php

/**
 * Categorizable listener
 *
 * @package csDoctrineActAsCategorizablePlugin
 * @subpackage listener
 * @author Thibault Jouannic <thibault@jouannic.fr>
 **/
class Doctrine_Template_Listener_Categorizable extends Doctrine_Record_Listener
{
  /**
   * We take care of the categories reference here
   * We simulate a many to many relation
   *
   * @see Doctrine_Connection_UnitOfWork::saveAssociations()
   * @see Doctrine_Record::preSave()
   **/
  public function postSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();
    $categories = $invoker->getCategories();

    if($categories instanceOf Doctrine_Collection)
    {
      // Associations deletions
      foreach ($categories->getDeleteDiff() as $c)
      {
        $q = Doctrine::getTable('CategoryObject')->createQuery()
          ->delete()
          ->addWhere('category_id = ?', $c->getId())
          ->addWhere('categorized_id = ?', $invoker->getId())
          ->addWhere('categorized_model = ?', get_class($invoker));
          $q->execute();
      }

      // Take care of new associations
      foreach ($categories->getInsertDiff() as $c)
      {
        // This is the added category
        $c->save();

        // And this is the refClass object
        $assocRecord = new CategoryObject();
        $assocRecord->set('category_id', $c->getId());
        $assocRecord->set('categorized_id', $invoker->getId());
        $assocRecord->set('categorized_model', get_class($invoker));
        $assocRecord->save();
      }

      // Take a snapshot to record the current collection state
      $categories->takeSnapshot();
    }
  }

  /**
   * Delete all associations record before delete
   *
   * @see Doctrine_Record::preDelete()
   **/
  public function preDelete(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();

    Doctrine::getTable('CategoryObject')->createQuery()
      ->delete()
      ->addWhere('categorized_id = ?', $invoker->getId())
      ->addWhere('categorized_model = ?', get_class($invoker))
      ->execute();

    $invoker->setCategories(null);
  }
}
