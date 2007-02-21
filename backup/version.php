<?php  //$Id$

/// This file defines the current version of the
/// backup/restore code that is being used.  This can be
/// compared against the values stored in the 
/// database (backup_version) to determine whether upgrades should
/// be performed (see db/backup_*.php)

    $backup_version = 2007022100;   // The current version is a date (YYYYMMDDXX)
    $backup_release = "1.8Beta";    // User-friendly version number

?>
