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

    if (!ini_get_bool('file_uploads')) {
        error("The PHP server variable 'file_uploads' is not turned On - $documentationlink");
    }


/// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        error("Moodle has not been configured yet.  You need to to edit config.php first.");
    }


/// Check settings in config.php

    $dirroot = dirname(realpath("../index.php"));
    if (!empty($dirroot) and $dirroot != $CFG->dirroot) {
        error("Please fix your settings in config.php:
              <P>You have:
              <P>\$CFG->dirroot = \"".addslashes($CFG->dirroot)."\";
              <P>but it should be:
              <P>\$CFG->dirroot = \"".addslashes($dirroot)."\";",
              "./");
    }

/// Set some necessary variables during set-up to avoid PHP warnings later on this page
    if (!isset($CFG->framename)) {
        $CFG->framename = "_top";
    }
    if (!isset($CFG->release)) {
        $CFG->release = "";
    }
    if (!isset($CFG->version)) {
        $CFG->version = "";
    }

/// Turn off time limits, sometimes upgrades can be slow.

    @set_time_limit(0);

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
        if (empty($agreelicence)) {
            $strlicense = get_string("license");
            print_header($strlicense, $strlicense, $strlicense, "", "", false, "&nbsp;", "&nbsp;");
            print_heading("<A HREF=\"http://moodle.org\">Moodle</A> - Modular Object-Oriented Dynamic Learning Environment");
            print_heading(get_string("copyrightnotice"));
            print_simple_box_start("center");
            echo text_to_html(get_string("gpl"));
            print_simple_box_end();
            echo "<br />";
            notice_yesno(get_string("doyouagree"), "index.php?agreelicence=true", 
                                                   "http://moodle.org/doc/?frame=licence.html");
            exit;
        }

        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");
        print_header($strdatabasesetup, $strdatabasesetup, $strdatabasesetup, "", "", false, "&nbsp;", "&nbsp;");
        if (file_exists("$CFG->libdir/db/$CFG->dbtype.sql")) {
            $db->debug = true;
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
        print_heading("Moodle $release");
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


/// Upgrade backup/restore system if necessary
    require_once("$CFG->dirroot/backup/lib.php");
    upgrade_backup_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade blocks system if necessary
    require_once("$CFG->dirroot/lib/blocklib.php");
    upgrade_blocks_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all blocks and load (or upgrade them if necessary)
    upgrade_blocks_plugins("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards
    
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

        if (!empty($module->requires)) {
            if ($module->requires > $CFG->version) {
                $info->modulename = $mod;
                $info->moduleversion  = $module->version;
                $info->currentmoodle = $CFG->version;
                $info->requiremoodle = $module->requires;
                notify(get_string('modulerequirementsnotmet', 'error', $info));
                unset($info);
                continue;
            }
        }

        $module->name = $mod;   // The name MUST match the directory
        
        if ($currmodule = get_record("modules", "name", $module->name)) {
            if ($currmodule->version == $module->version) {
                // do nothing
            } else if ($currmodule->version < $module->version) {
                if (empty($updated_modules)) {
                    $strmodulesetup  = get_string("modulesetup");
                    print_header($strmodulesetup, $strmodulesetup, $strmodulesetup, "", "", false, "&nbsp;", "&nbsp;");
                }
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
            @set_time_limit(0);  // To allow slow databases to complete the long SQL
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
    require_login();

    if (!isadmin()) {
        error("You need to be an admin user to use this page.", "$CFG->wwwroot/login/index.php");
    }


/// At this point everything is set up and the user is an admin, so print menu

    $stradministration = get_string("administration");
    print_header("$site->shortname: $stradministration","$site->fullname", "$stradministration");
    print_simple_box_start("center", "100%", "$THEME->cellcontent2", 20);
    print_heading($stradministration);

    if (!empty($CFG->upgrade)) {  // Print notice about extra upgrading that needs to be done
        print_simple_box(get_string("upgrade$CFG->upgrade", "admin", 
                                    "$CFG->wwwroot/$CFG->admin/upgrade$CFG->upgrade.php"), "center");
        print_spacer(10,10);
    }

    $table->tablealign = "right";
    $table->align = array ("right", "left");
    $table->wrap = array ("nowrap", "nowrap");
    $table->cellpadding = 4;
    $table->cellspacing = 3;
    $table->width = "40%";

    $configdata  = "<font size=+1>&nbsp;</font><a href=\"config.php\">".get_string("configvariables")."</a> - <font size=1>".
                    get_string("adminhelpconfigvariables")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"site.php\">".get_string("sitesettings")."</a> - <font size=1>".
                    get_string("adminhelpsitesettings")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"../theme/index.php\">".get_string("themes")."</a> - <font size=1>".
                    get_string("adminhelpthemes")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"lang.php\">".get_string("language")."</a> - <font size=1>".
                    get_string("adminhelplanguage")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"modules.php\">".get_string("managemodules")."</a> - <font size=1>".
                    get_string("adminhelpmanagemodules")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"blocks.php\">".get_string("manageblocks")."</a> - <font size=1>".
                    get_string("adminhelpmanageblocks")."</font><br />";
    $configdata .= "<font size=+1>&nbsp;</font><a href=\"filters.php\">".get_string("managefilters")."</a> - <font size=1>".
                    get_string("adminhelpmanagefilters")."</font><br />";
    if (!isset($CFG->disablescheduledbackups)) {
        $configdata .= "<font size=+1>&nbsp;</font><a href=\"backup.php\">".get_string("backup")."</a> - <font size=1>".
                        get_string("adminhelpbackup")."</font><br />";
    }

    $table->data[] = array("<font size=+1><b><a href=\"configure.php\">".get_string("configuration")."</a></b>", 
                            $configdata);


    $userdata = "<font size=+1>&nbsp;</font><a href=\"auth.php\">".get_string("authentication")."</a> - <font size=1>".
                 get_string("adminhelpauthentication")."</font><br />";

    if (is_internal_auth()) {
        $userdata .= "<font size=+1>&nbsp;</font><a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true\">".
                      get_string("addnewuser")."</a> - <font size=1>".
                      get_string("adminhelpaddnewuser")."</font><br />";
        $userdata .= "<font size=+1>&nbsp;</font><a href=\"$CFG->wwwroot/$CFG->admin/uploaduser.php\">".
                      get_string("uploadusers")."</a> - <font size=1>".
                      get_string("adminhelpuploadusers")."</font><br />";
    }
    $userdata .= "<font size=+1>&nbsp;</font><a href=\"user.php\">".get_string("edituser")."</a> - <font size=1>".
                 get_string("adminhelpedituser")."</font><br />";
    $userdata .= "<font size=+1>&nbsp;</font><a href=\"admin.php\">".get_string("assignadmins")."</a> - <font size=1>".
                 get_string("adminhelpassignadmins")."</font><br />";
    $userdata .= "<font size=+1>&nbsp;</font><a href=\"creators.php\">".get_string("assigncreators")."</a> - <font size=1>".
                 get_string("adminhelpassigncreators")."</font><br />";
    $userdata .= "<font size=+1>&nbsp;</font><a href=\"../course/index.php?edit=on\">".get_string("assignteachers")."</a> - <font size=1>".
                 get_string("adminhelpassignteachers").
                 " <img src=\"../pix/t/user.gif\" height=11 width=11></font><br />";
    $userdata .= "<font size=+1>&nbsp;</font><a href=\"../course/index.php?edit=off\">".get_string("assignstudents")."</a> - <font size=1>".
                 get_string("adminhelpassignstudents")."</font>";

    $table->data[] = array("<font size=+1><b><a href=\"users.php\">".get_string("users")."</a></b>", $userdata);

    $table->data[] = array("<font size=+1><b><a href=\"../course/index.php?edit=on\">".get_string("courses")."</a></b>",
                           "<font size=+1>&nbsp;</font>".get_string("adminhelpcourses"));
    $table->data[] = array("<font size=+1><b><a href=\"../course/log.php?id=$site->id\">".get_string("logs")."</a></b>",
                           "<font size=+1>&nbsp;</font>".get_string("adminhelplogs"));
    $table->data[] = array("<font size=+1><b><a href=\"../files/index.php?id=$site->id\">".get_string("sitefiles")."</a></b>",
                           "<font size=+1>&nbsp;</font>".get_string("adminhelpsitefiles"));
    if (file_exists("$CFG->dirroot/$CFG->admin/$CFG->dbtype")) {
        $table->data[] = array("<font size=+1><b><a href=\"$CFG->dbtype/frame.php\">".get_string("managedatabase")."</a></b>",
                               "<font size=+1>&nbsp;</font>".get_string("adminhelpmanagedatabase"));
    }

    print_table($table);
    
    //////////////////////////////////////////////////////////////////////////////////////////////////
    ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO REMOVE OR MODIFY THE COPYRIGHT NOTICE BELOW ////
    $copyrighttext = "<a href=\"http://moodle.org/\">Moodle</a> ".
                     "<a href=\"../doc/?frame=release.html\">$CFG->release</a> ($CFG->version)<br />".
                     "Copyright &copy; 1999-2004 Martin Dougiamas<br />".
                     "<a href=\"../doc/?frame=licence.html\">GNU Public License</a>";
    echo "<center><p><font size=1>$copyrighttext</font></p></center>";
    //////////////////////////////////////////////////////////////////////////////////////////////////


    echo "<table border=0 align=center width=100%><tr>";
    echo "<td align=center width=33%>";
    print_single_button("$CFG->wwwroot/doc", NULL, get_string("documentation"));
    echo "</td>";

    echo "<td align=center width=33%>";
    print_single_button("phpinfo.php", NULL, get_string("phpinfo"));
    echo "</td>";

    echo "<td align=center width=33%>";
    print_single_button("register.php", NULL, get_string("registration"));
    echo "</td>";
    echo "<tr></table>";

    print_simple_box_end();

    print_footer($site);

?>


