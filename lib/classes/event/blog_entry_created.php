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
 * Event for when a new blog entry is added..
 *
 * @package    core
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Class blog_entry_created
 *
 * Class for event to be triggered when a blog entry is created.
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_entry_created extends base {

    /** @var \blog_entry A reference to the active blog_entry object. */
    protected $blogentry;

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['objecttable'] = 'post';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Set blog_entry object to be used by observers.
     *
     * @param \blog_entry $blogentry A reference to the active blog_entry object.
     */
    public function set_blog_entry(\blog_entry $blogentry) {
        $this->blogentry = $blogentry;
    }

    /**
     * Returns created blog_entry object for event observers.
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
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('evententryadded', 'core_blog');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' created the blog entry with id '$this->objectid'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/blog/index.php', array('entryid' => $this->objectid));
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }

    public static function get_objectid_mapping() {
        // Blogs are not backed up, so no mapping required for restore.
        return array('db' => 'post', 'restore' => base::NOT_MAPPED);
    }
}
