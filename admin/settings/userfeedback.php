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
 * This file contains call to feedback settings
 *
 * @package    core
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $userfeedback->add(new admin_setting_configcheckbox('enableuserfeedback',
            new lang_string('enableuserfeedback', 'admin'),
            new lang_string('enableuserfeedback_desc', 'admin'), 0, 1, 0));

    $options = [
        core_userfeedback::REMIND_AFTER_UPGRADE => new lang_string('userfeedbackafterupgrade', 'admin'),
        core_userfeedback::REMIND_PERIODICALLY => new lang_string('userfeedbackperiodically', 'admin'),
        core_userfeedback::REMIND_NEVER => new lang_string('never'),
    ];
    $userfeedback->add(new admin_setting_configselect('userfeedback_nextreminder',
            new lang_string('userfeedbacknextreminder', 'admin'),
            new lang_string('userfeedbacknextreminder_desc', 'admin'), 1, $options));
    $userfeedback->hide_if('userfeedback_nextreminder', 'enableuserfeedback');

    $userfeedback->add(new admin_setting_configtext('userfeedback_remindafter',
            new lang_string('userfeedbackremindafter', 'admin'),
            new lang_string('userfeedbackremindafter_desc', 'admin'), 90, PARAM_INT));
    $userfeedback->hide_if('userfeedback_remindafter', 'enableuserfeedback');
    $userfeedback->hide_if('userfeedback_remindafter', 'userfeedback_nextreminder', 'eq', 3);

}
