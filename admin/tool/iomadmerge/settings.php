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
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (has_capability('tool/iomadmerge:iomadmerge', context_system::instance())) {
    /**
     * @var \part_of_admin_tree $ADMIN
     */
    if (!$ADMIN->locate('tool_iomadmerge')) {
        $ADMIN->add('accounts',
            new admin_category('tool_iomadmerge', get_string('pluginname', 'tool_iomadmerge')));
        $ADMIN->add('tool_iomadmerge',
            new admin_externalpage('tool_iomadmerge_merge', get_string('pluginname', 'tool_iomadmerge'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/iomadmerge/index.php',
                'tool/iomadmerge:iomadmerge'));
        $ADMIN->add('tool_iomadmerge',
            new admin_externalpage('tool_iomadmerge_viewlog', get_string('viewlog', 'tool_iomadmerge'),
                $CFG->wwwroot . '/' . $CFG->admin . '/tool/iomadmerge/view.php',
                'tool/iomadmerge:iomadmerge'));
    }
}

if ($hassiteconfig) {
    require_once(__DIR__ . '/lib/autoload.php');
    require_once(__DIR__ . '/lib.php');

    // Add configuration for making user suspension optional.
    $settings = new admin_settingpage('iomadmerge_settings',
        get_string('pluginname', 'tool_iomadmerge'));

    $settings->add(new admin_setting_configcheckbox('tool_iomadmerge/suspenduser',
        get_string('suspenduser_setting', 'tool_iomadmerge'),
        get_string('suspenduser_setting_desc', 'tool_iomadmerge'),
        1));

    $supporting_lang = (tool_iomadmerge_transactionssupported()) ? 'transactions_supported' : 'transactions_not_supported';

    $settings->add(new admin_setting_configcheckbox('tool_iomadmerge/transactions_only',
        get_string('transactions_setting', 'tool_iomadmerge'),
        get_string('transactions_setting_desc', 'tool_iomadmerge') . '<br /><br />' .
            get_string($supporting_lang, 'tool_iomadmerge'),
        1));

    $exceptionoptions = tool_iomadmerge_build_exceptions_options();
    $settings->add(new admin_setting_configmultiselect('tool_iomadmerge/excluded_exceptions',
        get_string('excluded_exceptions', 'tool_iomadmerge'),
        get_string('excluded_exceptions_desc', 'tool_iomadmerge', $exceptionoptions->defaultvalue),
        array($exceptionoptions->defaultkey), //default value: empty => apply all exceptions.
        $exceptionoptions->options));

    // Quiz attempts.
    $quizoptions = tool_iomadmerge_build_quiz_options();
    $settings->add(new admin_setting_configselect('tool_iomadmerge/quizattemptsaction',
        get_string('quizattemptsaction', 'tool_iomadmerge'),
        get_string('quizattemptsaction_desc', 'tool_iomadmerge', $quizoptions->allstrings),
        $quizoptions->defaultkey,
        $quizoptions->options)
    );

    $settings->add(new admin_setting_configcheckbox('tool_iomadmerge/uniquekeynewidtomaintain',
        get_string('uniquekeynewidtomaintain', 'tool_iomadmerge'),
        get_string('uniquekeynewidtomaintain_desc', 'tool_iomadmerge'),
        1));

    // Add settings
    $ADMIN->add('tools', $settings);
}
