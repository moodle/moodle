#
# A note on the naming convention used in this module:
#
# As a Dialogue instance can have a number of Dialogues 
# the fact that a course can have more than one Dialogue 
# instance complicates the nomenclature within the code. 
# We want to easily refer to dialogues within dialogues.
#
# For this reason a Dialogue instance is said to have 
# a set of Conversations (not dialogues). So the code
# (and the table structure here) uses conversations
# within dialogues. The term "conversation" is just an 
# INTERNAL name and it is NOT used in the user 
# interface. There is NO need to distinguish between 
# dialogue instances and a set of dialogues within one
# dialogue instance from the point of view of teachers 
# or students point of view.

#
# Table structure for table `dialogue`
#

CREATE TABLE prefix_dialogue (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  deleteafter smallint(5) unsigned NOT NULL default '14',
  dialoguetype tinyint(3) NOT NULL default '0',
  multipleconversations tinyint(3) NOT NULL default '0',
  maildefault tinyint(3) NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  name varchar(255) default NULL,
  intro text,
  PRIMARY KEY  (id)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `dialogue_conversations` (Virtual Conversations)
#

CREATE TABLE prefix_dialogue_conversations (
  id int(10) unsigned NOT NULL auto_increment,
  dialogueid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  recipientid int(10) unsigned NOT NULL default '0',
  lastid int(10) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  closed tinyint(3) NOT NULL default '0',
  seenon int(10) unsigned NOT NULL default '0',
  ctype tinyint(3) NOT NULL default '0',
  format tinyint(2) NOT NULL default '0',
  subject varchar(100) not null default '',
  PRIMARY KEY  (id),
  KEY (dialogueid),
  KEY (timemodified)
) TYPE=MyISAM COMMENT='All the conversations between pairs of people';

#
# Table structure for table `dialogue_entries`
#

CREATE TABLE prefix_dialogue_entries (
  id int(10) unsigned NOT NULL auto_increment,
  dialogueid int(10) unsigned NOT NULL default '0',
  conversationid int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  timecreated int(10) unsigned NOT NULL default '0',
  mailed int(1) unsigned NOT NULL default '0',
  text text NOT NULL,
  PRIMARY KEY  (id),
  KEY (conversationid)
) TYPE=MyISAM COMMENT='All the conversation entries';

#
# Data for the table `log_display`
#

INSERT INTO prefix_log_display VALUES ('dialogue', 'view', 'dialogue', 'name');
