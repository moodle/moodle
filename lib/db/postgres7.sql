------------------------------------------------------------------
-- My2Pg 1.24 translated dump
--
------------------------------------------------------------------

BEGIN;




--
-- Sequences for table COURSE
--

CREATE SEQUENCE course_id_seq;

-- Database : `moodle`
-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE "course" (
  "id" INT4 DEFAULT nextval('course_id_seq'),
  "category" INT4  NOT NULL DEFAULT '0',
  "password" varchar(50) NOT NULL DEFAULT '',
  "fullname" varchar(254) NOT NULL DEFAULT '',
  "shortname" varchar(15) NOT NULL DEFAULT '',
  "summary" TEXT DEFAULT '' NOT NULL,
  "format" INT2 NOT NULL DEFAULT '1',
  "teacher" varchar(100) NOT NULL DEFAULT 'Teacher',
  "startdate" INT4  NOT NULL DEFAULT '0',
  "enddate" INT4  NOT NULL DEFAULT '0',
  "timemodified" INT4  NOT NULL DEFAULT '0',
  PRIMARY KEY  (id)
);
-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--



--
-- Sequences for table COURSE_CATEGORIES
--

CREATE SEQUENCE course_categories_id_seq;

CREATE TABLE "course_categories" (
  "id" INT4 DEFAULT nextval('course_categories_id_seq'),
  "name" varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--



--
-- Sequences for table COURSE_MODULES
--

CREATE SEQUENCE course_modules_id_seq;

CREATE TABLE "course_modules" (
  "id" INT4 DEFAULT nextval('course_modules_id_seq'),
  "course" INT4  NOT NULL DEFAULT '0',
  "module" INT4  NOT NULL DEFAULT '0',
  "instance" INT4  NOT NULL DEFAULT '0',
  "week" INT4  NOT NULL DEFAULT '0',
  "added" INT4  NOT NULL DEFAULT '0',
  "deleted" INT2  NOT NULL DEFAULT '0',
  "score" INT2 NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- "Table" structure for table `course_weeks`
--



--
-- Sequences for table COURSE_WEEKS
--

CREATE SEQUENCE course_weeks_id_seq;

CREATE TABLE "course_weeks" (
  "id" INT4 DEFAULT nextval('course_weeks_id_seq'),
  "course" INT4  NOT NULL DEFAULT '0',
  "week" INT4  NOT NULL DEFAULT '0',
  "summary" varchar(255) NOT NULL DEFAULT '',
  "sequence" varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (id)
);
-- --------------------------------------------------------

--
-- "Table" structure for table `logs`
--



--
-- Sequences for table LOGS
--

CREATE SEQUENCE logs_id_seq;

CREATE TABLE "logs" (
  "id" INT4 DEFAULT nextval('logs_id_seq'),
  "time" INT4  NOT NULL DEFAULT '0',
  "user" INT4  NOT NULL DEFAULT '0',
  "course" INT4  NOT NULL DEFAULT '0',
  "ip" varchar(15) NOT NULL DEFAULT '',
  "url" varchar(200) NOT NULL DEFAULT '',
  "message" varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (id)
);
-- --------------------------------------------------------

--
-- "Table" structure for table `modules`
--



--
-- Sequences for table MODULES
--

CREATE SEQUENCE modules_id_seq;

CREATE TABLE "modules" (
  "id" INT4 DEFAULT nextval('modules_id_seq'),
  "name" varchar(20) NOT NULL DEFAULT '',
  "fullname" varchar(255) NOT NULL DEFAULT '',
  "version" INT4 NOT NULL DEFAULT '0',
  "cron" INT4  NOT NULL DEFAULT '0',
  "lastcron" INT4  NOT NULL DEFAULT '0',
  "search" varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- "Table" structure for table `user`
--



--
-- Sequences for table USER
--

CREATE SEQUENCE user_id_seq;

CREATE TABLE "user" (
  "id" INT4 DEFAULT nextval('user_id_seq'),
  "confirmed" INT2 NOT NULL DEFAULT '0',
  "username" varchar(100) NOT NULL DEFAULT '',
  "password" varchar(32) NOT NULL DEFAULT '',
  "idnumber" varchar(12) DEFAULT NULL,
  "firstname" varchar(20) NOT NULL DEFAULT '',
  "lastname" varchar(20) NOT NULL DEFAULT '',
  "email" varchar(100) NOT NULL DEFAULT '',
  "icq" varchar(15) DEFAULT NULL,
  "phone1" varchar(20) DEFAULT NULL,
  "phone2" varchar(20) DEFAULT NULL,
  "institution" varchar(40) DEFAULT NULL,
  "department" varchar(30) DEFAULT NULL,
  "address" varchar(70) DEFAULT NULL,
  "city" varchar(20) DEFAULT NULL,
  "country" char(2) DEFAULT NULL,
  "firstaccess" INT4  NOT NULL DEFAULT '0',
  "lastaccess" INT4  NOT NULL DEFAULT '0',
  "lastlogin" INT4  NOT NULL DEFAULT '0',
  "currentlogin" INT4  NOT NULL DEFAULT '0',
  "lastIP" varchar(15) DEFAULT NULL,
  "personality" varchar(5) DEFAULT NULL,
  "picture" INT2 DEFAULT NULL,
  "url" varchar(255) DEFAULT NULL,
  "description" text,
  "research" INT2  NOT NULL DEFAULT '0',
  "forwardmail" INT2  NOT NULL DEFAULT '0',
  "timemodified" INT4  NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- "Table" structure for table `user_admins`
--



--
-- Sequences for table USER_ADMINS
--

CREATE SEQUENCE user_admins_id_seq;

CREATE TABLE "user_admins" (
  "id" INT4 DEFAULT nextval('user_admins_id_seq'),
  "user" INT4  NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- "Table" structure for table `user_students`
--



--
-- Sequences for table USER_STUDENTS
--

CREATE SEQUENCE user_students_id_seq;

CREATE TABLE "user_students" (
  "id" INT4 DEFAULT nextval('user_students_id_seq'),
  "user" INT4  NOT NULL DEFAULT '0',
  "course" INT4  NOT NULL DEFAULT '0',
  "start" INT4  NOT NULL DEFAULT '0',
  "end" INT4  NOT NULL DEFAULT '0',
  "time" INT4  NOT NULL DEFAULT '0',
  PRIMARY KEY  (id),
);
-- --------------------------------------------------------

--
-- "Table" structure for table `user_teachers`
--



--
-- Sequences for table USER_TEACHERS
--

CREATE SEQUENCE user_teachers_id_seq;

CREATE TABLE "user_teachers" (
  "id" INT4 DEFAULT nextval('user_teachers_id_seq'),
  "user" INT4  NOT NULL DEFAULT '0',
  "course" INT4  NOT NULL DEFAULT '0',
  "authority" varchar(10) DEFAULT NULL,
  PRIMARY KEY  (id),
);



--
-- Indexes for table USER_TEACHERS
--

CREATE UNIQUE INDEX id_user_teachers_index ON "user_teachers" ("id");

--
-- Indexes for table COURSE_CATEGORIES
--

CREATE UNIQUE INDEX id_course_categories_index ON "course_categories" ("id");

--
-- Indexes for table USER_STUDENTS
--

CREATE UNIQUE INDEX id_user_students_index ON "user_students" ("id");

--
-- Indexes for table MODULES
--

CREATE UNIQUE INDEX id_modules_index ON "modules" ("id");

--
-- Indexes for table USER
--

CREATE UNIQUE INDEX username_user_index ON "user" ("username");
CREATE UNIQUE INDEX id_user_index ON "user" ("id");

--
-- Indexes for table USER_ADMINS
--

CREATE UNIQUE INDEX id_user_admins_index ON "user_admins" ("id");

--
-- Indexes for table COURSE_MODULES
--

CREATE UNIQUE INDEX id_course_modules_index ON "course_modules" ("id");

--
-- Sequences for table USER_TEACHERS
--

SELECT SETVAL('user_teachers_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "user_teachers"));

--
-- Sequences for table USER_STUDENTS
--

SELECT SETVAL('user_students_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "user_students"));

--
-- Sequences for table MODULES
--

SELECT SETVAL('modules_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "modules"));

--
-- Sequences for table USER
--

SELECT SETVAL('user_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "user"));

--
-- Sequences for table COURSE
--

SELECT SETVAL('course_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "course"));

--
-- Sequences for table USER_ADMINS
--

SELECT SETVAL('user_admins_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "user_admins"));

--
-- Sequences for table COURSE_CATEGORIES
--

SELECT SETVAL('course_categories_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "course_categories"));

--
-- Sequences for table COURSE_WEEKS
--

SELECT SETVAL('course_weeks_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "course_weeks"));

--
-- Sequences for table LOGS
--

SELECT SETVAL('logs_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "logs"));

--
-- Sequences for table COURSE_MODULES
--

SELECT SETVAL('course_modules_id_seq',(select case when max("id")>0 then max("id")+1 else 1 end from "course_modules"));

COMMIT;
