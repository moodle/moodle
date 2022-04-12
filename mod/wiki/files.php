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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Wiki files management
 *
 * @package mod_wiki
 * @copyright 2011 Dongsheng Cai <dongsheng@moodle.com>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->dirroot . '/mod/wiki/locallib.php');

$pageid       = required_param('pageid', PARAM_INT); // Page ID
$wid          = optional_param('wid', 0, PARAM_INT); // Wiki ID
$currentgroup = optional_param('group', 0, PARAM_INT); // Group ID
$userid       = optional_param('uid', 0, PARAM_INT); // User ID
$groupanduser = optional_param('groupanduser', null, PARAM_TEXT);

if (!$page = wiki_get_page($pageid)) {
    throw new \moodle_exception('incorrectpageid', 'wiki');
}

if ($groupanduser) {
    list($currentgroup, $userid) = explode('-', $groupanduser);
    $currentgroup = clean_param($currentgroup, PARAM_INT);
    $userid       = clean_param($userid, PARAM_INT);
}

if ($wid) {
    // in group mode
    if (!$wiki = wiki_get_wiki($wid)) {
        throw new \moodle_exception('incorrectwikiid', 'wiki');
    }
    if (!$subwiki = wiki_get_subwiki_by_group($wiki->id, $currentgroup, $userid)) {
        // create subwiki if doesn't exist
        $subwikiid = wiki_add_subwiki($wiki->id, $currentgroup, $userid);
        $subwiki = wiki_get_subwiki($subwikiid);
    }
} else {
    // no group
    if (!$subwiki = wiki_get_subwiki($page->subwikiid)) {
        throw new \moodle_exception('incorrectsubwikiid', 'wiki');
    }

    // Checking wiki instance of that subwiki
    if (!$wiki = wiki_get_wiki($subwiki->wikiid)) {
        throw new \moodle_exception('incorrectwikiid', 'wiki');
    }
}

// Checking course module instance
if (!$cm = get_coursemodule_from_instance("wiki", $subwiki->wikiid)) {
    throw new \moodle_exception('invalidcoursemodule');
}

// Checking course instance
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

$context = context_module::instance($cm->id);

$url = new moodle_url('/mod/wiki/files.php', ['pageid' => $pageid]);
$PAGE->set_url($url);
require_course_login($course, true, $cm);

if (!wiki_user_can_view($subwiki, $wiki)) {
    throw new \moodle_exception('cannotviewfiles', 'wiki');
}

$PAGE->set_title(get_string('wikifiles', 'wiki'));
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');
$PAGE->navbar->add(format_string(get_string('wikifiles', 'wiki')));
$PAGE->set_secondary_active_tab('modulepage');

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_wiki');

$actionbar = new \mod_wiki\output\action_bar($pageid, $PAGE->url);
echo $renderer->render_action_bar($actionbar);

echo $OUTPUT->box_start('generalbox');
echo $renderer->wiki_print_subwiki_selector($PAGE->activityrecord, $subwiki, $page, 'files');
echo $renderer->wiki_files_tree($context, $subwiki);
echo $OUTPUT->box_end();

if (has_capability('mod/wiki:managefiles', $context)) {
    echo $OUTPUT->single_button(new moodle_url('/mod/wiki/filesedit.php', array('subwiki'=>$subwiki->id, 'pageid'=>$pageid)), get_string('editfiles', 'wiki'), 'get');
}
echo $OUTPUT->footer();
