#
# Table structure for table `chat`
#

CREATE TABLE `prefix_chat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `intro` text NOT NULL,
  `keepdays` int(11) NOT NULL default '0',
  `studentlogs` int(4) NOT NULL default '0',
  `chattime` int(10) unsigned NOT NULL default '0',
  `schedule` int(4) NOT NULL default '0',
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='Each of these is a chat room';
# --------------------------------------------------------

#
# Table structure for table `chat_messages`
#

CREATE TABLE `prefix_chat_messages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `chatid` int(10) NOT NULL default '0',
  `userid` int(10) NOT NULL default '0',
  `groupid` int(10) NOT NULL default '0',
  `system` int(1) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `timemodifiedchat` (`timestamp`,`chatid`)
) TYPE=MyISAM COMMENT='Stores all the actual chat messages';
# --------------------------------------------------------

#
# Table structure for table `chat_users`
#

CREATE TABLE `prefix_chat_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `chatid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `groupid` int(11) NOT NULL default '0',
  `version` varchar(16) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `firstping` int(10) unsigned NOT NULL default '0',
  `lastping` int(10) unsigned NOT NULL default '0',
  `lastmessageping` int(10) unsigned NOT NULL default '0',
  `sid` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `lastping` (`lastping`)
) TYPE=MyISAM COMMENT='Keeps track of which users are in which chat rooms';


INSERT INTO prefix_log_display VALUES ('chat', 'view', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'add', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'update', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'report', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'talk', 'chat', 'name');

