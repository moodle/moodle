<?PHP // $Id$

/// Check that config.php exists
    if (!file_exists("../config.php")) {
        echo "<H2 align=center>You need to create a config.php.<BR>
                  See the <A HREF=\"http://moodle.com/doc/?frame=install.html\">installation instructions</A>.</H2>";
        die;
    }

    require_once("../config.php");


/// Check that PHP is of a sufficient version

    if (!check_php_version("4.1.0")) {
        $version = phpversion();
        print_heading("Sorry, Moodle requires PHP 4.1.0 or later (currently using version $version)");
        die;
    }

/// Check some PHP server settings

    $documentationlink = "please read the <A HREF=\"../doc/?frame=install.html&sub=webserver\">install documentation</A>";


    if (ini_get_bool('session.auto_start')) {
        error("The PHP server variable 'session.auto_start' should be Off - $documentationlink");
    }

    if (ini_get_bool('magic_quotes_runtime')) {
        error("The PHP server variable 'magic_quotes_runtime' should be Off - $documentationlink");
    }

    if (!ini_get_bool('magic_quotes_gpc')) {
        error("The PHP server variable 'magic_quotes_gpc' is not turned On - $documentationlink");
    }

    if (!ini_get_bool('file_uploads')) {
        error("The PHP server variable 'file_uploads' is not turned On - $documentationlink");
    }

    if (!ini_get_bool('short_open_tag')) {
        error("The PHP server variable 'short_open_tag' is not turned On - $documentationlink");
    }



/// Check that sessions are supported

    if (!is_readable(ini_get('session.save_path')) and !ini_get_bool('safe_mode')) {
        $sessionpath = ini_get('session.save_path');
        notify("Warning: It appears your server does not support sessions (session.save_path = '$sessionpath')");
    }


/// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }


/// Check settings in config.php

    $dirroot = dirname(realpath("../config.php"));
    if ($dirroot != $CFG->dirroot) {
        error("Please fix your settings in config.php:
              <P>You have:
              <P>\$CFG->dirroot = \"".addslashes($CFG->dirroot)."\";
              <P>but it should be:
              <P>\$CFG->dirroot = \"".addslashes($dirroot)."\";",
              "./");
    }


/// Check if the main tables have been installed yet or not.

    if (! $tables = $db->Metatables() ) {    // No tables yet at all.
        $maintables = false;

    } else {                                 // Check for missing main tables
        $maintables = true;
        $mtables = array("config", "course", "course_categories", "course_modules", 
                         "course_sections", "log", "log_display", "modules", 
                         "user", "user_admins", "user_students", "user_teachers");
        foreach ($mtables as $mtable) {
            if (!in_array($CFG->prefix.$mtable, $tables)) { 
                $maintables = false;
                break;
            }
        }
    }

    if (! $maintables) {
        if (!$agreelicence) {
            $strlicense = get_string("license");
            print_header($strlicense, $strlicense, $strlicense, "", "", false, "&nbsp;", "&nbsp;");
            print_heading("<A HREF=\"http://moodle.org\">Moodle</A> - Modular Object-Oriented Dynamic Learning Environment");
            print_heading(get_string("copyrightnotice"));
            print_simple_box_start("center");
            echo text_to_html(get_string("gpl"));
            print_simple_box_end();
            echo "<BR>";
            notice_yesno(get_string("doyouagree"), "index.php?agreelicence=true", 
                                                   "http://www.gnu.org/copyleft/gpl.html");
            exit;
        }

        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");
        print_header($strdatabasesetup, $strdatabasesetup, $strdatabasesetup, "", "", false, "&nbsp;", "&nbsp;");
        if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $db->debug = true;
            set_time_limit(0);  // To allow slow databases to complete the long SQL
            if (modify_database("$CFG->libdir/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                notify($strdatabasesuccess, "green");
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

    include_once("$CFG->dirroot/version.php");              # defines $version 
    include_once("$CFG->dirroot/lib/db/$CFG->dbtype.php");  # defines upgrades

    if ($CFG->version) { 
        if ($version > $CFG->version) {  // upgrade
            $a->oldversion = $CFG->version;
            $a->newversion = $version;
            $strdatabasechecking = get_string("databasechecking", "", $a);
            $strdatabasesuccess  = get_string("databasesuccess");
            print_header($strdatabasechecking, $strdatabasechecking, $strdatabasechecking, 
                         "", "", false, "&nbsp;", "&nbsp;");
            print_heading($strdatabasechecking);
            $db->debug=true;
            if (main_upgrade($CFG->version)) {
                $db->debug=false;
                if (set_config("version", $version)) {
                    notify($strdatabasesuccess, "green");
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
        $strcurrentversion = get_string("currentversion");
        print_header($strcurrentversion, $strcurrentversion, $strcurrentversion, 
                     "", "", false, "&nbsp;", "&nbsp;");

        if (set_config("version", $version)) {
            print_heading("You are currently using Moodle version $version (Release $release)");
            print_continue("index.php");
            die;
        } else {
            $db->debug=true;
            if (main_upgrade(0)) {
                print_continue("index.php");
            } else {
                error("A problem occurred inserting current version into databases");
            }
            $db->debug=false;
        }
    }

/// Updated human-readable release version if necessary

    if ($release <> $CFG->release) {  // Update the release version
        $strcurrentrelease = get_string("currentrelease");
        print_header($strcurrentrelease, $strcurrentrelease, $strcurrentrelease, "", "", false, "&nbsp;", "&nbsp;");
        print_heading($release);
        if (!set_config("release", $release)) {
            notify("ERROR: Could not update release version in database!!");
        }
        print_continue("index.php");
        print_simple_box_start("CENTER");
        include("$CFG->dirroot/lang/en/docs/release.html");
        print_simple_box_end();
        print_continue("index.php");
        exit;
    }


/// Find and check all modules and load them up or upgrade them if necessary

    if (!$mods = get_list_of_plugins("mod") ) {
        error("No modules installed!");
    }

    foreach ($mods as $mod) {

        if ($mod == "NEWMODULE") {   // Someone has unzipped the template, ignore it
            continue;
        }

        $fullmod = "$CFG->dirroot/mod/$mod";

        unset($module);

        if ( is_readable("$fullmod/version.php")) {
            include_once("$fullmod/version.php");  # defines $module with version etc
        } else {
            notify("Module $mod: $fullmod/version.php was not readable");
            continue;
        }

        if ( is_readable("$fullmod/db/$CFG->dbtype.php")) {
            include_once("$fullmod/db/$CFG->dbtype.php");  # defines upgrading function
        } else {
            notify("Module $mod: $fullmod/db/$CFG->dbtype.php was not readable");
            continue;
        }


        if (!isset($module)) {
            continue;
        }

        $module->name = $mod;   // The name MUST match the directory
        
        if ($currmodule = get_record("modules", "name", $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
                print_heading("$module->name module needs upgrading");
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
                        notify(get_string("modulesuccess", "", $module->name), "green");
                        echo "<HR>";
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
            if (empty($updated_modules)) {
                $strmodulesetup    = get_string("modulesetup");
                print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, "", "", false, "&nbsp;", "&nbsp;");
            }
            print_heading($module->name);
            $updated_modules = true;
            $db->debug = true;
            set_time_limit(0);  // To allow slow databases to complete the long SQL
            if (modify_database("$fullmod/db/$CFG->dbtype.sql")) {
                $db->debug = false;
                if ($module->id = insert_record("modules", $module)) {
                    notify(get_string("modulesuccess", "", $module->name), "green");
                    echo "<HR>";
                } else {
                    error("$module->name module could not be added to the module list!");
                }
            } else { 
                error("$module->name tables could NOT be set up successfully!");
            }
        }
    }

    if (!empty($updated_modules)) {
        print_continue("index.php");
        die;
    }


/// Insert default values for any important configuration variables

    include_once("$CFG->dirroot/lib/defaults.php");

    foreach ($defaults as $name => $value) {
        if (!isset($CFG->$name)) {
            $CFG->$name = $value;
            set_config($name, $value);
            $configchange = true;
        }
    }


/// If any new configurations were found then send to the config page to check

    if (!empty($configchange)) {
        redirect("config.php");
    }

/// Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("site.php");
    }

/// Set up the admin user
    if (! record_exists("user_admins")) {   // No admin user yet
        redirect("user.php");
    }

/// Check for valid admin user
    if (!iscreator()) {
        error("You need to be an admin user or teacher to use this page.", "$CFG->wwwroot/login/index.php");
    }


/// At this point everything is set up and the user is an admin, so print menu

    $stradministration = get_string("administration");
    print_header("$site->shortname: $stradministration","$site->fullname: $stradministration", "$stradministration");
    if (isadmin()) {
        $table->head  = array (get_string("site"), get_string("courses"), get_string("users"));
		$table->align = array ("CENTER", "CENTER", "CENTER");
		$table->data[0][0] = "<p><a href=\"config.php\">".get_string("configvariables")."</a></p>".
                         "<p><a href=\"site.php\">".get_string("sitesettings")."</a></p>".
                         "<p><a href=\"../course/log.php?id=$site->id\">".get_string("sitelogs")."</a></p>".
                         "<p><a href=\"../theme/index.php\">".get_string("choosetheme")."</a></p>".
                         "<p><a href=\"lang.php\">".get_string("checklanguage")."</a></p>".
                         "<p><a href=\"modules.php\">".get_string("managemodules")."</a></p>";
		if (file_exists("$CFG->dirroot/admin/$CFG->dbtype")) {
            $table->data[0][0] .= "<p><a href=\"$CFG->dbtype/frame.php\">".get_string("managedatabase")."</a></p>";
		}
		$table->data[0][1] = "<p><a href=\"../course/edit.php\">".get_string("addnewcourse")."</a></p>".
                         "<p><a href=\"teacher.php\">".get_string("assignteachers")."</a></p>".
                         "<p><a href=\"../course/delete.php\">".get_string("deletecourse")."</a></p>".
                         "<p><a href=\"../course/categories.php\">".get_string("categories")."</a></p>";
        if ($CFG->auth == "email" || $CFG->auth == "none" || $CFG->auth == "manual") {
		    $table->data[0][2] = "<p><a href=\"user.php?newuser=true\">".get_string("addnewuser")."</a></p>";
        }    
        $table->data[0][2] .=  "<p><a href=\"user.php\">".get_string("edituser")."</a></p>".
                         "<p><a href=\"admin.php\">".get_string("assignadmins")."</a></p>".
                         "<p><a href=\"creators.php\">".get_string("assigncreators")."</a></p>".
                         "<p><a href=\"auth.php\">".get_string("authentication")."</a></p>";
    } else { /// user is coursecreator
	    $table->head  = array (get_string("courses"));
		$table->align = array ("CENTER");
		$table->data[0][1] = "<p><a href=\"../course/edit.php\">".get_string("addnewcourse")."</a></p>".
		  "<p><a href=\"teacher.php\">".get_string("assignteachers")."</a></p>";
	}
    
    print_table($table);

    echo "<br><div align=center>";
    print_single_button("$CFG->wwwroot/doc", NULL, get_string("documentation"));
    echo "</div>";

    echo "<br><div align=center>";
    print_single_button("register.php", NULL, get_string("registration"));
    echo "</div>";

    print_heading("Moodle $CFG->release ($CFG->version)", "CENTER", 1);

    print_footer($site);

?>


