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
 * Event for when a new blog entry is deleted.
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Class blog_entry_deleted
 *
 * Event for when a new blog entry is deleted.
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class blog_entry_deleted extends base {

    /** @var \blog_entry A reference to the active blog_entry object. */
    protected $blogentry;

    /**
     * Set basic event properties.
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['objecttable'] = 'post';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string("evententrydeleted", "core_blog");
    }

    /**
     * Sets blog_entry object to be used by observers.
     *
     * @param \blog_entry $blogentry A reference to the active blog_entry object.
     */
    public function set_blog_entry(\blog_entry $blogentry) {
        $this->blogentry = $blogentry;
    }

    /**
     * Returns deleted blog entry for event observers.
     *
     * @throws \coding_exception
     * @return \blog_entry
     */
    public function get_blog_entry() {
        if ($this->is_restored()) {
            throw new \coding_exception('Function get_blog_entry() can not be used on restored events.');
        }
        return $this->blogentry;
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The blog entry with the id '$this->objectid' was deleted by user with the id '$this->userid'.";
    }

    /**
     * Does this event replace legacy event?
     *
     * @return string legacy event name
     */
    public static function get_legacy_eventname() {
        return 'blog_entry_deleted';
    }

    /**
     * Legacy event data if get_legacy_eventname() is not empty.
     *
     * @return \blog_entry
     */
    protected function get_legacy_eventdata() {
        return $this->blogentry;
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array (SITEID, 'blog', 'delete', 'index.php?userid=' . $this->relateduserid, 'deleted blog entry with entry id# '.
                $this->objectid);
    }
}
