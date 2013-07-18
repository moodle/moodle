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

namespace core\event;

/**
 * Event for when a new blog entry is deleted.
 *
 * @package    core_blog
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class blog_entry_deleted extends \core\event\base {

    /** @var  \blog_entry A reference to the active blog_entry object. */
    protected $customobject;

    protected function init() {
        $this->context = \context_system::instance();
        $this->data['objecttable'] = 'post';
        $this->data['crud'] = 'd';
        // TODO: MDL-37658 set level.
        $this->data['level'] = 50;
    }

    /**
     * Returns localised general event name.
     *
     * @return \lang_string
     */
    public static function get_name() {
        return new \lang_string("evententrydeleted", "core_blog");
    }

    /**
     * Set custom data of the event.
     *
     * @param \blog_entry $data A reference to the active blog_entry object.
     */
    public function set_custom_data($data) {
        $this->customobject = $data;
    }

    /**
     * Returns localised description of what happened.
     *
     * @return \lang_string
     */
    public function get_description() {
        $a = new \stdClass();
        $a->subject = $this->other['record']['subject'];
        $a->userid = $this->userid;
        return new \lang_string("evententrydeleteddesc", "core_blog", $a);
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
        return $this->customobject;
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        return array (SITEID, 'blog', 'delete', 'index.php?userid='.$this->userid, 'deleted blog entry with entry id# '. $this->objectid);
    }
}
