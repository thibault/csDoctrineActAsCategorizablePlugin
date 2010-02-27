<?php

/**
 * Doctrine_Template_Categorizable tests.
 */
include_once dirname(__FILE__).'/../bootstrap/unit.php';

LimeAnnotationSupport::enable();
$t = new lime_test(0);

// @Before

$article = new TestArticle();
$article->setTitle('my test article');
$article->save();

$category1 = new Category();
$category1->setName('category1');
$category1->save();

$category2 = new Category();
$category2->setName('category2');
$category2->save();

$q = Doctrine::getTable('CategoryObject')->createQuery()
  ->addWhere('categorized_model = ?', 'TestArticle')
  ->addWhere('categorized_id = ?', $article->getId());

// @After

$article->delete();

Doctrine::getTable('CategoryObject')->createQuery()
  ->delete()
  ->execute();

Doctrine::getTable('Category')->createQuery()
  ->delete()
  ->execute();

// @Test: a CategoryObject is created when you add category

$t->is(0, $q->count());

$article->Categories[] = $category1;
$article->save();

$t->is(1, $q->count());

$article->Categories[] = $category2;
$article->save();

$t->is(2, $q->count());

// @Test: you can add several categories at once

$t->is(0, $q->count());

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();

$t->is(2, $q->count());

// @Test: categories can be removed from an object

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();

unset($article->Categories[0]);
$article->save();

$t->is(1, $q->count());

