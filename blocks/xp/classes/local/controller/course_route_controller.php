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
 * Course route controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use coding_exception;

/**
 * Course route controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class course_route_controller extends route_controller {

    /** @var stdClass The course. */
    protected $course;
    /** @var int The course ID. */
    protected $courseid;
    /** @var bool Requires a wide view. */
    protected $iswideview = false;
    /** @var bool Whether the page supports groups. */
    protected $supportsgroups = false;
    /** @var \block_xp\local\course_world */
    protected $world;
    /** @var \block_xp\local\factory\course_world_navigation_factory The navigation factory. */
    protected $navfactory;

    /** @var int The group ID. */
    private $groupid;

    /**
     * Authentication.
     *
     * @return void
     */
    protected function require_login() {
        global $CFG;

        $courseid = intval($this->get_param('courseid'));
        if (!$courseid) {
            throw new coding_exception('Excepted a course ID parameter but got none.');
        }

        $this->courseid = $courseid;
        require_login($courseid);
    }

    /**
     * Post authentication.
     *
     * Use this to initialise objects which you'll need throughout the request.
     *
     * @return void
     */
    protected function post_login() {
        parent::post_login();
        $this->world = \block_xp\di::get('course_world_factory')->get_world($this->courseid);
        $this->courseid = $this->world->get_courseid();
        $this->navfactory = \block_xp\di::get('course_world_navigation_factory');
    }

    /**
     * Moodle page specifics.
     *
     * @return void
     */
    protected function page_setup() {
        global $CFG, $PAGE;

        // Note that the context was set by require_login().
        $PAGE->set_url($this->pageurl->get_compatible_url());
        $PAGE->set_pagelayout($this->get_page_layout());
        $PAGE->set_title($this->get_page_html_head_title());
        $PAGE->set_heading($this->get_page_title());

        if (!$this->iswideview) {
            $PAGE->add_body_class('limitedwidth');
        }
    }

    /**
     * Whether the page supports groups.
     *
     * @return bool
     */
    protected function is_supporting_groups() {
        return $this->supportsgroups;
    }

    /**
     * Return the group ID.
     *
     * @return int|false False if groups are not used used, 0 for all groups, else group ID.
     */
    final protected function get_groupid() {
        if (!$this->is_supporting_groups()) {
            throw new coding_exception('This page is not marked as supporting groups.');
        }
        if ($this->groupid === null) {
            $this->groupid = groups_get_course_group($this->get_course(), true);
        }
        return $this->groupid;
    }

    /**
     * The page layout to use.
     *
     * @return string
     */
    protected function get_page_layout() {
        return 'course';
    }

    /**
     * The page title (in <head>).
     *
     * @return string
     */
    abstract protected function get_page_html_head_title();

    /**
     * The page title.
     *
     * @return string
     */
    protected function get_page_title() {
        global $COURSE;
        return format_string($COURSE->fullname);
    }

    /**
     * Get the course.
     *
     * @return stdClass
     */
    final protected function get_course() {
        if (!$this->course) {
            if (!$this->courseid) {
                throw new coding_exception('Too early to request the course.');
            }
            $this->course = get_course($this->courseid, false);
        }
        return $this->course;
    }

    /**
     * Print the group menu.
     *
     * @return string
     */
    protected function print_group_menu() {
        if (!$this->is_supporting_groups()) {
            throw new coding_exception('This page is not marked as supporting groups.');
        }
        echo groups_print_course_menu($this->get_course(), $this->pageurl->get_compatible_url());
    }

}
