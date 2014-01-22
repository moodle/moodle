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
 * Abstract event for content viewing.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Class content_viewed.
 *
 * Base class for a content view event. Each plugin must extend this to create their own content view event.
 *
 * An example usage:-
 *  $event = \report_participation\event\content_viewed::create(array('courseid' => $course->id,
 *          'other' => array('content' => 'participants'));
 *  $event->set_page_detail();
 *  $event->set_legacy_logdata(array($course->id, "course", "report participation",
 *          "report/participation/index.php?id=$course->id", $course->id));
 *  $event->trigger();
 * where \report_participation\event\content_viewed extends \core\event\content_viewed
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      @type string content viewed content identifier.
 * }
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class content_viewed extends base {

    /** @var null|array $legacylogdata  Legacy log data */
    protected $legacylogdata = null;

    /**
     * Set basic properties of the event.
     */
    protected function init() {
        global $PAGE;

        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = $PAGE->context;
    }

    /**
     * Set basic page properties.
     */
    public function set_page_detail() {
        global $PAGE;
        if (!isset($this->data['other'])) {
            $this->data['other'] = array();
        }
        $this->data['other'] = array_merge(array('url'     => $PAGE->url->out_as_local_url(false),
                                             'heading'     => $PAGE->heading,
                                             'title'       => $PAGE->title), $this->data['other']);
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcontentviewed', 'moodle');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'User with id ' . $this->userid . ' viewed content';
    }

    /**
     * Set legacy logdata.
     *
     * @param array $legacydata legacy logdata.
     */
    public function set_legacy_logdata(array $legacydata) {
        $this->legacylogdata = $legacydata;
    }

    /**
     * Get legacy logdata.
     *
     * @return null|array legacy log data.
     */
    protected function get_legacy_logdata() {
        return $this->legacylogdata;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        if (debugging('', DEBUG_DEVELOPER)) {
            // Make sure this class is never used without a content identifier.
            if (empty($this->other['content'])) {
                throw new \coding_exception('content_viewed event must define content identifier.');
            }
        }
    }
}

