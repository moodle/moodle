<?PHP

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

	// CDC-FLAG
	if ($oldversion < 2004081700) {
		execute_sql("CREATE TABLE `mdl_lesson_default` 
		( `id` int(10) unsigned NOT NULL auto_increment,
		  `course` int(10) unsigned NOT NULL default '0',
		  `practice` tinyint(3) unsigned NOT NULL default '0',
		  `password` varchar(32) NOT NULL default '',
		  `usepassword` int(3) unsigned NOT NULL default '0',
		  `grade` tinyint(3) NOT NULL default '0',
		  `custom` int(3) unsigned NOT NULL default '0',
		  `ongoing` int(3) unsigned NOT NULL default '0',
		  `usemaxgrade` tinyint(3) unsigned NOT NULL default '0',
		  `maxanswers` int(3) unsigned NOT NULL default '4',
		  `maxattempts` int(3) unsigned NOT NULL default '5',
		  `review` tinyint(3) unsigned NOT NULL default '0',
		  `nextpagedefault` int(3) unsigned NOT NULL default '0',
		  `minquestions` tinyint(3) unsigned NOT NULL default '0',
		  `maxpages` int(3) unsigned NOT NULL default '0',
		  `timed` int(3) unsigned NOT NULL default '0',
		  `maxtime` int(10) unsigned NOT NULL default '0',
		  `retake` int(3) unsigned NOT NULL default '1',
		  `tree` int(3) unsigned NOT NULL default '0',
		  `slideshow` int(3) unsigned NOT NULL default '0',
		  `width` int(10) unsigned NOT NULL default '640',
		  `height` int(10) unsigned NOT NULL default '480',
		  `bgcolor` varchar(7) default '#FFFFFF',
		  `displayleft` int(3) unsigned NOT NULL default '0',
		  `highscores` int(3) unsigned NOT NULL default '0',
		  `maxhighscores` int(10) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) COMMENT = 'Defines lesson_default'");
	}
	
	if ($oldversion < 2004081100) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `practice` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `review` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
	}	
	
	if ($oldversion < 2004072100) {
		execute_sql(" create table mdl_lesson_high_scores
					( id int(10) unsigned not null auto_increment primary key,
					  lessonid int(10) unsigned not null,
					  userid int(10) unsigned not null,
					  gradeid int(10) unsigned not null,
					  nickname varchar(5) not null,
					  PRIMARY KEY  (`id`)
					)");

		execute_sql(" create table mdl_lesson_essay
					( id int(10) unsigned not null auto_increment primary key,
					  lessonid int(10) unsigned not null,
					  userid int(10) unsigned not null,
					  pageid int(10) unsigned not null,
					  answerid int(10) unsigned not null,
					  try int(10) unsigned not null,
					  answer text not null,
					  graded tinyint(3) unsigned not null default 0,
					  score int(10) unsigned not null default 0,
					  response text not null,
					  sent tinyint(3) unsigned not null default 0,
					  timesubmitted int(10) unsigned not null,
					  PRIMARY KEY  (`id`)
					)");

		execute_sql(" create table mdl_lesson_branch
					( id int(10) unsigned not null auto_increment primary key,
					  lessonid int(10) unsigned not null,
					  userid int(10) unsigned not null,
					  pageid int(10) unsigned not null,
					  retry int(10) unsigned not null,
					  flag  tinyint(3) unsigned not null,
					  timeseen int(10) unsigned not null,
					  PRIMARY KEY  (`id`)
					)");

		
		execute_sql(" create table mdl_lesson_timer
					( id int(10) unsigned not null auto_increment primary key,
  					lessonid int(10) unsigned not null,
					userid int(10) unsigned not null,
					starttime int(10) unsigned not null,
  					lessontime int(10) unsigned not null,
 	     		    PRIMARY KEY  (`id`)
					)");

	
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `layout` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER qoption");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `display` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER layout");

		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_answers` ADD `score` INT(10) NOT NULL DEFAULT '0' AFTER grade");
	
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `usepassword` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER name");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `password` VARCHAR(32) NOT NULL DEFAULT '' AFTER usepassword");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `custom` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `ongoing` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER custom");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `timed` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxpages");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxtime` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER timed");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `tree` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER retake");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `slideshow` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER tree");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `width` INT(10) UNSIGNED NOT NULL DEFAULT '640' AFTER slideshow");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `height` INT(10) UNSIGNED NOT NULL DEFAULT '480' AFTER width");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `bgcolor` CHAR(7) NOT NULL DEFAULT '#FFFFFF' AFTER height");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `displayleft` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER bgcolor");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `highscores` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER displayleft");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxhighscores` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER highscores");

	}
	// CDC-FLAG end	

    if ($oldversion < 2004021600) {

       delete_records("log_display", "module", "lesson");

       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');");

    }

    if ($oldversion < 2004022200) {

		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxattempts` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxanswers");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `nextpagedefault` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER maxattempts");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `maxpages` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qtype` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER lessonid");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_pages` ADD `qoption` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER qtype");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson_answers` ADD `grade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER jumpto");

    }

    if ($oldversion < 2004032000) {           // Upgrade some old beta lessons
		execute_sql(" UPDATE `{$CFG->prefix}lesson_pages` SET qtype = 3 WHERE qtype = 0");
    }
    
    if ($oldversion < 2004032400) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `usemaxgrade` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER grade");
		execute_sql(" ALTER TABLE `{$CFG->prefix}lesson` ADD `minquestions` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER nextpagedefault");
    }
 
    if ($oldversion < 2004032700) {
		table_column("lesson_answers", "", "flags", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
    }
     
    return true;
}

?>