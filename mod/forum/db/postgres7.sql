#
# Table structure for table `forum`
#

CREATE TABLE prefix_forum (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  type varchar(10) CHECK (type IN ('single','news','general','social','eachuser','teacher')) NOT NULL default 'general',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  open integer NOT NULL default '2',
  assessed integer NOT NULL default '0',
  assesspublic integer NOT NULL default '0',
  assesstimestart integer NOT NULL default '0',
  assesstimefinish integer NOT NULL default '0',
  scale integer NOT NULL default '0',
  maxbytes integer NOT NULL default '0',
  forcesubscribe integer NOT NULL default '0',
  rsstype integer NOT NULL default '0',
  rssarticles integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table `forum_discussions`
#

CREATE TABLE prefix_forum_discussions (
  id SERIAL PRIMARY KEY,
  course integer NOT NULL default '0',
  forum integer NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  firstpost integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  groupid integer NOT NULL default '0',
  assessed integer NOT NULL default '1',
  timemodified integer NOT NULL default '0',
  usermodified integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table `forum_posts`
#

CREATE TABLE prefix_forum_posts (
  id SERIAL PRIMARY KEY,
  discussion integer NOT NULL default '0',
  parent integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  created integer NOT NULL default '0',
  modified integer NOT NULL default '0',
  mailed integer NOT NULL default '0',
  subject varchar(255) NOT NULL default '',
  message text NOT NULL default '',
  format integer NOT NULL default '0',
  attachment VARCHAR(100) NOT NULL default '',
  totalscore integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table `forum_ratings`
#

CREATE TABLE prefix_forum_ratings (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  post integer NOT NULL default '0',
  time integer NOT NULL default '0',
  rating integer NOT NULL default '0'
);
# --------------------------------------------------------

#
# Table structure for table `forum_subscriptions`
#

CREATE TABLE prefix_forum_subscriptions (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  forum integer NOT NULL default '0'
);
# --------------------------------------------------------

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

