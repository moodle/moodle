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
include_once($CFG->dirroot.'/tag/lib.php');

$action   = required_param('action', PARAM_ALPHA);
$id       = optional_param('entryid', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$modid    = optional_param('modid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT); // needed for user tab - does nothing here

$PAGE->set_url('blog/edit.php', array('action' => $action, 'entryid' => $id, 'confirm' => $confirm, 'modid' => $modid, 'courseid' => $courseid));

$blog_headers = blog_get_headers();

require_login($courseid);

if ($action == 'edit') {
    $id = required_param('entryid', PARAM_INT);
}

if (empty($CFG->bloglevel)) {
    print_error('blogdisable', 'blog');
}

if (isguestuser()) {
    print_error('noguestentry', 'blog');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('moodle/blog:create', $sitecontext) && !has_capability('moodle/blog:manageentries', $sitecontext)) {
    print_error('cannoteditentryorblog');
}

$returnurl = new moodle_url($CFG->wwwroot . '/blog/index.php');

// Make sure that the person trying to edit have access right
if ($id) {
    if (!$existing = new blog_entry($id)) {
        print_error('wrongentryid', 'blog');
    }

    if (!blog_user_can_edit_entry($existing)) {
        print_error('notallowedtoedit', 'blog');
    }
    $userid    = $existing->userid;
    $returnurl->param('userid', $existing->userid);
} else {
    if (!has_capability('moodle/blog:create', $sitecontext)) {
        print_error('noentry', 'blog'); // manageentries is not enough for adding
    }
    $existing  = false;
    $userid    = $USER->id;
    $returnurl->param('userid', $userid);
}

if (!empty($courseid) && empty($modid)) {
    $returnurl->param('courseid', $courseid);
    $PAGE->set_context(get_context_instance(CONTEXT_COURSE, $courseid));
}

// If a modid is given, guess courseid
if (!empty($modid)) {
    $returnurl->param('modid', $modid);
    $courseid = $DB->get_field('course_modules', 'course', array('id' => $modid));
    $returnurl->param('courseid', $courseid);
    $PAGE->set_context(get_context_instance(CONTEXT_MODULE, $modid));
}

$strblogs = get_string('blogs','blog');

if ($action === 'delete'){
    if (!$existing) {
        print_error('wrongentryid', 'blog');
    }
    if (data_submitted() && $confirm && confirm_sesskey()) {
        $existing->delete();
        redirect($returnurl);
    } else {
        $optionsyes = array('entryid'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'courseid'=>$courseid);
        $optionsno = array('userid'=>$existing->userid, 'courseid'=>$courseid);
        $PAGE->set_title("$SITE->shortname: $strblogs");
        $PAGE->set_heading($SITE->fullname);
        echo $OUTPUT->header();
        //blog_print_entry($existing);
        $existing->print_html();
        echo '<br />';
        echo $OUTPUT->confirm(get_string('blogdeleteconfirm', 'blog'), new moodle_url('edit.php', $optionsyes),new moodle_url( 'index.php', $optionsno));
        echo $OUTPUT->footer();
        die;
    }
}

require_once('edit_form.php');

if (!empty($existing)) {
    if ($blogassociations = $DB->get_records('blog_association', array('blogid' => $existing->id))) {

        foreach ($blogassociations as $assocrec) {
            $contextrec = $DB->get_record('context', array('id' => $assocrec->contextid));

            switch ($contextrec->contextlevel) {
                case CONTEXT_COURSE:
                    $existing->courseassoc = $assocrec->contextid;
                    break;
                case CONTEXT_MODULE:
                    $existing->modassoc[] = $assocrec->contextid;
                    break;
            }
        }
    }
}

$textfieldoptions = array('trusttext'=>true, 'subdirs'=>true);
$blogeditform = new blog_edit_form(null, compact('existing', 'sitecontext', 'textfieldoptions', 'id'));
$draftitemid = file_get_submitted_draft_itemid('attachments');
file_prepare_draft_area($draftitemid, $PAGE->context->id, 'blog_attachment', empty($id)?null:$id);

$editordraftid = file_get_submitted_draft_itemid('summary');
$currenttext = file_prepare_draft_area($editordraftid, $PAGE->context->id, 'blog_post', empty($id) ? null : $id, array('subdirs'=>true), @$existing->summary);

$data = array('id'=>$id, 'summary'=>array('text'=>$currenttext, 'format'=>FORMAT_HTML, 'itemid' => $editordraftid));
$blogeditform->set_data($data); // set defaults

if ($blogeditform->is_cancelled()){
    redirect($returnurl);
} else if ($fromform = $blogeditform->get_data()){

    //save stuff in db
    switch ($action) {
        case 'add':
            $blogentry = new blog_entry($fromform, $blogeditform);
            $blogentry->summary = file_save_draft_area_files($fromform->summary['itemid'], $PAGE->context->id, 'blog_post', $blogentry->id, array('subdirs'=>true), $fromform->summary['text']);
            $blogentry->add();
        break;

        case 'edit':
            if (!$existing) {
                print_error('wrongentryid', 'blog');
            }
            $existing->edit($fromform, $blogeditform);
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

        if ($courseid) {  //pre-select the course for associations
            $context = get_context_instance(CONTEXT_COURSE, $courseid);
            $entry->courseassoc = $context->id;
        }

        if ($modid) { //pre-select the mod for associations
            $context = get_context_instance(CONTEXT_MODULE, $modid);
            $entry->modassoc = array($context->id);
        }
        break;

    case 'edit':
        if (!$existing) {
            print_error('wrongentryid', 'blog');
        }

        $entry->id           = $existing->id;
        $entry->subject      = $existing->subject;
        $entry->fakesubject  = $existing->subject;
        $entry->summary      = $existing->summary;
        $entry->fakesummary  = $existing->summary;
        $entry->publishstate = $existing->publishstate;
        $entry->format       = $existing->format;
        $entry->tags         = tag_get_tags_array('blog_entries', $entry->id);
        $entry->action       = $action;

        if (!empty($existing->courseassoc)) {
            $entry->courseassoc = $existing->courseassoc;
        }

        if (!empty($existing->modassoc)) {
            $entry->modassoc = $existing->modassoc;
        }

        $strformheading = get_string('updateentrywithid', 'blog');

        break;
    default :
        print_error('unknowaction');
}

$entry->modid = $modid;
$entry->courseid = $courseid;
$entry->attachments = $draftitemid;
$entry->summary = array('text' => @$existing->summary, 'format' => empty($existing->summaryformat) ? FORMAT_HTML : $existing->summaryformat, 'itemid' => $editordraftid);
$entry->summaryformat = (empty($existing->summaryformat)) ? FORMAT_HTML : $existing->summaryformat;
$PAGE->requires->data_for_js('blog_edit_existing', $entry);

// done here in order to allow deleting of entries with wrong user id above
if (!$user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}

$PAGE->requires->js('blog/edit_form.js');

echo $OUTPUT->header();

$blogeditform->set_data($entry);
$blogeditform->display();

$PAGE->requires->js_function_call('select_initial_course');

echo $OUTPUT->footer();

die;
