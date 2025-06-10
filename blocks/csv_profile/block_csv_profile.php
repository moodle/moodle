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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class extends moodle block base class
 */
class block_csv_profile extends block_base {
    /**
     * Standard moodle function
     *
     * @var $this->title is populated via the lang string.
     */
    public function init() {
        $this->title = get_string('csvprofile', 'block_csv_profile');
    }

    /**
     * Allows configuration within Moodle.
     *
     * @return true
     */
    public function has_config() {
        return true;
    }

    /**
     * Standard moodle function
     *
     * @return array of applicable formats
     */
    public function applicable_formats() {
        return array('site' => true);
    }

    /**
     * Standard moodle function
     *
     * @return false (only allow one instance per page)
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Standard moodle function
     *
     * @return content for the page
     */
    public function get_content() {
        global $CFG, $USER, $PAGE, $OUTPUT;

        $currentcontext = context_system::instance();

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        if (isloggedin() && has_capability('block/csv_profile:uploadcsv', $currentcontext, $USER->id)) {

            $renderer = $this->page->get_renderer('block_csv_profile');
            $this->content->text = $renderer->csv_profile_tree($currentcontext);

            $this->content->text .= $OUTPUT->single_button(new moodle_url('/blocks/csv_profile/edit.php',
                array('returnurl' => $PAGE->url->out())),
                        get_string('manageuploads', 'block_csv_profile'), 'get');
        }
        return $this->content;
    }
}
