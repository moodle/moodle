Feedback-Module
===============
Overview
--------
The Feedback-Module is intended to create individual surveys in an easy way.

This module consists of two parts
a) the "activity" feedback (required)
b) the "sideblock" feedback (optional)

The activity is the main part an can run without the block. Here you can create, fill out or analyse the surveys.
The sideblock is an optional part. It works as a bridge between different courses and
an central placed feedback-activity. So you can create one feedback on the main site of moodle and then publish
it in many courses.

Requirements
------------
Moodle 1.9 or later

Installation
------------
   The zip-archive includes the same directory hierarchy as moodle
   So you only have to copy the files to the correspondent place.
   copy the folder feedback.zip/mod/feedback --> moodle/mod/feedback
   and the folder feedback.zip/blocks/feedback --> moodle/blocks/feedback
   The langfiles normaly can be left into the folder mod/feedback/lang.
   The only exception is the feedback-block. The langfile is block_feedback.php and
   have to be copied into the correspondent lang folder of moodle/moodledata.
   All languages should be encoded with utf8.

After it you have to run the admin-page of moodle (http://your-moodle-site/admin)
in your browser. You have to loged in as admin before.
The installation process will be displayed on the screen.
That's all.

using the block-feature
-----------------------
   1. create one or more new feedback-activitys on the moodle main-site
   2. go into some course and enable the feedback-block. This block now shows the feedbacks from the main-site.
   3. login as student and go into the course where the feedback-block is enabled
   4. fill out the feedback chosen from block
   5. login as admin and look at the feedback you created above
   6. now you can analyse the answers over the courses

good luck





CHANGELOG
=========

04.04.2008
- the heading has not supported multilang strings

03.04.2008
- anonymous feedback was send the userid on email-notification
  (http://tracker.moodle.org/browse/CONTRIB-355)
- users can not see the own values if there are chars like single-(') or doublequotas (")

08.03.2008
- admin users now respect capability-setting
  (http://tracker.moodle.org/browse/CONTRIB-321)

17.12.2007
- resolved issue http://tracker.moodle.org/browse/CONTRIB-231

03.12.2007
- changing the table feedback_template, field public -> ispublic, public is reserved in oracle
- resolved issue http://tracker.moodle.org/browse/CONTRIB-95

16.09.2007
- changing access.php, removed the lines with coursecreator, added some RISK_xxx
- removed the check of legacy roles on has_capability()
- coursesearch now use unicode characters

13.09.2007
- updated edit.php to make the output more xhtml 1.0 strict like

11.09.2007
- Update feedback settings has help showing wrongly
- Menu on "Add question to activity" should be sorted alphabetically
- missing "Add Pagebreak" is now at the item-list
- no hardcoded css
- use new print_header() (note it only is available on moodle 1.9!!!)
- missing sesskey checks in feedback
- uninitialised $filename when importing into feedback

31.07.2007
- fixed bug with missing numbers after xml Question-Import.

29.07.2007
- added "require_course_login()" in view.php
- added missing langstring "no_itemname"

25.07.2007
- now defined in applicable_formats() to hide the block in moodle My-Site

07.07.2007
- all functions in lib.php now are with comments in phpdoc-style
- removed all depricated function-calls
- some code-styling changes (http://docs.moodle.org/en/Coding)

06.07.2007
- Adding some missing lang-strings
- fixing some notice-messages with $SESSION-lstgroupid
- excelexport now uses the localwincharset from langconfig.php
    Now it is possible to export excel with utf8. But to many data will crash the excel
    file. If the excefile crashes so you switch to latin-export in lang-settings
- adding the default permission CAP_ALLOW to the legacy-role:user for the capabilities view and complete

24.06.2007
- fixed excel-problem with tempfiles
- added new field "idnumber" into excel detailed report

21.06.2007
- better support for xhtml

09.05.2007
- items now are classes
- most of forms use formslib
- gui now uses tabs
- new item "captcha"
Now it only runs on Moodle 1.8 or later

09.05.2007
- added two columns (random_response,anonymous_response) to feedback_complete and feedback_complete_tmp
- userids now will be logged even if the feedback is anonymous so you now can filter by group
- excelexport now use pear so cell-values can be greater then 255 chars
- logs now include the cm->id

16.01.2007
- the installation now uses the install.xml
- roles are full implemented
- now radiobuttons and checkboxes can be aligned horizontally or vertically
- now you can insert pagebreakes
- a feedback what is switch to the next page is saved temporary.
  the user can cancel the completion and later continue at the last filled page.
- now you can ex-/import feedbacks into/from a xml-file
- course-reseting is supported

01.01.2007
happy new year!

14.09.2006 21:22
improve the layout of analysis (thanks to Katja Krueger)

02.06.2006 21:22
several bugfixes
improve the block "feedback"

20.05.2006 01:00
adding the block "feedback" to publish feedback over all courses
Thanks to Jun Yamog!

21.04.2006 16:00
version 2006042102
adding moving behavior like moodle activities
adding a dropdownlist on create/update item page to adjust the position

21.04.2006 16:00
version 2006042101
prefixed all function-names like "feedback_"
fixed security issues (e.g. optional_variable() >> optional_param())
improve group-feature

03.01.2006 16:00
Added "addslashes" and "stripslashes_safe" for preserving (')

03.10.2005 13:00
action handling error recovery improved (failed when debug=false)
function feedback_action_handler() argument list changed
added action handler debug modes: silent, normal, verbose

30.09.2005 00:00
version 2005300900
action handling functions added
picture item (an example for action handling added)
XHTML compliance improved
number of PHP Notify-level errors reduced
source code transferred to CVS

14.09.2005 00:20
fixed problem with restoring
new feature user-tracking (prevent multiple_submit)
new feature deleting of some completeds

22.08.2005 19:12
fixed problem with secureforms-option

16.08.2005 14:07
fixed html-syntax in edit.php

12.08.2005 21:38
fixed problem with IE
If feedback is not anonym now guest is it not allowed to fill it out

11.08.2005 22:00
added email-notification
anonymous feedbacks can be filled out by anonymous users

03.08.2005 01:20
item specific functions were moved into the items-librarys
now developers can create individual feedback-items
javascript based filling-control was replaced by php-based control

version = 2005072000
20.07.2005 01:09
adding group-ability
fixed missing bcmod()-Function-Problem
fixed referer-problem under https
