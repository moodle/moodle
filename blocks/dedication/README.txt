Introduction
------------

This block allows to see the dedication estimated time to a Moodle course by the participants of the course.

How dedication time is estimated?
---------------------------------

Time is estimated based in the concepts of Session and Session duration applied
to Moodle's log entries:

  Click:
  every time that a user access to a page in Moodle a log entry is stored.

  Session:
  set of two or more consecutive clicks in which the elapsed time between every
  pair of consecutive clicks does not overcome an established maximum time.

  Session duration:
  elapsed time between the first and the last click of the session.

Features
--------

This block is intended to be used only by teachers, so students aren't going to
see it and their dedication time. However, block can be configured to show
dedication time to students too.

Teachers can use a tool to analyze dedication time within a course. The tool
provides three views:

  Dedication time of the course:
  calculates total dedication time, mean dedication time and connections per day
  for each student.

  Dedication time of a group:
  the same but only for choosed group members.

  Dedication of a student:
  detalied sessions for a student with start date & time, duration and ip.

The tools provide an option to download all data in spreadsheet format. The use
of this tool is restricted by a capability to teachers and admins only.

This block cannot be used in the site page, only in courses pages.

All texts in English and Spanish.

Support
-------
Support is offered in English and Spanish in these forum discussions:

  English discussion: http://moodle.org/mod/forum/discuss.php?d=10948
  Spanish discussion: http://moodle.org/mod/forum/discuss.php?d=109489

Code repository: https://bitbucket.org/ciceidev/moodle_block_dedication
Issues: https://bitbucket.org/ciceidev/moodle_block_dedication/issues

Credits
-------

This block was developed by CICEI at Las Palmas de Gran Canaria University.
First version for Moodle 1.9 by Borja Rubio Reyes. Updated and improved version
for Moodle 2 by Aday Talavera Hierro.
