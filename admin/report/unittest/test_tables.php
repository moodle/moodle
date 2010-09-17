<?php

die;die;die;
/*
    if (empty($CFG->unittestprefix)) {
        die;
    }

    $CFG->xmlstrictheaders = false;

    // extra security
    session_write_close();

    $return_url = "$CFG->wwwroot/$CFG->admin/report/unittest/test_tables.php";

    // Temporarily override $DB and $CFG for a fresh install on the unit test prefix

    $real_cfg = $CFG;

    $CFG = new stdClass();
    $CFG->dbhost              = $real_cfg->dbhost;
    $CFG->dbtype              = $real_cfg->dbtype;
    $CFG->dblibrary           = $real_cfg->dblibrary;
    $CFG->dbuser              = $real_cfg->dbuser;
    $CFG->dbpass              = $real_cfg->dbpass;
    $CFG->dbname              = $real_cfg->dbname;
    $CFG->unittestprefix      = $real_cfg->unittestprefix;
    $CFG->wwwroot             = $real_cfg->wwwroot;
    $CFG->dirroot             = $real_cfg->dirroot;
    $CFG->libdir              = $real_cfg->libdir;
    $CFG->dataroot            = $real_cfg->dataroot;
    $CFG->admin               = $real_cfg->admin;
    $CFG->release             = $real_cfg->release;
    $CFG->version             = $real_cfg->version;
    $CFG->config_php_settings = $real_cfg->config_php_settings;
    $CFG->debug               = 0;

    $DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
    $DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->unittestprefix);

    $test_tables = $DB->get_tables();

    include("$CFG->dirroot/version.php");       // defines $version and $release

    /// Check if the main tables have been installed yet or not.
    if ($test_tables = $DB->get_tables() ) {    // No tables yet at all.
        //TODO: make sure these are test tables & delte all these tables
            $manager = $DB->get_manager();
            foreach ($test_tables as $table) {
                $xmldbtable = new xmldb_table($table);
                $manager->drop_table($xmldbtable);
            }
    }

/// return to original debugging level

    $DB->get_manager()->install_from_xmldb_file("$CFG->libdir/db/install.xml");

/// set all core default records and default settings
    require_once("$CFG->libdir/db/install.php");
    xmldb_main_install($version);

/// Continue with the instalation

    // Install the roles system.
    moodle_install_roles();

    // Install core event handlers
    events_update_definition();

    // Install core message providers
    message_update_providers();
    message_update_providers('message');

    // Write default settings unconditionally (i.e. even if a setting is already set, overwrite it)
    admin_apply_default_settings(NULL, true);


/// upgrade all plugins types
    $plugintypes = get_plugin_types();
    foreach ($plugintypes as $type => $location) {
        upgrade_plugins($type);
    }

/// just make sure upgrade logging is properly terminated
    upgrade_finished();

/// make sure admin user is created - this is the last step because we need
/// session to be working properly in order to edit admin account
    create_admin_user();


    redirect('index.php');
*/