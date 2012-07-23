<?php

/*
 * Please note that is file is always loaded last - it means that you can inject entries into other categories too.
 */

if ($hassiteconfig) {
    $ADMIN->add('modules', new admin_page_pluginsoverview());
    $ADMIN->add('modules', new admin_category('modsettings', new lang_string('activitymodules')));
    $ADMIN->add('modsettings', new admin_page_managemods());
    $modules = $DB->get_records('modules', array(), "name ASC");
    foreach ($modules as $module) {
        $modulename = $module->name;
        if (!file_exists("$CFG->dirroot/mod/$modulename/lib.php")) {
            continue;
        }
        $strmodulename = new lang_string('modulename', 'mod_'.$modulename);
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

    $ADMIN->add('modules', new admin_category('blocksettings', new lang_string('blocks')));
    $ADMIN->add('blocksettings', new admin_page_manageblocks());
    $blocks = $DB->get_records('block', array(), "name ASC");
    foreach ($blocks as $block) {
        $blockname = $block->name;
        if (!file_exists("$CFG->dirroot/blocks/$blockname/block_$blockname.php")) {
            continue;
        }
        $strblockname = new lang_string('pluginname', 'block_'.$blockname);
        if (file_exists($CFG->dirroot.'/blocks/'.$blockname.'/settings.php')) {
            $settings = new admin_settingpage('blocksetting'.$blockname, $strblockname, 'moodle/site:config', !$block->visible);
            include($CFG->dirroot.'/blocks/'.$blockname.'/settings.php');
            if ($settings) {
                $ADMIN->add('blocksettings', $settings);
            }
        }
    }

    // message outputs
    $ADMIN->add('modules', new admin_category('messageoutputs', new lang_string('messageoutputs', 'message')));
    $ADMIN->add('messageoutputs', new admin_page_managemessageoutputs());
    $ADMIN->add('messageoutputs', new admin_page_defaultmessageoutputs());
    require_once($CFG->dirroot.'/message/lib.php');
    $processors = get_message_processors();
    foreach ($processors as $processor) {
        $processorname = $processor->name;
        if (!$processor->available) {
            continue;
        }
        if ($processor->hassettings) {
            $strprocessorname = new lang_string('pluginname', 'message_'.$processorname);
            $settings = new admin_settingpage('messagesetting'.$processorname, $strprocessorname, 'moodle/site:config', !$processor->enabled);
            include($CFG->dirroot.'/message/output/'.$processor->name.'/settings.php');
            if ($settings) {
                $ADMIN->add('messageoutputs', $settings);
            }
        }
    }

    // authentication plugins
    $ADMIN->add('modules', new admin_category('authsettings', new lang_string('authentication', 'admin')));

    $temp = new admin_settingpage('manageauths', new lang_string('authsettings', 'admin'));
    $temp->add(new admin_setting_manageauths());
    $temp->add(new admin_setting_heading('manageauthscommonheading', new lang_string('commonsettings', 'admin'), ''));
    $temp->add(new admin_setting_special_registerauth());
    $temp->add(new admin_setting_configcheckbox('authpreventaccountcreation', new lang_string('authpreventaccountcreation', 'admin'), new lang_string('authpreventaccountcreation_help', 'admin'), 0));
    $temp->add(new admin_setting_configcheckbox('loginpageautofocus', new lang_string('loginpageautofocus', 'admin'), new lang_string('loginpageautofocus_help', 'admin'), 0));
    $temp->add(new admin_setting_configselect('guestloginbutton', new lang_string('guestloginbutton', 'auth'),
                                              new lang_string('showguestlogin', 'auth'), '1', array('0'=>new lang_string('hide'), '1'=>new lang_string('show'))));
    $temp->add(new admin_setting_configtext('alternateloginurl', new lang_string('alternateloginurl', 'auth'),
                                            new lang_string('alternatelogin', 'auth', htmlspecialchars(get_login_url())), ''));
    $temp->add(new admin_setting_configtext('forgottenpasswordurl', new lang_string('forgottenpasswordurl', 'auth'),
                                            new lang_string('forgottenpassword', 'auth'), ''));
    $temp->add(new admin_setting_confightmleditor('auth_instructions', new lang_string('instructions', 'auth'),
                                                new lang_string('authinstructions', 'auth'), ''));
    $temp->add(new admin_setting_configtext('allowemailaddresses', new lang_string('allowemailaddresses', 'admin'), new lang_string('configallowemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('denyemailaddresses', new lang_string('denyemailaddresses', 'admin'), new lang_string('configdenyemailaddresses', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configcheckbox('verifychangedemail', new lang_string('verifychangedemail', 'admin'), new lang_string('configverifychangedemail', 'admin'), 1));

    $temp->add(new admin_setting_configtext('recaptchapublickey', new lang_string('recaptchapublickey', 'admin'), new lang_string('configrecaptchapublickey', 'admin'), '', PARAM_NOTAGS));
    $temp->add(new admin_setting_configtext('recaptchaprivatekey', new lang_string('recaptchaprivatekey', 'admin'), new lang_string('configrecaptchaprivatekey', 'admin'), '', PARAM_NOTAGS));
    $ADMIN->add('authsettings', $temp);


    $auths = get_plugin_list('auth');
    $authsenabled = get_enabled_auth_plugins();
    foreach ($auths as $authname => $authdir) {
        $strauthname = new lang_string('pluginname', "auth_{$authname}");
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
    $ADMIN->add('modules', new admin_category('enrolments', new lang_string('enrolments', 'enrol')));
    $temp = new admin_settingpage('manageenrols', new lang_string('manageenrols', 'enrol'));
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

        $settings = new admin_settingpage('enrolsettings'.$enrol, new lang_string('pluginname', 'enrol_'.$enrol), 'moodle/site:config', !in_array($enrol, $enabled));
        // settings.php may create a subcategory or unset the settings completely
        include("$enrolpath/settings.php");
        if ($settings) {
            $ADMIN->add('enrolments', $settings);
        }

    }
    unset($enabled);
    unset($enrols);


/// Editor plugins
    $ADMIN->add('modules', new admin_category('editorsettings', new lang_string('editors', 'editor')));
    $temp = new admin_settingpage('manageeditors', new lang_string('editorsettings', 'editor'));
    $temp->add(new admin_setting_manageeditors());
    $htmleditors = editors_get_available();
    $ADMIN->add('editorsettings', $temp);

    $editors_available = editors_get_available();
    foreach ($editors_available as $editor=>$editorstr) {
        if (file_exists($CFG->dirroot . '/lib/editor/'.$editor.'/settings.php')) {
            $settings = new admin_settingpage('editorsettings'.$editor, new lang_string('pluginname', 'editor_'.$editor), 'moodle/site:config');
            // settings.php may create a subcategory or unset the settings completely
            include($CFG->dirroot . '/lib/editor/'.$editor.'/settings.php');
            if ($settings) {
                $ADMIN->add('editorsettings', $settings);
            }
        }
    }

/// License types
    $ADMIN->add('modules', new admin_category('licensesettings', new lang_string('licenses')));
    $temp = new admin_settingpage('managelicenses', new lang_string('managelicenses', 'admin'));

    require_once($CFG->libdir . '/licenselib.php');
    $licenses = array();
    $array = explode(',', $CFG->licenses);
    foreach ($array as $value) {
        $licenses[$value] = new lang_string($value, 'license');
    }
    $temp->add(new admin_setting_configselect('sitedefaultlicense', new lang_string('configsitedefaultlicense','admin'), new lang_string('configsitedefaultlicensehelp','admin'), 'allrightsreserved', $licenses));
    $temp->add(new admin_setting_managelicenses());
    $ADMIN->add('licensesettings', $temp);

/// Filter plugins
    $ADMIN->add('modules', new admin_category('filtersettings', new lang_string('managefilters')));

    $ADMIN->add('filtersettings', new admin_page_managefilters());

    // "filtersettings" settingpage
    $temp = new admin_settingpage('commonfiltersettings', new lang_string('commonfiltersettings', 'admin'));
    if ($ADMIN->fulltree) {
        $cachetimes = array(
            604800 => new lang_string('numdays','',7),
            86400 => new lang_string('numdays','',1),
            43200 => new lang_string('numhours','',12),
            10800 => new lang_string('numhours','',3),
            7200 => new lang_string('numhours','',2),
            3600 => new lang_string('numhours','',1),
            2700 => new lang_string('numminutes','',45),
            1800 => new lang_string('numminutes','',30),
            900 => new lang_string('numminutes','',15),
            600 => new lang_string('numminutes','',10),
            540 => new lang_string('numminutes','',9),
            480 => new lang_string('numminutes','',8),
            420 => new lang_string('numminutes','',7),
            360 => new lang_string('numminutes','',6),
            300 => new lang_string('numminutes','',5),
            240 => new lang_string('numminutes','',4),
            180 => new lang_string('numminutes','',3),
            120 => new lang_string('numminutes','',2),
            60 => new lang_string('numminutes','',1),
            30 => new lang_string('numseconds','',30),
            0 => new lang_string('no')
        );
        $items = array();
        $items[] = new admin_setting_configselect('cachetext', new lang_string('cachetext', 'admin'), new lang_string('configcachetext', 'admin'), 60, $cachetimes);
        $items[] = new admin_setting_configselect('filteruploadedfiles', new lang_string('filteruploadedfiles', 'admin'), new lang_string('configfilteruploadedfiles', 'admin'), 0,
                array('0' => new lang_string('none'), '1' => new lang_string('allfiles'), '2' => new lang_string('htmlfilesonly')));
        $items[] = new admin_setting_configcheckbox('filtermatchoneperpage', new lang_string('filtermatchoneperpage', 'admin'), new lang_string('configfiltermatchoneperpage', 'admin'), 0);
        $items[] = new admin_setting_configcheckbox('filtermatchonepertext', new lang_string('filtermatchonepertext', 'admin'), new lang_string('configfiltermatchonepertext', 'admin'), 0);
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
    $catname = new lang_string('portfolios', 'portfolio');
    $manage = new lang_string('manageportfolios', 'portfolio');
    $url = "$CFG->wwwroot/$CFG->admin/portfolio.php";

    $ADMIN->add('modules', new admin_category('portfoliosettings', $catname, empty($CFG->enableportfolios)));

    // Add manage page (with table)
    $temp = new admin_page_manageportfolios();
    $ADMIN->add('portfoliosettings', $temp);

    // Add common settings page
    $temp = new admin_settingpage('manageportfolioscommon', new lang_string('commonportfoliosettings', 'portfolio'));
    $temp->add(new admin_setting_heading('manageportfolioscommon', '', new lang_string('commonsettingsdesc', 'portfolio')));
    $fileinfo = portfolio_filesize_info(); // make sure this is defined in one place since its used inside portfolio too to detect insane settings
    $fileoptions = $fileinfo['options'];
    $temp->add(new admin_setting_configselect(
        'portfolio_moderate_filesize_threshold',
        new lang_string('moderatefilesizethreshold', 'portfolio'),
        new lang_string('moderatefilesizethresholddesc', 'portfolio'),
        $fileinfo['moderate'], $fileoptions));
    $temp->add(new admin_setting_configselect(
        'portfolio_high_filesize_threshold',
        new lang_string('highfilesizethreshold', 'portfolio'),
        new lang_string('highfilesizethresholddesc', 'portfolio'),
        $fileinfo['high'], $fileoptions));

    $temp->add(new admin_setting_configtext(
        'portfolio_moderate_db_threshold',
        new lang_string('moderatedbsizethreshold', 'portfolio'),
        new lang_string('moderatedbsizethresholddesc', 'portfolio'),
        20, PARAM_INT, 3));

    $temp->add(new admin_setting_configtext(
        'portfolio_high_db_threshold',
        new lang_string('highdbsizethreshold', 'portfolio'),
        new lang_string('highdbsizethresholddesc', 'portfolio'),
        50, PARAM_INT, 3));

    $ADMIN->add('portfoliosettings', $temp);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfolionew', new lang_string('addnewportfolio', 'portfolio'), $url, 'moodle/site:config', true), '', $url);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfoliodelete', new lang_string('deleteportfolio', 'portfolio'), $url, 'moodle/site:config', true), '', $url);
    $ADMIN->add('portfoliosettings', new admin_externalpage('portfoliocontroller', new lang_string('manageportfolios', 'portfolio'), $url, 'moodle/site:config', true), '', $url);

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
    $catname =new lang_string('repositories', 'repository');
    $managerepo = new lang_string('manage', 'repository');
    $url = $CFG->wwwroot.'/'.$CFG->admin.'/repository.php';
    $ADMIN->add('modules', new admin_category('repositorysettings', $catname));

    // Add main page (with table)
    $temp = new admin_page_managerepositories();
    $ADMIN->add('repositorysettings', $temp);

    // Add common settings page
    $temp = new admin_settingpage('managerepositoriescommon', new lang_string('commonrepositorysettings', 'repository'));
    $temp->add(new admin_setting_configtext('repositorycacheexpire', new lang_string('cacheexpire', 'repository'), new lang_string('configcacheexpire', 'repository'), 120));
    $temp->add(new admin_setting_configcheckbox('repositoryallowexternallinks', new lang_string('allowexternallinks', 'repository'), new lang_string('configallowexternallinks', 'repository'), 1));
    $temp->add(new admin_setting_configcheckbox('legacyfilesinnewcourses', new lang_string('legacyfilesinnewcourses', 'admin'), new lang_string('legacyfilesinnewcourses_help', 'admin'), 0));
    $ADMIN->add('repositorysettings', $temp);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorynew',
        new lang_string('addplugin', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorydelete',
        new lang_string('deleterepository', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositorycontroller',
        new lang_string('manage', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositoryinstancenew',
        new lang_string('createrepository', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    $ADMIN->add('repositorysettings', new admin_externalpage('repositoryinstanceedit',
        new lang_string('editrepositoryinstance', 'repository'), $url, 'moodle/site:config', true),
        '', $url);
    foreach (repository::get_types() as $repositorytype) {
      //display setup page for plugins with: general options or multiple instances (e.g. has instance config)
      $typeoptionnames = repository::static_function($repositorytype->get_typename(), 'get_type_option_names');
      $instanceoptionnames = repository::static_function($repositorytype->get_typename(), 'get_instance_option_names');
      if (!empty($typeoptionnames) || !empty($instanceoptionnames)) {

          $params = array('action'=>'edit', 'sesskey'=>sesskey(), 'repos'=>$repositorytype->get_typename());
          $settingsurl = new moodle_url("/$CFG->admin/repository.php", $params);
          $repositoryexternalpage = new admin_externalpage('repositorysettings'.$repositorytype->get_typename(), $repositorytype->get_readablename(), $settingsurl);
          $ADMIN->add('repositorysettings', $repositoryexternalpage);
      }
    }
}

/// Web services
    $ADMIN->add('modules', new admin_category('webservicesettings', new lang_string('webservices', 'webservice')));
    /// overview page
    $temp = new admin_settingpage('webservicesoverview', new lang_string('webservicesoverview', 'webservice'));
    $temp->add(new admin_setting_webservicesoverview());
    $ADMIN->add('webservicesettings', $temp);
    //API documentation
    $ADMIN->add('webservicesettings', new admin_externalpage('webservicedocumentation', new lang_string('wsdocapi', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/documentation.php", 'moodle/site:config', false));
    /// manage service
    $temp = new admin_settingpage('externalservices', new lang_string('externalservices', 'webservice'));
    $enablemobiledocurl = new moodle_url(get_docs_url('Enable_mobile_web_services'));
    $enablemobiledoclink = html_writer::link($enablemobiledocurl, new lang_string('documentation'));
    $temp->add(new admin_setting_enablemobileservice('enablemobilewebservice', new lang_string('enablemobilewebservice', 'admin'), new lang_string('configenablemobilewebservice', 'admin', $enablemobiledoclink), 0));
    $temp->add(new admin_setting_heading('manageserviceshelpexplaination', new lang_string('information', 'webservice'), new lang_string('servicehelpexplanation', 'webservice')));
    $temp->add(new admin_setting_manageexternalservices());
    $ADMIN->add('webservicesettings', $temp);
    $ADMIN->add('webservicesettings', new admin_externalpage('externalservice', new lang_string('editaservice', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalservicefunctions', new lang_string('externalservicefunctions', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_functions.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalserviceusers', new lang_string('externalserviceusers', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_users.php", 'moodle/site:config', true));
    $ADMIN->add('webservicesettings', new admin_externalpage('externalserviceusersettings', new lang_string('serviceusersettings', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/service_user_settings.php", 'moodle/site:config', true));
    /// manage protocol page link
    $temp = new admin_settingpage('webserviceprotocols', new lang_string('manageprotocols', 'webservice'));
    $temp->add(new admin_setting_managewebserviceprotocols());
    if (empty($CFG->enablewebservices)) {
        $temp->add(new admin_setting_heading('webservicesaredisabled', '', new lang_string('disabledwarning', 'webservice')));
    }

    // We cannot use $OUTPUT this early, doing so means that we lose the ability
    // to set the page layout on all admin pages.
    // $wsdoclink = $OUTPUT->doc_link('How_to_get_a_security_key');
    $url = new moodle_url(get_docs_url('How_to_get_a_security_key'));
    $wsdoclink = html_writer::tag('a', new lang_string('supplyinfo', 'webservice'), array('href'=>$url));
    $temp->add(new admin_setting_configcheckbox('enablewsdocumentation', new lang_string('enablewsdocumentation',
                        'admin'), new lang_string('configenablewsdocumentation', 'admin', $wsdoclink), false));
    $ADMIN->add('webservicesettings', $temp);
    /// links to protocol pages
    $webservices_available = get_plugin_list('webservice');
    $active_webservices = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
    foreach ($webservices_available as $webservice => $location) {
        if (file_exists("$location/settings.php")) {
            $name = new lang_string('pluginname', 'webservice_'.$webservice);
            $settings = new admin_settingpage('webservicesetting'.$webservice, $name, 'moodle/site:config', !in_array($webservice, $active_webservices) or empty($CFG->enablewebservices));
            include("$location/settings.php");
            if ($settings) {
                $ADMIN->add('webservicesettings', $settings);
            }
        }
    }
    /// manage token page link
    $ADMIN->add('webservicesettings', new admin_externalpage('addwebservicetoken', new lang_string('managetokens', 'webservice'), "$CFG->wwwroot/$CFG->admin/webservice/tokens.php", 'moodle/site:config', true));
    $temp = new admin_settingpage('webservicetokens', new lang_string('managetokens', 'webservice'));
    $temp->add(new admin_setting_managewebservicetokens());
    if (empty($CFG->enablewebservices)) {
        $temp->add(new admin_setting_heading('webservicesaredisabled', '', new lang_string('disabledwarning', 'webservice')));
    }
    $ADMIN->add('webservicesettings', $temp);

// Question type settings
if ($hassiteconfig || has_capability('moodle/question:config', $systemcontext)) {
    // Question behaviour settings.
    $ADMIN->add('modules', new admin_category('qbehavioursettings', new lang_string('questionbehaviours', 'admin')));
    $ADMIN->add('qbehavioursettings', new admin_page_manageqbehaviours());

    // Question type settings.
    $ADMIN->add('modules', new admin_category('qtypesettings', new lang_string('questiontypes', 'admin')));
    $ADMIN->add('qtypesettings', new admin_page_manageqtypes());
    $qtypes = get_plugin_list('qtype');
    foreach ($qtypes as $qtype => $path) {
        $settingsfile = $path . '/settings.php';
        if (file_exists($settingsfile)) {
            $settings = new admin_settingpage('qtypesetting' . $qtype,
                    new lang_string('pluginname', 'qtype_' . $qtype), 'moodle/question:config');
            include($settingsfile);
            if ($settings) {
                $ADMIN->add('qtypesettings', $settings);
            }
        }
    }
}

// Plagiarism plugin settings
if ($hassiteconfig && !empty($CFG->enableplagiarism)) {
    $ADMIN->add('modules', new admin_category('plagiarism', new lang_string('plagiarism', 'plagiarism')));
    $temp = new admin_settingpage('plagiarismsettings', new lang_string('plagiarismsettings', 'plagiarism'));
    $temp->add(new admin_setting_manageplagiarism());
    $ADMIN->add('plagiarism', $temp);
    foreach (get_plugin_list('plagiarism') as $plugin => $plugindir) {
        if (file_exists($plugindir.'/settings.php')) {
            $ADMIN->add('plagiarism', new admin_externalpage('plagiarism'.$plugin, new lang_string($plugin, 'plagiarism_'.$plugin), "$CFG->wwwroot/plagiarism/$plugin/settings.php", 'moodle/site:config'));
        }
    }
}
$ADMIN->add('reports', new admin_externalpage('comments', new lang_string('comments'), $CFG->wwwroot.'/comment/', 'moodle/site:viewreports'));

// Course reports settings
if ($hassiteconfig) {
    $pages = array();
    foreach (get_plugin_list('coursereport') as $report => $path) {
        $file = $CFG->dirroot . '/course/report/' . $report . '/settings.php';
        if (file_exists($file)) {
            $settings = new admin_settingpage('coursereport' . $report,
                    new lang_string('pluginname', 'coursereport_' . $report), 'moodle/site:config');
            // settings.php may create a subcategory or unset the settings completely
            include($file);
            if ($settings) {
                $pages[] = $settings;
            }
        }
    }
    if (!empty($pages)) {
        $ADMIN->add('modules', new admin_category('coursereports', new lang_string('coursereports')));
        foreach ($pages as $page) {
            $ADMIN->add('coursereports', $page);
        }
    }
    unset($pages);
}

// Now add reports
$pages = array();
foreach (get_plugin_list('report') as $report => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        $settings = new admin_settingpage('report' . $report,
                new lang_string('pluginname', 'report_' . $report), 'moodle/site:config');
        include($settings_path);
        if ($settings) {
            $pages[] = $settings;
        }
    }
}
$ADMIN->add('modules', new admin_category('reportplugins', new lang_string('reports')));
$ADMIN->add('reportplugins', new admin_externalpage('managereports', new lang_string('reportsmanage', 'admin'),
                                                    $CFG->wwwroot . '/' . $CFG->admin . '/reports.php'));
foreach ($pages as $page) {
    $ADMIN->add('reportplugins', $page);
}

/// Add all admin tools
if ($hassiteconfig) {
    $ADMIN->add('modules', new admin_category('tools', new lang_string('tools', 'admin')));
    $ADMIN->add('tools', new admin_externalpage('managetools', new lang_string('toolsmanage', 'admin'),
                                                     $CFG->wwwroot . '/' . $CFG->admin . '/tools.php'));
}

// Now add various admin tools
foreach (get_plugin_list('tool') as $plugin => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
    }
}

/// Add all local plugins - must be always last!
if ($hassiteconfig) {
    $ADMIN->add('modules', new admin_category('localplugins', new lang_string('localplugins')));
    $ADMIN->add('localplugins', new admin_externalpage('managelocalplugins', new lang_string('localpluginsmanage'),
                                                        $CFG->wwwroot . '/' . $CFG->admin . '/localplugins.php'));
}

foreach (get_plugin_list('local') as $plugin => $plugindir) {
    $settings_path = "$plugindir/settings.php";
    if (file_exists($settings_path)) {
        include($settings_path);
        continue;
    }
}
