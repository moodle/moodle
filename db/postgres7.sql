# $Id: postgres7.sql,v 1.1 2006/03/12 18:40:01 skodak Exp $

# This file contains a complete database schema for all the
# tables used by the book module, written in SQL

# It may also contain INSERT statements for particular data
# that may be used, especially new entries in the table log_display

CREATE TABLE prefix_book (
  id SERIAL PRIMARY KEY,
  course INT4 NOT NULL DEFAULT '0',
  name VARCHAR(255) NOT NULL DEFAULT '',
  summary TEXT NOT NULL DEFAULT '',
  numbering INT4 NOT NULL DEFAULT '0',
  disableprinting INT2 NOT NULL DEFAULT '0',
  customtitles INT2 NOT NULL DEFAULT '0',
  timecreated INT4 NOT NULL DEFAULT '0',
  timemodified INT4 NOT NULL DEFAULT '0'
);
# --------------------------------------------------------

CREATE TABLE prefix_book_chapters (
  id SERIAL PRIMARY KEY,
  bookid INT4 NOT NULL DEFAULT '0',
  pagenum INT4 NOT NULL DEFAULT '0',
  subchapter INT2 NOT NULL DEFAULT '0',
  title VARCHAR(255) NOT NULL DEFAULT '',
  content TEXT NOT NULL DEFAULT '',
  hidden INT2 NOT NULL DEFAULT '0',
  timecreated INT4 NOT NULL DEFAULT '0',
  timemodified INT4 NOT NULL DEFAULT '0',
  importsrc VARCHAR(255) NOT NULL DEFAULT ''
);
# --------------------------------------------------------

INSERT INTO prefix_log_display VALUES ('book', 'update',   'book', 'name');
INSERT INTO prefix_log_display VALUES ('book', 'view',     'book', 'name');
INSERT INTO prefix_log_display VALUES ('book', 'view all', 'book', 'name');
INSERT INTO prefix_log_display VALUES ('book', 'print',    'book', 'name');
