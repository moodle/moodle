#
# Table structure for table `forum`
#

CREATE TABLE prefix_forum (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  type enum('single','news','general','social','eachuser','teacher') NOT NULL default 'general',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL,
  open tinyint(2) unsigned NOT NULL default '2',
  assessed int(10) unsigned NOT NULL default '0',
  assesspublic int(4) unsigned NOT NULL default '0',
  assesstimestart int(10) unsigned NOT NULL default '0',
  assesstimefinish int(10) unsigned NOT NULL default '0',
  scale int(10) NOT NULL default '0',
  maxbytes int(10) unsigned NOT NULL default '0',
  forcesubscribe tinyint(1) unsigned NOT NULL default '0',
  trackingtype tinyint(2) unsigned NOT NULL default '1',
  rsstype tinyint(2) unsigned NOT NULL default '0',
  rssarticles tinyint(2) unsigned NOT NULL default '0',
  timemodified int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY course (course)
) COMMENT='Forums contain and structure discussion';
# --------------------------------------------------------

#
# Table structure for table `forum_discussions`
#

CREATE TABLE prefix_forum_discussions (
  id int(10) unsigned NOT NULL auto_increment,
  course int(10) unsigned NOT NULL default '0',
  forum int(10) unsigned NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  firstpost int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  groupid int(10) NOT NULL default '-1',
  assessed tinyint(1) NOT NULL default '1',
  timemodified int(10) unsigned NOT NULL default '0',
  usermodified int(10) unsigned NOT NULL default '0',
  timestart int(10) unsigned NOT NULL default '0',
  timeend int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY prefix_forum_discussions_forum_idx (forum),
  KEY prefix_forum_discussions_userid_idx (userid)
) COMMENT='Forums are composed of discussions';
# --------------------------------------------------------

#
# Table structure for table `forum_posts`
#

CREATE TABLE prefix_forum_posts (
  id int(10) unsigned NOT NULL auto_increment,
  discussion int(10) unsigned NOT NULL default '0',
  parent int(10) unsigned NOT NULL default '0',
  userid int(10) unsigned NOT NULL default '0',
  created int(10) unsigned NOT NULL default '0',
  modified int(10) unsigned NOT NULL default '0',
  mailed tinyint(2) unsigned NOT NULL default '0',
  subject varchar(255) NOT NULL default '',
  message text NOT NULL,
  format tinyint(2) NOT NULL default '0',
  attachment VARCHAR(100) NOT NULL default '',
  totalscore tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY prefix_forum_posts_parent_idx (parent),
  KEY prefix_forum_posts_discussion_idx (discussion),
  KEY prefix_forum_posts_userid_idx (userid),
  KEY prefix_forum_posts_created_idx (created),
  KEY prefix_forum_posts_mailed_idx (mailed)
) COMMENT='All posts are stored in this table';
# --------------------------------------------------------

#
# Table structure for table `forum_queue`
#

CREATE TABLE prefix_forum_queue (
  id int(10) unsigned NOT NULL auto_increment, 
  userid int(10) unsigned default 0 NOT NULL,
  discussionid int(10) unsigned default 0 NOT NULL,
  postid int(10) unsigned default 0 NOT NULL,
  PRIMARY KEY  (id),
  KEY user (userid),
  KEY post (postid)
) COMMENT='For keeping track of posts that will be mailed in digest form';
# --------------------------------------------------------

#
# Table structure for table `forum_ratings`
#

CREATE TABLE prefix_forum_ratings (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  post int(10) unsigned NOT NULL default '0',
  time int(10) unsigned NOT NULL default '0',
  rating tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id),
  KEY userid (userid),
  KEY post (post)
) COMMENT='Contains user ratings for individual posts';
# --------------------------------------------------------

#
# Table structure for table `forum_subscriptions`
#

CREATE TABLE prefix_forum_subscriptions (
  id int(10) unsigned NOT NULL auto_increment,
  userid int(10) unsigned NOT NULL default '0',
  forum int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id),
  KEY userid (userid),
  KEY forum (forum)
) COMMENT='Keeps track of who is subscribed to what forum';
# --------------------------------------------------------

#
# Table structure for table `forum_read`
#

CREATE TABLE prefix_forum_read (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) NOT NULL default '0',
  `forumid` int(10) NOT NULL default '0',
  `discussionid` int(10) NOT NULL default '0',
  `postid` int(10) NOT NULL default '0',
  `firstread` int(10) NOT NULL default '0',
  `lastread` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `prefix_forum_user_forum_idx` (`userid`,`forumid`),
  KEY `prefix_forum_user_discussion_idx` (`userid`,`discussionid`),
  KEY `prefix_forum_user_post_idx` (`userid`,`postid`)
) COMMENT='Tracks each users read posts';

#
# Table structure for table `forum_track_prefs`
#
CREATE TABLE prefix_forum_track_prefs (
  `id` int(10) unsigned NOT NULL auto_increment, 
  `userid` int(10) NOT NULL default '0',
  `forumid` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_forum_idx` (`userid`,`forumid`)
) COMMENT='Tracks each users untracked forums.';

#
# Dumping data for table `log_display`
#

INSERT INTO prefix_log_display VALUES ('forum', 'add', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'update', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'add discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'add post', 'forum_posts', 'subject');
INSERT INTO prefix_log_display VALUES ('forum', 'update post', 'forum_posts', 'subject');
INSERT INTO prefix_log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view subscribers', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view discussion', 'forum_discussions', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'view forum', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'subscribe', 'forum', 'name');
INSERT INTO prefix_log_display VALUES ('forum', 'unsubscribe', 'forum', 'name');
