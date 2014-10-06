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
 * Kaltura media gallery main viewing page.
 *
 * @package    local_kalturamediagallery
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

global $USER, $PAGE;

require_login();
$courseid = required_param('courseid', PARAM_INT);

$context = context_course::instance($courseid);
require_capability('local/kalturamediagallery:view', $context);

$mediagallery = get_string('heading_mediagallery', 'local_kalturamediagallery');

$course = get_course($courseid);

$PAGE->set_context($context);
$PAGE->set_course($course);
$site = get_site();
$header  = format_string($site->shortname).": $mediagallery";

$PAGE->navbar->add(get_string('nav_mediagallery', 'local_kalturamediagallery'));

$PAGE->set_url('/local/kalturamediagallery/index.php', array('courseid' => $courseid));
$PAGE->set_pagetype('kalturamediagallery-index');
$PAGE->set_pagelayout('standard');
$PAGE->set_title($header);
$PAGE->set_heading($header);

$pageclass = 'kaltura-mediagallery-body';
$PAGE->add_body_class($pageclass);

echo $OUTPUT->header();

// Request the launch content with an iframe tag.
$attr = array(
    'id' => 'contentframe',
    'height' => '600px',
    'width' => '100%',
    'allowfullscreen' => 'true',
    'webkitallowfullscreen' => 'true',
    'mozallowfullscreen' => 'true',
    'src' => 'lti_launch.php?courseid='.$courseid
);
echo html_writer::tag('iframe', '', $attr);

// Require a YUI module to make the iframe tag be as large as possible.
$params = array(
    'bodyclass' => $pageclass,
    'lastheight' => null,
    'padding' => 15
);
$PAGE->requires->yui_module('moodle-local_kaltura-lticontainer', 'M.local_kaltura.init', array($params), null, true);

echo $OUTPUT->footer();