<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    include_once(__DIR__.'/lib.php');
    require_once(__DIR__.'/settingslib.php');
    require_once(__DIR__."/turnitintooltwo_view.class.php");

    $migration_activation = optional_param('activation', null, PARAM_ALPHA);
    $turnitintooltwoview = new turnitintooltwo_view();

    $config = turnitintooltwo_admin_config();

    // Throw warning if necessary PHP libraries aren't installed.
    $librarywarning = '';
    if (!extension_loaded('XMLWriter')) {
        $librarywarning .= html_writer::tag('div', get_string('noxmlwriterlibrary', 'turnitintooltwo'),
                                                array('class' => 'tii_library_not_present_warning'));
    }
    if (!extension_loaded('mbstring')) {
        $librarywarning .= html_writer::tag('div', get_string('nombstringlibrary', 'turnitintooltwo'),
                                                array('class' => 'tii_library_not_present_warning'));
    }
    if (!extension_loaded('fileinfo')) {
        $librarywarning .= html_writer::tag('div', get_string('nofinfolibrary', 'turnitintooltwo'),
                                                array('class' => 'tii_library_not_present_warning'));
    }
    if (!extension_loaded('soap')) {
        $librarywarning .= html_writer::tag('div', get_string('nosoaplibrary', 'turnitintooltwo'),
                                                array('class' => 'tii_library_not_present_warning'));
    }

    $close = html_writer::tag('button', '&times;', array('class' => 'close', 'data-dismiss' => 'alert'));

    // If being directed here from the migration activation page, display appropriate message
    $migration_message = '';
    if ($migration_activation == 'failure') {
        $migration_message = html_writer::tag(
            'div',
            $close.get_string('migrationactivationfailure', 'turnitintooltwo'),
            array('class' => 'alert alert-danger', 'role' => 'alert')
        );
    }

    $tabmenu = $turnitintooltwoview->draw_settings_menu('settings').
                html_writer::tag('noscript', get_string('noscript', 'turnitintooltwo')).$librarywarning.
                html_writer::tag('link', '', array("rel" => "stylesheet", "type" => "text/css",
                                            "href" => $CFG->wwwroot."/mod/turnitintooltwo/styles.css"));

    $currentsection = optional_param('section', '', PARAM_ALPHAEXT);

    $version = (empty($module->version)) ? $module->versiondisk : $module->version;

    if ($currentsection == 'modsettingturnitintooltwo') {
        // Include javascript.
        $PAGE->requires->jquery();
        $PAGE->requires->jquery_plugin('turnitintooltwo-turnitintooltwo_settings', 'mod_turnitintooltwo');
    }

    // Offline mode provided by Androgogic. Set tiioffline in config.php.
    $offlinecomment = '';
    if (!empty($CFG->tiioffline)) {
        $offlinecomment = html_writer::start_tag('div', array('class' => 'offline_status'));
        $offlinecomment .= $OUTPUT->box(get_string('offlinestatus', 'turnitintooltwo'), 'offline');
        $offlinecomment .= html_writer::end_tag('div');
    }

    // Test connection to turnitin link.
    $testconnection = html_writer::start_tag('div', array('class' => 'test_connection', 'style' => 'display: none;'));
    $testconnection .= $OUTPUT->box($OUTPUT->pix_icon('globe', get_string('connecttest', 'turnitintooltwo'),
                                                'mod_turnitintooltwo')." ".
                                                    html_writer::tag('span',
                                                        get_string('connecttest', 'turnitintooltwo')),
                                                '', 'test_link');

    $testconnection .= $OUTPUT->box($OUTPUT->pix_icon('loader', get_string('testingconnection', 'turnitintooltwo'),
                                                        'mod_turnitintooltwo')." ".html_writer::tag('span',
                                                                    get_string('testingconnection', 'turnitintooltwo')),
                                                '', 'testing_container');
    $testconnection .= $OUTPUT->box('', '', 'test_result');
    $testconnection .= html_writer::end_tag('div');

    $desc = '('.get_string('moduleversion', 'turnitintooltwo').': '.$version.')';

    $settings->add(new admin_setting_heading(
        'turnitintooltwo_migration_status_header',
        '',
        $migration_message
    ));
    $settings->add(new admin_setting_heading('turnitintooltwo_header', $desc, $tabmenu));

    // Turnitin account configuration.
    $settings->add(new admin_setting_heading('turnitintooltwo_accountconfig',
                                            get_string('tiiaccountconfig', 'turnitintooltwo'), ''));

    $settings->add(new admin_setting_configtext_int_only('turnitintooltwo/accountid',
                                                    get_string("turnitinaccountid", "turnitintooltwo"),
                                                    get_string("turnitinaccountid_desc", "turnitintooltwo"), ''));

    $settings->add(new admin_setting_config_tii_secret_key('turnitintooltwo/secretkey',
                                                        get_string("turnitinsecretkey", "turnitintooltwo"),
                                                        get_string("turnitinsecretkey_desc", "turnitintooltwo"), '', 'PARAM_TEXT'));

    $testoptions = array(
        'https://api.turnitin.com' => 'https://api.turnitin.com',
        'https://api.turnitinuk.com' => 'https://api.turnitinuk.com',
        'https://sandbox.turnitin.com' => 'https://sandbox.turnitin.com'
    );

    // Set $CFG->turnitinqa and add URLs to $CFG->turnitinqaurls array in config.php file for testing other environments.
    if (!empty($CFG->turnitinqa)) {
        foreach ($CFG->turnitinqaurls as $url) {
            $testoptions[$url] = $url;
        }
    }

    $settings->add(new admin_setting_configselect('turnitintooltwo/apiurl',
                                    get_string("turnitinapiurl", "turnitintooltwo"),
                                    get_string("turnitinapiurl_desc", "turnitintooltwo").$offlinecomment.$testconnection,
                                    0, $testoptions));

    // Miscellaneous settings.
    $settings->add(new admin_setting_heading('turnitintooltwo_debugginglogs',
                                            get_string('tiidebugginglogs', 'turnitintooltwo'), ''));

    $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
    $diagnosticoptions = array(
            0 => get_string('diagnosticoptions_0', 'turnitintooltwo'),
            1 => get_string('diagnosticoptions_1', 'turnitintooltwo'),
            2 => get_string('diagnosticoptions_2', 'turnitintooltwo')
        );

    $settings->add(new admin_setting_configselect('turnitintooltwo/enablediagnostic',
                        get_string('turnitindiagnostic', 'turnitintooltwo'),
                        get_string('turnitindiagnostic_desc', 'turnitintooltwo'), 0, $diagnosticoptions));

    $settings->add(new admin_setting_configselect('turnitintooltwo/enableperformancelogs',
                        get_string('enableperformancelogs', 'turnitintooltwo'),
                        get_string('enableperformancelogs_desc', 'turnitintooltwo'), 0, $ynoptions));

    // Turnitin account settings.
    $accountnote = html_writer::tag('div',
                            get_string('tiiaccountsettings_desc', 'turnitintooltwo'),
                            array('class' => 'tii_checkagainstnote')
        );
    $settings->add(new admin_setting_heading('turnitintooltwo_accountsettings',
                                            get_string('tiiaccountsettings', 'turnitintooltwo'), $accountnote));

    $settings->add(new admin_setting_configselect('turnitintooltwo/usegrademark',
                                                    get_string('turnitinusegrademark', 'turnitintooltwo'),
                                                    get_string('turnitinusegrademark_desc', 'turnitintooltwo'),
                                                    1, $ynoptions));

    $settings->add(new admin_setting_configselect('turnitintooltwo/enablepeermark',
                                                    get_string('turnitinenablepeermark', 'turnitintooltwo'),
                                                    get_string('turnitinenablepeermark_desc', 'turnitintooltwo'),
                                                    1, $ynoptions));

    $settings->add(new admin_setting_configselect('turnitintooltwo/usegrammar',
                                                    get_string('turnitinusegrammar', 'turnitintooltwo'),
                                                    get_string('turnitinusegrammar_desc', 'turnitintooltwo'),
                                                    0, $ynoptions));

    $settings->add(new admin_setting_configselect('turnitintooltwo/useanon',
                                                    get_string('turnitinuseanon', 'turnitintooltwo'),
                                                    get_string('turnitinuseanon_desc', 'turnitintooltwo'),
                                                    0, $ynoptions));

    $settings->add(new admin_setting_configselect('turnitintooltwo/transmatch',
                                                    get_string('transmatch', 'turnitintooltwo'),
                                                    get_string('transmatch_desc', 'turnitintooltwo'),
                                                    0, $ynoptions));

    $repositoryoptions = array(
        ADMIN_REPOSITORY_OPTION_STANDARD => get_string('repositoryoptions_0', 'turnitintooltwo'),
        ADMIN_REPOSITORY_OPTION_EXPANDED => get_string('repositoryoptions_1', 'turnitintooltwo'),
        ADMIN_REPOSITORY_OPTION_FORCE_STANDARD => get_string('repositoryoptions_2', 'turnitintooltwo'),
        ADMIN_REPOSITORY_OPTION_FORCE_NO => get_string('repositoryoptions_3', 'turnitintooltwo'),
        ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL => get_string('repositoryoptions_4', 'turnitintooltwo')
    );

    $settings->add(new admin_setting_configselect('turnitintooltwo/repositoryoption',
                                                    get_string('turnitinrepositoryoptions', 'turnitintooltwo').
                                                    $OUTPUT->help_icon('turnitinrepositoryoptions', 'turnitintooltwo'),
                                                    get_string('turnitinrepositoryoptions_desc', 'turnitintooltwo'),
                                                    0, $repositoryoptions));

    // Miscellaneous settings.
    $settings->add(new admin_setting_heading('turnitintooltwo_miscsettings',
                                            get_string('tiimiscsettings', 'turnitintooltwo'), ''));

    if (empty($config->agreement)) {
        $config->agreement = get_string('turnitintooltwoagreement_default', 'turnitintooltwo');
    }

    $settings->add(new admin_setting_configtextarea('turnitintooltwo/agreement',
                                                    get_string('turnitintooltwoagreement', 'turnitintooltwo'),
                                                    get_string('turnitintooltwoagreement_desc', 'turnitintooltwo'), ''));

    $layoutoptions = array(
            0 => get_string('layoutoptions_0', 'turnitintooltwo'),
            1 => get_string('layoutoptions_1', 'turnitintooltwo')
        );

    // Following are values for student privacy settings.
    $settings->add(new admin_setting_heading('turnitintooltwo_privacy', get_string('studentdataprivacy', 'turnitintooltwo'),
                       get_string('studentdataprivacy_desc', 'turnitintooltwo')));

    if ($DB->count_records('turnitintooltwo_users') > 0 AND isset($config->enablepseudo)) {
        $selectionarray = ($config->enablepseudo == 1) ? array(1 => get_string('yes')) : array(0 => get_string('no'));
        $pseudoselect = new admin_setting_configselect('turnitintooltwo/enablepseudo',
                                                        get_string('enablepseudo', 'turnitintooltwo'),
                                                        get_string('enablepseudo_desc', 'turnitintooltwo'),
                                                        0, $selectionarray);
        $pseudoselect->nosave = true;
    } else if ($DB->count_records('turnitintooltwo_users') > 0) {
        $pseudoselect = new admin_setting_configselect('turnitintooltwo/enablepseudo',
                                                        get_string('enablepseudo', 'turnitintooltwo'),
                                                        get_string('enablepseudo_desc', 'turnitintooltwo'),
                                                        0, array( 0 => get_string('no', 'turnitintooltwo')));
    } else {
        $pseudoselect = new admin_setting_configselect('turnitintooltwo/enablepseudo',
                                                        get_string('enablepseudo', 'turnitintooltwo'),
                                                        get_string('enablepseudo_desc', 'turnitintooltwo'),
                                                        0, $ynoptions);
    }
    $settings->add($pseudoselect);

    if (isset($config->enablepseudo) AND $config->enablepseudo) {
        $settings->add(new admin_setting_configtext('turnitintooltwo/pseudofirstname',
                                                        get_string('pseudofirstname', 'turnitintooltwo'),
                                                        get_string('pseudofirstname_desc', 'turnitintooltwo'),
                                                        TURNITINTOOLTWO_DEFAULT_PSEUDO_FIRSTNAME));

        $lnoptions = array( 0 => get_string('user') );

        $userprofiles = $DB->get_records('user_info_field');
        foreach ($userprofiles as $profile) {
            $lnoptions[$profile->id] = get_string('profilefield', 'admin').': '.$profile->name;
        }

        $settings->add(new admin_setting_configselect('turnitintooltwo/pseudolastname',
                                                        get_string('pseudolastname', 'turnitintooltwo'),
                                                        get_string('pseudolastname_desc', 'turnitintooltwo'),
                                                        0, $lnoptions));

        $settings->add(new admin_setting_configselect('turnitintooltwo/lastnamegen',
                                                        get_string('pseudolastnamegen', 'turnitintooltwo'),
                                                        get_string('pseudolastnamegen_desc', 'turnitintooltwo' ),
                                                        0, $ynoptions));

        $settings->add(new admin_setting_configtext('turnitintooltwo/pseudosalt',
                                                        get_string('pseudoemailsalt', 'turnitintooltwo'),
                                                        get_string('pseudoemailsalt_desc', 'turnitintooltwo'), ''));

        $settings->add(new admin_setting_configtext('turnitintooltwo/pseudoemaildomain',
                                                        get_string('pseudoemaildomain', 'turnitintooltwo'),
                                                        get_string('pseudoemaildomain_desc', 'turnitintooltwo'), ''));
    }

    // Following are default values for new instance.
    $settings->add(new admin_setting_heading('turnitintooltwo/defaults',
                                                get_string('defaults', 'turnitintooltwo'),
                                                get_string('defaults_desc', 'turnitintooltwo')));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_type',
                                                    get_string('type', 'turnitintooltwo'),
                                                    '', 0, turnitintooltwo_filetype_array()));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_numparts',
                                                    get_string('numberofparts', 'turnitintooltwo'),
                                                    '', 1, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5)));

    if (!empty($config->useanon) && $currentsection == 'modsettingturnitintooltwo') {
        $settings->add(new admin_setting_configselect('turnitintooltwo/default_anon', get_string('anon', 'turnitintooltwo'),
                        '', 0, $ynoptions ));
    }

    if (!empty($config->transmatch) && $currentsection == 'modsettingturnitintooltwo') {
        $settings->add(new admin_setting_configselect('turnitintooltwo/default_transmatch',
                                                        get_string('transmatch', 'turnitintooltwo'),
                                                        '', 0, $ynoptions ));
    }

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_studentreports',
                                                    get_string('studentreports', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $gradedisplayoptions = array(1 => get_string('displaygradesaspercent', 'turnitintooltwo'),
                                 2 => get_string('displaygradesasfraction', 'turnitintooltwo'));
    $settings->add(new admin_setting_configselect('turnitintooltwo/default_gradedisplay',
                                                    get_string('displaygradesas', 'turnitintooltwo'),
                                                    '', 2, $gradedisplayoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_allownonor',
                                                    get_string('allownonor', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_allowlate',
                                                    get_string('allowlate', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $genparams = turnitintooltwo_get_report_gen_speed_params();
    $genoptions = array(0 => get_string('genimmediately1', 'turnitintooltwo'),
                        1 => get_string('genimmediately2', 'turnitintooltwo', $genparams),
                        2 => get_string('genduedate', 'turnitintooltwo'));
    $settings->add(new admin_setting_configselect('turnitintooltwo/default_reportgenspeed',
                                                    get_string('reportgenspeed', 'turnitintooltwo'),
                                                    '', 0, $genoptions ));

    $suboptions = array( 0 => get_string('norepository', 'turnitintooltwo'),
                        1 => get_string('standardrepository', 'turnitintooltwo'));

    if (!isset($config->repositoryoption)) {
        $config->repositoryoption = 0;
    }

    switch ($config->repositoryoption) {
        case 0; // Standard options.
            $settings->add(new admin_setting_configselect('turnitintooltwo/default_submitpapersto',
                                                    get_string('submitpapersto', 'turnitintooltwo').
                                                    $OUTPUT->help_icon('submitpapersto', 'turnitintooltwo'),
                                                    '', 1, $suboptions ));
            break;
        case 1; // Standard options + Allow Instituional Repository.
            $suboptions[2] = get_string('institutionalrepository', 'turnitintooltwo');

            $settings->add(new admin_setting_configselect('turnitintooltwo/default_submitpapersto',
                                                    get_string('submitpapersto', 'turnitintooltwo').
                                                    $OUTPUT->help_icon('submitpapersto', 'turnitintooltwo'),
                                                    '', 1, $suboptions ));
            break;
    }

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_spapercheck',
                                                    get_string('spapercheck', 'turnitintooltwo'),
                                                    '', 1, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_internetcheck',
                                                    get_string('internetcheck', 'turnitintooltwo'),
                                                    '', 1, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_journalcheck',
                                                    get_string('journalcheck', 'turnitintooltwo'),
                                                    '', 1, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_institutioncheck',
                                                    get_string('institutionalchecksettings', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_excludebiblio',
                                                    get_string('excludebiblio', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_excludequoted',
                                                    get_string('excludequoted', 'turnitintooltwo'),
                                                    '', 0, $ynoptions ));

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_grammar', get_string('erater', 'turnitintooltwo'),
                       '', 0, $ynoptions ));

    $handbookoptions = array(
                                1 => get_string('erater_handbook_advanced', 'turnitintooltwo'),
                                2 => get_string('erater_handbook_highschool', 'turnitintooltwo'),
                                3 => get_string('erater_handbook_middleschool', 'turnitintooltwo'),
                                4 => get_string('erater_handbook_elementary', 'turnitintooltwo'),
                                5 => get_string('erater_handbook_learners', 'turnitintooltwo')
                            );

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_grammar_handbook',
                                                    get_string('erater_handbook', 'turnitintooltwo'),
                                                    '', 2, $handbookoptions ));

    $dictionaryoptions = array(
                                'en_US' => get_string('erater_dictionary_enus', 'turnitintooltwo'),
                                'en_GB' => get_string('erater_dictionary_engb', 'turnitintooltwo'),
                                'en' => get_string('erater_dictionary_en', 'turnitintooltwo')
                            );

    $settings->add(new admin_setting_configselect('turnitintooltwo/default_grammar_dictionary',
                                                        get_string('erater_dictionary', 'turnitintooltwo'),
                                                        '', 'en_US', $dictionaryoptions ));

    $settings->add(new admin_setting_configcheckbox('turnitintooltwo/default_grammar_spelling',
                                                        get_string('erater_spelling', 'turnitintooltwo'), '', false));

    $settings->add(new admin_setting_configcheckbox('turnitintooltwo/default_grammar_grammar',
                                                        get_string('erater_grammar', 'turnitintooltwo'), '', false));

    $settings->add(new admin_setting_configcheckbox('turnitintooltwo/default_grammar_usage',
                                                        get_string('erater_usage', 'turnitintooltwo'), '', false));

    $settings->add(new admin_setting_configcheckbox('turnitintooltwo/default_grammar_mechanics',
                                                        get_string('erater_mechanics', 'turnitintooltwo'), '', false));

    $settings->add(new admin_setting_configcheckbox('turnitintooltwo/default_grammar_style',
                                                        get_string('erater_style', 'turnitintooltwo'), '', false));
}
