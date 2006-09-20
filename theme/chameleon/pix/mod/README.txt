ACTIVITY MODULES
----------------

These are main modules in Moodle, allowing various activities.


Each of these modules contains a number of expected components:

  mod.html: a form to setup/update a module instance

  version.php: defines some meta-info and provides upgrading code

  icon.gif: a 16x16 icon for the module

  db/mysql.sql: an SQL dump of all the required db tables and data
 
  index.php: a page to list all instances in a course

  view.php: a page to view a particular instance

  lib.php: any/all functions defined by the module should be in here.
         constants should be defined using MODULENAME_xxxxxx
         functions should be defined using modulename_xxxxxx

         There are a number of standard functions:

         modulename_add_instance()
         modulename_update_instance()
         modulename_delete_instance()

         modulename_user_complete()
         modulename_user_outline()

         modulename_cron()

         modulename_print_recent_activity()


If you are a developer and interested in developing new Modules see:
  
   Moodle Documentation:  http://moodle.org/doc
   Moodle Community:      http://moodle.org/community
