Moodle: Overview statistics report
==================================

This [Moodle](http://moodle.org) add-on produces various site and course report
charts.  The code has been designed in a way that makes adding more reports
easy.

For producing the graphs, [YUI Charts](http://yuilibrary.com/yui/docs/charts/)
module is used.  The code is using modern Moodle development techniques and
patterns, such as using LESS for producing CSS files and Shifter to compose the
Javascript modules.

Available site level reports
----------------------------

__Users logging in - per day__ chart displays the number of unique registered
users (not visits) who accessed the site per day during the last month.

__User countries__ chart displays the countries the users are coming from,
based on their user profile field.

__User preferred languages__ chart displays the UI languages the users have
selected as preferred ones in their profiles.

__Number of courses per category__ reports the number of courses in each course
category, both recursively (including subcategories) and own courses only.

__Number of courses per size__ displays the distribution graph of number of
activities per course. That is, how many courses are there with 0-4 activities,
5-9 activities, 10-14 activities etc.

Available course level reports
------------------------------

__Enrolled users__ chart displays the progress of user enrolments into the
course based on estimated historical figures.

Author
------

The add-on has been written and is currently maintained by David Mudrák
<david@moodle.com>, [@mudrd8mz](http://twitter.com/mudrd8mz).
