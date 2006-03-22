

-- 
-- Table structure for table `mdl_question_essay`
-- 

CREATE TABLE `prefix_question_essay` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` int(10) unsigned NOT NULL default '0',
  `answer` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `question` (`question`)
) TYPE=MyISAM COMMENT='Options for essay questions';

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_question_essay_states`
-- 

CREATE TABLE `prefix_question_essay_states` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `stateid` int(10) unsigned NOT NULL default '0',
  `graded` tinyint(4) unsigned NOT NULL default '0',
  `fraction` float NOT NULL default '0',
  `response` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='essay question type specific state information';

-- --------------------------------------------------------