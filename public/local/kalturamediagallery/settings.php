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

/*
 * @package    local
 * @subpackage kalturamediagallery
 * @copyright  2016 Queen Mary University of London
 * @author     Phil Lello <phil@dunlop-lello.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die('Invalid access');

global $CFG;
require_once $CFG->dirroot. '/local/kalturamediagallery/lib.php';

if ($hassiteconfig) {
    $settings = new admin_settingpage(
            'local_kalturamediagallery',
            get_string('pluginname', 'local_kalturamediagallery')
        );

    //heading
    $setting = new admin_setting_heading(
            'heading',
            '', get_string('setting_heading_desc', 'local_kalturamediagallery')
        );
    $setting->plugin = 'local_kalturamediagallery';
    $settings->add($setting);

    //link location
    $setting = new admin_setting_configselect(
            'link_location',
            get_string('link_location', 'local_kalturamediagallery'),
            get_string('link_location_desc', 'local_kalturamediagallery'),
            LOCAL_KALTURAMEDIAGALLERY_LINK_LOCATION_NAVIGATION_BLOCK,
            array(
                LOCAL_KALTURAMEDIAGALLERY_LINK_LOCATION_NAVIGATION_BLOCK => get_string('link_location_navigation', 'local_kalturamediagallery'),
                LOCAL_KALTURAMEDIAGALLERY_LINK_LOCATION_COURSE_SETTINGS => get_string('link_location_course_settings', 'local_kalturamediagallery'),
            )
        );
    $setting->plugin = 'local_kalturamediagallery';
    $settings->add($setting);

    $ADMIN->add('localplugins', $settings);
}
