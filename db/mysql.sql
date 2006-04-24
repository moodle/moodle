# $Id: mysql.sql,v 1.2 2006/04/24 19:35:48 skodak Exp $

# This file contains a complete database schema for all the
# tables used by the book module, written in SQL

# It may also contain INSERT statements for particular data
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_book (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  course INT(10) UNSIGNED NOT NULL DEFAULT '0',
  name VARCHAR(255) NOT NULL DEFAULT '',
  summary TEXT NOT NULL DEFAULT '',
  numbering TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  disableprinting TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  customtitles TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  timecreated INT(10) UNSIGNED NOT NULL DEFAULT '0',
  timemodified INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) COMMENT='Defines book';
# --------------------------------------------------------

CREATE TABLE prefix_book_chapters (
  id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  bookid INT(10) UNSIGNED NOT NULL DEFAULT '0',
  pagenum INT(10) UNSIGNED NOT NULL DEFAULT '0',
  subchapter TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  title VARCHAR(255) NOT NULL DEFAULT '',
  content LONGTEXT NOT NULL DEFAULT '',
  hidden TINYINT(2) UNSIGNED NOT NULL DEFAULT '0',
  timecreated INT(10) UNSIGNED NOT NULL DEFAULT '0',
  timemodified INT(10) UNSIGNED NOT NULL DEFAULT '0',
  importsrc VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id)
) COMMENT='Defines book_chapters';
# --------------------------------------------------------

INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('book', 'update',   'book', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('book', 'view',     'book', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('book', 'view all', 'book', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('book', 'print',    'book', 'name');
