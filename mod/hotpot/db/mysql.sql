#
# Table structure for table `hotpot`
#
CREATE TABLE prefix_hotpot (
    id int(10) unsigned NOT NULL auto_increment,
    course int(10) unsigned NOT NULL default '0',
    name varchar(255) NOT NULL default '',
    summary text NOT NULL default '',
    timeopen int(10) unsigned NOT NULL default '0',
    timeclose int(10) unsigned NOT NULL default '0',
    location int(4) unsigned NOT NULL default '0',
    reference varchar(255) NOT NULL default '',
    outputformat int(4) unsigned NOT NULL default '1',
    navigation int(4) unsigned NOT NULL default '1',
    studentfeedback tinyint(4) unsigned NOT NULL default '0',
    studentfeedbackurl varchar(255) NOT NULL default '',
    forceplugins int(4) unsigned NOT NULL default '0',
    shownextquiz int(4) unsigned NOT NULL default '0',
    review tinyint(4) NOT NULL default '0',
    grade int(10) NOT NULL default '0',
    grademethod tinyint(4) NOT NULL default '1',
    attempts smallint(6) NOT NULL default '0',
    password varchar(255) NOT NULL default '',
    subnet varchar(255) NOT NULL default '',
    clickreporting tinyint(4) unsigned NOT NULL default '0',
    timecreated int(10) unsigned NOT NULL default '0',
    timemodified int(10) unsigned NOT NULL default '0',
    PRIMARY KEY (id)
) TYPE=MyISAM COMMENT='details about Hot Potatoes quizzes';
#
# Table structure for table `hotpot_attempts`
#
CREATE TABLE prefix_hotpot_attempts (
    id int(10) unsigned NOT NULL auto_increment,
    hotpot int(10) unsigned NOT NULL default '0',
    userid int(10) unsigned NOT NULL default '0',
    starttime int(10) unsigned NOT NULL default '0',
    endtime int(10) unsigned NOT NULL default '0',
    score int(6) unsigned NOT NULL default '0',
    penalties int(6) unsigned NOT NULL default '0',
    attempt int(6) unsigned NOT NULL default '0',
    timestart int(10) unsigned NOT NULL default '0',
    timefinish int(10) unsigned NOT NULL default '0',
    status tinyint(4) unsigned NOT NULL default '1',
    clickreportid int(10) unsigned NOT NULL default '0',
    PRIMARY KEY (id),
    KEY hotpot_attempts_hotpot_idx (hotpot),
    KEY hotpot_attempts_userid_idx (userid)
) TYPE=MyISAM COMMENT='details about Hot Potatoes quiz attempts';
# 
# Table structure for table `hotpot_details`
#
CREATE TABLE prefix_hotpot_details (
    id int(10) unsigned NOT NULL auto_increment,
    attempt int(10) unsigned NOT NULL default '0',
    details text default '',
    PRIMARY KEY (id),
    KEY hotpot_details_attempt_idx (attempt)
) TYPE=MyISAM COMMENT='raw details (as XML) of Hot Potatoes quiz attempts';
#
# Table structure for table `hotpot_questions`
#
CREATE TABLE prefix_hotpot_questions (
    id int(10) unsigned NOT NULL auto_increment,
    name text NOT NULL default '',
    type tinyint(4) unsigned NOT NULL default '0',
    text int(10) unsigned NOT NULL default '0',
    hotpot int(10) unsigned NOT NULL default '0',
    md5key varchar(32) NOT NULL default '',
    PRIMARY KEY (id),
    KEY hotpot_questions_hotpot_idx (hotpot),
    KEY hotpot_questions_md5key_idx (md5key)
) TYPE=MyISAM COMMENT='details about questions in Hot Potatoes quiz attempts';
#
# Table structure for table `hotpot_responses`
#
CREATE TABLE prefix_hotpot_responses (
    id int(10) unsigned NOT NULL auto_increment,
    attempt int(10) unsigned NOT NULL default '0',
    question int(10) unsigned NOT NULL default '0',
    score smallint(6) NOT NULL default '0',
    weighting smallint(6) NOT NULL default '0',
    correct varchar(255) NOT NULL default '',
    wrong varchar(255) NOT NULL default '',
    ignored varchar(255) NOT NULL default '',
    hints smallint(6) unsigned NOT NULL default '0',
    clues smallint(6) unsigned NOT NULL default '0',
    checks smallint(6) unsigned NOT NULL default '0',
    PRIMARY KEY (id),
    KEY hotpot_responses_attempt_idx (attempt),
    KEY hotpot_responses_question_idx (question)
) TYPE=MyISAM COMMENT='details about responses in Hot Potatoes quiz attempts';
#
# Table structure for table `hotpot_strings`
#
CREATE TABLE prefix_hotpot_strings (
    id int(10) unsigned NOT NULL auto_increment,
    string text NOT NULL default '',
    md5key varchar(32) NOT NULL default '',
    PRIMARY KEY (id),
    KEY hotpot_strings_md5key_idx (md5key)
) TYPE=MyISAM COMMENT='strings used in Hot Potatoes questions and responses';
        
