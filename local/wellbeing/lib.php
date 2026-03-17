<?php
defined('MOODLE_INTERNAL') || die();
function local_wellbeing_extend_navigation_course(
    navigation_node $navigation,
    stdClass $course,
    context_course $context
) {

    global $PAGE, $DB;

    // ===== YOUR EXISTING CODE (UNCHANGED) =====

    if (strpos($PAGE->pagetype, 'course-view') !== 0) {
        return;
    }

    if (!\local_wellbeing\manager::is_enabled($course->id)) {
        return;
    }

    $url = new moodle_url('/local/wellbeing/dashboard.php', [
        'id' => $course->id
    ]);

    $buttonhtml = '
        <div id="wellbeing-btn-wrapper" style="text-align:left;margin:20px;">
            <a class="btn btn-primary" href="'.$url.'">
                Wellbeing Dashboard
            </a>
        </div>
    ';

    $PAGE->requires->js_init_code("
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('wellbeing-btn-wrapper')) {
                var container = document.querySelector('#region-main');
                if (container) {
                    container.insertAdjacentHTML('afterbegin', ".json_encode($buttonhtml).");
                }
            }
        });
    ");
}

function local_wellbeing_inject_assignment_metrics() {

    global $PAGE, $DB;

    // Only run on assignment edit page
    if ($PAGE->pagetype !== 'mod-assign-mod') {
        return;
    }

    // debugging("WELLBEING: Assignment edit page detected", DEBUG_DEVELOPER);

    $cmid = optional_param('update', 0, PARAM_INT);

    if (!$cmid) {
        // debugging("WELLBEING: CMID missing", DEBUG_DEVELOPER);
        return;
    }

    $cm = get_coursemodule_from_id('assign', $cmid);

    if (!$cm) {
        // debugging("WELLBEING: CM not found", DEBUG_DEVELOPER);
        return;
    }

    $courseid = $cm->course;

    if (!\local_wellbeing\manager::is_enabled($courseid)) {
        // debugging("WELLBEING: Course not enabled", DEBUG_DEVELOPER);
        return;
    }

    $record = $DB->get_record(
        'local_wellbeing_courses',
        ['courseid' => $courseid],
        '*',
        IGNORE_MISSING
    );

        $assignrecord = $DB->get_record('local_wb_assign_metrics', [
        'assignid' => $cm->id
    ]);

        $selectedmetrics = [];

        if ($assignrecord && !empty($assignrecord->metricname)) {
            $selectedmetrics = json_decode($assignrecord->metricname, true);
        }

        $config = [
            'metrics' => $record->metrics_name_json ?? '',
            'selectedmetrics' => $selectedmetrics
        ];


    // debugging("WELLBEING: Sending metrics → " . json_encode($config), DEBUG_DEVELOPER);

    $PAGE->requires->js(
        new moodle_url('/local/wellbeing/js/metrics.js')
    );

    $PAGE->requires->js_init_call(
        'M.local_wellbeing_metrics_init',
        [$config]
    );
}

function local_wellbeing_before_footer() {

    // Call the metrics loader
    local_wellbeing_inject_assignment_metrics();

}

function local_wellbeing_coursemodule_edit_post_actions($moduleinfo, $course) {

    global $DB;

    // Only for assignment module
    if ($moduleinfo->modulename !== 'assign') {
        return $moduleinfo;
    }

    // Read checkbox values
    $metrics = optional_param_array('wellbeing_metrics', [], PARAM_TEXT);

    if (empty($metrics)) {
        debugging("WELLBEING: No metrics selected", DEBUG_DEVELOPER);
        return $moduleinfo;
    }

    $assignid = $moduleinfo->coursemodule;
    $courseid = $course->id;

    // Convert metrics to JSON
    $metricsjson = json_encode(array_values($metrics));

    debugging("WELLBEING JSON → " . $metricsjson, DEBUG_DEVELOPER);

    // Check if record already exists
    $existing = $DB->get_record('local_wb_assign_metrics', [
        'assignid' => $assignid
    ]);

    if ($existing) {

        // Update existing row
        $existing->metricname = $metricsjson;
        $existing->timemodified = time();

        $DB->update_record('local_wb_assign_metrics', $existing);

    } else {

        // Insert new row
        $record = new stdClass();
        $record->assignid = $assignid;
        $record->courseid = $courseid;
        $record->metricname = $metricsjson;
        $record->timecreated = time();
        $record->timemodified = time();

        $DB->insert_record('local_wb_assign_metrics', $record);
    }

    debugging("WELLBEING: Metrics stored in JSON", DEBUG_DEVELOPER);

    return $moduleinfo;
}