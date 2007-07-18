-- 
-- Table structure for table `mdl_assignment_epaile_results`
-- 

CREATE TABLE `prefix_assignment_epaile_results` (
  `id` bigint(10) NOT NULL auto_increment,
  `submission` bigint(10) NOT NULL,
  `test` bigint(10) NOT NULL,
  `runtime` bigint(10) NOT NULL,
  `status` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'Possible values: Accepted, Wrong answer, Internal error',
  `output` varchar(255) collate utf8_unicode_ci default NULL COMMENT 'Program output',
  `error` text collate utf8_unicode_ci COMMENT 'Runtime error',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Test results' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_assignment_epaile_submissions`
-- 

CREATE TABLE `prefix_assignment_epaile_submissions` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `submission` bigint(10) unsigned NOT NULL default '0',
  `compileerrors` text collate utf8_unicode_ci,
  `compiletime` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `prefix_epaisubm_epa_ix` (`submission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Info about submitted assignments' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `mdl_assignment_epaile_tests`
-- 

CREATE TABLE `prefix_assignment_epaile_tests ` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `assignment` bigint(10) unsigned NOT NULL default '0',
  `input` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Program input',
  `output` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Expected output',
  PRIMARY KEY  (`id`),
  KEY `prefix_epaitest_epa_ix` (`assignment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Info about assignment tests' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Add new field to `assignment` table
--
ALTER TABLE `prefix_assignment` ADD `lang` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `var5` ;

--
-- Some values for `config` table
--
 INSERT INTO `prefix_config` (`name`,`value`) VALUES ('assignment_maxmem', '512000'),('assignment_maxcpu', '15');