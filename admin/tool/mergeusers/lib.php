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
 * Plugin callbacks.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright Universitat Rovira i Virgili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_user\output\myprofile\category;
use core_user\output\myprofile\node;
use tool_mergeusers\local\last_merge;
use tool_mergeusers\output\renderer;

/**
 * Profile callback to add merging data to a users profile.
 *
 * @param core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param null|stdClass $course Course object
 * @throws coding_exception
 * @throws dml_exception
 */
function tool_mergeusers_myprofile_navigation(
    core_user\output\myprofile\tree $tree,
    stdClass $user,
    bool $iscurrentuser,
    null|stdClass $course,
) {
    global $PAGE;

    if (!has_capability('tool/mergeusers:viewlog', context_system::instance())) {
        return;
    }

    /** @var renderer $renderer */
    $renderer = $PAGE->get_renderer('tool_mergeusers');
    $lastmerge = last_merge::from($user->id);

    // Display last merge.
    $category = new category('tool_mergeusers_info', get_string('pluginname', 'tool_mergeusers'));
    $tree->add_category($category);
    $node = new node(
        'tool_mergeusers_info',
        'olduser',
        get_string('lastmerge', 'tool_mergeusers'),
        null,
        null,
        $renderer->get_merge_detail($user, $lastmerge)
    );
    $category->add_node($node);
}
