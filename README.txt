README     $Id$
------

I highly recommend you read the detailed help on 
installation, upgrading and use of Moodle, stored
in the doc subdirectory of this installation, or 
read at:

   http://moodle.com/doc/


For the impatient, here is a basic outline of the 
installation process, which normally takes me only 
a few minutes:

1) Move the Moodle files into your web directory. 

2) Create an empty directory somewhere to store 
   uploaded files (NOT accessible via the web).
   This directory must be writeable by the web server
   process.

3) Create a single database for Moodle to store all
   it's tables in.

4) Copy the file config-dist.php to config.php, and 
   edit it with all your own settings.

5) Visit your new home page with a web browser.  Moodle
   will lead you through the rest of the setup, 
   creating an admin account and so on.

6) Set up a cron task to call the file admin/cron.php
   every five minutes or so.


Cheers!
Martin Dougiamas

