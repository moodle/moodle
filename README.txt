README     $Id$
------

If you are installing the first time, then you 
should read the installation guide which is 
part of the Moodle documentation (along with 
information about upgrading etc):

   http://moodle.com/doc/

There is a copy of all this documentation as part of 
this Moodle distribution.  You can access your local
copy of the installation guide here:

   lang/en/docs/install.html

Once Moodle is installed on your machine, then 
you can also access a local copy of all this 
documentation (localised to your language) at:

   http://yourmoodlesite.com/doc/


For the impatient, here is a basic outline of the 
installation process, which normally takes me only 
a few minutes:

1) Move the Moodle files into your web directory. 

2) Create an empty directory somewhere to store 
   uploaded files (NOT accessible via the web).
   This directory must be writeable by the web server
   process.

3) Create a single database for Moodle to store all
   it's tables in (or choose an existing database).

4) Copy the file config-dist.php to config.php, and 
   edit it with all your own settings.

5) Visit your new home page with a web browser.  Moodle
   will lead you through the rest of the setup, 
   creating an admin account and so on.

6) Set up a cron task to call the file admin/cron.php
   every five minutes or so.


Cheers!
Martin Dougiamas

