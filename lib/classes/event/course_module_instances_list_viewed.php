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
 * Course module instances list_viewed event.
 *
 * @package    core
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;
defined('MOODLE_INTERNAL') || die();

/**
 * Course module instances list viewed event class.
 *
 * This is an abstract to guide the developers in using this event name for their events.
 * It is intended to be used when the user viewes the list of all the instances of a module
 * in a course. This replaces the historical 'view all' log entry generated in mod/somemod/index.php.
 *
 * Example:
 *
 *     \mod_chat\event\instances_list_viewed extends \core\event\course_module_instances_list_viewed
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class course_module_instances_list_viewed extends \core\event\content_viewed {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['level'] = self::LEVEL_OTHER;
    }

    /**
     * Set page detail.
     *
     * Override to prevent its use.
     */
    public function set_page_detail() {
    }

    /**
     * Custom validation.
     *
     * The parent validation is ignored on purpose.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        if ($this->context->contextlevel !== CONTEXT_COURSE) {
           throw new \coding_exception('The context must be a course level context.');
        }
        // Hack to by pass the requirement of the parent class. 'view_all' was the old fashioned-way
        // to describe the page listing all the instances of a module in a course.
        $this->data['other']['content'] = 'view_all';
        parent::validate_data();
    }

}
