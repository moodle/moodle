This is v2.0.9 of the HotPot module
   It has been tested on Moodle 1.1 thru 1.5, MySQL and PostGres7 databases and PHP 4.1 thru 5.0.4

   This module may be distributed under the terms of the General Public License
   (see http://www.gnu.org/licenses/gpl.txt for details)

   This software is provided "AS IS" without a warranty of any kind.

================
IMPORTANT NOTICE
================
* Please be sure to use Hot Potatoes according to the conditions of use which are listed at the end of this file. If you restrict use via a required Moodle login, you most likely can still meet the 'freely available' condition if you make the same material on a separate URL that permits free access.  Otherwise, please purchase a license.


TO INSTALL OR UPDATE THIS MODULE

1. Install the core scripts of the hotpot module

   EITHER:
   (a) download the zip file into the "moodle/mod" folder on your Moodle site
   (b) unzip the zip file. This will create a folder called "moodle/mod/hotpot" on your Moodle site

   OR:
   (a) download the zip file onto your PC
   (b) upzip the zip file on your PC. This will create a "hotpot" folder on your PC
   (c) create a folder called "hotpot" on your Moodle site in the "moodle/mod" folder
   (d) upload the contents of the "hotpot" folder on your PC to the "moodle/mod/hotpot" folder on the Moodle site

2. Install the messages and help files of the hotpot module

   EITHER:
   (a) copy "moodle/mod/hotpot/lang/en/hotpot.php" on the Moodle site to "moodle/lang/en/hotpot.php"
   (b) copy "moodle/mod/hotpot/lang/en/help/hotpot" on the Moodle site to "moodle/lang/en/help/hotpot"
   (c) repeat for other languages you require from the "moodle/mod/hotpot/lang" folder

   OR:
   (a) upload the "hotpot/lang/en/hotpot.php" file on your PC to "moodle/lang/en/hotpot.php" on the Moodle site
   (b) create a folder called "hotpot" on your Moodle site in the "moodle/lang/en/help" folder
   (c) upload the contents of the "hotpot/lang/en/help/hotpot" folder on your PC to the "moodle/lang/en/help/hotpot" folder on the Moodle site
   (d) repeat for other languages you require from the "moodle/mod/hotpot/lang" folder 

3. [optional] copy and set up the file icons as follows:

   (a) copy the files in the "mod/hotpot/pix/f" folder on your Moodle site to "moodle/pix/f"
   (b) open the "moodle/files/mimetypes.php" file on your Moodle site with a text editor
   (c) insert the following lines just AFTER the line which reads "$mimeinfo = array (":

       // additional mimetypes for the "hotpot" module
       "jcb"  => array ("type"=>"text/xml", "icon"=>"jcb.gif"),
       "jcl"  => array ("type"=>"text/xml", "icon"=>"jcl.gif"),
       "jcw"  => array ("type"=>"text/xml", "icon"=>"jcw.gif"),
       "jmt"  => array ("type"=>"text/xml", "icon"=>"jmt.gif"),
       "jmx"  => array ("type"=>"text/xml", "icon"=>"jmx.gif"),
       "jqz"  => array ("type"=>"text/xml", "icon"=>"jqz.gif"),
       "rhb"  => array ("type"=>"text/xml", "icon"=>"xml.gif"),
       "sqt"  => array ("type"=>"text/xml", "icon"=>"xml.gif"),

   (d) save the changes and close the file

3. [optional] enable import from Hot Potatoes XML files to Moodle's Quiz module

   (a) create a folder called "hotpot" on your Moodle site in the "moodle/mod/quiz/format" folder
   (b) copy the "contents of the "moodle/mod/hotpot/quiz/format/hotpot" folder on your Moodle site to the "moodle/mod/quiz/format/hotpot" folder
   (c) open the "moodle/lang/en/quiz.php" file on your Moodle site with a text editor
   (d) insert the following line just BEFORE the final line which reads "?>":

        $string['hotpot'] = 'Hot Potatoes XML format';

   (d) save the changes and close the file

4. Login to your Moodle site as an administrator. 

   a) If you are installing this module for the first time, you should see a report saying that the following (prefix)_hotpot tables have been successfuly created:
      hotpot
      hotpot_attempts
      hotpot_questions
      hotpot_responses
      hotpot_strings

   b) If you are updating the module, you will probably see messages indicating the progress of the update
   
   c) If you get error messages, please copy and save them to a file on your PC, and then ask for help on Moodle's HotPot support forum:
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