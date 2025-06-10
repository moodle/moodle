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
 * Connects to LSU web service for downloading and updating user photos
 *
 * @package    block_my_picture
 * @copyright  2008, Adam Zapletal, 2017, Robert Russo, Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Set and get the config variable.
global $CFG;
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->dirroot . '/blocks/my_picture/lib.php');
require_login();

// The class for the my_picture scehduled task.
class grab_my_picture {

    /**
     *
     * Fetch and insert images from the web service
     * Called via the Moodle task scheduling system
     *
     * @global stdClass $CFG
     * @global stdClass $DB
     * @return true
     */
    public function run_grab_mypictures() {
        global $CFG, $DB;

        $s = function($k, $a=null) {
            return get_string($k, 'block_my_picture', $a);
        };

        // Quit if the webservice doesn't respond with json.
        if (!mypic_verifyWebserviceExists()) {
            mtrace(get_string("cron_webservice_err", "block_my_picture"));
            return true;
        }

        mtrace("\n" . $s('start'));

        if (get_config('block_my_picture', 'fetch')) {
            $limit = get_config('block_my_picture', 'cron_users');
            $users = mypic_get_users_without_pictures($limit);
            if (!$users) {
                mtrace($s('no_missing_pictures'));
            } else {
                mypic_batch_update($users);
            }
        }

        return true;
    }
}