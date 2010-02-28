<?php

/**
 * Categorizable tests.
 *
 * Ok, functional tests for a plugin really looks like unit tests
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

$t->is($q->count(), 0);

$article->Categories[] = $category1;
$article->save();

$t->is($q->count(), 1);

$article->Categories[] = $category2;
$article->save();

$t->is($q->count(), 2);

// @Test: you can add several categories at once

$t->is($q->count(), 0);

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();

$t->is($q->count(), 2);

// @Test: categories can be removed from an object

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();

unset($article->Categories[0]);
$article->save();

$t->is($q->count(), 1);

// @Test: getCategories returns the categories the article belongs

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();

$categories = $article->get('Categories');
$t->is(get_class($categories), 'Doctrine_Collection');
$t->is($categories->count(), 2);

// @Test: Categories are affected to the correct object

$newArticle = new TestArticle();
$newArticle->setTitle('new article');
$newArticle->Categories[]->name = 'new category';

$t->is($q->count(), 0);
$t->is($newArticle->getCategories()->count(), 1);

// @Test: Delete a category delete the association object

$article->Categories[] = $category1;
$article->Categories[] = $category2;
$article->save();
$article->Categories[1]->delete();

$q = Doctrine::getTable('CategoryObject')->createQuery();
$t->is($q->count(), 1);
