<?php

/**
 * Categorizable listener
 *
 * @package csDoctrineActAsCategorizablePlugin
 * @subpackage listener
 **/
class Doctrine_Template_Listener_Categorizable extends Doctrine_Record_Listener
{
  /**
   * Array of Categorizable options
   */
  protected $_options = array();

  /**
   * We take care of the categories reference here
   *
   * @see Doctrine_Connection_UnitOfWork::saveAssociations()
   * @see Doctrine_Record::preSave()
   **/
  public function preSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();

    $ref = $invoker->reference('Categories');
    if ($ref)
    {
      $rel = $invoker->getTable()->getRelation('Categories');

      if ($rel instanceof Doctrine_Relation_Association)
      {
        $assocTable = $rel->getAssociationTable();

        // Associations deletions
        foreach ($ref->getDeleteDiff() as $r)
        {
          Doctrine::getTable('CategoryObject')->createQuery()
            ->delete()
            ->addWhere('category_id = ?', $r->getId())
            ->addWhere('categorized_id = ?', $invoker->getId())
            ->addWhere('categorized_model = ?', get_class($invoker))
            ->execute();
        }

        // Take care of new associations
        foreach ($ref->getInsertDiff() as $r)
        {
          $r->save();
          $assocRecord = $assocTable->create();
          $assocRecord->set('category_id', $r->getId());
          $assocRecord->set('categorized_id', $invoker->getId());
          $assocRecord->set('categorized_model', get_class($invoker));
          $assocRecord->save();
          $ref->takeSnapshot();
        }
      }
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

    Doctrine::getTable('Category')->createQuery()
      ->delete()
      ->addWhere('categorized_id = ?', $invoker->getId())
      ->addWhere('categorized_model = ?', get_class($invoker))
      ->execute();
  }
}
