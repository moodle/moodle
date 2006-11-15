
CREATE TABLE prefix_groups_courses_groups (
                            id SERIAL PRIMARY KEY,
                            courseid integer NOT NULL default '0',
                            groupid integer NOT NULL default '0'
                          );
CREATE INDEX prefix_groups_courses_groups_courseid_idx ON prefix_groups_courses_groups (courseid);

CREATE TABLE prefix_groups_groups (
                            id SERIAL PRIMARY KEY,
                            name varchar(255) NOT NULL default '',
                            description text NOT NULL default '',
                            enrolmentkey varchar(50) NOT NULL default '',
                            lang varchar(10) NOT NULL default 'en',
                            theme varchar(50) NOT NULL default '',
                            picture integer NOT NULL default '0',
                            hidepicture integer NOT NULL default '0',
                            timecreated integer NOT NULL default '0',
                            timemodified integer NOT NULL default '0'
                          );

CREATE TABLE prefix_groups_groups_users (
                            id SERIAL PRIMARY KEY,
                            groupid integer NOT NULL default '0',
                            userid integer NOT NULL default '0',
                            timeadded integer NOT NULL default '0'
                          );
CREATE INDEX prefix_groups_groups_users_groupid_idx ON prefix_groups_groups_users (groupid);
CREATE INDEX prefix_groups_groups_users_userid_idx ON prefix_groups_groups_users (userid);
COMMENT ON TABLE prefix_groups_groups_users IS 'New groupings (OU).';

CREATE TABLE prefix_groups_courses_groupings (
                            id SERIAL PRIMARY KEY,
                            courseid integer NOT NULL default '0',
                            groupingid integer NOT NULL
                          );
CREATE INDEX prefix_groups_courses_groupings_courseid_idx ON prefix_groups_courses_groupings (courseid);
COMMENT ON TABLE prefix_groups_courses_groupings IS 'New groupings (OU).';
                                      
CREATE TABLE prefix_groups_groupings (
                            id SERIAL PRIMARY KEY,
                            name varchar(254) NOT NULL default '',
                            description text NOT NULL,
                            timecreated integer NOT NULL default '0',
                            viewowngroup integer NOT NULL,
                            viewallgroupsmembers integer NOT NULL,
                            viewallgroupsactivities integer NOT NULL,
                            teachersgroupmark integer NOT NULL,
                            teachersgroupview integer NOT NULL,
                            teachersoverride integer NOT NULL
                          );

CREATE TABLE prefix_groups_groupings_groups (
                            id SERIAL PRIMARY KEY,
                            groupingid integer default '0',
                            groupid integer NOT NULL,
                            timeadded integer NOT NULL default '0'
                          );
CREATE INDEX prefix_groups_groupings_groups_groupingid_idx ON prefix_groups_groupings_groups (groupingid);
