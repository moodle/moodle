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
 * assignment_upload assessable uploaded event.
 *
 * @package    assignment_upload
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignment_upload\event;

defined('MOODLE_INTERNAL') || die();

/**
 * assignment_upload assessable uploaded event class.
 *
 * @package    assignment_upload
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assessable_uploaded extends \core\event\assessable_uploaded {

    /**
     * Legacy files.
     *
     * @var array
     */
    protected $legacyfiles;

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "User {$this->userid} has uploaded a file in submission {$this->objectid}.";
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return stdClass
     */
    protected function get_legacy_eventdata() {
        $eventdata = new \stdClass();
        $eventdata->modulename   = 'assignment';
        $eventdata->cmid         = $this->context->instanceid;
        $eventdata->itemid       = $this->objectid;
        $eventdata->courseid     = $this->courseid;
        $eventdata->userid       = $this->userid;
        if ($this->legacyfiles) {
            $eventdata->files    = $this->legacyfiles; // This is depreceated - please use pathnamehashes instead!
        }
        $eventdata->pathnamehashes = $this->other['pathnamehashes'];
        return $eventdata;
    }

    /**
     * Return the legacy event name.
     *
     * @return string
     */
    public static function get_legacy_eventname() {
        return 'assessable_file_uploaded';
    }

    /**
     * Get legacy log data.
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        return array($this->courseid, 'assignment', 'upload', 'view.php?a=' . $this->other['assignmentid'],
            $this->other['assignmentid'], $this->context->id);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_assessable_uploaded', 'assignment_upload');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/assignment/view.php', array('id' => $this->context->instanceid));
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'assignment_submissions';
    }

    /**
     * Set legacy files.
     *
     * @param array $files
     * @return void
     */
    public function set_legacy_files($files) {
        $this->legacyfiles = $files;
    }

}
