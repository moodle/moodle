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
 * Admin page to cleanup oidc tokens.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');

require_login();

$context = context_system::instance();
$pageurl = new moodle_url('/auth/oidc/cleanupoidctokens.php');

admin_externalpage_setup('auth_oidc_cleanup_oidc_tokens');

require_admin();

$PAGE->set_url($pageurl);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading(get_string('cleanup_oidc_tokens', 'auth_oidc'));
$PAGE->set_title(get_string('cleanup_oidc_tokens', 'auth_oidc'));

$emptyuseridtokens = auth_oidc_get_tokens_with_empty_ids();
$mismatchedtokens = auth_oidc_get_tokens_with_mismatched_usernames();

$tokenstoclean = $emptyuseridtokens + $mismatchedtokens;

uasort($tokenstoclean, function($a, $b) {
    return strcmp($a->oidcusername, $b->oidcusername);
});

$deletetokenid = optional_param('id', 0, PARAM_INT);
if ($deletetokenid) {
    if (array_key_exists($deletetokenid, $tokenstoclean)) {
        auth_oidc_delete_token($deletetokenid);

        redirect($pageurl, get_string('token_deleted', 'auth_oidc'));
    }
}

if ($tokenstoclean) {
    $table = new html_table();
    $table->head = [
        get_string('table_token_id', 'auth_oidc'),
        get_string('table_oidc_username', 'auth_oidc'),
        get_string('table_token_unique_id', 'auth_oidc'),
        get_string('table_matching_status', 'auth_oidc'),
        get_string('table_matching_details', 'auth_oidc'),
        get_string('table_action', 'auth_oidc'),
    ];
    $table->colclasses = [
        'leftalign',
        'leftalign',
        'leftalign',
        'leftalign',
        'leftalign',
        'centeralign',
    ];
    $table->attributes['class'] = 'admintable generaltable';
    $table->id = 'cleanupoidctokens';
    $table->data = [];
    foreach ($tokenstoclean as $item) {
        $table->data[] = [
            $item->id,
            $item->oidcusername,
            $item->oidcuniqueid,
            $item->matchingstatus,
            $item->details,
            $item->action,
        ];
    }
}

echo $OUTPUT->header();

if ($tokenstoclean) {
    echo html_writer::table($table);
} else {
    echo html_writer::span(get_string('no_token_to_cleanup', 'auth_oidc'));
}

echo $OUTPUT->footer();
