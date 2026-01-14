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
 * Learning Plans Dashboard for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once('lib.php');

admin_externalpage_setup('local_coursematrix_dashboard');

$PAGE->set_url('/local/coursematrix/dashboard.php');
$PAGE->set_title(get_string('dashboard', 'local_coursematrix'));
$PAGE->set_heading(get_string('dashboard', 'local_coursematrix'));

// Get filter parameters.
$filterplan = optional_param('planid', 0, PARAM_INT);
$filterstatus = optional_param('status', '', PARAM_ALPHA);

// Get dashboard stats.
$stats = local_coursematrix_get_dashboard_stats();
$userlist = local_coursematrix_get_user_plan_list($filterplan ?: null, $filterstatus ?: null);
$plans = local_coursematrix_get_plans();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('dashboard', 'local_coursematrix'));

// Summary cards.
echo '<div class="row mb-4">';
echo '<div class="col-md-2 col-sm-4 mb-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h3 class="card-title">' . $stats->totalplans . '</h3>';
echo '<p class="card-text text-muted">' . get_string('totalplans', 'local_coursematrix') . '</p>';
echo '</div></div></div>';

echo '<div class="col-md-2 col-sm-4 mb-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h3 class="card-title">' . $stats->totalusers . '</h3>';
echo '<p class="card-text text-muted">' . get_string('totalusers', 'local_coursematrix') . '</p>';
echo '</div></div></div>';

echo '<div class="col-md-2 col-sm-4 mb-3">';
echo '<div class="card text-center bg-info text-white">';
echo '<div class="card-body">';
echo '<h3 class="card-title">' . $stats->activeusers . '</h3>';
echo '<p class="card-text">' . get_string('inprogress', 'local_coursematrix') . '</p>';
echo '</div></div></div>';

echo '<div class="col-md-2 col-sm-4 mb-3">';
echo '<div class="card text-center bg-danger text-white">';
echo '<div class="card-body">';
echo '<h3 class="card-title">' . $stats->overdueusers . '</h3>';
echo '<p class="card-text">' . get_string('status_overdue', 'local_coursematrix') . '</p>';
echo '</div></div></div>';

echo '<div class="col-md-2 col-sm-4 mb-3">';
echo '<div class="card text-center bg-success text-white">';
echo '<div class="card-body">';
echo '<h3 class="card-title">' . $stats->completedusers . '</h3>';
echo '<p class="card-text">' . get_string('status_completed', 'local_coursematrix') . '</p>';
echo '</div></div></div>';
echo '</div>';

// Per-plan statistics table.
echo $OUTPUT->heading(get_string('planstatistics', 'local_coursematrix'), 3);

if (!empty($stats->plans)) {
    $table = new html_table();
    $table->head = [
        get_string('planname', 'local_coursematrix'),
        get_string('totalusers', 'local_coursematrix'),
        get_string('inprogress', 'local_coursematrix'),
        get_string('status_overdue', 'local_coursematrix'),
        get_string('status_completed', 'local_coursematrix'),
        get_string('actions', 'local_coursematrix'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($stats->plans as $plan) {
        $viewurl = new moodle_url($PAGE->url, ['planid' => $plan->id]);

        $progress = '<div class="progress" style="min-width: 100px;">';
        if ($plan->total > 0) {
            $progress .= '<div class="progress-bar bg-info" style="width: ' . $plan->activepct . '%"></div>';
            $progress .= '<div class="progress-bar bg-danger" style="width: ' . $plan->overduepct . '%"></div>';
            $progress .= '<div class="progress-bar bg-success" style="width: ' . $plan->completedpct . '%"></div>';
        }
        $progress .= '</div>';

        $table->data[] = [
            s($plan->name),
            $plan->total,
            $plan->active . ' (' . $plan->activepct . '%)',
            '<span class="text-danger font-weight-bold">' . $plan->overdue . ' (' . $plan->overduepct . '%)</span>',
            '<span class="text-success">' . $plan->completed . ' (' . $plan->completedpct . '%)</span>',
            html_writer::link($viewurl, get_string('viewusers', 'local_coursematrix'), ['class' => 'btn btn-sm btn-secondary']),
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noplans', 'local_coursematrix'), 'info');
}

// User list with filters.
echo '<hr>';
echo $OUTPUT->heading(get_string('userlist', 'local_coursematrix'), 3);

// Filter form.
echo '<form method="get" class="form-inline mb-3">';
echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('filterbyplan', 'local_coursematrix') . '</label>';
echo '<select name="planid" class="form-control">';
echo '<option value="">' . get_string('allplans', 'local_coursematrix') . '</option>';
foreach ($plans as $plan) {
    $selected = ($filterplan == $plan->id) ? 'selected' : '';
    echo '<option value="' . $plan->id . '" ' . $selected . '>' . s($plan->name) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('filterbystatus', 'local_coursematrix') . '</label>';
echo '<select name="status" class="form-control">';
echo '<option value="">' . get_string('allstatuses', 'local_coursematrix') . '</option>';
$statuses = ['active' => 'status_active', 'overdue' => 'status_overdue', 'completed' => 'status_completed'];
foreach ($statuses as $key => $langkey) {
    $selected = ($filterstatus == $key) ? 'selected' : '';
    echo '<option value="' . $key . '" ' . $selected . '>' . get_string($langkey, 'local_coursematrix') . '</option>';
}
echo '</select>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary">Filter</button>';
echo '</form>';

// User list table.
if (!empty($userlist)) {
    $table = new html_table();
    $table->head = [
        get_string('user'),
        get_string('learningplan', 'local_coursematrix'),
        get_string('currentcourse', 'local_coursematrix'),
        get_string('startdate', 'local_coursematrix'),
        get_string('status', 'local_coursematrix'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($userlist as $up) {
        $statusclass = '';
        $statustext = get_string('status_' . $up->status, 'local_coursematrix');
        if ($up->status == 'overdue') {
            $statusclass = 'badge badge-danger';
        } else if ($up->status == 'completed') {
            $statusclass = 'badge badge-success';
        } else {
            $statusclass = 'badge badge-info';
        }

        $table->data[] = [
            $up->firstname . ' ' . $up->lastname . '<br><small class="text-muted">' . $up->email . '</small>',
            s($up->planname),
            $up->coursename ?: '-',
            $up->startdate ? userdate($up->startdate, get_string('strftimedateshort')) : '-',
            '<span class="' . $statusclass . '">' . $statustext . '</span>',
        ];
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noplandata', 'local_coursematrix'), 'info');
}

echo $OUTPUT->footer();
