CREATE TABLE prefix_lesson (
  id SERIAL8 PRIMARY KEY,
  course INT8  NOT NULL default '0',
  name varchar(255) NOT NULL default '',
  practice INT  NOT NULL DEFAULT '0',
  modattempts INT4 NOT NULL DEFAULT '0',
  usepassword INT  NOT NULL DEFAULT '0',
  password VARCHAR(32) NOT NULL default '',
  dependency INT8 NOT NULL DEFAULT '0',
  conditions text NOT NULL DEFAULT '',
  grade INT NOT NULL default '0',
  custom INT  NOT NULL DEFAULT '0',
  ongoing INT  NOT NULL DEFAULT '0',
  usemaxgrade INT NOT NULL default '0',
  maxanswers INT  NOT NULL default '4',
  maxattempts INT  NOT NULL default '5',
  review INT  NOT NULL DEFAULT '0',
  nextpagedefault INT  NOT NULL default '0',
  feedback INT  NOT NULL default '1',
  minquestions INT  NOT NULL default '0',
  maxpages INT  NOT NULL default '0',
  timed INT  NOT NULL DEFAULT '0',
  maxtime INT8  NOT NULL DEFAULT '0',
  retake INT  NOT NULL default '1',
  activitylink INT8  NOT NULL default '0',
  mediafile varchar(255) NOT NULL default '',
  mediaheight INT  NOT NULL DEFAULT '100',
  mediawidth INT  NOT NULL DEFAULT '650',
  mediaclose INT  NOT NULL DEFAULT '0',
  slideshow INT  NOT NULL DEFAULT '0',
  width INT8  NOT NULL DEFAULT '640',
  height INT8  NOT NULL DEFAULT '480',
  bgcolor VARCHAR(7) NOT NULL DEFAULT '#FFFFFF',
  displayleft INT  NOT NULL DEFAULT '0',
  displayleftif INT  NOT NULL DEFAULT '0',
  progressbar INT  NOT NULL DEFAULT '0',
  highscores INT  NOT NULL DEFAULT '0',
  maxhighscores INT8  NOT NULL DEFAULT '0',
  available INT8  NOT NULL default '0',
  deadline INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0'
);

CREATE INDEX prefix_lesson_course_idx ON prefix_lesson (course);

CREATE TABLE prefix_lesson_pages (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  prevpageid INT8  NOT NULL default '0',
  nextpageid INT8  NOT NULL default '0',
  qtype INT  NOT NULL default '0',
  qoption INT  NOT NULL default '0',
  layout INT  NOT NULL DEFAULT '1',
  display INT  NOT NULL DEFAULT '1',
  timecreated INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  contents text NOT NULL default ''
) ;

CREATE INDEX prefix_lesson_pages_lessonid_idx ON prefix_lesson_pages (lessonid);

CREATE TABLE prefix_lesson_answers (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  pageid INT8  NOT NULL default '0',
  jumpto int8 NOT NULL default '0',
  grade INT  NOT NULL default '0',
  score INT8 NOT NULL DEFAULT '0',
  flags INT  NOT NULL default '0',
  timecreated INT8  NOT NULL default '0',
  timemodified INT8  NOT NULL default '0',
  answer text NOT NULL default '',
  response text NOT NULL default ''
) ;

CREATE INDEX prefix_lesson_answers_pageid_idx ON prefix_lesson_answers (pageid);
CREATE INDEX prefix_lesson_answers_lessonid_idx ON prefix_lesson_answers (lessonid);

CREATE TABLE prefix_lesson_attempts (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  pageid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  answerid INT8  NOT NULL default '0',
  retry INT  NOT NULL default '0',
  correct INT8  NOT NULL default '0',
  useranswer text NOT NULL default '',
  timeseen INT8  NOT NULL default '0'
) ;
CREATE INDEX prefix_lesson_attempts_lessonid_idx ON prefix_lesson_attempts (lessonid);
CREATE INDEX prefix_lesson_attempts_pageid_idx ON prefix_lesson_attempts (pageid);
CREATE INDEX prefix_lesson_attempts_userid_idx ON prefix_lesson_attempts (userid);

CREATE TABLE prefix_lesson_grades (
  id SERIAL8 PRIMARY KEY,
  lessonid INT8  NOT NULL default '0',
  userid INT8  NOT NULL default '0',
  grade real  NOT NULL default '0',
  late INT  NOT NULL default '0',
  completed INT8  NOT NULL default '0'
) ;

CREATE INDEX prefix_lesson_grades_lessonid_idx ON prefix_lesson_grades (lessonid);
CREATE INDEX prefix_lesson_grades_userid_idx ON prefix_lesson_grades (userid);

CREATE TABLE prefix_lesson_default 
        ( id SERIAL8 PRIMARY KEY,
          course INT8  NOT NULL default '0',
          practice INT  NOT NULL default '0',
          modattempts INT4 NOT NULL default '0',
          usepassword INT  NOT NULL default '0',
          password varchar(32) NOT NULL default '',
          conditions text NOT NULL DEFAULT '',
          grade INT NOT NULL default '0',
          custom INT  NOT NULL default '0',
          ongoing INT  NOT NULL default '0',
          usemaxgrade INT  NOT NULL default '0',
          maxanswers INT  NOT NULL default '4',
          maxattempts INT  NOT NULL default '5',
          review INT  NOT NULL default '0',
          nextpagedefault INT  NOT NULL default '0',
          feedback INT  NOT NULL default '1',
          minquestions INT  NOT NULL default '0',
          maxpages INT  NOT NULL default '0',
          timed INT  NOT NULL default '0',
          maxtime INT8  NOT NULL default '0',
          retake INT  NOT NULL default '1',
          mediaheight INT  NOT NULL DEFAULT '100',
          mediawidth INT  NOT NULL DEFAULT '650',
          mediaclose INT  NOT NULL DEFAULT '0',
          slideshow INT  NOT NULL default '0',
          width INT8  NOT NULL default '640',
          height INT8  NOT NULL default '480',
          bgcolor varchar(7) default '#FFFFFF',
          displayleft INT  NOT NULL default '0',
          displayleftif INT  NOT NULL DEFAULT '0',
          progressbar INT  NOT NULL DEFAULT '0',
          highscores INT  NOT NULL default '0',
          maxhighscores INT8 NOT NULL default '0'
        ) ;

CREATE TABLE prefix_lesson_timer
    ( id SERIAL8 PRIMARY KEY,
        lessonid INT8  not null default '0',
      userid INT8  not null default '0',
      starttime INT8  not null default '0',
        lessontime INT8  not null default '0'
    );

CREATE TABLE prefix_lesson_branch
    ( id SERIAL8 PRIMARY KEY,
      lessonid INT8  not null default '0',
      userid INT8  not null default '0',
      pageid INT8  not null default '0',
      retry INT8  not null default '0',
      flag  INT  not null default '0',
      timeseen INT8  not null default '0'
    );

CREATE TABLE prefix_lesson_high_scores
    ( id SERIAL8 PRIMARY KEY,
      lessonid INT8  not null default '0',
      userid INT8  not null default '0',
      gradeid INT8  not null default '0',
      nickname varchar(5) not null default ''
    );


INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'start', 'lesson', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'end', 'lesson', 'name');
INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('lesson', 'view', 'lesson_pages', 'title');
