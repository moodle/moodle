<?php // $Id$

/// Check that config.php exists, if not then call the install script
    if (!file_exists('../config.php')) {
        header('Location: ../install.php');
        die;
    }

/// Check that PHP is of a sufficient version
/// Moved here because older versions do not allow while(@ob_end_clean());
    if (version_compare(phpversion(), "4.3.0") < 0) {
        $phpversion = phpversion();
        echo "Sorry, Moodle requires PHP 4.3.0 or later (currently using version $phpversion)";
        die;
    }

/// Turn off time limits and try to flush everything all the time, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    while(@ob_end_clean()); // ob_end_flush prevents sending of headers


    require_once('../config.php');
    include_once($CFG->dirroot.'/lib/adminlib.php');  // Contains various admin-only functions

    $id             = optional_param('id', '', PARAM_ALPHANUM);
    $confirmupgrade = optional_param('confirmupgrade', 0, PARAM_BOOL);
    $agreelicence = optional_param('agreelicence',0, PARAM_BOOL);


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
            print_simple_box_start('center');
            echo text_to_html(get_string("gpl"));
            print_simple_box_end();
            echo "<br />";
            notice_yesno(get_string("doyouagree"), "index.php?agreelicence=true",
                                                   "http://docs.moodle.org/en/License");
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

        if (!set_config("version", $version)) {
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
        notice(get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/en/Release_Notes'), 'index.php');
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

/// Send $CFG->unicodedb to DB to have it available for next requests
    set_config('unicodedb', $CFG->unicodedb);

/// If any new configurations were found then send to the config page to check

    if (!empty($configchange)) {
        redirect("config.php?installing=1");
    }

/// Find and check all main modules and load them up or upgrade them if necessary
    upgrade_activity_modules("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all questiontype plugins and upgrade if necessary
    // It is important that this is done AFTER the quiz module has been upgraded
    upgrade_plugins('qtype', 'question/type', "$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade backup/restore system if necessary
    require_once("$CFG->dirroot/backup/lib.php");
    upgrade_backup_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Upgrade blocks system if necessary
    require_once("$CFG->dirroot/lib/blocklib.php");
    upgrade_blocks_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all blocks and load (or upgrade them if necessary)
    upgrade_blocks_plugins("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check all enrolment plugins and upgrade if necessary
    upgrade_plugins('enrol', 'enrol', "$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards

/// Check for local database customisations
    require_once("$CFG->dirroot/lib/locallib.php");
    upgrade_local_db("$CFG->wwwroot/$CFG->admin/index.php");  // Return here afterwards


/// Set up the overall site name etc.
    if (! $site = get_site()) {
        redirect("site.php");
    }

/// Define the unique site ID code if it isn't already
    if (empty($CFG->siteidentifier)) {    // Unique site identification code
        set_config('siteidentifier', random_string(32).$_SERVER['HTTP_HOST']);
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

/// Deprecated database! Warning!!
    if (!empty($CFG->migrated_to_new_db)) {
        print_simple_box_start('center','60%');
        print_string('dbmigrationdeprecateddb','admin');
        print_simple_box_end();
    }

    if (!empty($CFG->upgrade)) {  // Print notice about extra upgrading that needs to be done
        print_simple_box(get_string("upgrade$CFG->upgrade", "admin",
                                    "$CFG->wwwroot/$CFG->admin/upgrade$CFG->upgrade.php"), "center", '60%');
        print_spacer(10,10);
    }

    if (ini_get_bool('register_globals') && !ini_get_bool('magic_quotes_gpc')) {
        print_simple_box(get_string('globalsquoteswarning', 'admin'), 'center', '60%');
    }

    if (is_dataroot_insecure()) {
        print_simple_box(get_string('datarootsecuritywarning', 'admin', $CFG->dataroot), 'center', '60%');
    }

/// If no recently cron run
    $lastcron = get_field_sql('SELECT max(lastcron) FROM ' . $CFG->prefix . 'modules');
    if (time() - $lastcron > 3600 * 24) {
        $strinstallation = get_string('installation', 'install');
        $helpbutton = helpbutton('install', $strinstallation, 'moodle', true, false, '', true);
        print_simple_box(get_string('cronwarning', 'admin')."&nbsp;".$helpbutton, 'center', '60%');
    }

/// Alert if we are currently in maintenance mode
    if (file_exists($CFG->dataroot.'/1/maintenance.html')) {
        print_simple_box(get_string('sitemaintenancewarning', 'admin') , 'center', '60%');
    }

/// Alert if we are currently in maintenance mode
    if (empty($CFG->unicodedb)) {
        print_simple_box(get_string('unicodeupgradenotice', 'admin') , 'center', '60%');
    }

/// Print slightly annoying registration button every six months   ;-)
/// You can set the "registered" variable to something far in the future 
/// if you really want to prevent this.   eg  9999999999
    if (!isset($CFG->registered) || $CFG->registered < (time() - 3600*24*30*6)) {
        $options = array();
        $options['sesskey'] = $USER->sesskey;
        print_simple_box_start('center','60%');
        echo '<div align="center">';
        print_string('pleaseregister', 'admin');
        print_single_button('register.php', $options, get_string('registration'));
        echo '</div>';
        print_simple_box_end();
    }

    $table->tablealign = "center";
    $table->align = array ("right", "left");
    $table->wrap = array ("nowrap", "nowrap");
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = '40%';

    $configdata  = '<div class="adminlink"><a href="config.php">'.get_string('configvariables', 'admin').
                   '</a> - <span class="explanation">'.get_string('adminhelpconfigvariables').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="site.php">'.get_string('sitesettings').
                   '</a> - <span class="explanation">'.get_string('adminhelpsitesettings').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="../theme/index.php">'.get_string('themes').
                   '</a> - <span class="explanation">'.get_string('adminhelpthemes').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="lang.php">'.get_string('language').
                   '</a> - <span class="explanation">'.get_string('adminhelplanguage').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="modules.php">'.get_string('managemodules').
                   '</a> - <span class="explanation">'.get_string('adminhelpmanagemodules').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="blocks.php">'.get_string('manageblocks').
                   '</a> - <span class="explanation">'.get_string('adminhelpmanageblocks').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="filters.php">'.get_string('managefilters').
                   '</a> - <span class="explanation">'.get_string('adminhelpmanagefilters').'</span></div>';
    if (!isset($CFG->disablescheduledbackups)) {
        $configdata .= '<div class="adminlink"><a href="backup.php">'.get_string('backup').
                       '</a> - <span class="explanation">'.get_string('adminhelpbackup').'</span></div>';
    }
    $configdata .= '<div class="adminlink"><a href="editor.php">'.get_string('editorsettings').
                   '</a> - <span class="explanation">'.get_string('adminhelpeditorsettings').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="calendar.php">'.get_string('calendarsettings', 'admin').
                   '</a> - <span class="explanation">'.get_string('helpcalendarsettings', 'admin').'</span></div>';
    $configdata .= '<div class="adminlink"><a href="maintenance.php">'.get_string('sitemaintenancemode', 'admin').
                   '</a> - <span class="explanation">'.get_string('helpsitemaintenance', 'admin').'</span></div>';

    $table->data[] = array('<strong><a href="configure.php">'.get_string('configuration').'</a></strong>', $configdata);


    $userdata =  '<div class="adminlink"><a href="auth.php?sesskey='.$USER->sesskey.'">'.get_string("authentication").
                 '</a> - <span class="explanation">'.get_string('adminhelpauthentication').'</span></div>';
    $userdata .= '<div class="adminlink"><a href="user.php">'.get_string('edituser').
                 '</a> - <span class="explanation">'.get_string('adminhelpedituser').'</span></div>';
    $userdata .= '<div class="adminlink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/user.php?newuser=true&amp;sesskey='.$USER->sesskey.'">'.
                 get_string('addnewuser').'</a> - <span class="explanation">'.get_string('adminhelpaddnewuser').'</span></div>';
    $userdata .= '<div class="adminlink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/uploaduser.php?sesskey='.$USER->sesskey.'">'.
                 get_string('uploadusers').'</a> - <span class="explanation">'.get_string('adminhelpuploadusers').'</span></div>';

    $table->data[] = array('<strong><a href="users.php">'.get_string('users').'</a></strong>', $userdata);

    $coursedata = '<div class="adminlink"><a href="../course/index.php?edit=on&amp;sesskey='.$USER->sesskey.'">'.get_string('managecourses').
                 '</a> - <span class="explanation">'.get_string('adminhelpcourses').'</span></div>';
    $coursedata .= '<div class="adminlink"><a href="enrol.php?sesskey='.$USER->sesskey.'">'.get_string('enrolmentplugins').
                 '</a> - <span class="explanation">'.get_string('adminhelpenrolments').'</span></div>';
    $coursedata .= '<div class="adminlink"><a href="../course/index.php?edit=off&amp;sesskey='.$USER->sesskey.'">'.
                 get_string('assignstudents').'</a> - <span class="explanation">'.get_string('adminhelpassignstudents').'</span></div>';
    $coursedata .= '<div class="adminlink"><a href="../course/index.php?edit=on&amp;sesskey='.$USER->sesskey.'">'.
                 get_string('assignteachers').'</a> - <span class="explanation">'.get_string('adminhelpassignteachers').
                 ' <img src="../pix/t/user.gif" height="11" width="11" alt="" /></span></div>';
    $coursedata .= '<div class="adminlink"><a href="creators.php?sesskey='.$USER->sesskey.'">'.get_string('assigncreators').
                 '</a> - <span class="explanation">'.get_string('adminhelpassigncreators').'</span></div>';
    $coursedata .= '<div class="adminlink"><a href="admin.php?sesskey='.$USER->sesskey.'">'.get_string('assignadmins').
                 '</a> - <span class="explanation">'.get_string('adminhelpassignadmins').'</span></div>';

    $table->data[] = array('<strong><a href="courses.php">'.get_string('courses').'</a></strong>', $coursedata);

    $miscdata = '<div class="adminlink"><a href="../files/index.php?id='.$site->id.'">'.get_string('sitefiles').
                 '</a> - <span class="explanation">'.get_string('adminhelpsitefiles').'</span></div>';
    $miscdata .= '<div class="adminlink"><a href="stickyblocks.php">'.get_string('stickyblocks','admin').
                 '</a> - <span class="explanation">'.get_string('adminhelpstickyblocks').'</span></div>';
    $miscdata .= '<div class="adminlink"><a href="report.php">'.get_string('reports').
                 '</a> - <span class="explanation">'.get_string('adminhelpreports').'</span></div>';
//to be enabled later
/*    $miscdata .= '<div class="adminlink"><a href="health.php">'.get_string('healthcenter').
                 '</a> - <span class="explanation">'.get_string('adminhelphealthcenter').'</span></div>';*/
    $miscdata .= '<div class="adminlink"><a href="environment.php">'.get_string('environment', 'admin').
                 '</a> - <span class="explanation">'.get_string('adminhelpenvironment').'</span></div>';
/// Optional stuff
    if (file_exists("$CFG->dirroot/$CFG->admin/$CFG->dbtype")) {
        $miscdata .= '<div class="adminlink"><a href="'.$CFG->dbtype.'/frame.php">'.get_string('managedatabase').
        			 '</a> - <span class="explanation">'.get_string('adminhelpmanagedatabase').'</span></div>';
    }

    $table->data[] = array('<strong><a href="misc.php">'.get_string('miscellaneous').'</a></strong>', $miscdata);


/// Hooks for Matt Oquists contrib code for repositories and portfolio.  
/// The eventual official versions may not look like this
    if (isset($CFG->portfolio) && file_exists("$CFG->dirroot/$CFG->portfolio")) {
                $table->data[] = array("<strong><a href=\"../portfolio/\">".get_string('portfolio','portfolio').'</a></
trong>',
                            '<div class="explanation">'.get_string('adminhelpportfolio','portfolio').'</div>');
    }

    if (isset($CFG->repo) && file_exists("$CFG->dirroot/$CFG->repo")) {
            $table->data[] = array("<strong><a href=\"../repository/?repoid=1&action=list\">".get_string('repository','
epository').'</a></strong>',
                            '<div class="explanation">'.get_string('adminhelprepository','repository').'</div>');
    }



    print_table($table);

    //////////////////////////////////////////////////////////////////////////////////////////////////
    ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO REMOVE OR MODIFY THE COPYRIGHT NOTICE BELOW ////
    $copyrighttext = '<a href="http://moodle.org/">Moodle</a> '.
                     '<a href="http://docs.moodle.org/en/Release">'.$CFG->release.'</a> ('.$CFG->version.')<br />'.
                     'Copyright &copy; 1999 onwards, Martin Dougiamas<br />'.
                     '<a href="http://docs.moodle.org/en/License">GNU Public License</a>';
    echo '<p class="copyright">'.$copyrighttext.'</p>';
    //////////////////////////////////////////////////////////////////////////////////////////////////


    echo '<div align="center">';
    $options = array();
    $options['sesskey'] = $USER->sesskey;
    print_single_button('register.php', $options, get_string('registration'));
    echo '</div>';


    print_simple_box_end();


    print_footer($site);

?>
