-- Database : `moodle`
-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE course (
  id SERIAL,
  category integer unsigned NOT NULL default '0',
  password varchar(50) NOT NULL default '',
  fullname varchar(254) NOT NULL default '',
  shortname varchar(15) NOT NULL default '',
  summary text NOT NULL,
  format integer NOT NULL default '1',
  teacher varchar(100) NOT NULL default 'Teacher',
  startdate integer unsigned NOT NULL default '0',
  enddate integer unsigned NOT NULL default '0',
  timemodified integer unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);
-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE course_categories (
  id SERIAL,
  name varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
);
-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--

CREATE TABLE course_modules (
  id SERIAL,
  course integer unsigned NOT NULL default '0',
  module integer unsigned NOT NULL default '0',
  instance integer unsigned NOT NULL default '0',
  week integer unsigned NOT NULL default '0',
  added integer unsigned NOT NULL default '0',
  deleted integer unsigned NOT NULL default '0',
  score integer NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `course_weeks`
--

CREATE TABLE course_weeks (
  id SERIAL,
  course integer unsigned NOT NULL default '0',
  week integer unsigned NOT NULL default '0',
  summary varchar(255) NOT NULL default '',
  sequence varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE logs (
  id SERIAL,
  datetime integer unsigned NOT NULL default '0',
  user integer unsigned NOT NULL default '0',
  course integer unsigned NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  url varchar(200) NOT NULL default '',
  message varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE modules (
  id SERIAL,
  name varchar(20) NOT NULL default '',
  fullname varchar(255) NOT NULL default '',
  version integer NOT NULL default '0',
  cron integer unsigned NOT NULL default '0',
  lastcron integer unsigned NOT NULL default '0',
  search varchar(255) NOT NULL default '',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE user (
  id SERIAL,
  confirmed integer NOT NULL default '0',
  username varchar(100) NOT NULL default '',
  password varchar(32) NOT NULL default '',
  idnumber varchar(12) default NULL,
  firstname varchar(20) NOT NULL default '',
  lastname varchar(20) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  icq varchar(15) default NULL,
  phone1 varchar(20) default NULL,
  phone2 varchar(20) default NULL,
  institution varchar(40) default NULL,
  department varchar(30) default NULL,
  address varchar(70) default NULL,
  city varchar(20) default NULL,
  country varchar(2) default NULL,
  firstaccess integer unsigned NOT NULL default '0',
  lastaccess integer unsigned NOT NULL default '0',
  lastlogin integer unsigned NOT NULL default '0',
  currentlogin integer unsigned NOT NULL default '0',
  lastIP varchar(15) default NULL,
  personality varchar(5) default NULL,
  picture integer default NULL,
  url varchar(255) default NULL,
  description text,
  research integer unsigned NOT NULL default '0',
  forwardmail integer unsigned NOT NULL default '0',
  timemodified integer unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY username (username),
  UNIQUE KEY id (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `user_admins`
--

CREATE TABLE user_admins (
  id SERIAL,
  user integer unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `user_students`
--

CREATE TABLE user_students (
  id SERIAL,
  user integer unsigned NOT NULL default '0',
  course integer unsigned NOT NULL default '0',
  start integer unsigned NOT NULL default '0',
  end integer unsigned NOT NULL default '0',
  datetime integer unsigned NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) ;
-- --------------------------------------------------------

--
-- Table structure for table `user_teachers`
--

CREATE TABLE user_teachers (
  id SERIAL,
  user integer unsigned NOT NULL default '0',
  course integer unsigned NOT NULL default '0',
  authority varchar(10) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY id (id)
) ;


