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
 * Event for when a new blog entry is associated with a context.
 *
 * @package    core
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for event to be triggered when a new blog entry is associated with a context.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string associatetype: type of blog association, course/coursemodule.
 *      - int blogid: id of blog.
 *      - int associateid: id of associate.
 *      - string subject: blog subject.
 * }
 *
 * @package    core
 * @since      Moodle 2.7
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class blog_association_created extends base {

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->context = \context_system::instance();
        $this->data['objecttable'] = 'blog_association';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventblogassociationadded', 'core_blog');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' associated the context '{$this->other['associatetype']}' with id " .
            "'{$this->other['associateid']}' to the blog entry with id '{$this->other['blogid']}'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/blog/index.php', array('entryid' => $this->other['blogid']));
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception when validation fails.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (empty($this->other['associatetype']) || ($this->other['associatetype'] !== 'course'
                && $this->other['associatetype'] !== 'coursemodule')) {
            throw new \coding_exception('The \'associatetype\' value must be set in other and be a valid type.');
        }

        if (!isset($this->other['blogid'])) {
            throw new \coding_exception('The \'blogid\' value must be set in other.');
        }

        if (!isset($this->other['associateid'])) {
            throw new \coding_exception('The \'associateid\' value must be set in other.');
        }

        if (!isset($this->other['subject'])) {
            throw new \coding_exception('The \'subject\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // Blogs are not included in backups, so no mapping required for restore.
        return array('db' => 'blog_association', 'restore' => base::NOT_MAPPED);
    }

    public static function get_other_mapping() {
        // Blogs are not included in backups, so no mapping required for restore.
        $othermapped = array();
        $othermapped['blogid'] = array('db' => 'post', 'restore' => base::NOT_MAPPED);
        // The associateid field is varying (context->instanceid) so cannot be mapped.

        return $othermapped;
    }
}
