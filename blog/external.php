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
require_once('external_form.php');
require_once($CFG->libdir . '/magpie/rss_fetch.inc');
require_once($CFG->dirroot.'/tag/lib.php');

require_login();

$user = $USER;

// TODO redirect if $CFG->useexternalblogs is off, $CFG->maxexternalblogsperuser == 0, or if user doesn't have caps to manage external blogs

$id = optional_param('id', null, PARAM_INT);
$returnurl = urldecode(optional_param('returnurl', $PAGE->url->out(), PARAM_RAW));
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
            $rss = fetch_rss($data->url);
            $new_external = new stdClass();
            $new_external->name = (empty($data->name)) ? $rss->channel['title'] : $data->name;
            $new_external->description = (empty($data->description)) ? $rss->channel['description'] : $data->description;
            $new_external->userid = $user->id;
            $new_external->url = $data->url;
            $new_external->timemodified = mktime();

            if ($new_external->id = $DB->insert_record('blog_external', $new_external)) {
                tag_set('blog_external', $new_external->id, $data->tags);
                // TODO success message
            } else {
                // TODO error message
            }

            break;

        case 'edit':
            if ($data->id && $DB->record_exists('blog_external', array('id' => $data->id))) {

                $rss = fetch_rss($data->url);
                $external->id = $data->id;
                $external->name = (empty($data->name)) ? $rss->channel['title'] : $data->name;
                $external->description = (empty($data->description)) ? $rss->channel['description'] : $data->description;
                $external->userid = $user->id;
                $external->url = $data->url;
                $external->timemodified = mktime();

                if ($DB->update_record('blog_external', $external)) {
                    tag_set('blog_external', $external->id, explode(',', $data->tags));
                    // TODO success message
                } else {
                    // TODO error message
                }

            } else {
                print_error('wrongexternalid', 'blog');
            }

            break;

        default :
            print_error('invalidaction');
    }

    redirect($returnurl);
}

$navlinks = array();
$navlinks[] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$user->id", 'type' => 'misc');
$navlinks[] = array('name' => $strblogs, 'link' => "$CFG->wwwroot/blog/index.php?userid=$user->id", 'type' => 'misc');
$navlinks[] = array('name' => $strformheading, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

$PAGE->set_heading("$SITE->shortname: $strblogs: $strexternalblogs", $SITE->fullname);
$PAGE->set_title("$SITE->shortname: $strblogs: $strexternalblogs");

echo $OUTPUT->header($navigation);
echo $OUTPUT->heading($strformheading, 2);

$external->returnurl = $returnurl;
$externalblogform->set_data($external);
$externalblogform->display();

echo $OUTPUT->footer();
