CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255), root_id INTEGER, lft INTEGER, rgt INTEGER, level INTEGER);
CREATE TABLE category_object (category_id INTEGER, categorized_model VARCHAR(50), categorized_id INTEGER, PRIMARY KEY(category_id, categorized_model, categorized_id));
CREATE TABLE test_article (id INTEGER PRIMARY KEY AUTOINCREMENT, title VARCHAR(255));
CREATE INDEX Categorized_idx ON category_object (categorized_model, categorized_id);
