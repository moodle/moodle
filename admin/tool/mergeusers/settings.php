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
 * Settings file.
 *
 * @package   tool_mergeusers
 * @author    Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author    Mike Holzer
 * @author    Forrest Gaston
 * @author    Juan Pablo Torres Herrera
 * @author    John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_mergeusers\local\database_transactions;
use tool_mergeusers\local\settings\json_setting;
use tool_mergeusers\local\config;

defined('MOODLE_INTERNAL') || die;

// Add options under the user menu.
if (has_capability('tool/mergeusers:mergeusers', context_system::instance())) {
    // phpcs:disable
    /**
     * @var part_of_admin_tree $ADMIN
     */
    if (!$ADMIN->locate('tool_mergeusers')) {
        // phpcs:enable
        global $CFG;
        $ADMIN->add(
            'accounts',
            new admin_externalpage(
                'tool_mergeusers_merge',
                get_string('pluginname', 'tool_mergeusers'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/mergeusers/index.php',
                'tool/mergeusers:mergeusers'
            ),
        );
        $ADMIN->add(
            'reports',
            new admin_externalpage(
                'tool_mergeusers_viewlog',
                get_string('viewlog', 'tool_mergeusers'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/mergeusers/view.php',
                'tool/mergeusers:viewlog'
            )
        );
    }
}

// Prepare admin setting pages.
// Keep only under administrators' responsibility the ability to customize the behaviour of the plugin.
$generalsettings = new admin_settingpage(
    'toolmergeusersgeneralsettings',
    new lang_string('settings:generalsettings', 'tool_mergeusers'),
    'moodle/site:config',
);
$databasesettings = new admin_settingpage(
    'toolmergeusersdatabasesettings',
    new lang_string('settings:databasesettings', 'tool_mergeusers'),
    'moodle/site:config',
);

// Build just the links on the settings page.
if ($hassiteconfig) {
    $ADMIN->add(
        'tools',
        new admin_category(
            'toolmergeuserscat',
            new lang_string('pluginname', 'tool_mergeusers'),
        ),
    );

    $ADMIN->add('toolmergeuserscat', $generalsettings);
    $ADMIN->add('toolmergeuserscat', $databasesettings);
}

// Only when showing the whole tree, show all options below.
if ($ADMIN->fulltree) {
    require_once(__DIR__ . '/settingslib.php');
    $tabs = [
        new tabobject(
            'toolmergeusersgeneralsettings',
            new moodle_url('/admin/settings.php', ['section' => 'toolmergeusersgeneralsettings']),
            new lang_string('settings:generalsettings', 'tool_mergeusers'),
        ),
        new tabobject(
            'toolmergeusersdatabasesettings',
            new moodle_url('/admin/settings.php', ['section' => 'toolmergeusersdatabasesettings']),
            new lang_string('settings:databasesettings', 'tool_mergeusers'),
        ),
    ];

    global $OUTPUT;
    $currenttab = optional_param('section', 'toolmergeusersgeneralsettings', PARAM_ALPHA);
    $tabtree = new tabtree($tabs, $currenttab);
    $tabs = new admin_setting_heading(
        'toolmergeuserstabs',
        null,
        $OUTPUT->render($tabtree)
    );

    $iscategoryselected = optional_param('category', false, PARAM_RAW_TRIMMED);
    $isaquery = optional_param('query', false, PARAM_RAW_TRIMMED);
    $showtabs = !$iscategoryselected && !$isaquery;

    // Add general settings.
    if ($showtabs) {
        $generalsettings->add($tabs);
    }

    // Add configuration for making user suspension optional.
    $generalsettings->add(new admin_setting_configcheckbox(
        'tool_mergeusers/suspenduser',
        get_string('suspenduser_setting', 'tool_mergeusers'),
        get_string('suspenduser_setting_desc', 'tool_mergeusers'),
        1
    ));

    $supportinglang = (database_transactions::are_supported()) ? 'transactions_supported' : 'transactions_not_supported';

    $generalsettings->add(new admin_setting_configcheckbox(
        'tool_mergeusers/transactions_only',
        get_string('transactions_setting', 'tool_mergeusers'),
        get_string('transactions_setting_desc', 'tool_mergeusers') . '<br /><br />' .
        get_string($supportinglang, 'tool_mergeusers'),
        1
    ));

    $exceptionoptions = tool_mergeusers_build_exceptions_options();
    $generalsettings->add(new admin_setting_configmultiselect(
        'tool_mergeusers/excluded_exceptions',
        get_string('excluded_exceptions', 'tool_mergeusers'),
        get_string('excluded_exceptions_desc', 'tool_mergeusers', $exceptionoptions->defaultvalue),
        [$exceptionoptions->defaultkey], // Default value: empty => apply all exceptions.
        $exceptionoptions->options
    ));

    // Quiz attempts.
    $quizoptions = tool_mergeusers_build_quiz_options();
    $generalsettings->add(new admin_setting_configselect(
        'tool_mergeusers/quizattemptsaction',
        get_string('quizattemptsaction', 'tool_mergeusers'),
        get_string('quizattemptsaction_desc', 'tool_mergeusers', $quizoptions->allstrings),
        $quizoptions->defaultkey,
        $quizoptions->options
    ));

    $generalsettings->add(new admin_setting_configcheckbox(
        'tool_mergeusers/uniquekeynewidtomaintain',
        get_string('uniquekeynewidtomaintain', 'tool_mergeusers'),
        get_string('uniquekeynewidtomaintain_desc', 'tool_mergeusers'),
        1
    ));

    $fields = tool_mergeusers_inform_about_pending_user_profile_fields();
    if ($fields->exists) {
        $generalsettings->add(new admin_setting_description(
            'tool_mergeusers/profilefields',
            new lang_string('profilefields', 'tool_mergeusers'),
            new lang_string('profilefieldsdesc', 'tool_mergeusers', $fields),
        ));
    }

    // Add database settings.
    if ($showtabs) {
        $databasesettings->add($tabs);
    }

    $config = config::instance();
    $oldconfiglocalphpjson = $config->json_from_config_local_php_file();
    $defaultjson = $config->json_from_default_config();
    $calculatedjson = $config->json_from_calculated_config();

    $databasesettings->add(new json_setting(
        'tool_mergeusers/customdbsettings',
        new lang_string('settings:customdbsettings', 'tool_mergeusers'),
        new lang_string('settings:customdbsettingsdesc', 'tool_mergeusers'),
        $oldconfiglocalphpjson, // Old config/config.local.php content as default value, if exists.
        60,
        12,
    ));

    $databasesettings->add(new admin_setting_description(
        'tool_mergeusers/calculateddbsettings',
        new lang_string('settings:calculateddbsettings', 'tool_mergeusers'),
        new lang_string(
            'settings:calculateddbsettingsdesc',
            'tool_mergeusers',
            [
                'defaultname' => new lang_string('settings:defaultdbsettings', 'tool_mergeusers'),
                'calculatedname' => new lang_string('settings:calculateddbsettings', 'tool_mergeusers'),
                'default' => $defaultjson,
                'calculated' => $calculatedjson,
            ],
        ),
    ));
}

// Prevent build normal settings page.
// We provide a separated settings pages for every concern.
$settings = null;
