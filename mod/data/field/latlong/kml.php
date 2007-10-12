<?php

// A lot of this initial stuff is copied from mod/data/view.php

require_once('../../../../config.php');
require_once('../../lib.php');

// Optional params: row id "rid" - if set then export just one, otherwise export all

$d       = required_param('d', PARAM_INT);   // database id
$fieldid = required_param('fieldid', PARAM_INT);   // field id
$rid     = optional_param('rid', 0, PARAM_INT);    //record id


if ($rid) {
    if (! $record = get_record('data_records', 'id', $rid)) {
        error('Record ID is incorrect');
    }
    if (! $data = get_record('data', 'id', $record->dataid)) {
        error('Data ID is incorrect');
    }
    if (! $course = get_record('course', 'id', $data->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
    if (! $field = get_record('data_fields', 'id', $fieldid)) {
        error('Field ID is incorrect');
    }
    if (! $field->type == 'latlong') { // Make sure we're looking at a latlong data type!
        error('Field ID is incorrect');
    }
    if (! $content = get_record('data_content', 'fieldid', $fieldid, 'recordid', $rid)) {
        error('Field content not found');
    }
} else {   // We must have $d
    if (! $data = get_record('data', 'id', $d)) {
        error('Data ID is incorrect');
    }
    if (! $course = get_record('course', 'id', $data->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
    if (! $field = get_record('data_fields', 'id', $fieldid)) {
        error('Field ID is incorrect');
    }
    if (! $field->type == 'latlong') { // Make sure we're looking at a latlong data type!
        error('Field ID is incorrect');
    }
    $record = NULL;
}

require_course_login($course, true, $cm);

/// If it's hidden then it's don't show anything.  :)
if (empty($cm->visible) and !has_capability('moodle/course:viewhiddenactivities',get_context_instance(CONTEXT_MODULE, $cm->id))) {
    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($data->name), "", $navigation,
        "", "", true, '', navmenu($course, $cm));
    notice(get_string("activityiscurrentlyhidden"));
}

/// If we have an empty Database then redirect because this page is useless without data
if (has_capability('mod/data:managetemplates', $context)) {
    if (!record_exists('data_fields','dataid',$data->id)) {      // Brand new database!
        redirect($CFG->wwwroot.'/mod/data/field.php?d='.$data->id);  // Redirect to field entry
    }
}




//header('Content-type: text/plain'); // This is handy for debug purposes to look at the KML in the browser
header('Content-type: application/vnd.google-earth.kml+xml kml');
header('Content-Disposition: attachment; filename="moodleearth-'.$d.'-'.$rid.'-'.$fieldid.'.kml"');


echo data_latlong_kml_top();

if($rid) { // List one single item
    $pm->name = data_latlong_kml_get_item_name($content, $field);
    $pm->description = "&lt;a href='$CFG->wwwroot/mod/data/view.php?d=$d&amp;rid=$rid'&gt;Item #$rid&lt;/a&gt; in Moodle data activity";
    $pm->long = $content->content1;
    $pm->lat = $content->content;
    echo data_latlong_kml_placemark($pm);
} else {   // List all items in turn

    $contents = get_records('data_content', 'fieldid', $fieldid);

    echo '<Document>';

    foreach($contents as $content) {
        $pm->name = data_latlong_kml_get_item_name($content, $field);
        $pm->description = "&lt;a href='$CFG->wwwroot/mod/data/view.php?d=$d&amp;rid=$content->recordid'&gt;Item #$content->recordid&lt;/a&gt; in Moodle data activity";
        $pm->long = $content->content1;
        $pm->lat = $content->content;
        echo data_latlong_kml_placemark($pm);
    }

    echo '</Document>';

}

echo data_latlong_kml_bottom();




function data_latlong_kml_top() {
    return '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.0">

';
}

function data_latlong_kml_placemark($pm) {
    return '<Placemark>
  <description>'.$pm->description.'</description>
  <name>'.$pm->name.'</name>
  <LookAt>
    <longitude>'.$pm->long.'</longitude>
    <latitude>'.$pm->lat.'</latitude>
    <range>30500.8880792294568</range>
    <tilt>46.72425699662645</tilt>
    <heading>0.0</heading>
  </LookAt>
  <visibility>0</visibility>
  <Point>
    <extrude>1</extrude>
    <altitudeMode>relativeToGround</altitudeMode>
    <coordinates>'.$pm->long.','.$pm->lat.',50</coordinates>
  </Point>
</Placemark>
';
}

function data_latlong_kml_bottom() {
    return '</kml>';
}

function data_latlong_kml_get_item_name($content, $field) {
    // $field->param2 contains the user-specified labelling method

    $name = '';

    if($field->param2 > 0) {
        $name = htmlspecialchars(get_field('data_content', 'content', 'fieldid', $field->param2, 'recordid', $content->recordid));
    }elseif($field->param2 == -2) {
        $name = $content->content . ', ' . $content->content1;
    }
    if($name=='') { // Done this way so that "item #" is the default that catches any problems
        $name = get_string('entry', 'data') . " #$content->recordid";
    }


    return $name;
}
