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
 * Event for when blog entries are viewed.
 *
 * @package    core
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for event to be triggered when blog entries are viewed.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int courseid: id of associated course.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_entries_viewed extends base {

    /** @var array List of url params accepted*/
    private $validparams = array('entryid', 'tagid', 'userid', 'modid', 'groupid', 'courseid', 'search', 'fromstart');

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventblogentriesviewed', 'core_blog');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed blog entries.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        $params = array();
        foreach ($this->validparams as $param) {
            if (!empty($this->other[$param])) {
                $params[$param] = $this->other[$param];
            }
        }
        return new \moodle_url('/blog/index.php', $params);
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        $params = array();
        foreach ($this->validparams as $param) {
            if (!empty($this->other[$param])) {
                $params[$param] = $this->other[$param];
            }
        }
        $url = new \moodle_url('index.php', $params);
        return array (SITEID, 'blog', 'view', $url->out(), 'view blog entry');
    }
}
