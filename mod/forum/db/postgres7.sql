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
  trackingtype integer NOT NULL default '1',
  rsstype integer NOT NULL default '0',
  rssarticles integer NOT NULL default '0',
  timemodified integer NOT NULL default '0'
);

CREATE INDEX prefix_forum_course_idx ON prefix_forum (course);
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
  usermodified integer NOT NULL default '0',
  timestart integer NOT NULL default '0',
  timeend integer NOT NULL default '0'
);

CREATE INDEX prefix_forum_discussions_forum_idx ON prefix_forum_discussions (forum);
CREATE INDEX prefix_forum_discussions_userid_idx ON prefix_forum_discussions (userid);

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

CREATE INDEX prefix_forum_posts_discussion_idx ON prefix_forum_posts (discussion);
CREATE INDEX prefix_forum_posts_parent_idx ON prefix_forum_posts (parent);
CREATE INDEX prefix_forum_posts_userid_idx ON prefix_forum_posts (userid);
CREATE INDEX prefix_forum_posts_created_idx ON prefix_forum_posts (created);
CREATE INDEX prefix_forum_posts_mailed_idx ON prefix_forum_posts (mailed);

# --------------------------------------------------------

#
# Table structure for table `forum_queue`
#

CREATE TABLE prefix_forum_queue (
  id SERIAL PRIMARY KEY,
  userid integer default 0 NOT NULL,
  discussionid integer default 0 NOT NULL,
  postid integer default 0 NOT NULL
);

CREATE INDEX prefix_forum_queue_userid_idx ON prefix_forum_queue (userid);
CREATE INDEX prefix_forum_queue_discussion_idx ON prefix_forum_queue (discussionid);
CREATE INDEX prefix_forum_queue_postid_idx ON prefix_forum_queue (postid);

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

CREATE INDEX prefix_forum_ratings_userid_idx ON prefix_forum_ratings (userid);
CREATE INDEX prefix_forum_ratings_post_idx ON prefix_forum_ratings (post);

# --------------------------------------------------------

#
# Table structure for table `forum_subscriptions`
#

CREATE TABLE prefix_forum_subscriptions (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  forum integer NOT NULL default '0'
);

CREATE INDEX prefix_forum_subscriptions_userid_idx ON prefix_forum_subscriptions (userid);
CREATE INDEX prefix_forum_subscriptions_forum_idx ON prefix_forum_subscriptions (forum);

# --------------------------------------------------------


#
# Table structure for table `forum_read`
#

CREATE TABLE prefix_forum_read (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  forumid integer NOT NULL default '0',
  discussionid integer NOT NULL default '0',
  postid integer NOT NULL default '0',
  firstread integer NOT NULL default '0',
  lastread integer NOT NULL default '0'
);

CREATE INDEX prefix_forum_user_forum_idx ON prefix_forum_read (userid, forumid);
CREATE INDEX prefix_forum_user_discussion_idx ON prefix_forum_read (userid, discussionid);
CREATE INDEX prefix_forum_user_post_idx ON prefix_forum_read (userid, postid);


# --------------------------------------------------------


#
# Table structure for table `forum_track_prefs`
#

CREATE TABLE prefix_forum_track_prefs (
  id SERIAL PRIMARY KEY,
  userid integer NOT NULL default '0',
  forumid integer NOT NULL default '0'
);

CREATE INDEX prefix_forum_track_user_forum_idx ON prefix_forum_track_prefs (userid, forumid);


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

