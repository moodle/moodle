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
 * Adds a search area to the queue for indexing.
 *
 * @package core_search
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(__DIR__ . '/../config.php');

// Check access.
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('searchareas', '', null, (new moodle_url('/admin/searchreindex.php'))->out(false));

// Get area parameter and check it exists.
$areaid = required_param('areaid', PARAM_ALPHANUMEXT);
$area = \core_search\manager::get_search_area($areaid);
if ($area === false) {
    throw new moodle_exception('invalidrequest');
}
$areaname = $area->get_visible_name();

$PAGE->set_primary_active_tab('siteadminnode');
$PAGE->set_secondary_active_tab('modules');

// Start page output.
$heading = get_string('gradualreindex', 'search', '');
$PAGE->set_title($areaname . ' - ' . get_string('gradualreindex', 'search', ''));
$PAGE->navbar->add($heading);
echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

// If sesskey is supplied, actually carry out the action.
if (optional_param('sesskey', '', PARAM_ALPHANUM)) {
    require_sesskey();

    // Get all contexts for search area. This query can take time in large cases.
    \core_php_time_limit::raise(0);
    $contextiterator = $area->get_contexts_to_reindex();

    $progress = new \core\progress\display_if_slow('');
    $progress->start_progress($areaname);

    // Request reindexing for each context (with low priority).
    $count = 0;
    foreach ($contextiterator as $context) {
        \core_php_time_limit::raise(30);
        \core_search\manager::request_index($context, $area->get_area_id(),
                \core_search\manager::INDEX_PRIORITY_REINDEXING);
        $progress->progress();
        $count++;
    }

    // Unset the iterator which should close the recordset (if there is one).
    unset($contextiterator);

    $progress->end_progress();

    $a = (object)['name' => html_writer::tag('strong', $areaname), 'count' => $count];
    echo $OUTPUT->box(get_string('gradualreindex_queued', 'search', $a));

    echo $OUTPUT->continue_button(new moodle_url('/admin/searchareas.php'));
} else {
    // Display confirmation prompt.
    echo $OUTPUT->confirm(get_string('gradualreindex_confirm', 'search', html_writer::tag('strong', $areaname)),
            new single_button(new moodle_url('/admin/searchreindex.php', ['areaid' => $areaid,
                'sesskey' => sesskey()]), get_string('continue'), 'post', single_button::BUTTON_PRIMARY),
            new single_button(new moodle_url('/admin/searchareas.php'), get_string('cancel'), 'get'));
}

echo $OUTPUT->footer();
