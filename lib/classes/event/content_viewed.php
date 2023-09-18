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
 * This class has been deprecated, please extend base event or other relevent abstract class.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @deprecated since Moodle 2.7
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

debugging('core\event\content_viewed has been deprecated. Please extend base event or other relevant abstract class.',
        DEBUG_DEVELOPER);

/**
 * Class content_viewed.
 *
 * This class has been deprecated, please extend base event or other relevent abstract class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - string content: name of the content viewed.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @deprecated since Moodle 2.7
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
        return "The user with id '$this->userid' viewed content.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception when validation does not pass.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        // Make sure this class is never used without a content identifier.
        if (empty($this->other['content'])) {
            throw new \coding_exception('The \'content\' value must be set in other.');
        }
    }

    public static function get_other_mapping() {
        return false;
    }

    /**
     * This event has been deprected.
     *
     * @return boolean
     */
    public static function is_deprecated() {
        return true;
    }
}
