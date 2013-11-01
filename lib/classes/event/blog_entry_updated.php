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
 * Event to be triggered when a blog entry is updated.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Class blog_entry_updated
 *
 * Event to be triggered when a blog entry is updated.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_entry_updated extends base {

    /** @var \blog_entry A reference to the active blog_entry object. */
    protected $customobject;

    /**
     * Set basic event properties.
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['objecttable'] = 'post';
        $this->data['crud'] = 'u';
        $this->data['level'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Set custom data of the event.
     *
     * @param \blog_entry $data A reference to the active blog_entry object.
     */
    public function set_custom_data(\blog_entry $data) {
        $this->customobject = $data;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('evententryupdated', 'core_blog');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'Blog entry id '. $this->objectid. ' was updated by userid '. $this->userid;
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/blog/index.php', array('entryid' => $this->objectid, 'userid' => $this->userid));
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return \blog_entry
     */
    protected function get_legacy_eventdata() {
        return $this->customobject;
    }

    /**
     * Legacy event name.
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'blog_entry_edited';
    }

    /**
     * Replace legacy add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array(SITEID, 'blog', 'update', 'index.php?userid=' . $this->relateduserid . '&entryid=' . $this->objectid,
                 $this->customobject->subject);
    }
}

