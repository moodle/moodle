<?PHP // $Id$

    require("../config.php");

/// Normally this file is never used.
///
/// It can be useful to jump-start a new moodle installation when 
/// it is being installed into a database that already contains
/// some tables.   
/// 
/// For this case you would call this script directly
///
/// Warning: this method ASSUMES that all existing tables with 
/// have different names to any Moodle tables, so make sure 
/// there aren't any Moodle-named tables in your database before
/// using this script.   See lib/db/mysql.sql and mod/*/db/mysql.sql

    if ($CFG->version) { // To avoid this being used on a working site.
        echo "No need to use this";

    } else {

        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");

        print_header($strdatabasesetup, $strdatabasesetup, $strdatabasesetup);
        if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $db->debug = true;
            if (modify_database("$CFG->libdir/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                notify($strdatabasesuccess);
            } else {
                $db->debug = false;
                error("Error: Main databases NOT set up successfully");
            }
        } else {
            error("Error: Your database ($CFG->dbtype) is not yet fully supported by Moodle.  
                   See the lib/db directory.");
        }
        print_continue("index.php");
    }

