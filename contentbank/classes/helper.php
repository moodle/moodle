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
 * Contains helper class for the content bank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_contentbank;

/**
 * Helper class for the content bank.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Getting content bank page ready for the breadcrumbs.
     *
     * @param \context $context Context of the current page.
     * @param string $title Title of the current page.
     * @param bool $internal True if is an internal page, false otherwise.
     */
    public static function get_page_ready(\context $context, string $title, bool $internal = false): void {
        global $PAGE, $DB;

        $PAGE->set_context($context);
        $PAGE->set_heading(self::get_page_heading($context));
        $PAGE->set_secondary_active_tab('contentbank');
        $cburl = new \moodle_url('/contentbank/index.php', ['contextid' => $context->id]);

        switch ($context->contextlevel) {
            case CONTEXT_COURSE:
                $courseid = $context->instanceid;
                $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
                $PAGE->set_course($course);
                \navigation_node::override_active_url(new \moodle_url('/course/view.php', ['id' => $courseid]));
                $PAGE->navbar->add($title, $cburl);
                $PAGE->set_pagelayout('incourse');
                break;
            case CONTEXT_COURSECAT:
                $coursecat = $context->instanceid;
                \navigation_node::override_active_url(new \moodle_url('/course/index.php', ['categoryid' => $coursecat]));
                $PAGE->navbar->add($title, $cburl);
                $PAGE->set_pagelayout('coursecategory');
                break;
            default:
                if ($node = $PAGE->navigation->find('contentbank', \global_navigation::TYPE_CUSTOM)) {
                    $node->make_active();
                }
                $PAGE->set_pagelayout('standard');
        }
    }

    /**
     * Get the page heading based on the current context
     *
     * @param \context $context The current context of the page
     * @return string
     */
    public static function get_page_heading(\context $context): string {
        global $SITE;

        $title = get_string('contentbank');
        if ($context->id == \context_system::instance()->id) {
            $title = $SITE->fullname;
        } else if ($context->contextlevel == CONTEXT_COURSE) {
            $course = get_course($context->instanceid);
            $title = $course->fullname;
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            $category = \core_course_category::get($context->instanceid);
            $title = $category->get_formatted_name();
        }

        return $title;
    }
}
