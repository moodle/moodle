This is v2.1 of the HotPot module
   It has been confirmed to work on Moodle 1.1 thru 1.5, MySQL and PostGres7 databases and PHP 4.1 thru 5.0.4 (except PHP 5.0.4)

   This module may be distributed under the terms of the General Public License
   (see http://www.gnu.org/licenses/gpl.txt for details)

   This software is provided "AS IS" without a warranty of any kind.

===============
IMPORTANT NOTES
===============
* Please be sure to use Hot Potatoes according to the conditions of use which are listed at the end of this file. If you restrict use via a required Moodle login, you most likely can still meet the 'freely available' condition if you make the same material on a separate URL that permits free access.  Otherwise, please purchase a license.

* This is beta software, so please do not use it on live Moodle sites.

* If you decide to install it on your test Moodle site, please back up your entire Moodle database, and your current "moodle/mod/hotpot" folder, before attempting the install.


TO INSTALL OR UPDATE THIS MODULE

1. Login to your Moodle site as an administrator.

2. Run the install script

   (a) Start the install script: http://yourserver.org/moodle/mod/hotpot/install.php
   (b) Select which components you wish to install (probably all of them)
   (c) Click the [Install] button. 
   (d) Read the report on the screen and confirm all components were successfully installed

3. Access the Moodle configuration page: http://yourserver.org/moodle/admin/config.php

   * If you are installing this module for the first time, you should see a report saying that the following (prefix)_hotpot tables have been successfuly created:
      hotpot
      hotpot_attempts
      hotpot_questions
      hotpot_responses
      hotpot_strings

   * If you are updating the module, you will probably see messages indicating the progress of the update
   
   * If you get error messages, please copy and save them to a file on your PC, and then ask for help on Moodle HotPot supprot forum:
      go to http://moodle.org 
      then click "Free Support",
      then click "HotPot"


TO USE THIS MODULE:

1. Create a Hot Potatoes quiz of any type using Version 6 of Hot Potatoes.

2. Upload the quiz's source file (".jcl", ".jcw", ".jmt", ".jmx", ".jqz") or ".html" file to the file area of the intended course.

3. Also upload any graphics, sound files, stylesheets or javascripts that the quiz uses

4. On the main page for the course, confirm editing is turned on (click the "Turn editing on" button) then select "ADD / Hot Potatoes XML Quiz".

6, The "Adding a new Hot Potatoes Quiz" page appears. Click the "Choose or Upload a file ..." button and select the desired quiz file.

7. Review the other settings for the quiz and click "Save Changes" when you are ready

8. You can view reports of the results via the "Hot Potatoes Quizzes" link on the "Activities menu". Students will see links to all the quizzes. Administrators and teachers will additionally see links to the statistical reports for each quiz.

9. You can also import the questions from the Hot Potatoes source file to the Moodle Quiz database, if you installed "mod/quiz/format/hotpot/format.php"


==============================
HOT POTATOES CONDITIONS OF USE
==============================

**Reproduced from the Hot Potatoes site**

Hot Potatoes is offered free to the educational community by the University of Victoria Humanities Computing and Media Centre (formerly the Language Centre), under certain conditions. Hot Potatoes is free for use by state educational institutions which are non-profit making, on the condition that the material produced using the program is freely available to anyone via the WWW. However, you need to purchase a licence under any of the following conditions:
?   You do not work for a public sector educational establishment. 
?   You charge money for access to the material you make with Hot Potatoes. 
?   You restrict access to the material in some way. (The only exception here is if you have an account on www.hotpot.net, where you ARE allowed to use password restrictions.) 
?   You want to use the Masher program included with the Hot Potatoes suite.

For more information on licences, and details on how to purchase one, check out our Website at:
    http://www.halfbakedsoftware.com/hotpot/

If you intend using the programs to generate more than a handful of exercises, please make sure you register. This costs you nothing -- see How to register for details.
Martin Holmes, Half-Baked Software and the University of Victoria HCMC, 1998-2004.