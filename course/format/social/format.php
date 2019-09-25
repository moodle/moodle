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
 * Course format featuring social forum.
 *
 * @package   format_social
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$pageno = optional_param('p', 0, PARAM_INT);

require_once($CFG->dirroot.'/mod/forum/lib.php');

$forum = forum_get_course_forum($course->id, 'social');
if (empty($forum)) {
    echo $OUTPUT->notification('Could not find or create a social forum here');
}

$coursemodule = get_coursemodule_from_instance('forum', $forum->id);
$modcontext = context_module::instance($coursemodule->id);

$entityfactory = mod_forum\local\container::get_entity_factory();
$forumentity = $entityfactory->get_forum_from_stdclass($forum, $modcontext, $coursemodule, $course);

// Print forum intro above posts  MDL-18483.
if (trim($forum->intro) != '') {
    $options = (object) [
        'para' => false,
    ];
    $introcontent = format_module_intro('forum', $forum, $coursemodule->id);

    if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $modcontext)) {
        $streditsummary  = get_string('editsummary');
        $introcontent .= html_writer::start_div('editinglink');
        $introcontent .= html_writer::link(
            new moodle_url('/course/modedit.php', [
                'update' => $coursemodule->id,
                'sesskey' => sesskey(),
            ]),
            $OUTPUT->pix_icon('t/edit', $streditsummary),
            [
                'title' => $streditsummary,
            ]
        );
        $introcontent .= html_writer::end_div();
    }
    echo $OUTPUT->box($introcontent, 'generalbox', 'intro');
}

echo html_writer::div(forum_get_subscribe_link($forum, $modcontext), 'subscribelink');

$numdiscussions = course_get_format($course)->get_course()->numdiscussions;
if ($numdiscussions < 1) {
    // Make sure that the value is at least one.
    $numdiscussions = 1;
}

$rendererfactory = mod_forum\local\container::get_renderer_factory();
$discussionsrenderer = $rendererfactory->get_social_discussion_list_renderer($forumentity);
$cm = \cm_info::create($coursemodule);
echo $discussionsrenderer->render($USER, $cm, null, null, $pageno, $numdiscussions);
