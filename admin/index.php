<?PHP // $Id$

    require("../config.php");


/// Check that PHP is of a sufficient version

    if ( ! check_php_version("4.1.0") ) {
        $version = phpversion();
        print_heading("Sorry, Moodle requires PHP 4.1.0 or later (currently using version $version)");
        die;
    }

/// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }

/// Check databases and modules and install as needed.
    if (! $db->Metatables() ) { 

        if (!$agreelicence) {
            $strlicense = get_string("license");
            print_header($strlicense, $strlicense, $strlicense);
            print_heading("<A HREF=\"http://moodle.com\">Moodle</A> - Modular Object-Oriented Dynamic Learning Environment");
            print_heading(get_string("copyrightnotice"));
            print_simple_box_start("CENTER");
            echo text_to_html(get_string("gpl"));
            print_simple_box_end();
            echo "<BR>";
            notice_yesno(get_string("doyouagree"), "index.php?agreelicence=true", 
                                                   "http://www.gnu.org/copyleft/gpl.html");
            exit;
        }

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
            error("Error: Your database ($CFG->dbtype) is not yet fully supported by Moodle.  See the lib/db directory.");
        }
        print_continue("index.php");
        die;
    }

/// Check version of Moodle code on disk compared with database
/// and upgrade if possible.

    include_once("$CFG->dirroot/version.php");  # defines $version and upgrades

    if ($CFG->version) { 
        if ($version > $CFG->version) {  // upgrade
            $a->oldversion = $CFG->version;
            $a->newversion = $version;
            $strdatabasechecking = get_string("databasechecking", "", $a);
            $strdatabasesuccess  = get_string("databasesuccess");
            print_header($strdatabasechecking, $strdatabasechecking, $strdatabasechecking);
            notify($strdatabasechecking);
            $db->debug=true;
            if (upgrade_moodle($CFG->version)) {
                $db->debug=false;
                if (set_config("version", $version)) {
                    notify($strdatabasesuccess);
                    print_continue("index.php");
                    die;
                } else {
                    notify("Upgrade failed!  (Could not update version in config table)");
                }
            } else {
                $db->debug=false;
                notify("Upgrade failed!  See /version.php");
            }
        } else if ($version < $CFG->version) {
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
        }
       
    } else {
        $strdatabaseupgrades = get_string("databaseupgrades");
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);

        if (set_config("version", $version)) {
            notify("You are currently using Moodle version $version (Release $release)");
            print_continue("index.php");
            die;
        } else {
            $db->debug=true;
            if (upgrade_moodle(0)) {
                print_continue("index.php");
            } else {
                error("A problem occurred inserting current version into databases");
            }
            $db->debug=false;
        }
    }

/// Updated human-readable release version if necessary

    if ($release <> $CFG->release) {  // Update the release version
        $strdatabaseupgrades = get_string("databaseupgrades");
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);
        print_heading($release);
        if (!set_config("release", $release)) {
            notify("ERROR: Could not update release version in database!!");
        }
        print_continue("index.php");
        print_simple_box_start("CENTER");
        include("$CFG->dirroot/RELEASE.html");
        print_simple_box_end();
        print_continue("index.php");
        exit;
    }


/// Find and check all modules and load them up or upgrade them if necessary

    if (!$mods = get_list_of_plugins("mod") ) {
        error("No modules installed!");
    }

    foreach ($mods as $mod) {
        $fullmod = "$CFG->dirroot/mod/$mod";

        unset($module);

        include_once("$fullmod/version.php");  # defines $module with version etc

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
                        notify(get_string("modulesuccess", "", $module->name));
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
                $strmodulesetup    = get_string("modulesetup");
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup);
            }
            $updated_modules = true;
            $db->debug = true;
            if (modify_database("$fullmod/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                if ($module->id = insert_record("modules", $module)) {
                    notify(get_string("modulesuccess", "", $module->name));
                } else {
                    error("$module->name module could not be added to the module list!");
                }
            } else { 
                error("$module->name tables could NOT be set up successfully!");
            }
        }
    }

    if ($updated_modules) {
        print_continue("index.php");
        die;
    }


/// Insert default values for any important configuration variables

    include_once("$CFG->dirroot/lib/defaults.php");

    $CFG = (array)$CFG;
    foreach ($defaults as $name => $value) {
        if (!isset($CFG[$name])) {
            $config->name  = $name;
            $config->value = $CFG[$name] = $value;
            insert_record("config", $config);
            $configchange = true;
        }
    }
    $CFG = (object)$CFG;

/// If any new configurations were found then send to the config page to check

    if ($configchange) {
        redirect("$CFG->wwwroot/admin/config.php");
    }

/// Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/site.php");
    }

/// Set up the admin user
    if (!isadmin()) {
        if (! record_exists_sql("SELECT * FROM user_admins")) {   // No admin user yet
            redirect("$CFG->wwwroot/admin/user.php");
        }
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
    }


/// At this point everything is set up and the user is an admin, so print menu

    $stradministration = get_string("administration");
    print_header("$site->fullname: $stradministration","$site->fullname: $stradministration", "$stradministration");

    $table->head  = array (get_string("site"), get_string("courses"), get_string("users"));
    $table->align = array ("CENTER", "CENTER", "CENTER");
    $table->data[0][0] = "<P><A HREF=\"config.php\">".get_string("configvariables")."</A></P>".
                         "<P><A HREF=\"site.php\">".get_string("sitesettings")."</A></P>".
                         "<P><A HREF=\"../course/log.php?id=$site->id\">".get_string("sitelogs")."</A></P>".
                         "<P><A HREF=\"../theme/index.php\">".get_string("choosetheme")."</A></P>".
                         "<P><A HREF=\"lang.php\">".get_string("checklanguage")."</A></P>";
    if (file_exists("$CFG->dirroot/admin/$CFG->dbtype")) {
        $table->data[0][0] .= "<P><A HREF=\"$CFG->dbtype/\">".get_string("managedatabase")."</A></P>";
    }
    $table->data[0][1] = "<P><A HREF=\"../course/edit.php\">".get_string("addnewcourse")."</A></P>".
                         "<P><A HREF=\"../course/teacher.php\">".get_string("assignteachers")."</A></P>".
                         "<P><A HREF=\"../course/delete.php\">".get_string("deletecourse")."</A></P>".
                         "<P><A HREF=\"../course/categories.php\">".get_string("categories")."</A></P>";
    $table->data[0][2] = "<P><A HREF=\"user.php?newuser=true\">".get_string("addnewuser")."</A></P>".
                         "<P><A HREF=\"user.php\">".get_string("edituser")."</A></P>";

    print_table($table);

    print_heading("Moodle $CFG->release ($CFG->version)", "CENTER", 1);

    print_footer();

?>


