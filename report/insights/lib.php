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
 * This page lists public api for tool_monitor plugin.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the tool items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass        $course     The course to object for the tool
 * @param context         $context    The context of the course
 * @return void
 */
function report_insights_extend_navigation_course($navigation, $course, $context) {

    if (has_capability('moodle/analytics:listinsights', $context)) {

        $modelids = \core_analytics\manager::cached_models_with_insights($context);
        if (!empty($modelids)) {
            $url = new moodle_url('/report/insights/insights.php', array('contextid' => $context->id));
            $node = navigation_node::create(get_string('insights', 'report_insights'), $url, navigation_node::TYPE_SETTING,
                null, null, new pix_icon('i/report', get_string('insights', 'report_insights')));
            $navigation->add_node($node);
        }
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 *
 * @return bool
 */
function report_insights_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {

    $context = \context_user::instance($user->id);
    if (\core_analytics\manager::check_can_list_insights($context, true)) {

        $modelids = \core_analytics\manager::cached_models_with_insights($context);
        if (!empty($modelids)) {
            $url = new moodle_url('/report/insights/insights.php', array('contextid' => $context->id));
            $node = new core_user\output\myprofile\node('reports', 'insights', get_string('insights', 'report_insights'),
                null, $url);
            $tree->add_node($node);
        }
    }
}

/**
 * Adds nodes to category navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $context The context of the course
 * @return void|null return null if we don't want to display the node.
 */
function report_insights_extend_navigation_category_settings($navigation, $context) {

    if (has_capability('moodle/analytics:listinsights', $context)) {

        $modelids = \core_analytics\manager::cached_models_with_insights($context);
        if (!empty($modelids)) {
            $url = new moodle_url('/report/insights/insights.php', array('contextid' => $context->id));

            $node = navigation_node::create(
                get_string('insights', 'report_insights'),
                $url,
                navigation_node::NODETYPE_LEAF,
                null,
                'insights',
                new pix_icon('i/report', get_string('insights', 'report_insights'))
            );

            $navigation->add_node($node);
        }
    }
}
