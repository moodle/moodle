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
 * Page to export forum discussions.
 *
 * @package    mod_forum
 * @copyright  2019 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/dataformatlib.php');
require_once($CFG->dirroot . '/calendar/externallib.php');

$forumid = required_param('id', PARAM_INT);
$userids = optional_param_array('userids', [], PARAM_INT);
$discussionids = optional_param_array('discids', [], PARAM_INT);
$from = optional_param_array('from', [], PARAM_INT);
$to = optional_param_array('to', [], PARAM_INT);
$fromtimestamp = optional_param('timestampfrom', '', PARAM_INT);
$totimestamp = optional_param('timestampto', '', PARAM_INT);

if (!empty($from['enabled'])) {
    unset($from['enabled']);
    $from = core_calendar_external::get_timestamps([$from])['timestamps'][0]['timestamp'];
} else {
    $from = $fromtimestamp;
}

if (!empty($to['enabled'])) {
    unset($to['enabled']);
    $to = core_calendar_external::get_timestamps([$to])['timestamps'][0]['timestamp'];
} else {
    $to = $totimestamp;
}

$vaultfactory = mod_forum\local\container::get_vault_factory();
$managerfactory = mod_forum\local\container::get_manager_factory();
$legacydatamapperfactory = mod_forum\local\container::get_legacy_data_mapper_factory();

$forumvault = $vaultfactory->get_forum_vault();

$forum = $forumvault->get_from_id($forumid);
if (empty($forum)) {
    throw new moodle_exception('Unable to find forum with id ' . $forumid);
}

$capabilitymanager = $managerfactory->get_capability_manager($forum);
if (!$capabilitymanager->can_export_forum($USER)) {
    throw new moodle_exception('cannotexportforum', 'forum');
}

$course = $forum->get_course_record();
$coursemodule = $forum->get_course_module_record();
$cm = cm_info::create($coursemodule);

require_course_login($course, true, $cm);

$url = new moodle_url('/mod/forum/export.php');
$pagetitle = get_string('export', 'mod_forum');
$context = $forum->get_context();

$form = new mod_forum\form\export_form($url->out(false), [
    'forum' => $forum
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/mod/forum/view.php', ['id' => $cm->id]));
} else if ($data = $form->get_data()) {
    $dataformat = $data->format;

    // This may take a very long time and extra memory.
    \core_php_time_limit::raise();
    raise_memory_limit(MEMORY_HUGE);

    $discussionvault = $vaultfactory->get_discussion_vault();
    $postvault = $vaultfactory->get_post_vault();
    if ($data->discussionids) {
        $discussionids = $data->discussionids;
    } else if (empty($discussionids)) {
        $discussions = $discussionvault->get_all_discussions_in_forum($forum);
        $discussionids = array_map(function ($discussion) {
            return $discussion->get_id();
        }, $discussions);
    }

    $filters = ['discussionids' => $discussionids];
    if ($data->useridsselected) {
        $filters['userids'] = $data->useridsselected;
    }
    if ($data->from) {
        $filters['from'] = $data->from;
    }
    if ($data->to) {
        $filters['to'] = $data->to;
    }

    // Retrieve posts based on the selected filters.
    $posts = $postvault->get_from_filters($USER, $filters, $capabilitymanager->can_view_any_private_reply($USER));

    $striphtml = !empty($data->striphtml);
    $humandates = !empty($data->humandates);

    $fields = ['id', 'discussion', 'parent', 'userid', 'created', 'modified', 'mailed', 'subject', 'message',
                'messageformat', 'messagetrust', 'attachment', 'totalscore', 'mailnow', 'deleted', 'privatereplyto',
                'wordcount', 'charcount'];

    $datamapper = $legacydatamapperfactory->get_post_data_mapper();
    $exportdata = new ArrayObject($datamapper->to_legacy_objects($posts));
    $iterator = $exportdata->getIterator();

    require_once($CFG->libdir . '/dataformatlib.php');
    $filename = clean_filename('discussion');
    download_as_dataformat(
        $filename,
        $dataformat,
        $fields,
        $iterator,
        function($exportdata) use ($fields, $striphtml, $humandates) {
            $data = $exportdata;
            if ($striphtml) {
                // The following call to html_to_text uses the option that strips out
                // all URLs, but format_text complains if it finds @@PLUGINFILE@@ tokens.
                // So, we need to replace @@PLUGINFILE@@ with a real URL, but it doesn't
                // matter what. We use http://example.com/.
                $data->message = str_replace('@@PLUGINFILE@@/', 'http://example.com/', $data->message);
                $data->message = html_to_text(format_text($data->message, $data->messageformat), 0, false);
                $data->messageformat = FORMAT_PLAIN;
            }
            if ($humandates) {
                $data->created = userdate($data->created);
                $data->modified = userdate($data->modified);
            }
            foreach ($fields as $field) {
                // Convert any boolean fields to their integer equivalent for output.
                if (is_bool($data->$field)) {
                    $data->$field = (int) $data->$field;
                }
            }
            return $data;
        });
    die;
}

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

// It is possible that the following fields have been provided in the URL.
$form->set_data(['useridsselected' => $userids, 'discussionids' => $discussionids, 'from' => $from, 'to' => $to]);

$form->display();

echo $OUTPUT->footer();
