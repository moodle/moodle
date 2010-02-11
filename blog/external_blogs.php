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
 * List of external blogs for current user.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');

require_login();

$PAGE->set_url('/blog/external_blogs.php');
require_capability('moodle/blog:manageexternal', get_context_instance(CONTEXT_SYSTEM));

$delete = optional_param('delete', null, PARAM_INT);

$strexternalblogs = get_string('externalblogs','blog');
$straddnewexternalblog = get_string('addnewexternalblog','blog');
$strblogs = get_string('blogs','blog');
$message = null;

if ($delete && confirm_sesskey()) {
    $externalbloguserid = $DB->get_field('blog_external', 'userid', array('id' => $delete));
    if ($externalbloguserid == $USER->id) {
        $DB->delete_records('blog_external', array('id' => $delete));
        $message = get_string('externalblogdeleted', 'blog');
    }
}

$blogs = $DB->get_records('blog_external', array('userid' => $USER->id));

$PAGE->navbar->add(fullname($USER), new moodle_url('/user/view.php', array('id'=>$USER->id)));
$PAGE->navbar->add($strblogs, new moodle_url('/blog/index.php', array('userid'=>$USER->id)));
$PAGE->navbar->add($strexternalblogs);
$PAGE->set_heading("$SITE->shortname: $strblogs: $strexternalblogs", $SITE->fullname);
$PAGE->set_title("$SITE->shortname: $strblogs: $strexternalblogs");

echo $OUTPUT->header();
echo $OUTPUT->heading($strexternalblogs, 2);

if (!empty($message)) {
    echo $OUTPUT->notification($message);
}

echo $OUTPUT->box_start('generalbox boxaligncenter');

if (!empty($blogs)) {
    $table = new html_table();
    $table->cellpadding = 4;
    $table->add_class('generaltable boxaligncenter');
    $table->head = array(get_string('name'), get_string('url'), get_string('timefetched', 'blog'), get_string('valid', 'blog'), get_string('actions'));

    foreach ($blogs as $blog) {
        if ($blog->failedlastsync) {
            $validicon = $OUTPUT->pix_icon('i/cross_red_big', get_string('feedisinvalid', 'blog'));
        } else {
            $validicon = $OUTPUT->pix_icon('i/tick_green_big', get_string('feedisvalid', 'blog'));
        }

        $editurl = new moodle_url('/blog/external_blog_edit.php', array('id' => $blog->id));
        $editicon = $OUTPUT->action_icon($editurl, get_string('editexternalblog', 'blog'), 't/edit');

        $deletelink = new html_link(new moodle_url('/blog/external_blog_edit.php', array('id' => $blog->id, 'sesskey'=>sesskey())));
        $deletelink->add_confirm_action(get_string('externalblogdeleteconfirm', 'blog'));
        $deleteicon = $OUTPUT->action_icon($deletelink, get_string('deleteexternalblog', 'blog'), 't/delete');

        $table->data[] = html_table_row::make(array($blog->name, $blog->url, userdate($blog->timefetched), $validicon, $editicon . $deleteicon));
    }
    echo $OUTPUT->table($table);
}

$newexternalurl = new moodle_url('/blog/external_blog_edit.php');
echo $OUTPUT->link(html_link::make($newexternalurl, $straddnewexternalblog));
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
