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
 * Definition of block_backadel tasks.
 *
 * @package    block_backadel
 * @category   block
 * @copyright  2016 Louisiana State University - David Elliott, Robert Russo, Chad Mazilly
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Get the requisite dependencies.
require_once($CFG->dirroot . '/blocks/backadel/lib.php');
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');

/**
 * Main class for setting up the block.
 * @uses block_list
 * @package block_backadel
 */
class block_backadel extends block_list {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_backadel');
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return array('site' => true, 'my' => false, 'course' => false);
    }

    /**
     * Block has configuration.
     *
     * @return true
     */
    public function has_config() {
        return true;
    }

    /**
     * Returns the time running.
     *
     * @return time running
     */
    public function seconds2human($secondsrun) {
        $s = $secondsrun%60 . ' seconds';
        $m = floor(($secondsrun%3600)/60) . ' minutes,';
        $h = floor(($secondsrun%86400)/3600) . ' hours,';
        $d = floor(($secondsrun%2592000)/86400) . ' days,';
        $M = floor($secondsrun/2592000) . ' months,';

        if ($M == '0 months,' || $d == '0 days,' || $h == '0 hours,' || $m == '0 minutes,') {
            $es = 'and ' . $s;
        } else {
            $es = $s;
        }

        $em = $m == '0 minutes,' ? '' : $m;
        $eh = $h == '0 hours,' ? '' : $h;
        $ed = $d == '0 days,' ? '' : $d;
        $eM = $M == '0 months,' ? '' : $M;

        return "$eM $ed $eh $em $es";
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        // Set up the globals we need.
        global $DB, $CFG, $USER, $OUTPUT;

        // Check to make sure the Admin is using the block.
        if (!is_siteadmin($USER->id)) {
            return $this->content;
        }

        // Return the content if there is any.
        if ($this->content !== null) {
            return $this->content;
        }

        // Set up the table.
        $table = 'block_backadel_statuses';

        // Get the number of pending and failed backups.
        $numpending = $DB->count_records_select($table, "status='SUCCESS'");
        $numfailed = $DB->count_records_select($table, "status='FAIL'");

        // Set the $running varuable to the backup status.
        $running = get_config('block_backadel', 'running');

        // Give the admin the running / not status.
        if (!$running) {
            $statustext = get_string('status_not_running', 'block_backadel');
        } else {
            $secondsrun = round(time() - $running);
            $timerunning = self::seconds2human($secondsrun);
            $statustext = get_string('status_running', 'block_backadel', $timerunning);
        }

        // Build the block itself.
        $icons = array();
        $items = array();
        $params = array('class' => 'icon');

/*
        // Build the icon list.
        $icons[] = $OUTPUT->pix_icon('i/backup', '', 'moodle', $params);
        $icons[] = $OUTPUT->pix_icon('i/delete', '', 'moodle', $params);
        $icons[] = $OUTPUT->pix_icon('i/risk_xss', '', 'moodle', $params);
        $icons[] = $OUTPUT->pix_icon('i/calendareventtime', '', 'moodle', $params);

        // Build the list of items.
        $items[] = $this->build_link('index');
        $items[] = $this->build_link('delete') . "($numpending)";
        $items[] = $this->build_link('failed') . "($numfailed)";
        $items[] = $statustext;
*/

        // Build the list of items with icons and links on the same line.
        $items[] = $OUTPUT->pix_icon('i/backup', '', 'moodle', $params) .
            '' . $this->build_link('index');
        $items[] = $OUTPUT->pix_icon('i/delete', '', 'moodle', $params) .
            '' . $this->build_link('delete') . " ($numpending)";
        $items[] = $OUTPUT->pix_icon('i/risk_xss', '', 'moodle', $params) .
            '' . $this->build_link('failed') . " ($numfailed)";
        $items[] = $OUTPUT->pix_icon('i/calendareventtime', '', 'moodle', $params) .
            '' . $statustext;

        // Bring it all together.
        $this->content = new stdClass;
        $this->content->icons = $icons;
        $this->content->items = $items;
        $this->content->footer = '';

        // Return the block.
        return $this->content;
    }

    /**
     * Set up the page link
     *
     * @return link
     */
    public function build_link($page) {
        $url = new moodle_url("/blocks/backadel/$page.php");
        return html_writer::link($url, get_string("block_$page", 'block_backadel'));
    }
}
