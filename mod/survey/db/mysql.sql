# phpMyAdmin MySQL-Dump
# version 2.2.1
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#
# Host: localhost
# Generation Time: Nov 14, 2001 at 04:39 PM
# Server version: 3.23.36
# PHP Version: 4.0.6
# Database : `moodle`
# --------------------------------------------------------

#
# Table structure for table `survey`
#

CREATE TABLE survey (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  template int(10) unsigned NOT NULL default '0',
  days smallint(6) NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text,
  questions varchar(255) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM COMMENT='all surveys';

#
# Dumping data for table `survey`
#

INSERT INTO survey VALUES (1, 0, 0, 0, 985017600, 985017600, 'COLLES (Actual)', 'The purpose of this questionnaire is to help us understand how well the online delivery of this unit enabled you to learn. \r\n\r\nEach one of the 24 statements below asks about your experience in this unit.\r\n\r\nThere are no \'right\' or \'wrong\' answers; we are interested only in your opinion. Please be assured that your responses will be treated with a high degree of confidentiality, and will not affect your assessment.\r\n\r\nYour carefully considered responses will help us improve the way this unit is presented online in the future.\r\n\r\nThanks very much.\r\n', '25,26,27,28,29,30,43,44');
INSERT INTO survey VALUES (2, 0, 0, 0, 985017600, 985017600, 'COLLES (Preferred)', 'The purpose of this questionnaire is to help us understand what you value in an online learning experience.\r\n\r\nEach one of the 24 statements below asks about your <B>preferred</B> (ideal) experience in this unit.\r\n\r\nThere are no \'right\' or \'wrong\' answers; we are interested only in your opinion. Please be assured that your responses will be treated with a high degree of confidentiality, and will not affect your assessment.\r\n\r\nYour carefully considered responses will help us improve the way this unit is presented online in the future.\r\n\r\nThanks very much.\r\n', '31,32,33,34,35,36,43,44');
INSERT INTO survey VALUES (3, 0, 0, 0, 985017600, 985017600, 'COLLES (Preferred and Actual)', 'The purpose of this questionnaire is to help us understand how well the online delivery of this unit enabled you to learn. \r\n\r\nEach one of the 24 statements below asks you to compare your <B>preferred</B> (ideal) and <B>actual</B> experience in this unit.\r\n\r\nThere are no \'right\' or \'wrong\' answers; we are interested only in your opinion. Please be assured that your responses will be treated with a high degree of confidentiality, and will not affect your assessment.\r\n\r\nYour carefully considered responses will help us improve the way this unit is presented online in the future.\r\n\r\nThanks very much.\r\n', '37,38,39,40,41,42,43,44');
INSERT INTO survey VALUES (4, 0, 0, 0, 985017600, 985017600, 'ATTLS (20 item version)', 'The purpose of this questionnaire is to help \r\nus evaluate your attitudes towards thinking and learning.\r\n', '65,67,68');
# --------------------------------------------------------

#
# Table structure for table `survey_analysis`
#

CREATE TABLE survey_analysis (
  id int(10) unsigned NOT NULL auto_increment,
  survey int(10) unsigned NOT NULL default '0',
  user int(10) unsigned NOT NULL default '0',
  notes text NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

#
# Dumping data for table `survey_analysis`
#

# --------------------------------------------------------

#
# Table structure for table `survey_answers`
#

CREATE TABLE survey_answers (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  survey int(10) unsigned NOT NULL default '0',
  question int(10) unsigned NOT NULL default '0',
  time int(10) unsigned default NULL,
  answer1 char(255) default NULL,
  answer2 char(255) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM;

#
# Dumping data for table `survey_answers`
#

# --------------------------------------------------------

#
# Table structure for table `survey_questions`
#

CREATE TABLE survey_questions (
  id int(10) unsigned NOT NULL auto_increment,
  owner int(10) unsigned NOT NULL default '0',
  text varchar(255) NOT NULL default '',
  multi varchar(100) NOT NULL default '',
  intro varchar(50) default NULL,
  type tinyint(3) NOT NULL default '0',
  options text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `survey_questions`
#

INSERT INTO survey_questions VALUES (1, 0, 'my learning focuses on issues that interest me.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (2, 0, 'what I learn is important for my professional practice.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (3, 0, 'I learn how to improve my professional practice.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (4, 0, 'what I learn connects well with my professional practice.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (5, 0, 'I think critically about how I learn.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (6, 0, 'I think critically about my own ideas.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (7, 0, 'I think critically about other students\' ideas.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (8, 0, 'I think critically about ideas in the readings.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (9, 0, 'I explain my ideas to other students.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (10, 0, 'I ask other students to explain their ideas.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (11, 0, 'other students ask me to explain my ideas.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (12, 0, 'other students respond to my ideas.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (13, 0, 'the tutor stimulates my thinking.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (14, 0, 'the tutor encourages me to participate.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (15, 0, 'the tutor models good discourse.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (16, 0, 'the tutor models critical self-reflection.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (17, 0, 'other students encourage my participation.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (18, 0, 'other students praise my contribution.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (19, 0, 'other students value my contribution.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (20, 0, 'other students empathise with my struggle to learn.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (21, 0, 'I make good sense of other students\' messages.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (22, 0, 'other students make good sense of my messages.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (23, 0, 'I make good sense of the tutor\'s messages.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (24, 0, 'the tutor makes good sense of my messages.', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO survey_questions VALUES (25, 0, 'Relevance', '1,2,3,4', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (26, 0, 'Reflective Thinking', '5,6,7,8', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (27, 0, 'Interactivity', '9,10,11,12', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (28, 0, 'Tutor Support', '13,14,15,16', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (29, 0, 'Peer Support', '17,18,19,20', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (30, 0, 'Interpretation', '21,22,23,24', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (31, 0, 'Relevance', '1,2,3,4', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (32, 0, 'Reflective Thinking', '5,6,7,8', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (33, 0, 'Interactivity', '9,10,11,12', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (34, 0, 'Tutor Support', '13,14,15,16', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (35, 0, 'Peer Support', '17,18,19,20', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (36, 0, 'Interpretation', '21,22,23,24', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (37, 0, 'Relevance', '1,2,3,4', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (38, 0, 'Reflective Thinking', '5,6,7,8', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (39, 0, 'Interactivity', '9,10,11,12', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (40, 0, 'Tutor Support', '13,14,15,16', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (41, 0, 'Peer Support', '17,18,19,20', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (42, 0, 'Interpretation', '21,22,23,24', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO survey_questions VALUES (43, 0, 'How long did this survey take you to complete?', '', '', 1, 'under 1 min,1-2 min,2-3 min,3-4 min,4-5-min,5-10 min,more than 10\r');
INSERT INTO survey_questions VALUES (44, 0, 'Do you have any other comments?', '', '', 0, '\r');
INSERT INTO survey_questions VALUES (64, 0, 'I spend time figuring out what\'s "wrong" with things. For example, I\'ll look for something in a literary interpretation that isn\'t argued well enough.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (58, 0, 'I try to point out weaknesses in other people\'s thinking to help them clarify their arguments.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (59, 0, 'I tend to put myself in other people\'s shoes when discussing controversial issues, to see why they think the way they do.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (60, 0, 'One could call my way of analysing things "putting them on trial" because I am careful to consider all the evidence.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (61, 0, 'I value the use of logic and reason over the incorporation of my own concerns when solving problems.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (62, 0, 'I can obtain insight into opinions that differ from mine through empathy.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (63, 0, 'When I encounter people whose opinions seem alien to me, I make a deliberate effort to "extend" myself into that person, to try to see how they could have those opinions.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (56, 0, 'I have certain criteria I use in evaluating arguments.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (57, 0, 'I\'m more likely to try to understand someone else\'s opinion that to try to evaluate it.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (55, 0, 'I try to think with people instead of against them.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (54, 0, 'It\'s important for me to remain as objective as possible when I analyze something.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (53, 0, 'I often find myself arguing with the authors of books that I read, trying to logically figure out why they\'re wrong.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (52, 0, 'I am always interested in knowing why people say and believe the things they do.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (51, 0, 'I find that I can strengthen my own position through arguing with someone who disagrees with me.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (50, 0, 'I enjoy hearing the opinions of people who come from backgrounds different to mine - it helps me to understand how the same things can be seen in such different ways.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (49, 0, 'I feel that the best way for me to achieve my own identity is to interact with a variety of other people.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (48, 0, 'The most important part of my education has been learning to understand people who are very different to me.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (47, 0, 'I like to understand where other people are "coming from", what experiences have led them to feel the way they do.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (45, 0, 'In evaluating what someone says, I focus on the quality of their argument, not on the person who\'s presenting it.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (46, 0, 'I like playing devil\'s advocate - arguing the opposite of what someone is saying.', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (65, 0, 'Attitudes Towards Thinking and Learning', '45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 'In discussion ...', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (67, 0, 'Connected Learning', '63,62,59,57,55,49,52,50,48,47', 'Connected knowers...', -1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO survey_questions VALUES (68, 0, 'Separate Learning', '46,54,45,51,60,53,56,58,61,64', 'Separate knowers...', -1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');


#
# Dumping data for table `log_display`
#

INSERT INTO log_display VALUES ('survey', 'download', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view form', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view graph', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view report', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'submit', 'survey', 'name');
