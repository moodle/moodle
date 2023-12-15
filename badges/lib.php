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
 * Defines various library functions.
 *
 * @package   core_badges
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
function core_badges_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $PAGE, $USER, $SITE;
    require_once($CFG->dirroot . '/badges/renderer.php');
    if (empty($CFG->enablebadges) || (!empty($course) && empty($CFG->badges_allowcoursebadges))) {
        // Y U NO LIKE BADGES ?
        return true;
    }

    // Add category. This node should appear after 'contact' so that administration block appears towards the end. Refer MDL-49928.
    $category = new core_user\output\myprofile\category('badges', get_string('badges', 'badges'), 'contact');
    $tree->add_category($category);
    $context = context_user::instance($user->id);
    $courseid = empty($course) ? 0 : $course->id;

    if ($USER->id == $user->id || has_capability('moodle/badges:viewotherbadges', $context)) {
        $records = badges_get_user_badges($user->id, $courseid, null, null, null, true);
        $renderer = new core_badges_renderer($PAGE, '');

        // Local badges.
        if ($records) {
            $title = get_string('localbadgesp', 'badges', format_string($SITE->fullname));
            $content = $renderer->print_badges_list($records, $user->id, true);
            $localnode = $mybadges = new core_user\output\myprofile\node('badges', 'localbadges', $title, null, null, $content);
            $tree->add_node($localnode);
        }

        // External badges.
        if ($courseid == 0 && !empty($CFG->badges_allowexternalbackpack)) {
            $backpack = get_backpack_settings($user->id);
            if (isset($backpack->totalbadges) && $backpack->totalbadges !== 0) {
                $title = get_string('externalbadgesp', 'badges');
                $content = $renderer->print_badges_list($backpack->badges, $user->id, true, true);
                $externalnode = $mybadges = new core_user\output\myprofile\node('badges', 'externalbadges', $title, null, null,
                    $content);
                $tree->add_node($externalnode);
            }
        }
    }
}

/**
 * Returns badges tagged with a specified tag.
 *
 * @param object $tag
 * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
 *             are displayed on the page and the per-page limit may be bigger
 * @param null|int $fromctx context id where the link was displayed, may be used by callbacks
 *            to display items in the same context first
 * @param null|int $ctx context id where to search for records
 * @param bool $rec search in subcontexts as well
 * @param int $page 0-based number of page being displayed
 * @return \core_tag\output\tagindex
 */
function badge_get_tagged_badges(object $tag, bool $exclusivemode = false, null|int $fromctx = 0, null|int $ctx = 0,
                                 bool $rec = true, int $page = 0): object {
    global $OUTPUT;

    $badgecount = $tag->count_tagged_items('core_badges', 'badge');
    $perpage = $exclusivemode ? 20 : 5;
    $content = '';
    $totalpages = ceil($badgecount / $perpage);

    if ($badgecount) {
        $badges = $tag->get_tagged_items('core_badges', 'badge', $page * $perpage, $perpage);
        $tagfeed = new core_tag\output\tagfeed();
        foreach ($badges as $badge) {
            $badgelink = new moodle_url('/badges/badgeclass.php', ['id' => $badge->id]);
            $fullname = html_writer::link($badgelink, $badge->name);
            $icon = html_writer::link($badgelink, html_writer::empty_tag('img',
                ['src' => $OUTPUT->image_url('i/badge')]));
            $tagfeed->add($icon, $fullname, '<br>');
        }

        $items = $tagfeed->export_for_template($OUTPUT);

        $content .= $OUTPUT->render_from_template('core_tag/tagfeed', $items);
    }
    return new core_tag\output\tagindex($tag, 'core_badges', 'badge', $content,
        $exclusivemode, $fromctx, $ctx, $rec, $page, $totalpages);
}
