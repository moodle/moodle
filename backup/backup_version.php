<?PHP  //$Id$
// This file defines the current version of the
// backup/restore code that is being used.  This can be
// compared against the values stored in the 
// database (backup_version) to determine whether upgrades should
// be performed (see db/backup_*.php)

$backup_version = 2003063003;   // The current version is a date (YYYYMMDDXX)

$backup_release = "0.7.0 alpha <font color=red>(Previous backup compatibility broken !!)</font><p align=center>
                   <a href=\"http://moodle.org/bugs/bug.php?op=show&bugid=84\">See Bug 84</a>";  // User-friendly version number
