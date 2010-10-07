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
 * @package    core
 * @subpackage tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('locallib.php');
require_once($CFG->dirroot.'/lib/weblib.php');
require_once($CFG->dirroot.'/blog/lib.php');

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$tagid       = optional_param('id', 0, PARAM_INT); // tag id
$tagname     = optional_param('tag', '', PARAM_TAG); // tag

$edit        = optional_param('edit', -1, PARAM_BOOL);
$userpage    = optional_param('userpage', 0, PARAM_INT); // which page to show
$perpage     = optional_param('perpage', 24, PARAM_INT);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);

if ($tagname) {
    $tag = tag_get('name', $tagname, '*');
} else if ($tagid) {
    $tag = tag_get('id', $tagid, '*');
}

if (empty($tag)) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

$PAGE->set_url('/tag/index.php', array('id' => $tag->id));
$PAGE->set_subpage($tag->id);
$PAGE->set_context($systemcontext);
$PAGE->set_pagelayout('standard');
$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');

if (($edit != -1) and $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$tagname = tag_display_name($tag);
$title = get_string('tag', 'tag') .' - '. $tagname;

$button = '';
if ($PAGE->user_allowed_editing() ) {
    $button = $OUTPUT->edit_button(new moodle_url("$CFG->wwwroot/tag/index.php", array('id' => $tagid)));
}

$PAGE->navbar->add(get_string('tags', 'tag'), new moodle_url('/tag/search.php'));
$PAGE->navbar->add($tagname);
$PAGE->set_title($title);
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_button($button);
echo $OUTPUT->header();

// Manage all tags links
if (has_capability('moodle/tag:manage', $systemcontext)) {
    echo '<div class="managelink"><a href="'. $CFG->wwwroot .'/tag/manage.php">'. get_string('managetags', 'tag') .'</a></div>' ;
}

$tagname  = tag_display_name($tag);

if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
    $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
}

echo $OUTPUT->heading($tagname, 2, 'headingblock header tag-heading');
tag_print_management_box($tag);
tag_print_description_box($tag);

echo '<div class="relatedpages"><p><a href="#course">'.get_string('courses').
 '</a> | <a href="#blog">'.get_string('relatedblogs', 'tag').
 '</a> | <a href="#user">'.get_string('users').'</a></p></div>';

// Display courses tagged with the tag
require_once($CFG->dirroot.'/tag/coursetagslib.php');
if ($courses = coursetag_get_tagged_courses($tag->id)) {

    $totalcount = count( $courses );
    echo $OUTPUT->box_start('generalbox', 'tag-blogs'); //could use an id separate from tag-blogs, but would have to copy the css style to make it look the same

    $heading = get_string('courses') . ' ' . get_string('taggedwith', 'tag', $tagname) .': '. $totalcount;
    echo "<a name='course'></a>";
    echo $OUTPUT->heading($heading, 3);

    foreach ($courses as $course) {
        print_course($course);
    }

    echo $OUTPUT->box_end();
}

// Print up to 10 previous blogs entries
if (has_capability('moodle/blog:view', $systemcontext)) {
    require_once($CFG->dirroot.'/blog/lib.php');
    require_once($CFG->dirroot.'/blog/locallib.php');

    $bloglisting = new blog_listing(array('tag' => $tag->id));
    $limit = 10;
    $start = 0;

    if ($blogs = $bloglisting->get_entries($start, $limit)) {

        echo $OUTPUT->box_start('generalbox', 'tag-blogs');
        $heading = get_string('relatedblogs', 'tag', $tagname). ' ' . get_string('taggedwith', 'tag', $tagname);
        echo "<a name='blog'></a>";
        echo $OUTPUT->heading($heading, 3);

        echo '<ul id="tagblogentries">';
        foreach ($blogs as $blog) {
            if ($blog->publishstate == 'draft') {
                $class = 'class="dimmed"';
            } else {
                $class = '';
            }
            echo '<li '.$class.'>';
            echo '<a '.$class.' href="'.$CFG->wwwroot.'/blog/index.php?entryid='.$blog->id.'">';
            echo format_string($blog->subject);
            echo '</a>';
            echo ' - ';
            echo '<a '.$class.' href="'.$CFG->wwwroot.'/user/view.php?id='.$blog->userid.'">';
            echo fullname($blog);
            echo '</a>';
            echo ', '. userdate($blog->lastmodified);
            echo '</li>';
        }
        echo '</ul>';

        $allblogsurl = new moodle_url('/blog/index.php', array('tagid' => $tag->id));
        echo '<p class="moreblogs"><a href="'.$allblogsurl->out().'">'.get_string('seeallblogs', 'tag', $tagname).'</a></p>';

        echo $OUTPUT->box_end();
    }
}

$usercount = tag_record_count('user', $tag->id);
if ($usercount > 0) {

    //user table box
    echo $OUTPUT->box_start('generalbox', 'tag-user-table');

    $heading = get_string('users'). ' ' . get_string('taggedwith', 'tag', $tagname) . ': ' . $usercount;
    echo "<a name='user'></a>";
    echo $OUTPUT->heading($heading, 3);

    $baseurl = new moodle_url('/tag/index.php', array('id' => $tag->id));
    $pagingbar = new paging_bar($usercount, $userpage, $perpage, $baseurl);
    $pagingbar->pagevar = 'userpage';
    echo $OUTPUT->render($pagingbar);
    tag_print_tagged_users_table($tag, $userpage * $perpage, $perpage);
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
