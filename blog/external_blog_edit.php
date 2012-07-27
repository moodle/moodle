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
 * Form page for an external blog link.
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once('external_blog_edit_form.php');
require_once($CFG->libdir . '/simplepie/moodle_simplepie.php');
require_once($CFG->dirroot.'/tag/lib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/blog:manageexternal', $context);

// TODO redirect if $CFG->useexternalblogs is off, $CFG->maxexternalblogsperuser == 0, or if user doesn't have caps to manage external blogs

$id = optional_param('id', null, PARAM_INT);
$url = new moodle_url('/blog/external_blog_edit.php');
if ($id !== null) {
    $url->param('id', $id);
}
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

$returnurl = new moodle_url('/blog/external_blogs.php');

$action = (empty($id)) ? 'add' : 'edit';

$external = new stdClass();

// Check that this id exists
if (!empty($id) && !$DB->record_exists('blog_external', array('id' => $id))) {
    print_error('wrongexternalid', 'blog');
} elseif (!empty($id)) {
    $external = $DB->get_record('blog_external', array('id' => $id));
}

$strformheading = ($action == 'edit') ? get_string('editexternalblog', 'blog') : get_string('addnewexternalblog', 'blog');
$strexternalblogs = get_string('externalblogs','blog');
$strblogs = get_string('blogs','blog');

$externalblogform = new blog_edit_external_form();

if ($externalblogform->is_cancelled()){
    redirect($returnurl);

} else if ($data = $externalblogform->get_data()) {
    //save stuff in db
    switch ($action) {
        case 'add':
            $rss = new moodle_simplepie($data->url);

            $newexternal = new stdClass();
            $newexternal->name = (empty($data->name)) ? $rss->get_title() : $data->name;
            $newexternal->description = (empty($data->description)) ? $rss->get_description() : $data->description;
            $newexternal->userid = $USER->id;
            $newexternal->url = $data->url;
            $newexternal->filtertags = $data->filtertags;
            $newexternal->timemodified = time();

            $newexternal->id = $DB->insert_record('blog_external', $newexternal);
            blog_sync_external_entries($newexternal);
            tag_set('blog_external', $newexternal->id, explode(',', $data->autotags));

            break;

        case 'edit':
            if ($data->id && $DB->record_exists('blog_external', array('id' => $data->id))) {

                $rss = new moodle_simplepie($data->url);

                $external->id = $data->id;
                $external->name = (empty($data->name)) ? $rss->get_title() : $data->name;
                $external->description = (empty($data->description)) ? $rss->get_description() : $data->description;
                $external->userid = $USER->id;
                $external->url = $data->url;
                $external->filtertags = $data->filtertags;
                $external->timemodified = time();

                $DB->update_record('blog_external', $external);
                tag_set('blog_external', $external->id, explode(',', $data->autotags));

            } else {
                print_error('wrongexternalid', 'blog');
            }

            break;

        default :
            print_error('invalidaction');
    }

    redirect($returnurl);
}

$PAGE->set_heading("$SITE->shortname: $strblogs: $strexternalblogs", $SITE->fullname);
$PAGE->set_title("$SITE->shortname: $strblogs: $strexternalblogs");

echo $OUTPUT->header();
echo $OUTPUT->heading($strformheading, 2);

$externalblogform->set_data($external);
$externalblogform->display();

echo $OUTPUT->footer();
