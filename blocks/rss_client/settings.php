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
 * Settings for the RSS client block.
 *
 * @package   block_rss_client
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_rss_client_num_entries', get_string('numentries', 'block_rss_client'),
                       get_string('clientnumentries', 'block_rss_client'), 5, PARAM_INT));

    $settings->add(new admin_setting_configtext('block_rss_client_timeout', get_string('timeout2', 'block_rss_client'),
                       get_string('timeout', 'block_rss_client'), 30, PARAM_INT));

    $link ='<a href="'.$CFG->wwwroot.'/blocks/rss_client/managefeeds.php">'.get_string('feedsaddedit', 'block_rss_client').'</a>';
    $settings->add(new admin_setting_heading('block_rss_addheading', '', $link));
}