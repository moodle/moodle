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
 * @package    report_coursesize
 * @copyright  2014 Catalyst IT {@link http://www.catalyst.net.nz}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$ADMIN->add('reports', new admin_externalpage('reportcoursesize', get_string('pluginname', 'report_coursesize'),
                                              "$CFG->wwwroot/report/coursesize/index.php", 'report/coursesize:view'));

$settings = new admin_settingpage('report_coursesize_settings', new lang_string('pluginname', 'report_coursesize'));
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configselect(
        'report_coursesize/calcmethod',
        new lang_string('calcmethod', 'report_coursesize'),
        new lang_string('calcmethodhelp', 'report_coursesize'),
        'cron',
        array(
            'cron' => new lang_string('calcmethodcron', 'report_coursesize'),
            'live' => new lang_string('calcmethodlive', 'report_coursesize'),
        )
    ));

    $settings->add(new admin_setting_configtext(
        'report_coursesize/numberofusers',
        new lang_string('numberofusers', 'report_coursesize'),
        new lang_string('numberofusershelp', 'report_coursesize'),
        10,
        PARAM_INT
    ));
}
