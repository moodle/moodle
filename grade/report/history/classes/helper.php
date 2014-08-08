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
 * Helper class for gradehistory report.
 *
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradereport_history;

defined('MOODLE_INTERNAL') || die;

/**
 * Helper class for gradehistory report.
 *
 * @since      Moodle 2.8
 * @package    gradereport_history
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get an instance of the user select button {@link gradereport_history_user_button}.
     *
     * @param int $courseid       course id.
     * @param array $currentusers List of currently selected users.
     *
     * @return output\user_button the user select button.
     */
    public static function get_user_select_button($courseid, array $currentusers = null) {
        global $PAGE;
        $button = new output\user_button($PAGE->url, get_string('selectusers', 'gradereport_history'), 'get');
        $button->class .= ' gradereport_history_plugin';

        $module = array('moodle-gradereport_history-userselector');
        $arguments = array(
            'courseid'            => $courseid,
            'ajaxurl'             => '/grade/report/history/users_ajax.php',
            'url'                 => $PAGE->url->out(false),
            'selectedUsers'       => $currentusers,
        );

        $function = 'Y.M.gradereport_history.UserSelector.init';
        $button->require_yui_module($module, $function, array($arguments));
        $button->strings_for_js(array(
            'errajaxsearch',
            'finishselectingusers',
            'foundoneuser',
            'foundnusers',
            'loadmoreusers',
            'selectusers',
        ), 'gradereport_history');
        $button->strings_for_js(array(
            'loading'
        ), 'admin');
        $button->strings_for_js(array(
            'noresults',
            'search'
        ));

        return $button;
    }

}
