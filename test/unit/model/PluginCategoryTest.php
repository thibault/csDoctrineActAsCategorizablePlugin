<?php

/**
 * PluginCategory test
 */
include_once dirname(__FILE__).'/../../bootstrap/unit.php';

LimeAnnotationSupport::enable();
$t = new lime_test(0);

// @Before

$article = new TestArticle();
$article->setTitle('my test article');
$article->save();

$article2 = new TestArticle();
$article2->setTitle('my second test article');
$article2->save();

$category1 = new Category();
$category1->setName('category1');
$category1->save();

$category2 = new Category();
$category2->setName('category2');
$category2->save();

$tree = Doctrine::getTable('Category')->getTree();
$tree->createRoot($category1);

$category2->getNode()->insertAsLastChildOf($category1);
$category1->refresh();

$q = Doctrine::getTable('CategoryObject')->createQuery()
  ->addWhere('categorized_model = ?', 'TestArticle')
  ->addWhere('categorized_id = ?', $article->getId());

// @After

$article->delete();
$article2->delete();

Doctrine::getTable('CategoryObject')->createQuery()
  ->delete()
  ->execute();

Doctrine::getTable('Category')->createQuery()
  ->delete()
  ->execute();

// @Test: getObjects() returns all objects in the category

$article->Categories[] = $category1;
$article->save();

$article2->Categories[] = $category1;
$article2->save();

$objects = $category1->getObjects('TestArticle');

$t->is(get_class($objects), 'Doctrine_Collection');
$t->is($objects->count(), 2);
$t->is($category2->getObjects('TestArticle')->count(), 0);
$t->is(get_class($objects[0]), 'TestArticle');

// @Test: getSubtreeObjects returns all objects in the subtree

$article->Categories[] = $category1;
$article->save();

$article2->Categories[] = $category2;
$article2->save();

$t->is($category1->getSubtreeObjects('TestArticle')->count(), 2);
$t->is($category2->getSubtreeObjects('TestArticle')->count(), 1);

