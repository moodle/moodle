# phpMyAdmin MySQL-Dump
# version 2.2.1
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: localhost
# Generation Time: Nov 14, 2001 at 04:39 PM
# Server version: 3.23.36
# PHP Version: 4.0.6
# Database : moodle
# --------------------------------------------------------

#
# Table structure for table survey
#

CREATE TABLE prefix_survey (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  template integer NOT NULL default '0',
  days integer NOT NULL default '0',
  timecreated integer NOT NULL default '0',
  timemodified integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text,
  questions varchar(255) default NULL
);

#
# Dumping data for table survey
#

INSERT INTO prefix_survey (id, course, template, days, timecreated, timemodified, name, intro, questions) VALUES (1, 0, 0, 0, 985017600, 985017600, 'collesaname', 'collesaintro', '25,26,27,28,29,30,43,44');
INSERT INTO prefix_survey (id, course, template, days, timecreated, timemodified, name, intro, questions) VALUES (2, 0, 0, 0, 985017600, 985017600, 'collespname', 'collespintro', '31,32,33,34,35,36,43,44');
INSERT INTO prefix_survey (id, course, template, days, timecreated, timemodified, name, intro, questions) VALUES (3, 0, 0, 0, 985017600, 985017600, 'collesapname', 'collesapintro', '37,38,39,40,41,42,43,44');
INSERT INTO prefix_survey (id, course, template, days, timecreated, timemodified, name, intro, questions) VALUES (4, 0, 0, 0, 985017600, 985017600, 'attlsname', 'attlsintro', '65,67,68');
INSERT INTO prefix_survey (id, course, template, days, timecreated, timemodified, name, intro, questions) VALUES (5, 0, 0, 0, 985017600, 985017600, 'ciqname', 'ciqintro', '69,70,71,72,73');



#
# Table structure for table survey_analysis
#

CREATE TABLE prefix_survey_analysis (
  id SERIAL PRIMARY KEY,
  survey integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  notes text NOT NULL default ''
);

#
# Dumping data for table survey_analysis
#

# --------------------------------------------------------

#
# Table structure for table survey_answers
#

CREATE TABLE prefix_survey_answers (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  survey integer NOT NULL default '0',
  question integer NOT NULL default '0',
  time integer default NULL,
  answer1 text default NULL,
  answer2 text default NULL
);

#
# Dumping data for table survey_answers
#

# --------------------------------------------------------

#
# Table structure for table survey_questions
#

CREATE TABLE prefix_survey_questions (
  id SERIAL PRIMARY KEY,
  text varchar(255) NOT NULL default '',
  shorttext varchar(30) NOT NULL default '',
  multi varchar(100) NOT NULL default '',
  intro varchar(50) default NULL,
  type integer NOT NULL default '0',
  options text
);

#
# Dumping data for table survey_questions
#

INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (1, 'colles1', 'colles1short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (2, 'colles2', 'colles2short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (3, 'colles3', 'colles3short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (4, 'colles4', 'colles4short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (5, 'colles5', 'colles5short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (6, 'colles6', 'colles6short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (7, 'colles7', 'colles7short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (8, 'colles8', 'colles8short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (9, 'colles9', 'colles9short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (10, 'colles10', 'colles10short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (11, 'colles11', 'colles11short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (12, 'colles12', 'colles12short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (13, 'colles13', 'colles13short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (14, 'colles14', 'colles14short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (15, 'colles15', 'colles15short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (16, 'colles16', 'colles16short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (17, 'colles17', 'colles17short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (18, 'colles18', 'colles18short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (19, 'colles19', 'colles19short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (20, 'colles20', 'colles20short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (21, 'colles21', 'colles21short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (22, 'colles22', 'colles22short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (23, 'colles23', 'colles23short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (24, 'colles24', 'colles24short', '', '', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (25, 'collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (26, 'collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (27, 'collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (28, 'collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (29, 'collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (30, 'collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 1, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (31, 'collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (32, 'collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (33, 'collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (34, 'collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (35, 'collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (36, 'collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 2, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (37, 'collesm1', 'collesm1short', '1,2,3,4', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (38, 'collesm2', 'collesm2short', '5,6,7,8', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (39, 'collesm3', 'collesm3short', '9,10,11,12', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (40, 'collesm4', 'collesm4short', '13,14,15,16', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (41, 'collesm5', 'collesm5short', '17,18,19,20', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (42, 'collesm6', 'collesm6short', '21,22,23,24', 'collesmintro', 3, 'scaletimes5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (43, 'howlong', '', '', '', 1, 'howlongoptions');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (44, 'othercomments', '', '', '', 0, '');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (64, 'attls20', 'attls20short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (58, 'attls14', 'attls14short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (59, 'attls15', 'attls15short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (60, 'attls16', 'attls16short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (61, 'attls17', 'attls17short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (62, 'attls18', 'attls18short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (63, 'attls19', 'attls19short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (56, 'attls12', 'attls12short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (57, 'attls13', 'attls13short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (55, 'attls11', 'attls11short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (54, 'attls10', 'attls10short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (53, 'attls9', 'attls9short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (52, 'attls8', 'attls8short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (51, 'attls7', 'attls7short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (50, 'attls6', 'attls6short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (49, 'attls5', 'attls5short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (48, 'attls4', 'attls4short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (47, 'attls3', 'attls3short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (45, 'attls1', 'attls1short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (46, 'attls2', 'attls2short', '', '', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (65, 'attlsm1', 'attlsm1', '45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 'attlsmintro', 1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (67, 'attlsm2', 'attlsm2', '63,62,59,57,55,49,52,50,48,47', 'attlsmintro', -1, 'scaleagree5');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (68, 'attlsm3', 'attlsm3', '46,54,45,51,60,53,56,58,61,64', 'attlsmintro', -1, 'scaleagree5');


INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (69, 'ciq1', 'ciq1short', '', '', 0, '');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (70, 'ciq2', 'ciq2short', '', '', 0, '');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (71, 'ciq3', 'ciq3short', '', '', 0, '');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (72, 'ciq4', 'ciq4short', '', '', 0, '');
INSERT INTO prefix_survey_questions (id, text, shorttext, multi, intro, type, options) VALUES (73, 'ciq5', 'ciq5short', '', '', 0, '');

#
# Dumping data for table log_display
#

INSERT INTO prefix_log_display VALUES ('survey', 'add', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'update', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'download', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view form', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view graph', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'view report', 'survey', 'name');
INSERT INTO prefix_log_display VALUES ('survey', 'submit', 'survey', 'name');
