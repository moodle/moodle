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

CREATE TABLE `survey_questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `text` varchar(255) NOT NULL default '',
  `shorttext` varchar(30) NOT NULL default '',
  `multi` varchar(100) NOT NULL default '',
  `intro` varchar(50) default NULL,
  `type` tinyint(3) NOT NULL default '0',
  `options` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Dumping data for table `survey_questions`
#

INSERT INTO `survey_questions` VALUES (1, 'my learning focuses on issues that interest me.', 'focus on interesting issues', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (2, 'what I learn is important for my professional practice.', 'important to my practice', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (3, 'I learn how to improve my professional practice.', 'improve my practice', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (4, 'what I learn connects well with my professional practice.', 'connects with my practice', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (5, 'I think critically about how I learn.', 'I\'m critical of my learning', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (6, 'I think critically about my own ideas.', 'I\'m critical of my own ideas', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (7, 'I think critically about other students\' ideas.', 'I\'m critical of other students', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (8, 'I think critically about ideas in the readings.', 'I\'m critical of readings', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (9, 'I explain my ideas to other students.', 'I explain my ideas', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (10, 'I ask other students to explain their ideas.', 'I ask for explanations', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (11, 'other students ask me to explain my ideas.', 'I\'m asked to explain', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (12, 'other students respond to my ideas.', 'students respond to me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (13, 'the tutor stimulates my thinking.', 'tutor stimulates thinking', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (14, 'the tutor encourages me to participate.', 'tutor encourages me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (15, 'the tutor models good discourse.', 'tutor models discourse', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (16, 'the tutor models critical self-reflection.', 'tutor models self-reflection', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (17, 'other students encourage my participation.', 'students encourage me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (18, 'other students praise my contribution.', 'students praise me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (19, 'other students value my contribution.', 'students value me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (20, 'other students empathise with my struggle to learn.', 'student empathise', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (21, 'I make good sense of other students\' messages.', 'I understand other students', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (22, 'other students make good sense of my messages.', 'students understand me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (23, 'I make good sense of the tutor\'s messages.', 'I understand the tutor', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (24, 'the tutor makes good sense of my messages.', 'tutor understands me', '', '', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r\n');
INSERT INTO `survey_questions` VALUES (25, 'Relevance', 'Relevance', '1,2,3,4', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (26, 'Reflective Thinking', 'Reflective Thinking', '5,6,7,8', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (27, 'Interactivity', 'Interactivity', '9,10,11,12', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (28, 'Tutor Support', 'Tutor Support', '13,14,15,16', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (29, 'Peer Support', 'Peer Support', '17,18,19,20', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (30, 'Interpretation', 'Interpretation', '21,22,23,24', 'In this online unit...', 1, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (31, 'Relevance', 'Relevance', '1,2,3,4', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (32, 'Reflective Thinking', 'Reflective Thinking', '5,6,7,8', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (33, 'Interactivity', 'Interactivity', '9,10,11,12', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (34, 'Tutor Support', 'Tutor Support', '13,14,15,16', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (35, 'Peer Support', '', '17,18,19,20', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (36, 'Interpretation', '', '21,22,23,24', 'In this online unit, I prefer that...', 2, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (37, 'Relevance', '', '1,2,3,4', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (38, 'Reflective Thinking', '', '5,6,7,8', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (39, 'Interactivity', '', '9,10,11,12', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (40, 'Tutor Support', '', '13,14,15,16', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (41, 'Peer Support', '', '17,18,19,20', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (42, 'Interpretation', '', '21,22,23,24', 'In this online unit...', 3, 'Almost Never,Seldom,Sometimes,Often,Almost Always\r');
INSERT INTO `survey_questions` VALUES (43, 'How long did this survey take you to complete?', '', '', '', 1, 'under 1 min,1-2 min,2-3 min,3-4 min,4-5-min,5-10 min,more than 10\r');
INSERT INTO `survey_questions` VALUES (44, 'Do you have any other comments?', '', '', '', 0, '\r');
INSERT INTO `survey_questions` VALUES (64, 'I spend time figuring out what\'s "wrong" with things. For example, I\'ll look for something in a literary interpretation that isn\'t argued well enough.', 'what\'s wrong?', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (58, 'I try to point out weaknesses in other people\'s thinking to help them clarify their arguments.', 'point out weaknesses', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (59, 'I tend to put myself in other people\'s shoes when discussing controversial issues, to see why they think the way they do.', 'put myself in their shoes', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (60, 'One could call my way of analysing things "putting them on trial" because I am careful to consider all the evidence.', 'putting on trial', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (61, 'I value the use of logic and reason over the incorporation of my own concerns when solving problems.', 'i value logic most', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (62, 'I can obtain insight into opinions that differ from mine through empathy.', 'insight from empathy', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (63, 'When I encounter people whose opinions seem alien to me, I make a deliberate effort to "extend" myself into that person, to try to see how they could have those opinions.', 'make effort to extend', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (56, 'I have certain criteria I use in evaluating arguments.', 'use criteria to evaluate', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (57, 'I\'m more likely to try to understand someone else\'s opinion that to try to evaluate it.', 'try to understand', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (55, 'I try to think with people instead of against them.', 'think WITH people', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (54, 'It\'s important for me to remain as objective as possible when I analyze something.', 'remain objective', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (53, 'I often find myself arguing with the authors of books that I read, trying to logically figure out why they\'re wrong.', 'argue with authors', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (52, 'I am always interested in knowing why people say and believe the things they do.', 'know why people do', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (51, 'I find that I can strengthen my own position through arguing with someone who disagrees with me.', 'strengthen by argue', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (50, 'I enjoy hearing the opinions of people who come from backgrounds different to mine - it helps me to understand how the same things can be seen in such different ways.', 'enjoy hearing opinions', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (49, 'I feel that the best way for me to achieve my own identity is to interact with a variety of other people.', 'interact with variety', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (48, 'The most important part of my education has been learning to understand people who are very different to me.', 'understand different people', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (47, 'I like to understand where other people are "coming from", what experiences have led them to feel the way they do.', 'where people come from', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (45, 'In evaluating what someone says, I focus on the quality of their argument, not on the person who\'s presenting it.', 'focus quality of argument', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (46, 'I like playing devil\'s advocate - arguing the opposite of what someone is saying.', 'play devil\'s advocate', '', '', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (65, 'Attitudes Towards Thinking and Learning', '', '45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64', 'In discussion ...', 1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (67, 'Connected Learning', '', '63,62,59,57,55,49,52,50,48,47', 'Connected knowers...', -1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');
INSERT INTO `survey_questions` VALUES (68, 'Separate Learning', '', '46,54,45,51,60,53,56,58,61,64', 'Separate knowers...', -1, 'Strongly disagree,Somewhat disagree,Neither agree nor disagree,Somewhat agree,Strongly agree');

    



#
# Dumping data for table `log_display`
#

INSERT INTO log_display VALUES ('survey', 'download', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view form', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view graph', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'view report', 'survey', 'name');
INSERT INTO log_display VALUES ('survey', 'submit', 'survey', 'name');
