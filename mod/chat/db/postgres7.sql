#
# Table structure for table `chat`
#

CREATE TABLE prefix_chat (
  id SERIAL,
  course INTEGER NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  intro text NOT NULL default '',
  keepdays INTEGER NOT NULL default '0',
  studentlogs INTEGER NOT NULL default '0',
  chattime INTEGER NOT NULL default '0',
  schedule INTEGER NOT NULL default '0',
  timemodified INTEGER NOT NULL default '0',
  PRIMARY KEY  (id)
);

CREATE INDEX prefix_chat_course_idx ON prefix_chat(course);

# --------------------------------------------------------

#
# Table structure for table `chat_messages`
#

CREATE TABLE prefix_chat_messages (
  id SERIAL,
  chatid integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  groupid integer NOT NULL default '0',
  system integer NOT NULL default '0',
  message text NOT NULL default '',
  timestamp integer NOT NULL default '0',
  PRIMARY KEY  (id)
);

CREATE INDEX prefix_chat_messages_chatid_idx ON prefix_chat_messages (chatid);
CREATE INDEX prefix_chat_messages_userid_idx ON prefix_chat_messages (userid);
CREATE INDEX prefix_chat_messages_groupid_idx ON prefix_chat_messages (groupid);
CREATE INDEX prefix_chat_messages_timemodifiedchatid_idx ON prefix_chat_messages(timestamp,chatid);

# --------------------------------------------------------

#
# Table structure for table `chat_users`
#

CREATE TABLE prefix_chat_users (
  id SERIAL,
  chatid integer NOT NULL default '0',
  userid integer NOT NULL default '0',
  groupid integer NOT NULL default '0',
  version varchar(16) NOT NULL default '',
  ip varchar(15) NOT NULL default '',
  firstping integer NOT NULL default '0',
  lastping integer NOT NULL default '0',
  lastmessageping integer NOT NULL default '0',
  sid varchar(32) NOT NULL default '',
  PRIMARY KEY  (id)
);

CREATE INDEX prefix_chat_users_chatid_idx ON prefix_chat_users (chatid);
CREATE INDEX prefix_chat_users_userid_idx ON prefix_chat_users (userid);
CREATE INDEX prefix_chat_users_groupid_idx ON prefix_chat_users (groupid);
CREATE INDEX prefix_chat_users_lastping_idx ON prefix_chat_users (lastping);

INSERT INTO prefix_log_display VALUES ('chat', 'view', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'add', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'update', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'report', 'chat', 'name');
INSERT INTO prefix_log_display VALUES ('chat', 'talk', 'chat', 'name');
