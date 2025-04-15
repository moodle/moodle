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
$context = context_system::instance();
$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_url(new moodle_url('/blog/external_blogs.php'));
require_capability('moodle/blog:manageexternal', $context);

$delete = optional_param('delete', null, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$strexternalblogs = get_string('externalblogs', 'blog');
$straddnewexternalblog = get_string('addnewexternalblog', 'blog');
$strblogs = get_string('blogs', 'blog');

$PAGE->set_title("{$strblogs}: {$strexternalblogs}");
$PAGE->set_pagelayout('standard');

if ($delete) {
    $externalblog = $DB->get_record('blog_external', ['id' => $delete, 'userid' => $USER->id], '*', MUST_EXIST);

    if ($confirm) {
        require_sesskey();

        // Delete the external blog.
        $DB->delete_records('blog_external', array('id' => $delete));

        // Delete the external blog's posts.
        $deletewhere = 'module = :module
                            AND userid = :userid
                            AND ' . $DB->sql_isnotempty('post', 'uniquehash', false, false) . '
                            AND ' . $DB->sql_compare_text('content') . ' = ' . $DB->sql_compare_text(':delete');
        $DB->delete_records_select('post', $deletewhere, array('module' => 'blog_external',
                                                               'userid' => $USER->id,
                                                               'delete' => $delete));

        // Log this action.
        $eventparms = array('context' => $context, 'objectid' => $delete);
        $event = \core\event\blog_external_removed::create($eventparms);
        $event->add_record_snapshot('blog_external', $externalblog);
        $event->trigger();

        redirect($PAGE->url, get_string('externalblogdeleted', 'blog'));
    } else {
        echo $OUTPUT->header();
        echo $OUTPUT->heading("{$strexternalblogs}: " . s($externalblog->name), 2);

        echo $OUTPUT->confirm(
            get_string('deleteexternalblog', 'blog'),
            new moodle_url($PAGE->url->out_omit_querystring(), ['delete' => $delete, 'confirm' => 1]),
            $PAGE->url,
        );

        echo $OUTPUT->footer();
        die;
    }
}

$blogs = $DB->get_records('blog_external', array('userid' => $USER->id));

echo $OUTPUT->header();
echo $OUTPUT->heading($strexternalblogs, 2);

echo $OUTPUT->box_start('generalbox boxaligncenter');

if (!empty($blogs)) {
    $table = new html_table();
    $table->cellpadding = 4;
    $table->attributes['class'] = 'generaltable boxaligncenter';
    $table->head = array(get_string('name'),
                         get_string('url', 'blog'),
                         get_string('timefetched', 'blog'),
                         get_string('valid', 'blog'),
                         get_string('actions'));

    foreach ($blogs as $blog) {
        if ($blog->failedlastsync) {
            $validicon = $OUTPUT->pix_icon('i/invalid', get_string('feedisinvalid', 'blog'));
        } else {
            $validicon = $OUTPUT->pix_icon('i/valid', get_string('feedisvalid', 'blog'));
        }

        $editurl = new moodle_url('/blog/external_blog_edit.php', array('id' => $blog->id));
        $editicon = $OUTPUT->action_icon($editurl, new pix_icon('t/edit', get_string('editexternalblog', 'blog')));

        $deletelink = new moodle_url('/blog/external_blogs.php', ['delete' => $blog->id]);
        $deleteicon = $OUTPUT->action_icon($deletelink, new pix_icon('t/delete', get_string('deleteexternalblog', 'blog')));

        $table->data[] = new html_table_row(array($blog->name,
                                                  $blog->url,
                                                  userdate($blog->timefetched),
                                                  $validicon,
                                                  $editicon . $deleteicon));
    }
    echo html_writer::table($table);
}

$newexternalurl = new moodle_url('/blog/external_blog_edit.php');
echo html_writer::link($newexternalurl, $straddnewexternalblog);
echo $OUTPUT->box_end();

// Log this page.
$event = \core\event\blog_external_viewed::create(array('context' => $context));
$event->trigger();
echo $OUTPUT->footer();
