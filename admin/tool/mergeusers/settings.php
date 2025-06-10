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
 * Version information
 *
 * @package    tool
 * @subpackage mergeusers
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (has_capability('tool/mergeusers:mergeusers', context_system::instance())) {
    /**
     * @var \part_of_admin_tree $ADMIN
     */
    if (!$ADMIN->locate('tool_mergeusers')) {
        $ADMIN->add('accounts',
            new admin_category('tool_mergeusers', get_string('pluginname', 'tool_mergeusers')));
        $ADMIN->add('tool_mergeusers',
            new admin_externalpage('tool_mergeusers_merge', get_string('pluginname', 'tool_mergeusers'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/mergeusers/index.php',
                'tool/mergeusers:mergeusers'));
        $ADMIN->add('tool_mergeusers',
            new admin_externalpage('tool_mergeusers_viewlog', get_string('viewlog', 'tool_mergeusers'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/mergeusers/view.php',
                'tool/mergeusers:mergeusers'));
    }
}

if ($hassiteconfig) {
    require_once(__DIR__ . '/lib/autoload.php');
    require_once(__DIR__ . '/lib.php');

    // Add configuration for making user suspension optional.
    $settings = new admin_settingpage('mergeusers_settings',
        get_string('pluginname', 'tool_mergeusers'));

    $settings->add(new admin_setting_configcheckbox('tool_mergeusers/suspenduser',
        get_string('suspenduser_setting', 'tool_mergeusers'),
        get_string('suspenduser_setting_desc', 'tool_mergeusers'),
        1));

    $supporting_lang = (tool_mergeusers_transactionssupported()) ? 'transactions_supported' : 'transactions_not_supported';

    $settings->add(new admin_setting_configcheckbox('tool_mergeusers/transactions_only',
        get_string('transactions_setting', 'tool_mergeusers'),
        get_string('transactions_setting_desc', 'tool_mergeusers') . '<br /><br />' .
            get_string($supporting_lang, 'tool_mergeusers'),
        1));

    $exceptionoptions = tool_mergeusers_build_exceptions_options();
    $settings->add(new admin_setting_configmultiselect('tool_mergeusers/excluded_exceptions',
        get_string('excluded_exceptions', 'tool_mergeusers'),
        get_string('excluded_exceptions_desc', 'tool_mergeusers', $exceptionoptions->defaultvalue),
        array($exceptionoptions->defaultkey), //default value: empty => apply all exceptions.
        $exceptionoptions->options));

    // Quiz attempts.
    $quizoptions = tool_mergeusers_build_quiz_options();
    $settings->add(new admin_setting_configselect('tool_mergeusers/quizattemptsaction',
        get_string('quizattemptsaction', 'tool_mergeusers'),
        get_string('quizattemptsaction_desc', 'tool_mergeusers', $quizoptions->allstrings),
        $quizoptions->defaultkey,
        $quizoptions->options)
    );

    $settings->add(new admin_setting_configcheckbox('tool_mergeusers/uniquekeynewidtomaintain',
        get_string('uniquekeynewidtomaintain', 'tool_mergeusers'),
        get_string('uniquekeynewidtomaintain_desc', 'tool_mergeusers'),
        1));

    // Add settings
    $ADMIN->add('tools', $settings);
}
