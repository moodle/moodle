<?PHP // $Id$

    require("../config.php");


    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }

    // Check databases and modules and install as needed.
    if (! $db->Metatables() ) { 
        print_header("Setting up database", "Setting up database", "Setting up databases for the first time");

        if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $db->debug = true;
            if (modify_database("$CFG->libdir/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                notify("Main databases set up successfully");
            } else {
                $db->debug = false;
                error("Error: Main databases NOT set up successfully");
            }
        } else {
            error("Error: Your database ($CFG->dbtype) is not yet supported by Moodle.  See the lib/db directory.");
        }
        print_heading("<A HREF=\"index.php\">Continue</A>");
        die;
    }

    // Check version of Moodle code on disk compared with database
    // and upgrade if possible.

    include_once("$CFG->dirroot/version.php");  # defines $version and upgrades

    if ($dversion = get_field("config", "value", "name", "version")) { 
        if ($version > $dversion) {  // upgrade
            print_header("Upgrading database", "Upgrading database", "Upgrading main databases");
            notify("Upgrading databases from version $dversion to $version...");
            $db->debug=true;
            if (upgrade_moodle($dversion)) {
                $db->debug=false;
                if (set_field("config", "value", "$version", "name", "version")) {
                    notify("Databases were successfully upgraded");
                    print_heading("<A HREF=\"index.php\">Continue</A>");
                    die;
                } else {
                    notify("Upgrade failed!  (Could not update version in config table)");
                }
            } else {
                $db->debug=false;
                notify("Upgrade failed!  See /version.php");
            }
        } else if ($version < $dversion) {
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
        }
       
    } else {
        $dversion->name  = "version";
        $dversion->value = $version;
        print_header("Upgrading database", "Upgrading database", "Upgrading main databases");
        if (insert_record("config", $dversion)) {
            notify("You are currently using Moodle version $version");
            print_heading("<A HREF=\"index.php\">Continue</A>");
            die;
        } else {
            $db->debug=true;
            if (upgrade_moodle(0)) {
                print_heading("<A HREF=\"index.php\">Continue</A>");
            } else {
                error("A problem occurred inserting current version into databases");
            }
            $db->debug=false;
        }
    }


    // Find and check all modules and load them up or upgrade them if necessary

    $dir = opendir("$CFG->dirroot/mod");
    while ($mod = readdir($dir)) {
        if ($mod == "." || $mod == ".." || $mod == "CVS") {
            continue;
        }

        $fullmod = "$CFG->dirroot/mod/$mod";
        if (filetype($fullmod) != "dir") {
            continue;
        }

        unset($module);

        include_once("$CFG->dirroot/mod/$mod/version.php");  # defines $module with version etc

        if (!isset($module)) {
            continue;
        }

        $module->name = $mod;   // The name MUST match the directory
        
        if ($currmodule = get_record("modules", "name", $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
                notify("$module->name module needs upgrading");
                $upgrade_function = $module->name."_upgrade";
                if (function_exists($upgrade_function)) {
                    $db->debug=true;
                    if ($upgrade_function($currmodule->version, $module)) {
                        $db->debug=false;
                        // OK so far, now update the modules record
                        $module->id = $currmodule->id;
                        if (! update_record("modules", $module)) {
                            error("Could not update $module->name record in modules table!");
                        }
                        notify("$module->name module was successfully upgraded");
                    } else {
                        $db->debug=false;
                        notify("Upgrading $module->name from $currmodule->version to $module->version FAILED!");
                    }
                }
                $updated_modules = true;
            } else {
                error("Version mismatch: $module->name can't downgrade $currmodule->version -> $module->version !");
            }
    
        } else {    // module not installed yet, so install it
            if (!$updated_modules) {
                print_header("Setting up database", "Setting up database", "Setting up module tables", "");
            }
            $updated_modules = true;
            $db->debug = true;
            if (modify_database("$fullmod/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                if ($module->id = insert_record("modules", $module)) {
                    notify("$module->name tables have been set up correctly");
                } else {
                    error("$module->name module could not be added to the module list!");
                }
            } else { 
                error("$module->name tables could NOT be set up successfully!");
            }
        }
    }

    if ($updated_modules) {
        print_heading("<A HREF=\"index.php\">Continue</A>");
        die;
    }

    // Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("site.php");
    }

    if (!isadmin()) {
        if (! record_exists_sql("SELECT * FROM user_admins")) {
            redirect("user.php");
        }
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/");
    }


    // At this point, the databases exist, and the user is an admin

    $stradministration = get_string("administration");
    print_header("$site->fullname: $stradministration","$site->fullname: $stradministration", "$stradministration");

    $table->head  = array (get_string("site"), get_string("courses"), get_string("users"));
    $table->align = array ("CENTER", "CENTER", "CENTER");
    $table->data[0][0] = "<P><A HREF=\"site.php\">".get_string("sitesettings")."</A></P>".
                         "<P><A HREF=\"../course/log.php?id=$site->id\">".get_string("sitelogs")."</A></P>";
    $table->data[0][1] = "<P><A HREF=\"../course/edit.php\">".get_string("addnewcourse")."</A></P>".
                         "<P><A HREF=\"../course/teacher.php\">".get_string("assignteachers")."</A></P>".
                         "<P><A HREF=\"../course/delete.php\">".get_string("deletecourse")."</A></P>";
    $table->data[0][2] = "<P><A HREF=\"user.php?newuser=true\">".get_string("addnewuser")."</A></P>".
                         "<P><A HREF=\"user.php\">".get_string("edituser")."</A></P>";

    print_table($table);

    print_footer();
?>


