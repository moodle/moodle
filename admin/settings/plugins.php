<?php

/*
 * Please note that is file is always loaded last - it means that you can inject entries into other categories too.
 */

if ($hassiteconfig) {
    $ADMIN->add('modules', new admin_category('modsettings', get_string('activitymodules')));
    $ADMIN->add('modsettings', new admin_page_managemods());
    $modules = $DB->get_records('modules', array(), "name ASC");
    foreach ($modules as $module) {
        $modulename = $module->name;
        if (!file_exists("$CFG->dirroot/mod/$modulename/lib.php")) {
            continue;
        }
        $strmodulename = get_string('modulename', 'mod_'.$modulename);
        if (file_exists($CFG->dirroot.'/mod/'.$modulename.'/settings.php')) {
            // do not show disabled modules in tree, keep only settings link on manage page
            $settings = new admin_settingpage('modsetting'.$modulename, $strmodulename, 'moodle/site:config', !$module->visible);
            include($CFG->dirroot.'/mod/'.$modulename.'/settings.php');
            if ($settings) {
                $ADMIN->add('modsettings', $settings);
            }
        }
    }

    // hidden script for converting journals to online assignments (or something like that) linked from elsewhere
    $ADMIN->add('modsettings', new admin_externalpage('oacleanup', 'Online Assignment Cleanup', $CFG->wwwroot.'/'.$CFG->admin.'/oacleanup.php', 'moodle/site:config', true));

    $ADMIN->add('modules', new admin_category('blocksettings', get_string('blocks')));
    $ADMIN->add('blocksettings', new admin_page_manageblocks());
    $blocks = $DB->get_records('block', array(), "name ASC");
    foreach ($blocks as $block) {
        $blockname = $block->name;
        if (!file_exists("$CFG->dirroot/blocks/$blockname/block_$blockname.php")) {
            continue;
        }
        $strblockname = get_string('pluginname', 'block_'.$blockname);
        if (file_exists($CFG->dirroot.'/blocks/'.$blockname.'/settings.php')) {
            $settings = new admin_settingpage('blocksetting'.$blockname, $strblockname, 'moodle/site:config', !$block->visible);
            include($CFG->dirroot.'/blocks/'.$blockname.'/settings.php');
            if ($settings) {
                $ADMIN->add('blocksettings', $settings);
            }
        }
    }

    // authentication plugins
    $ADMIN->add('modules', new admin_category('authsettings', get_string('authentication', 'admin')));

    $temp = new admin_settingpage('manageauths', get_string('authsettings', 'admin'));
    $temp->add(new admin_setting_manageauths());
    $temp->add(new admin_setting_heading('manageauthscommonheading', get_string('commonsettings', 'admin'), ''));
    $temp->add(new admin_setting_special_registerauth());
    $temp->add(new admin_setting_configselect('guestloginbutton', get_string('guestloginbutton', 'auth'),
                                              get_string('showguestlogin', 'auth'), '1', array('0'=>get_string('hide'), '1'=>get_string('show'))));
    $temp->add(new admin_setting_configtext('alternateloginurl', get_string('alternateloginurl', 'auth'),
                                            get_string('alternatelogin', 'auth', htmlspecialchars(get_login_url())), ''));
    $temp->add(new admin_setting_configtext('forgottenpasswordurl', get_string('forgottenpasswordurl', 'auth'),
                                            get_string('forgottenpassword', 'auth'), ''));
    $temp->add(new admin_setting_confightmleditor('auth_instructions', get_string('instructions', 'auth'),
                                                get_string('authinstructions', 'auth'), ''));
    $temp->add(new admin_setting_configtext('allowemailaddresses', get_string('allowemailaddresses', 'admin'), get_string('configallowemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('denyemailaddresses', get_string('denyemailaddresses', 'admin'), get_string('configdenyemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configcheckbox('verifychangedemail', get_string('verifychangedemail', 'admin'), get_string('configverifychangedemail', 'admin'), 1));

    $temp->add(new admin_setting_configtext('recaptchapublickey', get_string('recaptchapublickey', 'admin'), get_string('configrecaptchapublickey', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('recaptchaprivatekey', get_string('recaptchaprivatekey', 'admin'), get_string('configrecaptchaprivatekey', 'admin'), '', PARAM_NOTAGS));
    $ADMIN->add('authsettings', $temp);


    $auths = get_plugin_list('auth');
    $authsenabled = get_enabled_auth_plugins();
    foreach ($auths as $authname => $authdir) {
        $strauthname = get_string('pluginname', "auth_{$authname}");
        // do not show disabled auths in tree, keep only settings link on manage page
        $enabled = in_array($authname, $authsenabled);
        if (file_exists($authdir.'/settings.php')) {
            // TODO: finish implementation of common settings - locking, etc.
            $settings = new admin_settingpage('authsetting'.$authname, $strauthname, 'moodle/site:config', !$enabled);
            include($authdir.'/settings.php');
            if ($settings) {
                $ADMIN->add('authsettings', $settings);
            }

        } else {
            $ADMIN->add('authsettings', new admin_externalpage('authsetting'.$authname, $strauthname, "$CFG->wwwroot/$CFG->admin/auth_config.php?auth=$authname", 'moodle/site:config', !$enabled));
        }
    }


    // Enrolment plugins
    $ADMIN->add('modules', new admin_category('enrolments', get_string('enrolments', 'enrol')));
    $temp = new admin_settingpage('manageenrols', get_string('manageenrols', 'enrol'));
    $temp->add(new admin_setting_manageenrols());
    if (empty($CFG->enrol_plugins_enabled)) {
        $enabled = array();
    } else {
        $enabled = explode(',', $CFG->enrol_plugins_enabled);
    }
    $enrols = get_plugin_list('enrol');
    $ADMIN->add('enrolments', $temp);
    foreach($enrols as $enrol=>$enrolpath) {
        if (!file_exists("$enrolpath/settings.php")) {
            continue;
        }

        $settings = new admin_settingpage('enrolsettings'.$enrol, get_string('pluginname', 'enrol_'.$enrol), 'moodle/site:config', !in_array($enrol, $enabled));
        // settings.php may create a subcategory or unset the settings completely
        include("$enrolpath/settings.php");
        if ($settings) {
            $ADMIN->add('enrolments', $settings);
        }

    }
    unset($enabled);
    unset($enrols);


/// Editor plugins
    $ADMIN->add('modules', new admin_category('editorsettings', get_string('editors', 'editor')));
    $temp = new admin_settingpage('manageeditors', get_string('editorsettings', 'editor'));
    $temp->add(new admin_setting_manageeditors());
    $htmleditors = editors_get_available();
    $ADMIN->add('editorsettings', $temp);

    $editors_available = editors_get_available();
    foreach ($editors_available as $editor=>$editorstr) {
        if (file_exists($CFG->dirroot . '/lib/editor/'.$editor.'/settings.php')) {
            $settings = new admin_settingpage('editorsettings'.$editor, get_string('pluginname', 'editor_'.$editor), 'moodle/site:config');
            // settings.php may create a subcategory or unset the settings completely
            include($CFG->dirroot . '/lib/editor/'.$editor.'/settings.php');
            if ($settings) {
                $ADMIN->add('editorsettings', $settings);
            }
        }
    }

/// License types
    $ADMIN->add('modules', new admin_category('licensesettings', get_string('licenses')));
    $temp = new admin_settingpage('managelicenses', get_string('managelicenses', 'admin'));

    require_once($CFG->libdir . '/licenselib.php');
    $licenses = array();
    $array = explode(',', $CFG->licenses);
    foreach ($array as $value) {
        $licenses[$value] = get_string($value, 'license');
    }
    $temp->add(new admin_setting_configselect('sitedefaultlicense', get_string('configsitedefaultlicense','admin'), get_string('configsitedefaultlicensehelp','admin'), 'allrightsreserved', $licenses));
    $temp->add(new admin_setting_managelicenses());
    $ADMIN->add('licensesettings', $temp);

/// Filter plugins
    $ADMIN->add('modules', new admin_category('filtersettings', get_string('managefilters')));

    $ADMIN->add('filtersettings', new admin_page_managefilters());

    // "filtersettings" settingpage
    $temp = new admin_settingpage('commonfiltersettings', get_string('commonfiltersettings', 'admin'));
    if ($ADMIN->fulltree) {
        $cachetimes = array(
            604800 => get_string('numdays','',7),
            86400 => get_string('numdays','',1),
            43200 => get_string('numhours','',12),
            10800 => get_string('numhours','',3),
            7200 => get_string('numhours','',2),
            3600 => get_string('numhours','',1),
            2700 => get_string('numminutes','',45),
            1800 => get_string('numminutes','',30),
            900 => get_string('numminutes','',15),
            600 => get_string('numminutes','',10),
            540 => get_string('numminutes','',9),
            480 => get_string('numminutes','',8),
            420 => get_string('numminutes','',7),
            360 => get_string('numminutes','',6),
            300 => get_string('numminutes','',5),
            240 => get_string('numminutes','',4),
            180 => get_string('numminutes','',3),
            120 => get_string('numminutes','',2),
            60 => get_string('numminutes','',1),
            30 => get_string('numseconds','',30),
            0 => get_string('no')
        );
        $items = array();
        $items[] = new admin_setting_configselect('cachetext', get_string('cachetext', 'admin'), get_string('configcachetext', 'admin'), 60, $cachetimes);
        $items[] = new admin_setting_configselect('filteruploadedfiles', get_string('filteruploadedfiles', 'admin'), get_string('configfilteruploadedfiles', 'admin'), 0,
                array('0' => get_string('none'), '1' => get_string('allfiles'), '2' => get_string('htmlfilesonly')));
        $items[] = new admin_setting_configcheckbox('filtermatchoneperpage', get_string('filtermatchoneperpage', 'admin'), get_string('configfiltermatchoneperpage', 'admin'), 0);
        $items[] = new admin_setting_configcheckbox('filtermatchonepertext', get_string('filtermatchonepertext', 'admin'), get_string('configfiltermatchonepertext', 'admin'), 0);
        foreach ($items as $item) {
            $item->set_updatedcallback('reset_text_filters_cache');
            $temp->add($item);
        }
    }
    $ADMIN->add('filtersettings', $temp);

    $activefilters = filter_get_globally_enabled();
    $filternames = filter_get_all_installed();
    foreach ($filternames as $filterpath => $strfiltername) {
        if (file_exists("$CFG->dirroot/$filterpath/filtersettings.php")) {
            $settings = new admin_settingpage('filtersetting'.str_replace('/', '', $filterpath),
                    $strfiltername, 'moodle/site:config', !isset($activefilters[$filterpath]));
            include("$CFG->dirroot/$filterpath/filtersettings.php");
            if ($settings) {
                $ADMIN->add('filtersettings', $settings);
            }
        }
    }


    //== Portfolio settings ==
    require_once($CFG->libdir. '/portfoliolib.php');
    $catname = get_string('portfolios', 'portfolio');
    $manage = get_string('manageportfolios', 'portfolio');
    $url = "$CFG->wwwroot/$CFG->admin/portfolio.php";

    $ADMIN->add('modules', new admin_category('portfoliosettings', $catname, empty($CFG->enableportfolios)));

    // Add manage page (with table)
    $temp = new admin_page_manageportfolios();
    $ADMIN->add('portfoliosettings', $temp);

    // Add common settings page
    $temp = new admin_settingpage('manageportfolioscommon', get_string('commonportfoliosettings', 'portfolio'));
    $temp->add(new admin_setting_heading('manageportfolioscommon', '', get_string('commonsettingsdesc', 'portfolio')));
    $fileinfo = portfolio_filesize_info(); // make sure this is defined in one place since its used inside portfolio too to detect insane settings
    $fileoptions = $fileinfo['options'];
    $temp->add(new admin_setting_configselect(
        'portfolio_moderate_filesize_threshold',
        get_string('moderatefilesizethreshold', 'portfolio'),
        get_string('moderatefilesizethresholddesc', 'portfolio'),
        $fileinfo['moderate'], $fileoptions));
    $temp->add(new admin_setting_configselect(
        'portfolio_high_filesize_threshold',
        get_string('highfilesizethreshold', 'portfolio'),
        get_string('highfilesizethresholddesc', 'portfolio'),
        $fileinfo['high'], $fileoptions));

    $temp->add(new admin_setting_configtext(
        'portfolio_moderate_db_threshold',
        get_string('moderatedbsizethreshold', 'portfolio'),
        get_string('moderatedbsizethresholddesc', 'portfolio'),
        20, PARAM_INTEGER, 3));

    $temp->add(new admin_setting_configtext(
        'portfolio_high_db_threshold',
        get_string('highdbsizethreshold', 'portfolio'),
        get_string('highdbsizethresholddesc', 'portfolio'),
        50, PARAM_INTEGER, 3));

    $ADMIN->add('portfoliosettings', $temp);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfolionew', get_string('addnewportfolio', 'portfolio'), $url, 'moodle/site:config', true), '', $url);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfoliodelete', get_string('deleteportfolio', 'portfolio'), $url, 'moodle/site:config', true), '', $url);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfoliocontroller', get_string('manageportfolios', 'portfolio'), $url, 'moodle/site:config', true), '', $url);

    foreach (portfolio_instances(false, false) as $portfolio) {
        require_once($CFG->dirroot . '/portfolio/' . $portfolio->get('plugin') . '/lib.php');
        $classname = 'portfolio_plugin_' . $portfolio->get('plugin');
        $ADMIN->add(
            'portfoliosettings',
            new admin_externalpage(
                'portfoliosettings' . $portfolio->get('id'),
                $portfolio->get('name'),
                $url . '?action=edit&pf=' . $portfolio->get('id'),
                'moodle/site:config'
            ),
            $portfolio->get('name'),
            $url . '?action=edit&pf=' . $portfolio->get('id')
        );
    }

    // repository setting
    require_once("$CFG->dirroot/repository/lib.php");
    $catname =get_string('repositories', 'repository');
    $managerepo = get_string('manage', 'repository');
    $url = $CFG->wwwroot.'/'.$CFG->admin.'/repository.php';
    $ADMIN->add('modules', new admin_category('repositorysettings', $catname));

    // Add main page (with table)
    $temp = new admin_page_managerepositories();
    $ADMIN->add('repositorysettings', $temp);

    // Add common settings page
    $temp = new admin_settingpage('managerepositoriescommon', get_string('commonrepositorysettings', 'repository'));
    $temp->add(new admin_setting_configtext('repositorycacheexpire', get_string('cacheexpire', 'repository'), get_string('configcacheexpire', 'repository'), 120));
    $temp->add(new admin_setting_configcheckbox('repositoryallowexternallinks', get_string('allowexternallinks', 'repository'), get_string('configallowexternallinks', 'repository'), 1));
    $temp->add(new admin_setting_configcheckbox('legacyfilesinnewcourses', get_string('legacyfilesinnewcourses', 'admin'), get_string('legacyfilesinnewcourses_help', 'admin'), 0));
    $ADMIN->add('repositorysettings', $temp);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorynew',
        get_string('addplugin', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorydelete',
        get_string('deleterepository', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorycontroller',
        get_string('manage', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositoryinstancenew',
        get_string('createrepository', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositoryinstanceedit',
        get_string('editrepositoryinstance', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    foreach (repository::get_types() as $repositorytype) {
      //display setup page for plugins with: general options or multiple instances (e.g. has instance config)
      $typeoptionnames = repository::static_function($repositorytype->get_typename(), 'get_type_option_names');
      $instanceoptionnames = repository::static_function($repositorytype->get_typename(), 'get_instance_option_names');
      if (!empty($typeoptionnames) || !empty($instanceoptionnames)) {
            $ADMIN->add('repositorysettings',
                new admin_externalpage('repositorysettings'.$repositorytype->get_typename(),
                        $repositorytype->get_readablename(),
                        $url . '?action=edit&repos=' . $repositorytype->get_typename()),
                        'moodle/site:config');
        }
    }
}

/// Web services
    $ADMIN->add('modules', new admin_category('webservicesettings', get_string('webservices', 'webservice')));
    /// overview page
    $temp = new admin_settingpage('webservicesoverview', get_string('webservicesoverview', 'webservice'));
    $temp->add(new admin_setting_webservicesoverview());
    $ADMIN->add('webservicesettings', $temp);
    /// manage service
    $temp = new admin_settingpage('externalservices', get_string('externalservices', 'webservice'));
    $temp->add(new admin_setting_heading('manageserviceshelpexplaination', get_string('information', 'webservice'), get_string('servicehelpexplanation', 'webservice')));
    $temp->add(new admin_setting_manageexternalservices());
    $ADMIN->add('webservicesettings', $temp);
    $ADMIN->add('webservicesettings', new admin_externalpage('externalservice', get_string('editaservice', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalservicefunctions', get_string('externalservicefunctions', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_functions.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalserviceusers', get_string('externalserviceusers', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_users.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalserviceusersettings', get_string('serviceusersettings', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_user_settings.php", 'moodle/site:config', true));
    /// manage protocol page link
    $temp = new admin_settingpage('webserviceprotocols', get_string('manageprotocols', 'webservice'));
    $temp->add(new admin_setting_managewebserviceprotocols());
    if (empty($CFG->enablewebservices)) {
        $temp->add(new admin_setting_heading('webservicesaredisabled', '', get_string('disabledwarning', 'webservice')));
    }

    // We cannot use $OUTPUT this early, doing so means that we lose the ability
    // to set the page layout on all admin pages.
    // $wsdoclink = $OUTPUT->doc_link('How_to_get_a_security_key');
    $url = new moodle_url(get_docs_url('How_to_get_a_security_key'));
    $wsdoclink = html_writer::tag('a', get_string('supplyinfo'),array('href'=>$url));
    $temp->add(new admin_setting_configcheckbox('enablewsdocumentation', get_string('enablewsdocumentation',
                        'admin'), get_string('configenablewsdocumentation', 'admin', $wsdoclink), false));
    $ADMIN->add('webservicesettings', $temp);
    /// links to protocol pages
    $webservices_available = get_plugin_list('webservice');
    $active_webservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
    foreach ($webservices_available as $webservice => $location) {
        if (file_exists("$location/settings.php")) {
            $name = get_string('pluginname', 'webservice_'.$webservice);
            $settings = new admin_settingpage('webservicesetting'.$webservice, $name, 'moodle/site:config', !in_array($webservice, $active_webservices) or empty($CFG->enablewebservices));
            include("$location/settings.php");
            if ($settings) {
                $ADMIN->add('webservicesettings', $settings);
            }
        }
    }
    /// manage token page link
    $ADMIN->add('webservicesettings', new admin_externalpage('addwebservicetoken', get_string('managetokens', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/tokens.php", 'moodle/site:config', true));
    $temp = new admin_settingpage('webservicetokens', get_string('managetokens', 'webservice'));
    $temp->add(new admin_setting_managewebservicetokens());
    if (empty($CFG->enablewebservices)) {
        $temp->add(new admin_setting_heading('webservicesaredisabled', '', get_string('disabledwarning', 'webservice')));
    }
    $ADMIN->add('webservicesettings', $temp);

// Question type settings
if ($hassiteconfig || has_capability('moodle/question:config', $systemcontext)) {
    // Question type settings.
    $ADMIN->add('modules', new admin_category('qtypesettings', get_string('questiontypes', 'admin')));
    $ADMIN->add('qtypesettings', new admin_page_manageqtypes());
    $qtypes = get_plugin_list('qtype');
    foreach ($qtypes as $qtype => $path) {
        $settingsfile = $path . '/settings.php';
        if (file_exists($settingsfile)) {
            $settings = new admin_settingpage('qtypesetting' . $qtype,
                    get_string('pluginname', 'qtype_' . $qtype), 'moodle/question:config');
            include($settingsfile);
            if ($settings) {
                $ADMIN->add('qtypesettings', $settings);
            }
        }
    }
}

// Plagiarism plugin settings
if ($hassiteconfig && !empty($CFG->enableplagiarism)) {
    $ADMIN->add('modules', new admin_category('plagiarism', get_string('plagiarism', 'plagiarism')));
    $temp = new admin_settingpage('plagiarismsettings', get_string('plagiarismsettings', 'plagiarism'));
    $temp->add(new admin_setting_manageplagiarism());
    $ADMIN->add('plagiarism', $temp);
    foreach (get_plugin_list('plagiarism') as $plugin => $plugindir) {
        if (file_exists($plugindir.'/settings.php')) {
            $ADMIN->add('plagiarism', new admin_externalpage('plagiarism'.$plugin, get_string($plugin, 'plagiarism_'.$plugin), "$CFG->wwwroot/plagiarism/$plugin/settings.php", 'moodle/site:config'));
        }
    }
}
$ADMIN->add('reports', new admin_externalpage('comments', get_string('comments'), $CFG->wwwroot.'/comment/', 'moodle/site:viewreports'));

// Course reports settings
if ($hassiteconfig) {
    $pages = array();
    foreach (get_plugin_list('coursereport') as $report => $path) {
        $file = $CFG->dirroot . '/course/report/' . $report . '/settings.php';
        if (file_exists($file)) {
            $settings = new admin_settingpage('coursereport' . $report,
                    get_string('pluginname', 'coursereport_' . $report), 'moodle/site:config');
            // settings.php may create a subcategory or unset the settings completely
            include($file);
            if ($settings) {
                $pages[] = $settings;
            }
        }
    }
    if (!empty($pages)) {
        $ADMIN->add('modules', new admin_category('coursereports', get_string('coursereports')));
        foreach ($pages as $page) {
            $ADMIN->add('coursereports', $page);
        }
    }
}

// Now add reports
foreach (get_plugin_list('report') as $plugin => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
        continue;
    }

    $index_path = "$plugindir/index.php";
    if (!file_exists($index_path)) {
        continue;
    }
    // old style 3rd party plugin without settings.php
    $www_path = "$CFG->wwwroot/$CFG->admin/report/$plugin/index.php";
    $reportname = get_string($plugin, 'report_' . $plugin);
    $ADMIN->add('reports', new admin_externalpage('report'.$plugin, $reportname, $www_path, 'moodle/site:viewreports'));
}


/// Add all local plugins - must be always last!
if ($hassiteconfig) {
    $ADMIN->add('modules', new admin_category('localplugins', get_string('localplugins')));
    $ADMIN->add('localplugins', new admin_externalpage('managelocalplugins', get_string('localpluginsmanage'),
                                                        $CFG->wwwroot . '/' . $CFG->admin . '/localplugins.php'));
}

foreach (get_plugin_list('local') as $plugin => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
        continue;
    }
}
