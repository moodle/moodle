<?PHP  //$Id$
// This file defines the current version of the
// backup/restore code that is being used.  This can be
// compared against the values stored in the 
// database (backup_version) to determine whether upgrades should
// be performed (see db/backup_*.php)

$backup_version = 2004083120;  // YYYYMMDD   = date of first major branch release 1.4
                               //         X  = point release version 1,2,3 etc
                               //          Y = increments between point releases

$backup_release = "1.4.2";  // User-friendly version number
