===============================================
This is v2.1.24 of the HotPot module for Moodle
===============================================

   This module allows teachers to administer Hot Potatoes and TexToys quizzes via Moodle.
   It has been tested on:
      - Hot Potatoes 6
      - Moodle 1.6.2 (and latest 1.5.4+ with manual editing of version.php)
      - PHP 4.1 thru 5.1.2
      - MySQL 4.x thru 5.0
      - PostgreSQL 7.3 thru 8.2

   This module may be distributed under the terms of the General Public License
   (see http://www.gnu.org/licenses/gpl.txt for details)

   This software is provided "AS IS" without a warranty of any kind.

   Sponsors who have generously contributed to the development of this software:
      - Agencia de Gestio d'Ajuts Universitaris i de Recerca (AGAUR), 
        Autonomous Government of Catalonia, Spain
        (via Josep M. Fontana, Universitat Pompeu Fabra)
      - Rikkyo Univeristy, Japan (via Paul Allum)
      - Universite de Franche-Comte, France (via Glenys Hanson)


================
IMPORTANT NOTICE
================
*  Please be sure to use Hot Potatoes according to the conditions of use which are listed at the end of this file. If you restrict use via a required Moodle login, you most likely can still meet the 'freely available' condition if you make the same material on a separate URL that permits free access.  Otherwise, please purchase a license.


======================================
TO INSTALL OR UPDATE THE HOTPOT MODULE
======================================

1. Install the core scripts of the hotpot module

   (a) download http://download.moodle.org/download.php/modules/hotpot.zip
       to the "mod" folder on your Moodle site
   (b) remove the current "mod/hotpot" folder, if there is one (Unix: rm -fR hotpot)
   (c) unzip the zip file so it creates a new "mod/hotpot" folder

2. Install the messages and help files of the hotpot module

   On Moodle 1.5 and earlier:
   (a) download http://moodle.cvs.sourceforge.net/moodle/moodle/lang/en_utf8/hotpot.php
       to "lang/en/hotpot.php" on your Moodle site
   (b) download the files in http://moodle.cvs.sourceforge.net/moodle/moodle/lang/en_utf8/help/hotpot/
       into "lang/en/help/hotpot/" on your Moodle site

   On Moodle 1.6 and later:
   (a) locate the "lang" folder on your Moodle site. 
       Note: the default location for the "lang" folder is in the "$CFG->dataroot" directory defined in the config.php file for your Moodle site
   (b) download http://moodle.cvs.sourceforge.net/moodle/moodle/lang/en_utf8/hotpot.php
       to "lang/en_utf8/hotpot.php" on your Moodle site
   (c) download the files in http://moodle.cvs.sourceforge.net/moodle/moodle/lang/en_utf8/help/hotpot/
       into "lang/en_utf8/help/hotpot/" on your Moodle site

3. copy and set up the file icons
   [This step is not required for Moodle 1.5 and later, and is optional for Moodle 1.1 to 1.4]

   (a) copy the hotpot icons (jcb.gif, jcl.gif, jcw.gif, jmt.gif, jmx.gif, jqz.gif)
       from http://moodle.cvs.sourceforge.net/moodle/moodle/pix/f/
       to "pix/f" on your Moodle site           
   (b) open the "files/mimetypes.php" file on your Moodle site with a text editor
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

4. enable import from Hot Potatoes XML files to Moodle's Quiz module
   [This step is not required for Moodle 1.5 and later, and is optional for Moodle 1.1 to 1.4]

   (a) if necessary, create a folder at "mod/quiz/format/hotpot" on your Moodle site
   (b) download http://moodle.cvs.sourceforge.net/moodle/moodle/question/format/hotpot/format.php
       to "mod/hotpot/quiz/format/hotpot/format.php" on your Moodle site

   (c) open the "lang/en/quiz.php" file on your Moodle site with a text editor
   (d) insert the following line just BEFORE the final line which reads "?>":
        $string['hotpot'] = 'Hot Potatoes XML format';
   (e) save the changes and close the file
   
   Steps (f), (g) and (h) are only required on Moodle 1.4.5
   (f) open the "mod/quiz/import.php" file on your Moodle site with a text editor 
   (g) if necessary, add 'hotpot' to the "fileformats" array on lines 9-10:
        $fileformats = array('aiken','aon','blackboard','coursetestmanager',
          'gift','learnwise','missingword','webct','xml','hotpot' );
   (h) save the changes and close the file

5. Login to your Moodle site as an administrator

   a) If you are installing this module for the first time, you should see a report saying that the following (prefix)_hotpot tables have been successfuly created:
      hotpot
      hotpot_attempts
      hotpot_details
      hotpot_questions
      hotpot_responses
      hotpot_strings

   b) If you are updating the module, you will probably see messages indicating the progress of the update
   
   c) If you get error messages, please copy and save them to a file on your PC, and then ask for help on Moodle's HotPot support forum:
      go to http://moodle.org 
      then click "Free Support",
      then click "HotPot"


========================
TO USE THE HOTPOT MODULE
========================

1. If you haven't already done so, download and install the Hot Potatoes software from http://www.halfbakedsoftware.com onto your PC

2. Create any type of Hot Potatoes quiz on your PC using the Hot Potatoes software

3. Upload the quiz's source file (".jcl", ".jcw", ".jmt", ".jmx", ".jqz") or ".html" file from your PC to the "Files" area of a course on your Moodle site.

4. Also upload any graphics, sound files, stylesheets or javascripts that the quiz uses

5. On the main page for the Moodle course, confirm editing is turned on (click the "Turn editing on" button), then, in the section where you want to add the HotPot activity, select "Hot Potatoes Quiz" on the "Add an activity" drop-down menu .

   If "Hot Potatoes Quiz" does not appear on the "Add an activity" menu, you may need to enable the HotPot module as follows: 

   a) from the site's front page select "Admin" then "Modules"
   b) click the "closed eye" icon for the "Hot Potatoes Quiz" module

   If there is no "Hot Potatoes Quiz" on the "Modules" page, you probably have not put the hotpot.php messages file in the right place. Please refer to step 2, "Install the messages and help files", of the installation procedure (see previous section)

6. The "Adding a new Hot Potatoes Quiz" page appears. Click the "Choose or Upload a file ..." button and select the desired Hot Potaotes quiz file.

7. Review the other settings for the quiz and click "Save Changes" when you are ready

8. You can view reports of the results via the "Hot Potatoes Quizzes" link on the "Activities menu". Students will see links to all the quizzes. Administrators and teachers will additionally see links to the statistical reports for each quiz.

9. You can also import the questions from the Hot Potatoes source file to the Moodle Quiz database, if you completed step 4, "enable import from Hot Potatoes XML files", of the installation procedure (see previous section)


==============================
HOT POTATOES CONDITIONS OF USE
==============================
** Reproduced from the Hot Potatoes site **

Hot Potatoes is offered free to the educational community by the University of Victoria Humanities Computing and Media Centre (formerly the Language Centre), under certain conditions. Hot Potatoes is free for use by state educational institutions which are non-profit making, on the condition that the material produced using the program is freely available to anyone via the WWW. However, you need to purchase a licence under any of the following conditions:
*  You do not work for a public sector educational establishment. 
*  You charge money for access to the material you make with Hot Potatoes. 
*  You restrict access to the material in some way. (The only exception here is if you have an account on www.hotpot.net, where you ARE allowed to use password restrictions.) 
*  You want to use the Masher program included with the Hot Potatoes suite.

For more information on licences, and details on how to purchase one, check out our Website at:
   http://www.halfbakedsoftware.com/hotpot/

If you intend using the programs to generate more than a handful of exercises, please make sure you register. This costs you nothing -- see How to register for details.
Martin Holmes, Half-Baked Software and the University of Victoria HCMC, 1998-2004.