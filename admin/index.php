<?php // $Id$

/// Check that config.php exists, if not then call the install script
    if (!file_exists("../config.php")) {
        header('Location: ../install.php');
        die;
    }

    require_once("../config.php");
    include_once("$CFG->dirroot/lib/adminlib.php");  // Contains various admin-only functions

    $id = optional_param('id', '', PARAM_ALPHANUM);
    $confirmupgrade = optional_param('confirmupgrade','');


/// Check that PHP is of a sufficient version

    if (!check_php_version("4.1.0")) {
        $version = phpversion();
        print_heading("Sorry, Moodle requires PHP 4.1.0 or later (currently using version $version)");
        die;
    }


/// Check some PHP server settings

    $documentationlink = "please read the <a href=\"../doc/?frame=install.html&amp;sub=webserver\">install documentation</a>";

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
        error("Moodle has not been configured yet.  You need to edit config.php first.");
    }


/// Check settings in config.php

    $dirroot = dirname(realpath("../index.php"));
    if (!empty($dirroot) and $dirroot != $CFG->dirroot) {
        error("Please fix your settings in config.php:
              <p>You have:
              <p>\$CFG->dirroot = \"".addslashes($CFG->dirroot)."\";
              <p>but it should be:
              <p>\$CFG->dirroot = \"".addslashes($dirroot)."\";",
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

/// Turn off time limits and try to flush everything all the time, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    @ob_end_flush();


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
            print_heading("<a href=\"http://moodle.org\">Moodle</a> - Modular Object-Oriented Dynamic Learning Environment");
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

    $stradministration = get_string("administration");

    if ($CFG->version) {
        if ($version > $CFG->version) {  // upgrade

            $a->oldversion = "$CFG->release ($CFG->version)";
            $a->newversion = "$release ($version)";
            $strdatabasechecking = get_string("databasechecking", "", $a);

            if (empty($confirmupgrade)) {
                print_header($strdatabasechecking, $stradministration, $strdatabasechecking,
                        "", "", false, "&nbsp;", "&nbsp;");
                notice_yesno(get_string('upgradesure', 'admin', $a->newversion), 'index.php?confirmupgrade=yes', 'index.php');
                exit;

            } else {
                $strdatabasesuccess  = get_string("databasesuccess");
                print_header($strdatabasechecking, $stradministration, $strdatabasechecking,
                        "", "", false, "&nbsp;", "&nbsp;");
                print_heading($strdatabasechecking);
                $db->debug=true;
                if (main_upgrade($CFG->version)) {
                    $db->debug=false;
                    if (set_config("version", $version)) {
                        remove_dir($CFG->dataroot . '/cache', true); // flush cache
                        notify($strdatabasesuccess, "green");
                        print_continue("index.php");
                        exit;
                    } else {
                        notify("Upgrade failed!  (Could not update version in config table)");
                    }
                } else {
                    $db->debug=false;
                    notify("Upgrade failed!  See /version.php");
                }
            }
        } else if ($version < $CFG->version) {
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
        }

    } else {
        $strcurrentversion = get_string("currentversion");
        print_header($strcurrentversion, $stradministration, $strcurrentversion,
                     "", "", false, "&nbsp;", "&nbsp;");

        if (set_config("version", $version)) {
            print_heading("Moodle $release ($version)");
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



/// Find and check all main modules and load them up or upgrade them if necessary
    upgrade_activity_modules("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade backup/restore system if necessary
    require_once("$CFG->dirroot/backup/lib.php");
    upgrade_backup_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade blocks system if necessary
    require_once("$CFG->dirroot/lib/blocklib.php");
    upgrade_blocks_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all blocks and load (or upgrade them if necessary)
    upgrade_blocks_plugins("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all enrolment plugins and upgrade if necessary
    upgrade_enrol_plugins("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check for local database customisations
    require_once("$CFG->dirroot/lib/locallib.php");
    upgrade_local_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards


/// Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("site.php");
    }

/// Define the unique site ID code if it isn't already
    if (empty($CFG->siteidentifier)) {    // Unique site identification code
        set_config('siteidentifier', random_string(32));
    }

/// Check if the guest user exists.  If not, create one.
    if (! record_exists("user", "username", "guest")) {
        $guest->auth        = "manual";
        $guest->username    = "guest";
        $guest->password    = md5("guest");
        $guest->firstname   = addslashes(get_string("guestuser"));
        $guest->lastname    = " ";
        $guest->email       = "root@localhost";
        $guest->description = addslashes(get_string("guestuserinfo"));
        $guest->confirmed   = 1;
        $guest->lang        = $CFG->lang;
        $guest->timemodified= time();

        if (! $guest->id = insert_record("user", $guest)) {
            notify("Could not create guest user record !!!");
        }
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

/// Check if we are returning from moodle.org registration and if so, we mark that fact to remove reminders

    if (!empty($id)) {
        if ($id == $CFG->siteidentifier) {
            set_config('registered', time());
        }
    }

/// At this point everything is set up and the user is an admin, so print menu

    $stradministration = get_string("administration");
    print_header("$site->shortname: $stradministration","$site->fullname", "$stradministration");
    print_simple_box_start('center', '100%', '', 20);
    print_heading($stradministration);

    if (!empty($CFG->upgrade)) {  // Print notice about extra upgrading that needs to be done
        print_simple_box(get_string("upgrade$CFG->upgrade", "admin",
                                    "$CFG->wwwroot/$CFG->admin/upgrade$CFG->upgrade.php"), "center");
        print_spacer(10,10);
    }

/// If no recently cron run
    $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
    if (time() - $lastcron > 3600 * 24) {
        print_simple_box(get_string('cronwarning', 'admin') , 'center');
    }

/// Alert if we are currently in maintenance mode
    if (file_exists($CFG->dataroot.'/1/maintenance.html')) {
        print_simple_box(get_string('sitemaintenancewarning', 'admin') , 'center');
    }

/// Print slightly annoying registration button every six months   ;-)
/// You can set the "registered" variable to something far in the future 
/// if you really want to prevent this.   eg  9999999999
    if (!isset($CFG->registered) || $CFG->registered < (time() - 3600*24*30*6)) {
        $options = array();
        $options['sesskey'] = $USER->sesskey;
        print_simple_box_start('center');
        echo '<div align="center">';
        print_string('pleaseregister', 'admin');
        print_single_button('register.php', $options, get_string('registration'));
        echo '</div>';
        print_simple_box_end();
    }

    $table->tablealign = "right";
    $table->align = array ("right", "left");
    $table->wrap = array ("nowrap", "nowrap");
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = "40%";

    $configdata  = "<font size=\"+1\">&nbsp;</font><a href=\"config.php\">".get_string("configvariables", 'admin')."</a> - <font size=\"1\">".
                    get_string("adminhelpconfigvariables")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"site.php\">".get_string("sitesettings")."</a> - <font size=\"1\">".
                    get_string("adminhelpsitesettings")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"../theme/index.php\">".get_string("themes")."</a> - <font size=\"1\">".
                    get_string("adminhelpthemes")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"lang.php\">".get_string("language")."</a> - <font size=\"1\">".
                    get_string("adminhelplanguage")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"modules.php\">".get_string("managemodules")."</a> - <font size=\"1\">".
                    get_string("adminhelpmanagemodules")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"blocks.php\">".get_string("manageblocks")."</a> - <font size=\"1\">".
                    get_string("adminhelpmanageblocks")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"filters.php\">".get_string("managefilters")."</a> - <font size=\"1\">".
                    get_string("adminhelpmanagefilters")."</font><br />";
    if (!isset($CFG->disablescheduledbackups)) {
        $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"backup.php\">".get_string("backup")."</a> - <font size=\"1\">".
                        get_string("adminhelpbackup")."</font><br />";
    }
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"editor.php\">". get_string("editorsettings") ."</a> - <font size=\"1\">".
                    get_string("adminhelpeditorsettings")."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"calendar.php\">". get_string('calendarsettings', 'admin') ."</a> - <font size=\"1\">".
                    get_string('helpcalendarsettings', 'admin')."</font><br />";
    $configdata .= "<font size=\"+1\">&nbsp;</font><a href=\"maintenance.php\">". get_string('sitemaintenancemode', 'admin') ."</a> - <font size=\"1\">".
                    get_string('helpsitemaintenance', 'admin')."</font><br />";

    $table->data[] = array("<font size=\"+1\"><b><a href=\"configure.php\">".get_string("configuration")."</a></b></font>",
                            $configdata);


    $userdata = "<font size=\"+1\">&nbsp;</font><a href=\"auth.php?sesskey=$USER->sesskey\">".get_string("authentication")."</a> - <font size=\"1\">".
                 get_string("adminhelpauthentication")."</font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"user.php\">".get_string("edituser")."</a> - <font size=\"1\">".
                 get_string("adminhelpedituser")."</font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"$CFG->wwwroot/$CFG->admin/user.php?newuser=true&amp;sesskey=$USER->sesskey\">".
                 get_string("addnewuser")."</a> - <font size=\"1\">".
                 get_string("adminhelpaddnewuser")."</font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"$CFG->wwwroot/$CFG->admin/uploaduser.php?sesskey=$USER->sesskey\">".
                 get_string("uploadusers")."</a> - <font size=\"1\">".
                 get_string("adminhelpuploadusers")."</font><br />";

    $userdata .= "<hr /><font size=\"+1\">&nbsp;</font><a href=\"enrol.php?sesskey=$USER->sesskey\">".get_string("enrolments")."</a> - <font size=\"1\">".
                 get_string("adminhelpenrolments")."</font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"../course/index.php?edit=off&amp;sesskey=$USER->sesskey\">".get_string("assignstudents")."</a> - <font size=\"1\">".
                 get_string("adminhelpassignstudents")."</font><br />";

    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"../course/index.php?edit=on&amp;sesskey=$USER->sesskey\">".get_string("assignteachers")."</a> - <font size=\"1\">".
                 get_string("adminhelpassignteachers").
                 " <img src=\"../pix/t/user.gif\" height=\"11\" width=\"11\" alt=\"\" /></font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"creators.php?sesskey=$USER->sesskey\">".get_string("assigncreators")."</a> - <font size=\"1\">".
                 get_string("adminhelpassigncreators")."</font><br />";
    $userdata .= "<font size=\"+1\">&nbsp;</font><a href=\"admin.php?sesskey=$USER->sesskey\">".get_string("assignadmins")."</a> - <font size=\"1\">".
                 get_string("adminhelpassignadmins")."</font><br />";

    $table->data[] = array("<font size=\"+1\"><b><a href=\"users.php\">".get_string("users")."</a></b></font>", $userdata);

    $table->data[] = array("<font size=\"+1\"><b><a href=\"../course/index.php?edit=on&amp;sesskey=$USER->sesskey\">".get_string("courses")."</a></b></font>",
                           "<font size=\"+1\">&nbsp;</font>".get_string("adminhelpcourses"));
    $table->data[] = array("<font size=\"+1\"><b><a href=\"../course/log.php?id=$site->id\">".get_string("logs")."</a></b></font>",
                           "<font size=\"+1\">&nbsp;</font>".get_string("adminhelplogs"));
    $table->data[] = array("<font size=\"+1\"><b><a href=\"../files/index.php?id=$site->id\">".get_string("sitefiles")."</a></b></font>",
                           "<font size=\"+1\">&nbsp;</font>".get_string("adminhelpsitefiles"));
    $table->data[] = array("<font size=+1><b><a href=\"mymoodle.php\">".get_string('mymoodle','my')."</a></b>",
                           "<font size=+1>&nbsp;</font>".get_string("adminhelpmymoodle"));
    if (file_exists("$CFG->dirroot/$CFG->admin/$CFG->dbtype")) {
        $table->data[] = array("<font size=\"+1\"><b><a href=\"$CFG->dbtype/frame.php\">".get_string("managedatabase")."</a></b></font>",
                               "<font size=\"+1\">&nbsp;</font>".get_string("adminhelpmanagedatabase"));
    }

    print_table($table);

    //////////////////////////////////////////////////////////////////////////////////////////////////
    ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO REMOVE OR MODIFY THE COPYRIGHT NOTICE BELOW ////
    $copyrighttext = "<a href=\"http://moodle.org/\">Moodle</a> ".
                     "<a href=\"../doc/?frame=release.html\">$CFG->release</a> ($CFG->version)<br />".
                     "Copyright &copy; 1999-2005 Martin Dougiamas<br />".
                     "<a href=\"../doc/?frame=licence.html\">GNU Public License</a>";
    echo "<center><p><font size=\"1\">$copyrighttext</font></p></center>";
    //////////////////////////////////////////////////////////////////////////////////////////////////


    echo '<table width="100%" cellspacing="0"><tr>';
    echo '<td align="center" width="33%">';
    print_single_button($CFG->wwwroot.'/doc/', NULL, get_string('documentation'));
    echo '</td>';

    echo '<td align="center" width="33%">';
    print_single_button('phpinfo.php', NULL, get_string('phpinfo'));
    echo '</td>';

    echo '<td align="center" width="33%">';
    $options = array();
    $options['sesskey'] = $USER->sesskey;
    print_single_button('register.php', $options, get_string('registration'));
    echo '</td></tr></table>';

    print_simple_box_end();

    print_footer($site);

?>
