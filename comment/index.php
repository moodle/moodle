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

/*
 * Comments management interface
 *
 * @package   core
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/comment/locallib.php');

admin_externalpage_setup('comments', '', null, '', array('pagelayout'=>'report'));

$PAGE->requires->js_init_call('M.core_comment.init_admin', null, true);

$action     = optional_param('action', '', PARAM_ALPHA);
$commentid  = optional_param('commentid', 0, PARAM_INT);
$commentids = optional_param('commentids', '', PARAM_ALPHANUMEXT);
$page       = optional_param('page', 0, PARAM_INT);
$confirm    = optional_param('confirm', 0, PARAM_INT);

$manager = new comment_manager();

if ($action and !confirm_sesskey()) {
    // no action if sesskey not confirmed
    $action = '';
}

if ($action === 'delete') {
    // delete a single comment
    if (!empty($commentid)) {
        if (!$confirm) {
            echo $OUTPUT->header();
            $optionsyes = array('action'=>'delete', 'commentid'=>$commentid, 'confirm'=>1, 'sesskey'=>sesskey());
            $optionsno  = array('sesskey'=>sesskey());
            $buttoncontinue = new single_button(new moodle_url('/comment/index.php', $optionsyes), get_string('delete'));
            $buttoncancel = new single_button(new moodle_url('/comment/index.php', $optionsno), get_string('cancel'));
            echo $OUTPUT->confirm(get_string('confirmdeletecomments', 'admin'), $buttoncontinue, $buttoncancel);
            echo $OUTPUT->footer();
            die;
        } else {
            if ($manager->delete_comment($commentid)) {
                redirect($CFG->wwwroot.'/comment/');
            } else {
                $err = 'cannotdeletecomment';
            }
        }
    }
    // delete a list of comments
    if (!empty($commentids)) {
        if ($manager->delete_comments($commentids)) {
            die('yes');
        } else {
            die('no');
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('comments'));
echo $OUTPUT->box_start('generalbox commentsreport');
if (!empty($err)) {
    throw new \moodle_exception($err, 'error', $CFG->wwwroot.'/comment/');
}
if (empty($action)) {
    echo '<form method="post">';
    $return = $manager->print_comments($page);
    // if no comments available, $return will be false
    if ($return) {
        echo '<input type="submit" class="btn btn-primary" id="comments_delete" name="batchdelete"
            value="'.get_string('delete').'" />';
    }
    echo '</form>';
}

echo $OUTPUT->box_end();
echo $OUTPUT->footer();
