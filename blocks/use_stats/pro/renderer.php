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
 * Master block ckass for use_stats compiler
 *
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_use_stats_pro_renderer {

    /**
     * @global type $OUTPUT
     * @global type $COURSE
     * @global type $USER
     * @param type $userid
     * @param type $from
     * @param type $to
     * @param type $context
     * @return type
     */
    public function button_pdf($userid, $from, $to, $context) {
        global $OUTPUT, $COURSE, $USER;

        // XSS security.
        $capabilities = array('block/use_stats:seegroupdetails',
                              'block/use_stats:seecoursedetails',
                              'block/use_stats:seesitedetails');
        if (!has_any_capability($capabilities, $context)) {
            // Force report about yourself.
            $userid = $USER->id;
        }

        $config = get_config('block_use_stats');

        $now = time();
        $filename = 'report_user_'.$userid.'_'.date('Ymd_His', $now).'.pdf';

        $reportscope = (@$config->displayactivitytimeonly == DISPLAY_FULL_COURSE) ? 'fullcourse' : 'activities';
        $params = array(
            'id' => $COURSE->id,
            'from' => $from,
            'to' => $to,
            'userid' => $userid,
            'scope' => $reportscope,
            'timesession' => $now,
            'outputname' => $filename);

        $url = new moodle_url('/report/trainingsessions/pro/tasks/userpdfreportallcourses_batch_task.php', $params);

        $str = '';
        $str .= $OUTPUT->single_button($url, get_string('printpdf', 'block_use_stats'));

        return $str;
    }
}