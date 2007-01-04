

CREATE TABLE prefix_groups_courses_groupings (
    id SERIAL PRIMARY KEY,
    courseid integer NOT NULL default 0,
    groupingid integer NOT NULL default 0
);
CREATE INDEX prefix_groups_courses_groupings_courseid_idx ON prefix_groups_courses_groupings (courseid);
COMMENT ON TABLE prefix_groups_courses_groupings IS 'New groupings (OU).';


CREATE TABLE prefix_groups_courses_groups (
    id SERIAL PRIMARY KEY,
    courseid integer NOT NULL default '0',
    groupid integer NOT NULL default '0'
);
CREATE INDEX prefix_groups_courses_groups_courseid_idx ON prefix_groups_courses_groups (courseid);


CREATE TABLE prefix_groups_groupings (
    id SERIAL PRIMARY KEY,
    name varchar(254) NOT NULL,
    description text NOT NULL default '',
    timecreated integer NOT NULL default 0,
    viewowngroup integer NOT NULL default 1,
    viewallgroupsmembers integer NOT NULL default 0,
    viewallgroupsactivities integer NOT NULL default 0,
    teachersgroupmark integer NOT NULL default 0,
    teachersgroupview integer NOT NULL default 0,
    teachersoverride integer NOT NULL default 0
);


CREATE TABLE prefix_groups_groupings_groups (
    id SERIAL PRIMARY KEY,
    groupingid integer NOT NULL default 0,
    groupid integer NOT NULL default 0,
    timeadded integer NOT NULL default 0
);
CREATE INDEX prefix_groups_groupings_groups_groupingid_idx ON prefix_groups_groupings_groups (groupingid);


CREATE TABLE prefix_groups (
    id SERIAL PRIMARY KEY,
    name varchar(255) NOT NULL,
    description text NOT NULL default '',
    enrolmentkey varchar(50) NOT NULL default '',
    lang varchar(10) NOT NULL default 'en',
    theme varchar(50) NOT NULL default '',
    picture integer NOT NULL default '0',
    hidepicture integer NOT NULL default '0',
    timecreated integer NOT NULL default '0',
    timemodified integer NOT NULL default '0'
);


CREATE TABLE prefix_groups_members (
    id SERIAL PRIMARY KEY,
    groupid integer NOT NULL default '0',
    userid integer NOT NULL default '0',
    timeadded integer NOT NULL default '0'
);
CREATE INDEX prefix_groups_members_groupid_idx ON prefix_groups_members (groupid);
CREATE INDEX prefix_groups_members_userid_idx ON prefix_groups_members (userid);
COMMENT ON TABLE prefix_groups_members IS 'New groupings (OU).';
