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
 * A scheduled task.
 *
 * The scheduled task for fetching photos.
 *
 * @package    block_my_picture
 * @copyright  2017 Robert Russo, Louisiana State University
 */
namespace block_my_picture\task;

defined('MOODLE_INTERNAL') || die();

// Extend the Moodle scheduled task class with our mods.
class grab_mypictures extends \core\task\scheduled_task {

    // Get a descriptive name for this task (shown to admins).
    // @return string.
    public function get_name() {
        return get_string('grab_mypictures', 'block_my_picture');
    }

    // Do the job and throw exceptions on errors (the job will be retried).
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/my_picture/classes/grab_my_picture.php');
        $mypictures = new \grab_my_picture();
        $mypictures->run_grab_mypictures();
    }
}
