moodle-tool_uploadcourse
========================

Is a Moodle admin/tools plugin for uploading course outlines
in much the same way that admin/tools/uploaduser works for users.
These plugins became available from Moodle 2.2x and onwards, as
this is when the admin/tools framework first appeared.

https://gitorious.org/moodle-tool_uploadcourse

There is also a bulk course category upload function available at https://gitorious.org/moodle-tool_uploadcoursecategory

If you need to manage course enrolments via bulk upload then you should look at 
the core user upload facility - http://docs.moodle.org/22/en/Upload_users

This takes CSV files as input and enables override or augmentation
with default parameter values.

All the usual add,updated,rename, and delete functions.


Thanks to Moshe Golden for help with getting the command line interface going.


!!!! NOTE !!!!
===============

This plugin used to come with the full directory structure
admin/tool/uploadcourse - this is nolonger the case so the
installation proceedure has changed!

General installation proceedures are here:
http://docs.moodle.org/20/en/Installing_contributed_modules_or_plugins

The basic process is:
Download https://gitorious.org/moodle-tool_uploadcourse/moodle-tool_uploadcourse/archive-tarball/master
unpack the file (probably called master) with tar -xzvf master
This will give you a directory called moodle-tool_uploadcourse-moodle-tool_uploadcourse
Move this directory and rename it into it's final position:
mv moodle-tool_uploadcourse-moodle-tool_uploadcourse <Moodle dirroot>/admin/tool/uploadcourse

Alternatively you can use git:
cd <Moodle dirroot>/admin/tool
git clone git@gitorious.org:moodle-tool_uploadcourse/moodle-tool_uploadcourse.git uploadcourse

Be careful about leaving the .git directory in your live environment.


CSV File format
===============

Possible column names are:
fullname, shortname, category, idnumber, summary,
format, showgrades, newsitems, teacher, editingteacher, student, modinfo,
manager, coursecreator, guest, user, startdate, numsections, maxbytes, visible, groupmode, restrictmodules,
enablecompletion, completionstartonenrol, completionnotify, hiddensections, groupmodeforce, lang, theme,
cost, showreports, notifystudents, expirynotify, expirythreshold, requested,
deleted,     // 1 means delete course
oldshortname, // for renaming
backupfile, // for restoring a course template after creation
templatename, // course to use as a template - the shortname
reset, // reset the course contents after upload - this resets everything
          - so you loose groups, roles, logs, grades etc. Be Careful!!!

An example file is:

fullname,shortname,category,idnumber,summary,backupfile
Computer Science 101,CS101,Cat1,CS101,The first thing you will ever know,/path/to/backup-moodle2-course-cs101-20120213-0748-nu.mbz

As a general rule, the input values for fields are what you find on the data entry form if you inspect the HTML element.

Format
======
The options for the format value are 'scorm', 'social', weeks', and 'topics'.

Role Names
===========
 'teacher', 'editingteacher', 'student', 'manager',
'coursecreator', 'guest', 'user' are - where config permitting - you can
substitute your own name for these roles (string value).

Category
========
For category you must supply the category name as it is in Moodle and this
field is case sensitive.  If Sub Categories are involved then the full
category hierarchy needs to be specified as a '/' delimited string eg:
'Miscellaneous / Sub Cat / Sub Sub Cat'.  The delimiter can be escaped with
a back slash eg:  'some\/category'.

Course Templating
=================
add column backupfile which has the fully qualified path name to a file on
the server that has a a Moodle course backup in it. 

Add a column templatename which is the shortname of an existing course that 
will be copied over the top of the new course.

Course Enrolment Methods
=========================

Enrolment methods need special CSV columns as there can be many per course, and the fields for each 
method are flexible.  The following is an example with two enrolment methods - manual, and self - firstly you need 
the column identifying the enrolment method enrolmethod_<n>, and then add the corresponding field values subscripted with _<n>.
eg:
fullname,shortname,category,idnumber,summary,enrolmethod_1,status_1,enrolmethod_2,name_2,password_2,customtext1_2
Parent,Parent,,Parent,Parent,manual,1,self,self1,letmein,this is a custom message 1
Students,Students,,Students,Students,manual,0,self,self2,letmein,this is a custom message 2
Teachers,Teachers,,Teachers,Teachers,manual,0,self,self3,letmein,this is a custom message 3

add the special columns for:
 * delete - delete_<n> with value 1
 * disable - disable_<n> with value 1

startdate enrol_startdate enrol_enddate
=======================================
For startdate enrolstartdate, and enrolenddate the values should be supplied in the form like 31.01.2012 or
31/01/2012 that can be consumed by strtotime() (http://php.net/manual/en/function.strtotime.php) - check
your PHP locale settings for the fine tuning eg: m/d/y vs d/m/y.

Enrolment method field 'enrolperiod' must be in seconds.  If this is supplied then enrolenddate will be calculated
as enrolstartdate + enrolperiod.

enrolperiod should be supplied in multiples of enrolment period measurements - 1 hour = 3600, 1 day = 86400
and so on. OR - you can pass a text string that php strtotime() can recognise eg: '2 weeks' or '10 days'

Enrolment Method Role
=====================
Default Role for an enrolment method is supplied by adding the 'role_<n>' column.  The expected value is the
descriptive label for the given role eg: 'Student', or "Teacher'.

Enrolment example:
fullname,shortname,category,idnumber,summary,enrolmethod_1,enrolperiod_1,role_1
a name,short1,Miscellaneous,id1,a summary,manual,864000,Manager

Update Course:
=================
Make sure you have shortname in the csv. After uploading the file, select:
Upload type: one of the update existing related options
Existing course details: Overide with file 
Allow Renames: Yes

Update example:
fullname,shortname
new full name,short1


Run it in batch mode
=====================
Execute Course Upload in batch mode - this must be run as the www-data user (or the equivalent user that the web server runs under).

Options:
-v, --verbose              Print verbose progress information
-h, --help                 Print out this help
-a, --action               Action to perform - addnew, addupdate, update, forceadd
-m, --mode                 Mode of execution - delete, rename, nochange, file, filedefaults, missing
-f, --file                 CSV File
-d, --delimiter            delimiter - colon,semicolon,tab,cfg,comma
-e, --encoding             File encoding - utf8 etc
-c, --category             Course category
-s, --templateshortname    Template course by shortname
-t, --template             Template course by backup file
-g, --format               Course format - weeks,scorm,social,topics
-n, --numsections          Number of sections


Example:
sudo -u www-data /usr/bin/php admin/tool/uploadcourse/cli/uploadcourse.php --action=addupdate \
                 --mode=delete --file=./courses.csv --delimiter=comma



Installation
=================
git clone this repository into <moodle root>/admin/tools/uploadcourse directory.

Point your browser at Moodle, and login as admin.  This should kick off
the upgrade so that Moodle can now recognise the new plugin.

This was inspired in part by a need for a complimentary function for uploading
courses (as for users) for the the NZ MLE tools for Identity and 
Access Managment (synchronising users with the School SMS):
https://gitorious.org/pla-udi
and
https://gitorious.org/pla-udi/mle_ide_tools

Copyright (C) Piers Harding 2011 and beyond, All rights reserved

moodle-tool_uploadcourse free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
