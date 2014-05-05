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
 * @package   block_rss_client
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
        $mform->addElement('header', 'rsseditfeedheader', get_string('feed', 'block_rss_client'));

        $mform->addElement('text', 'url', get_string('feedurl', 'block_rss_client'), array('size' => 60));
        $mform->setType('url', PARAM_URL);
        $mform->addRule('url', null, 'required');

        $mform->addElement('checkbox', 'autodiscovery', get_string('enableautodiscovery', 'block_rss_client'));
        $mform->setDefault('autodiscovery', 1);
        $mform->setAdvanced('autodiscovery');
        $mform->addHelpButton('autodiscovery', 'enableautodiscovery', 'block_rss_client');

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

    function definition_after_data(){
        $mform =& $this->_form;

        if($mform->getElementValue('autodiscovery')){
            $mform->applyFilter('url', 'feed_edit_form::autodiscover_feed_url');
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $rss =  new moodle_simplepie();
        // set timeout for longer than normal to try and grab the feed
        $rss->set_timeout(10);
        $rss->set_feed_url($data['url']);
        $rss->set_autodiscovery_cache_duration(0);
        $rss->set_autodiscovery_level(SIMPLEPIE_LOCATOR_NONE);
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
            $data->title = '';
            $data->description = '';

            if($this->title){
                $data->title = $this->title;
            }

            if($this->description){
                $data->description = $this->description;
            }
        }
        return $data;
    }

    /**
     * Autodiscovers a feed url from a given url, to be used by the formslibs
     * filter function
     *
     * Uses simplepie with autodiscovery set to maximum level to try and find
     * a feed to subscribe to.
     * See: http://simplepie.org/wiki/reference/simplepie/set_autodiscovery_level
     *
     * @param string URL to autodiscover a url
     * @return string URL of feed or original url if none found
     */
    public static function autodiscover_feed_url($url){
            $rss =  new moodle_simplepie();
            $rss->set_feed_url($url);
            $rss->set_autodiscovery_level(SIMPLEPIE_LOCATOR_ALL);
            // When autodiscovering an RSS feed, simplepie will try lots of
            // rss links on a page, so set the timeout high
            $rss->set_timeout(20);
            $rss->init();

            if($rss->error()){
                return $url;
            }

            // return URL without quoting..
            $discoveredurl = new moodle_url($rss->subscribe_url());
            return $discoveredurl->out(false);
    }
}

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$courseid = optional_param('courseid', 0, PARAM_INT);
$rssid = optional_param('rssid', 0, PARAM_INT); // 0 mean create new.

if ($courseid == SITEID) {
    $courseid = 0;
}
if ($courseid) {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $PAGE->set_course($course);
    $context = $PAGE->context;
} else {
    $context = context_system::instance();
    $PAGE->set_context($context);
}

$managesharedfeeds = has_capability('block/rss_client:manageanyfeeds', $context);
if (!$managesharedfeeds) {
    require_capability('block/rss_client:manageownfeeds', $context);
}

$urlparams = array('rssid' => $rssid);
if ($courseid) {
    $urlparams['courseid'] = $courseid;
}
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$managefeeds = new moodle_url('/blocks/rss_client/managefeeds.php', $urlparams);

$PAGE->set_url('/blocks/rss_client/editfeed.php', $urlparams);
$PAGE->set_pagelayout('admin');

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
    redirect($managefeeds);

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

    redirect($managefeeds);

} else {
    if ($isadding) {
        $strtitle = get_string('addnewfeed', 'block_rss_client');
    } else {
        $strtitle = get_string('editafeed', 'block_rss_client');
    }

    $PAGE->set_title($strtitle);
    $PAGE->set_heading($strtitle);

    $PAGE->navbar->add(get_string('blocks'));
    $PAGE->navbar->add(get_string('pluginname', 'block_rss_client'));
    $PAGE->navbar->add(get_string('managefeeds', 'block_rss_client'), $managefeeds );
    $PAGE->navbar->add($strtitle);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strtitle, 2);

    $mform->display();

    echo $OUTPUT->footer();
}

