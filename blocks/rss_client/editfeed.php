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
 * Script to let a user edit the properties of a particular RSS feed.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir .'/simplepie/moodle_simplepie.php');

class feed_edit_form extends moodleform {
    protected $isadding;
    protected $caneditshared;
    protected $title = '';
    protected $description = '';

    function __construct($actionurl, $isadding, $caneditshared) {
        $this->isadding = $isadding;
        $this->caneditshared = $caneditshared;
        parent::moodleform($actionurl);
    }

    function definition() {
        $mform =& $this->_form;

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'header', get_string('feed', 'block_rss_client'));

        $mform->addElement('text', 'url', get_string('feedurl', 'block_rss_client'), array('size' => 60));
        $mform->setType('url', PARAM_URL);
        $mform->addRule('url', null, 'required');

        $validatejs = "window.open('http://feedvalidator.org/check.cgi?url='+" .
                "getElementById('id_url').value, 'validate', " .
                "'width=640,height=480,scrollbars=yes,status=yes,resizable=yes'); return true;";
        $validatelink = '<a href="#" onclick="' . $validatejs . '">' . get_string('validatefeed', 'block_rss_client') . '</a>';
        $mform->addElement('static', 'validatelink', '', $validatelink);

        $mform->addElement('text', 'preferredtitle', get_string('customtitlelabel', 'block_rss_client'), array('size' => 60));
        $mform->setType('preferredtitle', PARAM_NOTAGS);

        if ($this->caneditshared) {
            $mform->addElement('selectyesno', 'shared', get_string('sharedfeed', 'block_rss_client'));
            $mform->setDefault('shared', 0);
        }

        $submitlabal = null; // Default
        if ($this->isadding) {
            $submitlabal = get_string('addnewfeed', 'block_rss_client');
        }
        $this->add_action_buttons(true, $submitlabal);
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $rss =  new moodle_simplepie();
        // set timeout for longer than normal to try and grab the feed
        $rss->set_timeout(10);
        $rss->set_feed_url($data['url']);
        $rss->init();

        if ($rss->error()) {
            $errors['url'] = get_string('errorloadingfeed', 'block_rss_client', $rss->error());
        } else {
            $this->title = $rss->get_title();
            $this->description = $rss->get_description();
        }

        return $errors;
    }

    function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->title = $this->title;
            $data->description = $this->description;
        }
        return $data;
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INTEGER);
$rssid = optional_param('rssid', 0, PARAM_INTEGER); // 0 mean create new.

if ($courseid == SITEID) {
    $courseid = 0;
}
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $PAGE->set_course($course);
    $context = $PAGE->context;
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
    $PAGE->set_context($context);
}

$managesharedfeeds = has_capability('block/rss_client:manageanyfeeds', $context);
if (!$managesharedfeeds) {
    require_capability('block/rss_client:manageownfeeds', $context);
}

$urlparams = array('rssid' => $rssid);
$manageparams = array();
if ($courseid) {
    $urlparams['courseid'] = $courseid;
    $manageparams[] = 'courseid=' . $courseid;
}
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
    $manageparams = 'returnurl=' . $returnurl;
}
if ($manageparams) {
    $manageparams = '?' . implode('&', $manageparams);
} else {
    $manageparams = '';
}
$PAGE->set_url('blocks/rss_client/editfeed.php', $urlparams);
$PAGE->set_generaltype('form');

if ($rssid) {
    $isadding = false;
    $rssrecord = $DB->get_record('block_rss_client', array('id' => $rssid), '*', MUST_EXIST);
} else {
    $isadding = true;
    $rssrecord = new stdClass;
}

$mform = new feed_edit_form($PAGE->url, $isadding, $managesharedfeeds);
$mform->set_data($rssrecord);

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/blocks/rss_client/managefeeds.php' . $manageparams);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;
    if (!$managesharedfeeds) {
        $data->shared = 0;
    }

    if ($isadding) {
        $DB->insert_record('block_rss_client', $data);
    } else {
        $data->id = $rssid;
        $DB->update_record('block_rss_client', $data);
    }

    redirect($CFG->wwwroot . '/blocks/rss_client/managefeeds.php' . $manageparams);

} else {
    if ($isadding) {
        $strtitle = get_string('addnewfeed', 'block_rss_client');
    } else {
        $strtitle = get_string('editafeed', 'block_rss_client');
    }

    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);

    $navlinks = array(
        array('name' => get_string('administration'), 'link' => "$CFG->wwwroot/$CFG->admin/index.php", 'type' => 'misc'),
        array('name' => get_string('managemodules'), 'link' => null, 'type' => 'misc'),
        array('name' => get_string('blocks'), 'link' => null, 'type' => 'misc'),
        array('name' => get_string('feedstitle', 'block_rss_client'), 'link' => "$CFG->wwwroot/$CFG->admin/settings.php?section=blocksettingrss_client", 'type' => 'misc'),
        array('name' => get_string('managefeeds', 'block_rss_client'), 'link' => $CFG->wwwroot . '/blocks/rss_client/managefeeds.php' . $manageparams, 'type' => 'misc'),
        array('name' => $strtitle, 'link' => null,  'type' => 'misc'),
    );
    $navigation = build_navigation($navlinks);

    echo $OUTPUT->header($navigation);
    echo $OUTPUT->heading($strtitle, 2);

    $mform->display();

    echo $OUTPUT->footer();
}

