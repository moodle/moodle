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
 * Blog entry edit page
 *
 * @package    moodlecore
 * @subpackage blog
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(__FILE__)).'/config.php');
include_once('lib.php');
include_once('locallib.php');

$action   = required_param('action', PARAM_ALPHA);
$id       = optional_param('entryid', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$modid    = optional_param('modid', 0, PARAM_INT); // To associate the entry with a module instance
$courseid = optional_param('courseid', 0, PARAM_INT); // To associate the entry with a course

$PAGE->set_url('/blog/edit.php', array('action' => $action, 'entryid' => $id, 'confirm' => $confirm, 'modid' => $modid, 'courseid' => $courseid));

// If action is add, we ignore $id to avoid any further problems
if (!empty($id) && $action == 'add') {
    $id = null;
}

$returnurl = new moodle_url('/blog/index.php');

if (!empty($courseid) && empty($modid)) {
    $returnurl->param('courseid', $courseid);
    $PAGE->set_context(context_course::instance($courseid));
}

// If a modid is given, guess courseid
if (!empty($modid)) {
    $returnurl->param('modid', $modid);
    $courseid = $DB->get_field('course_modules', 'course', array('id' => $modid));
    $returnurl->param('courseid', $courseid);
    $PAGE->set_context(context_module::instance($modid));
}

// If courseid is empty use the system context
if (empty($courseid)) {
    $PAGE->set_context(context_system::instance());
}

$blogheaders = blog_get_headers();

require_login($courseid);

if ($action == 'edit') {
    $id = required_param('entryid', PARAM_INT);
}

if (empty($CFG->enableblogs)) {
    print_error('blogdisable', 'blog');
}

if (isguestuser()) {
    print_error('noguestentry', 'blog');
}

$sitecontext = context_system::instance();
if (!has_capability('moodle/blog:create', $sitecontext) && !has_capability('moodle/blog:manageentries', $sitecontext)) {
    print_error('cannoteditentryorblog');
}

// Make sure that the person trying to edit has access right
if ($id) {
    if (!$entry = new blog_entry($id)) {
        print_error('wrongentryid', 'blog');
    }

    if (!blog_user_can_edit_entry($entry)) {
        print_error('notallowedtoedit', 'blog');
    }
    $userid = $entry->userid;
    $entry->subject      = clean_text($entry->subject);
    $entry->summary      = clean_text($entry->summary, $entry->format);

} else {
    if (!has_capability('moodle/blog:create', $sitecontext)) {
        print_error('noentry', 'blog'); // manageentries is not enough for adding
    }
    $entry  = new stdClass();
    $entry->id = null;
    $userid = $USER->id;
}
$returnurl->param('userid', $userid);

// Blog renderer.
$output = $PAGE->get_renderer('blog');

$strblogs = get_string('blogs','blog');

if ($action === 'delete'){
    if (empty($entry->id)) {
        print_error('wrongentryid', 'blog');
    }
    if (data_submitted() && $confirm && confirm_sesskey()) {
        // Make sure the current user is the author of the blog entry, or has some deleteanyentry capability
        if (!blog_user_can_edit_entry($entry)) {
            print_error('nopermissionstodeleteentry', 'blog');
        } else {
            $entry->delete();
            redirect($returnurl);
        }
    } else if (blog_user_can_edit_entry($entry)) {
        $optionsyes = array('entryid'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'courseid'=>$courseid);
        $optionsno = array('userid'=>$entry->userid, 'courseid'=>$courseid);
        $PAGE->set_title("$SITE->shortname: $strblogs");
        $PAGE->set_heading($SITE->fullname);
        echo $OUTPUT->header();

        // Output the entry.
        $entry->prepare_render();
        echo $output->render($entry);

        echo '<br />';
        echo $OUTPUT->confirm(get_string('blogdeleteconfirm', 'blog'), new moodle_url('edit.php', $optionsyes),new moodle_url( 'index.php', $optionsno));
        echo $OUTPUT->footer();
        die;
    }
} else if ($action == 'add') {
    $PAGE->set_title("$SITE->shortname: $strblogs: " . get_string('addnewentry', 'blog'));
    $PAGE->set_heading($SITE->shortname);
} else if ($action == 'edit') {
    $PAGE->set_title("$SITE->shortname: $strblogs: " . get_string('editentry', 'blog'));
    $PAGE->set_heading($SITE->shortname);
}

if (!empty($entry->id)) {
    if ($CFG->useblogassociations && ($blogassociations = $DB->get_records('blog_association', array('blogid' => $entry->id)))) {

        foreach ($blogassociations as $assocrec) {
            $context = context::instance_by_id($assocrec->contextid);

            switch ($context->contextlevel) {
                case CONTEXT_COURSE:
                    $entry->courseassoc = $assocrec->contextid;
                    break;
                case CONTEXT_MODULE:
                    $entry->modassoc = $assocrec->contextid;
                    break;
            }
        }
    }
}

require_once('edit_form.php');
$summaryoptions = array('subdirs'=>false, 'maxfiles'=> 99, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>true, 'context'=>$sitecontext);
$attachmentoptions = array('subdirs'=>false, 'maxfiles'=> 99, 'maxbytes'=>$CFG->maxbytes);

$blogeditform = new blog_edit_form(null, compact('entry', 'summaryoptions', 'attachmentoptions', 'sitecontext', 'courseid', 'modid'));

$entry = file_prepare_standard_editor($entry, 'summary', $summaryoptions, $sitecontext, 'blog', 'post', $entry->id);
$entry = file_prepare_standard_filemanager($entry, 'attachment', $attachmentoptions, $sitecontext, 'blog', 'attachment', $entry->id);

if (!empty($CFG->usetags) && !empty($entry->id)) {
    include_once($CFG->dirroot.'/tag/lib.php');
    $entry->tags = tag_get_tags_array('post', $entry->id);
}

$entry->action = $action;
// set defaults
$blogeditform->set_data($entry);

if ($blogeditform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $blogeditform->get_data()){

    switch ($action) {
        case 'add':
            $blogentry = new blog_entry(null, $data, $blogeditform);
            $blogentry->add();
            $blogentry->edit($data, $blogeditform, $summaryoptions, $attachmentoptions);
        break;

        case 'edit':
            if (empty($entry->id)) {
                print_error('wrongentryid', 'blog');
            }

            $entry->edit($data, $blogeditform, $summaryoptions, $attachmentoptions);
        break;

        default :
            print_error('invalidaction');
    }

    redirect($returnurl);
}


// gui setup
switch ($action) {
    case 'add':
        // prepare new empty form
        $entry->publishstate = 'site';
        $strformheading = get_string('addnewentry', 'blog');
        $entry->action       = $action;

        if ($CFG->useblogassociations) {

            //pre-select the course for associations
            if ($courseid) {
                $context = context_course::instance($courseid);
                $entry->courseassoc = $context->id;
            }

            //pre-select the mod for associations
            if ($modid) {
                $context = context_module::instance($modid);
                $entry->modassoc = $context->id;
            }
        }
        break;

    case 'edit':
        if (empty($entry->id)) {
            print_error('wrongentryid', 'blog');
        }
        $entry->tags = tag_get_tags_array('post', $entry->id);
        $strformheading = get_string('updateentrywithid', 'blog');

        break;

    default :
        print_error('unknowaction');
}

$entry->modid = $modid;
$entry->courseid = $courseid;

echo $OUTPUT->header();
$blogeditform->display();
echo $OUTPUT->footer();

die;
