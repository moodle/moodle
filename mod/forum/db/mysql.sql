#
# Table structure for table `forum`
#

CREATE TABLE forum (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  type enum('discussion','news','general','social','eachuser') NOT NULL default 'general',
  name varchar(255) NOT NULL default '',
  intro tinytext NOT NULL,
  open tinyint(1) unsigned NOT NULL default '0',
  assessed tinyint(1) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM COMMENT='Discussion Forums';
# --------------------------------------------------------

#
# Table structure for table `forum_subscriptions`
#

CREATE TABLE forum_subscriptions (
  id int(10) unsigned NOT NULL auto_increment,
  user int(10) unsigned NOT NULL default '0',
  forum int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) TYPE=MyISAM COMMENT='keeps track of who is subscribed to what forum';


#
# Dumping data for table `log_display`
#

INSERT INTO log_display VALUES ('forum', 'view forum', 'forum', 'name');
INSERT INTO log_display VALUES ('forum', 'subscribe', 'forum', 'name');
INSERT INTO log_display VALUES ('forum', 'unsubscribe', 'forum', 'name');
