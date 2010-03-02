<?php

/**
 * Doctrine_Template_Categorizable tests.
 */
include_once dirname(__FILE__).'/../../bootstrap/unit.php';

LimeAnnotationSupport::enable();
$t = new lime_test(0);

// @Before
$article = new TestArticle();
$article->setTitle('my test article');
$article->save();

// @After
$article->delete();

// @Test: Template is available

$t->ok(is_callable(array($article, 'getCategories')));
