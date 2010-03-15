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
 */
require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

require_login();
admin_externalpage_setup('comments');

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/comment:delete', $context);

$PAGE->requires->yui2_lib('yahoo');
$PAGE->requires->yui2_lib('dom');
$PAGE->requires->yui2_lib('event');
$PAGE->requires->yui2_lib('animation');
$PAGE->requires->yui2_lib('json');
$PAGE->requires->yui2_lib('connection');
$PAGE->requires->js('/comment/admin.js');

$action     = optional_param('action', '', PARAM_ALPHA);
$commentid  = optional_param('commentid', 0, PARAM_INT);
$commentids = optional_param('commentids', '', PARAM_ALPHANUMEXT);
$page       = optional_param('page', 0, PARAM_INT);

$manager = new comment_manager();

if ($action and !confirm_sesskey()) {
    // no action if sesskey not confirmed
    $action = '';
}

if ($action === 'delete') {
    // delete a single comment
    if (!empty($commentid)) {
        if ($manager->delete_comment($commentid)) {
            redirect($CFG->httpswwwroot.'/comment/', get_string('deleted'));
        } else {
            $err = 'cannotdeletecomment';
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

admin_externalpage_print_header();
echo $OUTPUT->heading(get_string('comments'));
if (!empty($err)) {
    print_error($err, 'error', $CFG->httpswwwroot.'/comment/');
}
if (empty($action)) {
    $manager->print_comments($page);
    echo '<div class="mdl-align">';
    echo '<button id="comments_delete">'.get_string('delete').'</button>';
    echo '</div>';
}
echo $OUTPUT->footer();
