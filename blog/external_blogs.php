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

$PAGE->navbar->add(fullname($USER), new moodle_url($CFG->wwwroot.'/user/view.php', array('id'=>$USER->id)));
$PAGE->navbar->add($strblogs, new moodle_url($CFG->wwwroot.'/blog/index.php', array('userid'=>$USER->id)));
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
            $validicon = html_image::make($OUTPUT->pix_url('i/cross_red_big'));
            $validicon->alt = get_string('feedisinvalid', 'blog');
            $validicon->title = get_string('feedisinvalid', 'blog');
        } else {
            $validicon = html_image::make($OUTPUT->pix_url('i/tick_green_big'));
            $validicon->alt = get_string('feedisvalid', 'blog');
            $validicon->title = get_string('feedisvalid', 'blog');
        }

        $editicon = new moodle_action_icon;
        $editicon->link->url = new moodle_url($CFG->wwwroot.'/blog/external_blog_edit.php', array('id' => $blog->id));
        $editicon->link->title = get_string('editexternalblog', 'blog');
        $editicon->image->src = $OUTPUT->pix_url('t/edit');
        $editicon->image->alt = get_string('editexternalblog', 'blog');

        $deleteicon = new moodle_action_icon;
        $deleteicon->link->url = new moodle_url($CFG->wwwroot.'/blog/external_blogs.php', array('delete' => $blog->id, 'sesskey' => sesskey()));
        $deleteicon->link->title = get_string('deleteexternalblog', 'blog');
        $deleteicon->image->src = $OUTPUT->pix_url('t/delete');
        $deleteicon->image->alt = get_string('deleteexternalblog', 'blog');
        $deleteicon->add_confirm_action(get_string('externalblogdeleteconfirm', 'blog'));
        $icons = $OUTPUT->action_icon($editicon) . $OUTPUT->action_icon($deleteicon);
        $table->data[] = html_table_row::make(array($blog->name, $blog->url, userdate($blog->timefetched), $OUTPUT->image($validicon), $icons));
    }
    echo $OUTPUT->table($table);
}

$newexternalurl = new moodle_url($CFG->wwwroot.'/blog/external_blog_edit.php');
echo $OUTPUT->link(html_link::make($newexternalurl, $straddnewexternalblog));
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
