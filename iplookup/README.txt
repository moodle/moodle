iplookup 
--------

These plugins will show you something about an IP address.

Moodle scripts call the index.php in this directory like this:
   
   /iplookup/index.php?ip=222.222.222.222&user=1

Both parameters are optional, they default to the current user.

index.php loads the lib.php from a specified subdirectory 
to actually display some sort of map or description.

The current plugin is selected using $CFG->iplookup.

Cheers,
Martin
