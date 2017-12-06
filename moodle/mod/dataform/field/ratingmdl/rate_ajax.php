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
 * This page receives ratingmdl ajax rating submissions.
 *
 * Similar to rating/rate_ajax.php except for it allows retrieving multiple aggregations.
 *
 * @package dataformfield_ratingmdl
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once('../../../../config.php');
require_once('ratinglib.php');

$contextid         = required_param('contextid', PARAM_INT);
$component         = required_param('component', PARAM_COMPONENT);
$ratingarea        = required_param('ratingarea', PARAM_AREA);
$itemid            = required_param('itemid', PARAM_INT);
$scaleid           = required_param('scaleid', PARAM_INT);
$userrating        = required_param('rating', PARAM_INT);
// Which user is being rated. Required to update their grade.
$rateduserid       = required_param('rateduserid', PARAM_INT);
// We're going to calculate the aggregate and return it to the client.
$aggregationmethod = optional_param('aggregation', RATING_AGGREGATE_NONE, PARAM_SEQUENCE);

$result = new stdClass;

// If session has expired and its an ajax request so we cant do a page redirect.
if (!isloggedin()) {
    $result->error = get_string('sessionerroruser', 'error');
    echo json_encode($result);
    die();
}

list($context, $course, $cm) = get_context_info_array($contextid);

// Instantiate the Dataform.
$df = mod_dataform_dataform::instance($cm->instance);
require_login($df->course->id, false, $df->cm);

// Sesskey.
if (!confirm_sesskey()) {
    echo $OUTPUT->header();
    echo get_string('ratepermissiondenied', 'rating');
    echo $OUTPUT->footer();
    die();
}

$PAGE->set_context($df->context);
$PAGE->set_url('/mod/dataform/field/ratingmdl/rate_ajax.php', array('contextid' => $context->id));

// Get the field.
$field = $df->field_manager->get_field_by_name($ratingarea);

// Get the entry.
$entry = $DB->get_record('dataform_entries', array('id' => $itemid));

// Get the user's rating record if exists.
$params = array(
    'contextid' => $df->context->id,
    'component' => 'mod_dataform',
    'ratingarea' => $field->name,
    'itemid' => $entry->id,
    'userid' => $USER->id,
);
$ratingrec = $DB->get_record('rating', $params);

// Get the entry rating.
$raterelement = "c$field->id". '_ratinguserid';
$ratingelement = "c$field->id". '_usersrating';

$entry->$raterelement = $USER->id;
$entry->$ratingelement = !empty($ratingrec->rating) ? $ratingrec->rating : null;
if (!$entryrating = $field->get_entry_rating($entry, true)) {
    $result->error = get_string('ratepermissiondenied', 'rating');
    echo json_encode($result);
    die();
}
$entry->rating = $entryrating;

$rm = new ratingmdl_rating_manager();

// Check the module rating permissions.
if (!$field->user_can_rate($entry, $USER->id)) {
    $result->error = get_string('ratepermissiondenied', 'rating');
    $result->value = $entryrating->rating;
    echo json_encode($result);
    die();
}

// Check that the rating is valid.
$params = array(
    'context'     => $context,
    'component'   => $component,
    'ratingarea'  => $ratingarea,
    'itemid'      => $itemid,
    'scaleid'     => $scaleid,
    'rating'      => $userrating,
    'rateduserid' => $rateduserid,
    'aggregation' => $aggregationmethod
);
if (!$rm->check_rating_is_valid($params)) {
    $result->error = get_string('ratinginvalid', 'rating');
    $result->value = $entryrating->rating;
    echo json_encode($result);
    die();
}

// Check that the rating value is in accordance with the field settings.
if ($validationcode = $field->user_can_assign_the_rating_value($entryrating, $userrating)) {
    $result->error = get_string("ratinginvalid$validationcode", 'dataformfield_ratingmdl', $userrating);
    $result->value = $entryrating->rating;
    echo json_encode($result);
    die();
}

// Rating options used to update the rating then retrieving the aggregations.
$ratingoptions = new stdClass;
$ratingoptions->context = $context;
$ratingoptions->ratingarea = $ratingarea;
$ratingoptions->component = $component;
$ratingoptions->itemid  = $itemid;
$ratingoptions->scaleid = $scaleid;
$ratingoptions->userid  = $USER->id;

if ($userrating != RATING_UNSET_RATING) {
    $rating = new rating($ratingoptions);
    $rating->update_rating($userrating);
} else {
    // Delete the rating if the user set to Rate.
    $options = new stdClass;
    $options->contextid = $context->id;
    $options->component = $component;
    $options->ratingarea = $ratingarea;
    $options->userid = $USER->id;
    $options->itemid = $itemid;

    $rm->delete_ratings($options);
}

// Try to update grades in case grading by entries.
$df->grade_manager->update_calculated_grades(array('userid' => $rateduserid));

// Need to retrieve the updated item to get its new aggregate value.
$item = new stdClass;
$item->id = $itemid;

// Most of $ratingoptions variables were previously set.
$ratingoptions->items = array($itemid => $item);
$ratingoptions->aggregate = array(
    RATING_AGGREGATE_AVERAGE,
    RATING_AGGREGATE_MAXIMUM,
    RATING_AGGREGATE_MINIMUM,
    RATING_AGGREGATE_SUM,
);

$firstrating = null;
if ($items = $rm->get_ratings($ratingoptions)) {
    $firstitem = reset($items);
    $firstrating = $firstitem->rating;
}
$aggr = $field->get_rating_display_aggregates($firstrating);

// Result.
$result->success = true;
$result->ratingcount = $aggr->count;
$result->ratingavg = $aggr->avg;
$result->ratingmax = $aggr->max;
$result->ratingmin = $aggr->min;
$result->ratingsum = $aggr->sum;
$result->itemid = $itemid;

echo json_encode($result);
