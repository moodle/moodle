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
# Table structure for table dialogue
#

CREATE TABLE prefix_dialogue (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  deleteafter INT8  NOT NULL default '14',
  dialoguetype INT NOT NULL default '0',
  multipleconversations INT NOT NULL default '0',
  maildefault INT NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  name varchar(255) default NULL,
  intro text
) ;
# --------------------------------------------------------

#
# Table structure for table dialogue_conversations (Virtual Conversations)
#

CREATE TABLE prefix_dialogue_conversations (
  id SERIAL8 PRIMARY KEY,
  dialogueid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  recipientid INT8  NOT NULL default '0',
  lastid INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  closed INT NOT NULL default '0',
  seenon INT8  NOT NULL default '0',
  ctype INT NOT NULL default '0',
  format INT2 NOT NULL default '0',
  subject varchar(100) not null default ''
) ;
CREATE INDEX prefix_dialogue_conversations_timemodified_idx ON prefix_dialogue_conversations  (timemodified) ;
CREATE INDEX prefix_dialogue_conversations_dialogueid_idx ON prefix_dialogue_conversations  (dialogueid) ;

#
# Table structure for table dialogue_entries
#

CREATE TABLE prefix_dialogue_entries (
  id SERIAL8 PRIMARY KEY,
  dialogueid INT8  NOT NULL default '0',
  conversationid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  mailed INT2  NOT NULL default '0',
  text text NOT NULL default ''
) ;
CREATE INDEX prefix_dialogue_entries_conversationid_idx ON prefix_dialogue_entries  (conversationid) ;

#
# Data for the table log_display
#

INSERT INTO prefix_log_display VALUES ('dialogue', 'view', 'dialogue', 'name');
