<?php
/**
 */
class PluginCategoryObjectTable extends Doctrine_Table
{
  /**
   * Delete all the records for the corresponding category id
   *
   * @param integer $id The category id
   **/
  public function deleteByCategoryId($id)
  {
    $this->createQuery()
      ->delete()
      ->addWhere('category_id = ?', $id)
      ->execute();
  }
}
