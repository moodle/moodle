<?PHP // $Id$

    require("../config.php");


    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }

    // Check databases and modules and install as needed.
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

    // Check version of Moodle code on disk compared with database
    // and upgrade if possible.

    include_once("$CFG->dirroot/version.php");  # defines $version and upgrades

    if ($dversion = get_field("config", "value", "name", "version")) { 
        if ($version > $dversion) {  // upgrade
            $a->oldversion = $dversion;
            $a->newversion = $version;
            $strdatabasechecking = get_string("databasechecking", "", $a);
            $strdatabasesuccess  = get_string("databasesuccess");
            print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);
            notify($strdatabasechecking);
            $db->debug=true;
            if (upgrade_moodle($dversion)) {
                $db->debug=false;
                if (set_field("config", "value", "$version", "name", "version")) {
                    notify($strdatabasesuccess);
                    print_continue("$CFG->wwwroot");
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
        $strdatabaseupgrades = get_string("databaseupgrades");
        print_header($strdatabaseupgrades, $strdatabaseupgrades, $strdatabaseupgrades);
        if (insert_record("config", $dversion)) {
            notify("You are currently using Moodle version $version");
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


    // Find and check all modules and load them up or upgrade them if necessary


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

    // Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/site.php");
    }

    if (!isadmin()) {
        if (! record_exists_sql("SELECT * FROM user_admins")) {   // No admin user yet
            redirect("$CFG->wwwroot/admin/user.php");
        }
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/");
    }


    // At this point, the databases exist, and the user is an admin

    $stradministration = get_string("administration");
    print_header("$site->fullname: $stradministration","$site->fullname: $stradministration", "$stradministration");

    $table->head  = array (get_string("site"), get_string("courses"), get_string("users"));
    $table->align = array ("CENTER", "CENTER", "CENTER");
    $table->data[0][0] = "<P><A HREF=\"site.php\">".get_string("sitesettings")."</A></P>".
                         "<P><A HREF=\"../course/log.php?id=$site->id\">".get_string("sitelogs")."</A></P>".
                         "<P><A HREF=\"../theme/index.php\">".get_string("choosetheme")."</A></P>".
                         "<P><A HREF=\"lang.php\">".get_string("checklanguage")."</A></P>";
    $table->data[0][1] = "<P><A HREF=\"../course/edit.php\">".get_string("addnewcourse")."</A></P>".
                         "<P><A HREF=\"../course/teacher.php\">".get_string("assignteachers")."</A></P>".
                         "<P><A HREF=\"../course/delete.php\">".get_string("deletecourse")."</A></P>";
    $table->data[0][2] = "<P><A HREF=\"user.php?newuser=true\">".get_string("addnewuser")."</A></P>".
                         "<P><A HREF=\"user.php\">".get_string("edituser")."</A></P>";

    print_table($table);

    print_footer();
?>


