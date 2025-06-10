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
 * Library of internal classes and functions for module hotpot
 *
 * All the hotpot specific functions, needed to implement the module
 * logic, should go to here. Instead of having bunch of function named
 * hotpot_something() taking the hotpot instance as the first
 * parameter, we use a class hotpot that provides all methods.
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/lib.php');     // we extend this library here
require_once($CFG->libdir . '/gradelib.php');   // we use some rounding and comparing routines here

/**
 * Full-featured hotpot API
 *
 * This wraps the hotpot database record with a set of methods that are called
 * from the module itself. The class should be initialized right after you get
 * $hotpot, $cm and $course records at the begining of the script.
 */
class hotpot {

    /**#@+
     * internal codes to indicate what text is to be used
     * for the name and introduction of a HotPot instance
     *
     * @var integer
     */
    const TEXTSOURCE_FILE           = 0; // was TEXTSOURCE_QUIZ
    const TEXTSOURCE_FILENAME       = 1;
    const TEXTSOURCE_FILEPATH       = 2;
    const TEXTSOURCE_SPECIFIC       = 3;
    /**#@-*/

    /**#@+
     * database codes to indicate what navigation aids are used
     * when the quiz apears in the browser
     *
     * @var integer
     */
    const NAVIGATION_NONE           = 0; // was 6
    const NAVIGATION_MOODLE         = 1; // was NAVIGATION_BAR
    const NAVIGATION_FRAME          = 2;
    const NAVIGATION_EMBED          = 3; // was NAVIGATION_IFRAME
    const NAVIGATION_ORIGINAL       = 4;
    const NAVIGATION_TOPBAR         = 5; // was NAVIGATION_GIVEUP but that was replaced by stopbutton
    /**#@-*/

    /**#@+
     * database codes to indicate the grading method for a HotPot instance
     *
     * @var integer
     */
    const GRADEMETHOD_HIGHEST       = 1;
    const GRADEMETHOD_AVERAGE       = 2;
    const GRADEMETHOD_FIRST         = 3;
    const GRADEMETHOD_LAST          = 4;
    /**#@-*/

    /**#@+
     * database codes to indicate the source/config location for a HotPot instance
     *
     * @var integer
     */
    const LOCATION_COURSEFILES      = 0;
    const LOCATION_SITEFILES        = 1;
    const LOCATION_WWW              = 2;
    /**#@-*/

    /**#@+
     * bit-masks used to extract bits from the hotpot "title" setting
     *
     * @var integer
     */
    const TITLE_SOURCE              = 0x03; // 1st - 2nd bits
    const TITLE_UNITNAME            = 0x04; // 3rd bit
    const TITLE_SORTORDER           = 0x08; // 4th bit
    /**#@-*/

    /**#@+
     * database codes for the following time fields
     *  - timelimit : the maximum length of one attempt
     *  - delay3 : the delay after end of quiz before control returns to Moodle
     *
     * @var integer
     */
    const TIME_SPECIFIC             = 0;
    const TIME_TEMPLATE             = -1;
    const TIME_AFTEROK              = -2;
    const TIME_DISABLE              = -3;
    /**#@-*/

    /**#@+
     * internal codes to indicate whether a HotPot can resume or restart
     *
     * @var integer
     */
    const CONTINUE_RESUMEQUIZ       = 1;
    const CONTINUE_RESTARTQUIZ      = 2;
    const CONTINUE_RESTARTUNIT      = 3;
    const CONTINUE_ABANDONUNIT      = 4;
    /**#@-*/

    /**#@+
     * internal codes to refer to the HTTP return code
     *
     * @var integer
     */
    const HTTP_NO_RESPONSE          = 0; // was false
    const HTTP_204_RESPONSE         = 1; // was true
    /**#@-*/

    /**#@+
     * database code to indicate current state of HotPot attempt
     *
     * @var integer
     */
    const STATUS_INPROGRESS         = 1;
    const STATUS_TIMEDOUT           = 2;
    const STATUS_ABANDONED          = 3;
    const STATUS_COMPLETED          = 4;
    const STATUS_PAUSED             = 5;
    /**#@-*/

    /**#@+
     * database code to indicate the kind of feedback link, if any
     *
     * @var integer
     */
    const FEEDBACK_NONE             = 0;
    const FEEDBACK_WEBPAGE          = 1;
    const FEEDBACK_FORMMAIL         = 2;
    const FEEDBACK_MOODLEFORUM      = 3;
    const FEEDBACK_MOODLEMESSAGING  = 4;
    /**#@-*/

    /**#@+
     * database code to indicate the kind of STOP button
     *
     * @var integer
     */
    const STOPBUTTON_NONE           = 0;
    const STOPBUTTON_LANGPACK       = 1;
    const STOPBUTTON_SPECIFIC       = 2;
    /**#@-*/

    /**#@+
     * database code to indicate the kind of previous/next activity
     *
     * @var integer
     */
    const ACTIVITY_NONE             = 0;
    const ACTIVITY_COURSE_ANY       = -1;
    const ACTIVITY_SECTION_ANY      = -2;
    const ACTIVITY_COURSE_HOTPOT    = -3;
    const ACTIVITY_SECTION_HOTPOT   = -4;
    /**#@-*/

    /**#@+
     * database code to indicate options on the entry page
     *
     * @var integer
     */
    const ENTRYOPTIONS_TITLE        = 0x01;
    const ENTRYOPTIONS_GRADING      = 0x02;
    const ENTRYOPTIONS_DATES        = 0x04;
    const ENTRYOPTIONS_ATTEMPTS     = 0x08;
    /**#@-*/

    /**#@+
     * database code to indicate options on the exit page
     *
     * @var integer
     */
    const EXITOPTIONS_TITLE         = 0x01;
    const EXITOPTIONS_ENCOURAGEMENT = 0x02;
    const EXITOPTIONS_ATTEMPTSCORE  = 0x04;
    const EXITOPTIONS_HOTPOTGRADE   = 0x08;
    const EXITOPTIONS_RETRY         = 0x10;
    const EXITOPTIONS_INDEX         = 0x20;
    const EXITOPTIONS_COURSE        = 0x40;
    const EXITOPTIONS_GRADES        = 0x80;
    /**#@-*/

    /**#@+
     * database code to indicate which CSS styles should be overridden
     *
     * @var integer
     */
    const BODYSTYLES_BACKGROUND     = 0x01;
    const BODYSTYLES_COLOR          = 0x02;
    const BODYSTYLES_FONT           = 0x04;
    const BODYSTYLES_MARGIN         = 0x08;
    /**#@-*/

    /**#@+
     * three sets of 6 bits define the times at which a quiz may be reviewed
     * e.g. 0x3f = 0011 1111 (i.e. right most 6 bits)
     *
     * @var integer
     */
    const REVIEW_DURINGATTEMPT = 0x0003f; // 1st set of 6 bits : during attempt
    const REVIEW_AFTERATTEMPT  = 0x00fc0; // 2nd set of 6 bits : after attempt (but before quiz closes)
    const REVIEW_AFTERCLOSE    = 0x3f000; // 3rd set of 6 bits : after the quiz closes
    /**#@-*/

    /**#@+
     * within each group of 6 bits we determine what should be shown
     * e.g. 0x1041 = 00-0001 0000-01 00-0001 (i.e. 3 sets of 6 bits)
     *
     * @var integer
     */
    const REVIEW_RESPONSES = 0x1041; // 1*0x1041 : 1st bit of each 6-bit set : Show student responses
    const REVIEW_ANSWERS   = 0x2082; // 2*0x1041 : 2nd bit of each 6-bit set : Show correct answers
    const REVIEW_SCORES    = 0x4104; // 3*0x1041 : 3rd bit of each 6-bit set : Show scores
    const REVIEW_FEEDBACK  = 0x8208; // 4*0x1041 : 4th bit of each 6-bit set : Show feedback
    /**#@-*/

    /** @var stdclass course module record */
    public $cm;

    /** @var stdclass course record */
    public $course;

    /** @var stdclass context object */
    public $context;

    /** @var int hotpot instance identifier */
    public $id;

    /** @var string hotpot activity name */
    public $name;

    /** @var string url or path of the source file for this HotPot instance */
    public $sourcefile;

    /** @var string the type of the source file for this HotPot instance */
    public $sourcetype;

    /** @var int the file itemid of the sourcefile for this HotPot instance */
    public $sourcelocation;

    /** @var string url or path of the config file for this HotPot instance */
    public $configfile;

    /** @var int the location of the configfile for this HotPot instance */
    public $configlocation;

    /** @var xxx */
    public $entrycm;

    /** @var xxx */
    public $entrygrade;

    /** @var xxx */
    public $entrypage;

    /** @var xxx */
    public $entrytext;

    /** @var xxx */
    public $entryformat;

    /** @var xxx */
    public $entryoptions;

    /** @var xxx */
    public $exitpage;

    /** @var xxx */
    public $exittext;

    /** @var xxx */
    public $exitformat;

    /** @var xxx */
    public $exitoptions;

    /** @var xxx */
    public $exitcm;

    /** @var xxx */
    public $exitgrade;

    /** @var string the output format to be used when generating browser content */
    public $outputformat;

    /** @var int navigation aids to be used when this HotPot instance appears in the browser */
    public $navigation;

    /** @var int defines what will be displayed as the title in the browser */
    public $title;

    /** @var int indicates what kind of of stop button, if any, will be displayed */
    public $stopbutton;

    /** @var string the string to be displayed on the stop button */
    public $stoptext;

    /** @var boolean flag to indicate copy-paste should be allowed or not */
    public $allowpaste;

    /** @var boolean flag to indicate quiz content should be run processed by Moodle filters */
    public $usefilters;

    /** @var boolean flag to indicate quiz content should be linked to Moodle glossaries */
    public $useglossary;

    /** @var string name, if any, of mediaplayer filter to be used to replace media players  */
    public $usemediafilter;

    /** @var int what kind of popup, if any, should be shown for student feedback */
    public $studentfeedback;

    /** @var string url, if any, to which student feedback will be sent */
    public $studentfeedbackurl;

    /** @var int time after which this HotPot becomes available */
    public $timeopen;

    /** @var int time after which this HotPot is no longer available */
    public $timeclose;

    /** @var int the time limit for a single attempt at this HotPot */
    public $timelimit;

    /** @var int minimum time delay, in seconds, between first and second attempt */
    public $delay1;

    /** @var int minimum time delay, in seconds, between attempts after the second attempt */
    public $delay2;

    /** @var int delay, in seconds, between finishing a quiz and returning control to Moodle */
    public $delay3;

    /** @var string optional password required to access this HotPot instance */
    public $password;

    /** @var string optional IP mask to limit access this HotPot instance */
    public $subnet;

    /** @var xxx */
    public $reviewoptions;

    /** @var int 0-100 to show maximum number of attempts allowed at this HotPot instance */
    public $attemptlimit;

    /** @var int code denoting the grading method for this HotPot instance */
    public $grademethod;

    /** @var int 0-100 to show maximum grade for this HotPot instance */
    public $gradeweighting;

    /** @var boolean if true, every click of "hint", "clue" or "check" will be stored in the Moodle database */
    public $clickreporting;

    /** @var boolean if true, the raw xml returned form the attempt will be stored in the Moodle database  */
    public $discarddetails;

    /** @var int timestamp of when the module was modified */
    public $timemodified;

    /** @var int timestamp of when the module was created */
    public $timecreated;

    /**#@+ @var int completion settings */
    public $completionmingrade;
    public $completionpass;
    public $completioncompleted;
    /**#@-*/

    /** @var int timestamp of when this object was created */
    public $time;

    /** @var object representing the source file */
    public $source;

    /** @var object representing the config file */
    public $config;

    /** @var object representing a record from the hotpot_attempt table */
    public $attempt;

    /** @var array cache of all attempts by current user at this HotPot activity */
    public $attempts;

    /** @var object representing the grade_grade this HotPot activity */
    public $gradeitem;

    /** @var boolean cache for hotpot:attempt capability */
    public $canattempt;

    /** @var boolean cache for hotpot:deleteallattempts capability */
    public $candeleteallattempts;

    /** @var boolean cache for hotpot:deletemyattempts capability */
    public $candeletemyattempts;

    /** @var boolean cache for hotpot:manage capability */
    public $canmanage;

    /** @var boolean cache for hotpot:preview capability */
    public $canpreview;

    /** @var boolean cache for hotpot:reviewallattempts capability */
    public $canreviewallattempts;

    /** @var boolean cache for hotpot:reviewmyattempts capability */
    public $canreviewmyattempts;

    /** @var boolean cache for hotpot:view capability */
    public $canview;

    /** @var boolean cache of swithc to show if user can start this hotpot */
    public $canstart;

    /**
     * Initializes the hotpot API instance using the data from DB
     *
     * Makes deep copy of all passed records properties. Replaces integer $course attribute
     * with a full database record (course should not be stored in instances table anyway).
     *
     * The method is "protected" to prevent it being called directly. To create a new
     * instance of this class please use the self::create() method (see below).
     *
     * @param stdclass $dbrecord HotPot instance data from the {hotpot} table
     * @param stdclass $cm       Course module record as returned by {@link get_coursemodule_from_id()}
     * @param stdclass $course   Course record from {course} table
     * @param stdclass $context  The context of the hotpot instance
     * @param stdclass $attempt  attempt data from the {hotpot_attempts} table
     */
    private function __construct($dbrecord, $cm, $course, $context=null, $attempt=null) {
        foreach ($dbrecord as $field => $value) {
            if (property_exists('hotpot', $field)) {
                $this->$field = $value;
            }
        }
        $this->cm = $cm;
        $this->course = $course;
        if (is_null($context)) {
            $this->context = hotpot_get_context(CONTEXT_MODULE, $this->cm->id);
        } else {
            $this->context = $context;
        }
        if (is_null($attempt)) {
            // do nothing
        } else {
            $this->attempt = $attempt;
        }
        $this->time = time();
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Static methods                                                             //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * Creates a new HotPot object
     *
     * @param stdclass $dbrecord a row from the hotpot table
     * @param stdclass $cm a row from the course_modules table
     * @param stdclass $course a row from the course table
     * @return hotpot the new hotpot object
     */
    static public function create($dbrecord, $cm, $course, $context=null, $attempt=null) {
        return new hotpot($dbrecord, $cm, $course, $context, $attempt);
    }

    /**
     * set_user_editing
     */
    static public function set_user_editing() {
        global $USER;
        $editmode = optional_param('editmode', null, PARAM_BOOL);
        if (! is_null($editmode)) {
            $USER->editing = $editmode;
        }
    }

    /**
     * Returns the localized list of navigation settings for a HotPot instance
     *
     * @return array
     */
    static public function available_navigations_list() {
        return array (
            self::NAVIGATION_MOODLE   => get_string('navigation_moodle', 'mod_hotpot'),
            self::NAVIGATION_TOPBAR   => get_string('navigation_topbar', 'mod_hotpot'),
            self::NAVIGATION_FRAME    => get_string('navigation_frame', 'mod_hotpot'),
            self::NAVIGATION_EMBED    => get_string('navigation_embed', 'mod_hotpot'),
            self::NAVIGATION_ORIGINAL => get_string('navigation_original', 'mod_hotpot'),
            self::NAVIGATION_NONE     => get_string('navigation_none', 'mod_hotpot')
        );
    }

    /**
     * Returns the localized list of feedback settings for a HotPot instance
     *
     * @return array
     */
    static public function available_feedbacks_list() {
        global $CFG;
        $list = array (
            self::FEEDBACK_NONE        => get_string('none'),
            self::FEEDBACK_WEBPAGE     => get_string('feedbackwebpage',  'mod_hotpot'),
            self::FEEDBACK_FORMMAIL    => get_string('feedbackformmail', 'mod_hotpot'),
            self::FEEDBACK_MOODLEFORUM => get_string('feedbackmoodleforum', 'mod_hotpot')
        );
        if ($CFG->messaging) {
            $list[self::FEEDBACK_MOODLEMESSAGING] = get_string('feedbackmoodlemessaging', 'mod_hotpot');
        }
        return $list;
    }

    /**
     * Returns the list of media players for the HotPot module
     *
     * @return array
     */
    static public function available_mediafilters_list() {
        $plugins = get_list_of_plugins('mod/hotpot/mediafilter'); // sorted

        if (in_array('moodle', $plugins)) {
            // make 'moodle' the first element in the plugins array
            unset($plugins[array_search('moodle', $plugins)]);
            array_unshift($plugins, 'moodle');
        }

        // define element type for list of mediafilters (select, radio, checkbox)
        $options = array('' => get_string('none'));
        foreach ($plugins as $plugin) {
            $options[$plugin] = get_string('mediafilter_'.$plugin, 'mod_hotpot');
        }
        return $options;
    }

    /**
     * Returns the localized list of output format setings for a given HotPot sourcetype
     *
     * @return array
     */
    static public function available_outputformats_list($sourcetype) {

        $outputformats = array(
            '0' => get_string('outputformat_best', 'mod_hotpot')
        );
        if ($sourcetype) {
            $classes = self::get_classes('hotpotattempt', 'renderer.php', 'mod_', '_renderer');
            foreach ($classes as $class) {
                // use call_user_func() to prevent syntax error in PHP 5.2.x
                $sourcetypes = call_user_func(array($class, 'sourcetypes'));
                if (in_array($sourcetype, $sourcetypes)) {
                    // strip prefix, "mod_hotpot_attempt_", and suffix, "_renderer"
                    $outputformat = substr($class, 19, -9);
                    $outputformats[$outputformat] = get_string('outputformat_'.$outputformat, 'mod_hotpot');
                }
            }
            uksort($outputformats, array('hotpot', 'uksort_outputformats'));

            // remove "best" if there is only one compatible output format
            // if (count($outputformats)==2) {
            //     unset($outputformats[0]);
            // }
        }
        return $outputformats;
    }

    /**
     * uksort_outputformats
     *
     * @param string $a
     * @param string $b
     * @return integer
     */
    static public function uksort_outputformats($a, $b) {

        if ($a == '0') {
            return -1;
        }
        if ($b == '0') {
            return 1;
        }

        $a = explode('_', $a);
        $b = explode('_', $b);

        $a_count = count($a);
        $b_count = count($b);

        // special rules for comparing two "hp" classes
        $hp = ($a[0] == 'hp' && $b[0] == 'hp');

        $i = 0;
        while ($i < $a_count && $i < $b_count) {
            // For HP files, the source format ($i=1) and output version ($i=4)
            // are sorted in DESCENDING order.
            if ($hp && ($i == 1 || $i == 4)) {
                $asc = false;
            } else {
                $asc = true;
            }
            if ($a[$i] < $b[$i]) {
                return ($asc ? -1 : 1);
            }
            if ($a[$i] > $b[$i]) {
                return ($asc ? 1 : -1);
            }
            $i++;
        }

        // For Jmatch/JMix, the drag-and-drop formats (v6/v7 plus) come first
        if ($hp && ($a[2] == 'jmatch' || $a[2] == 'jmix') && ($a[4] == 'v6' || $a[4] == 'v7')) {
            if ($a_count == 6 && $a[5] == 'plus') {
                return -1;
            }
            if ($b_count == 6 && $b[5] == 'plus') {
                return 1;
            }
        }

        // Otherwise, shorter formats come first.
        if ($a_count < $b_count) {
            return -1;
        }
        if ($a_count > $b_count) {
            return 1;
        }
        return 0;
    }

    /**
     * Returns the localized list of attempt limit settings for a HotPot instance
     *
     * @return array
     */
    static public function available_attemptlimits_list() {
        $options = array(
            0 => get_string('attemptsunlimited', 'mod_hotpot'),
        );
        for ($i=1; $i<=10; $i++) {
            $options[$i] = "$i";
        }
        return $options;
    }

    /**
     * Returns the localized list of grade method settings for a HotPot instance
     *
     * @return array
     */
    static public function available_grademethods_list() {
        return array (
            self::GRADEMETHOD_HIGHEST => get_string('highestscore', 'mod_hotpot'),
            self::GRADEMETHOD_AVERAGE => get_string('averagescore', 'mod_hotpot'),
            self::GRADEMETHOD_FIRST   => get_string('firstattempt', 'mod_hotpot'),
            self::GRADEMETHOD_LAST    => get_string('lastattempt', 'mod_hotpot'),
        );
    }

    /**
     * Returns the localized list of status settings for a HotPot attempt
     *
     * @return array
     */
    static public function available_statuses_list() {
        return array (
            self::STATUS_INPROGRESS => get_string('inprogress', 'mod_hotpot'),
            self::STATUS_TIMEDOUT   => get_string('timedout', 'mod_hotpot'),
            self::STATUS_ABANDONED  => get_string('abandoned', 'mod_hotpot'),
            self::STATUS_COMPLETED  => get_string('completed', 'mod_hotpot')
        );
    }

    /**
     * Returns the localized list of grade method settings for a HotPot instance
     *
     * @return array
     */
    static public function available_namesources_list() {
        return array (
            self::TEXTSOURCE_FILE     => get_string('textsourcefile', 'mod_hotpot'),
            self::TEXTSOURCE_FILENAME => get_string('textsourcefilename', 'mod_hotpot'),
            self::TEXTSOURCE_FILEPATH => get_string('textsourcefilepath', 'mod_hotpot'),
            self::TEXTSOURCE_SPECIFIC => get_string('textsourcespecific', 'mod_hotpot')
        );
    }

    /**
     * Returns the localized list of grade method settings for a HotPot instance
     *
     * @return array
     */
    static public function available_titles_list() {
        return array (
            self::TEXTSOURCE_SPECIFIC => get_string('hotpotname', 'mod_hotpot'),
            self::TEXTSOURCE_FILE     => get_string('textsourcefile', 'mod_hotpot'),
            self::TEXTSOURCE_FILENAME => get_string('textsourcefilename', 'mod_hotpot'),
            self::TEXTSOURCE_FILEPATH => get_string('textsourcefilepath', 'mod_hotpot')
        );
    }

    /**
     * Returns the localized list of maximum grade settings for a HotPot instance
     *
     * @return array
     */
    static public function available_gradeweightings_list() {
        $options = array();
        for ($i=100; $i>=1; $i--) {
            $options[$i] = $i;
        }
        $options[0] = get_string('nograde');
        return $options;
    }

    /**
     * Detects the type of the source file
     *
     * @param stored_file $sourcefile the file that has just been uploaded and stored
     * @return string the type of the source file (e.g. hp_6_jcloze_xml)
     */
    static public function get_sourcetype($sourcefile) {
        // include all the hotpot_source classes
        $classes = self::get_classes('hotpotsource');

        // loop through the classes checking to see if this file is recognized
        // use call_user_func() to prevent syntax error in PHP 5.2.x
        foreach ($classes as $class) {
            if (call_user_func(array($class, 'is_quizfile'), $sourcefile)) {
                return call_user_func(array($class, 'get_type'), $class);
            }
        }

        // file is not a recognized quiz type :-(
        return '';
    }

    /**
     * Returns a js module object for the HotPot module
     *
     * @param array $requires
     *    e.g. array('base', 'dom', 'event-delegate', 'event-key')
     * @return array $strings
     *    e.g. array(
     *        array('timesup', 'quiz'),
     *        array('functiondisabledbysecuremode', 'quiz'),
     *        array('flagged', 'question')
     *    )
     */
    static public function get_js_module(array $requires = null, array $strings = null) {
        return array(
            'name' => 'mod_hotpot',
            'fullpath' => '/mod/hotpot/module.js',
            'requires' => $requires,
            'strings' => $strings,
        );
    }

    /**
     * get_version_info
     *
     * @param xxx $info
     * @return xxx
     */
    static public function get_version_info($info)  {
        global $CFG;

        static $plugin = null;
        if (is_null($plugin)) {
            $plugin = new stdClass();
            require($CFG->dirroot.'/mod/hotpot/version.php');
        }

        if (isset($plugin->$info)) {
            return $plugin->$info;
        } else {
            return "no $info found";
        }
    }

   /**
    * load_mediafilter_filter
    *
    * @param xxx $classname
    */
   static public function load_mediafilter_filter($classname)  {
        global $CFG;
        $path = $CFG->dirroot.'/mod/hotpot/mediafilter/'.$classname.'/class.php';

        // check the filter exists
        if (! file_exists($path)) {
            debugging('hotpot mediafilter class is not accessible: '.$classname, DEBUG_DEVELOPER);
            return false;
        }

        return require_once($path);
    }

    /**
     * sourcefile_options
     *
     * @param xxx $context
     * @return xxx
     */
    static public function sourcefile_options() {
        return array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => -1);
    }

    /**
     * text_editors_options
     *
     * @param xxx $context
     * @return xxx
     */
    static public function text_editors_options($context)  {
        return array('subdirs' => 1, 'maxbytes' => 0, 'maxfiles' => EDITOR_UNLIMITED_FILES,
                     'changeformat' => 1, 'context' => $context, 'noclean' => 1, 'trusttext' => 0);
    }

    /**
     * text_page_types
     *
     * @return xxx
     */
    static public function text_page_types() {
        return array('entry', 'exit');
    }

    /**
     * text_page_options
     *
     * @param xxx $type
     * @return xxx
     */
    static public function text_page_options($type)  {
        if ($type=='entry') {
            return array(
                'title'         => self::ENTRYOPTIONS_TITLE,
                'grading'       => self::ENTRYOPTIONS_GRADING,
                'dates'         => self::ENTRYOPTIONS_DATES,
                'attempts'      => self::ENTRYOPTIONS_ATTEMPTS
            );
        }
        if ($type=='exit') {
            return array(
                'title'         => self::ENTRYOPTIONS_TITLE,
                'encouragement' => self::EXITOPTIONS_ENCOURAGEMENT,
                'attemptscore'  => self::EXITOPTIONS_ATTEMPTSCORE,
                'hotpotgrade'   => self::EXITOPTIONS_HOTPOTGRADE,
                'retry'         => self::EXITOPTIONS_RETRY,
                'index'         => self::EXITOPTIONS_INDEX,
                'course'        => self::EXITOPTIONS_COURSE,
                'grades'        => self::EXITOPTIONS_GRADES
            );
        }
        return array();
    }

    /**
     * reviewoptions_timesitems
     *
     * @return xxx
     */
    static public function reviewoptions_times_items() {
        return array(
            array( // times
                'duringattempt' => self::REVIEW_DURINGATTEMPT,
                'afterattempt'  => self::REVIEW_AFTERATTEMPT,
                'afterclose'    => self::REVIEW_AFTERCLOSE
            ),
            array( // items
                'responses'     => self::REVIEW_RESPONSES,
                'answers'       => self::REVIEW_ANSWERS,
                'scores'        => self::REVIEW_SCORES,
                'feedback'      => self::REVIEW_FEEDBACK
            )
        );
    }

    /**
     * user_preferences_fields
     *
     * @return array of user_preferences used by the HotPot module
     */
    static public function user_preferences_fieldnames() {
        return array(
            // fields used only when adding a new HotPot
            'namesource','entrytextsource','exittextsource','quizchain',

            // source/config files
            'sourcefile','sourcelocation','configfile','configlocation',

            // entry/exit pages
            'entrypage','entryformat','entryoptions',
            'exitpage','exitformat','exitoptions',
            'entrycm','entrygrade','exitcm','exitgrade',

            // display
            'outputformat','navigation','title','stopbutton','stoptext','allowpaste',
            'usefilters','useglossary','usemediafilter','studentfeedback','studentfeedbackurl',

            // access restrictions
            'timeopen','timeclose','timelimit','delay1','delay2','delay3',
            'password','subnet','reviewoptions','attemptlimit',

            // grading and reporting
            'grademethod','gradeweighting','clickreporting','discarddetails'
        );
    }

    /**
     * string_ids
     *
     * @param xxx $field_value
     * @return xxx
     */
    static public function string_ids($field_value, $max_field_length=255)  {
        $ids = array();

        $strings = explode(',', $field_value);
        foreach($strings as $str) {
            if ($id = self::string_id($str)) {
                $ids[] = $id;
            }
        }
        $ids = implode(',', $ids);

        // we have to make sure that the list of $ids is no longer
        // than the maximum allowable length for this field
        if (strlen($ids) > $max_field_length) {

            // truncate $ids just before last comma in allowable field length
            // Note: largest possible id is something like 9223372036854775808
            //       so we must leave space for that in the $ids string
            $ids = substr($ids, 0, $max_field_length - 20);
            $ids = substr($ids, 0, strrpos($ids, ','));

            // create single $str(ing) containing all $strings not included in $ids
            $str = implode(',', array_slice($strings, substr_count($ids, ',') + 1));

            // append the id of the string containing all the strings not yet in $ids
            if ($id = self::string_id($str)) {
                $ids .= ','.$id;
            }
        }

        // return comma separated list of string $ids
        return $ids;
    }

    /**
     * string_id
     *
     * @param xxx $str
     * @return xxx
     */
    static public function string_id($str)  {
        global $DB;

        if (! isset($str) || ! is_string($str) || trim($str)=='') {
            // invalid input string
            return false;
        }

        // create md5 key
        $md5key = md5($str);

        if ($id = $DB->get_field('hotpot_strings', 'id', array('md5key'=>$md5key))) {
            // string already exists
            return $id;
        }

        // create a new string record
        $record = (object)array('string'=>$str, 'md5key'=>$md5key);
        if (! $id = $DB->insert_record('hotpot_strings', $record)) {
            print_error('error_insertrecord', 'hotpot', '', 'hotpot_strings');
        }

        // new string was successfully added
        return $id;
    }

    /**
     * get_strings
     *
     * @param xxx $ids
     * @return xxx
     */
    static public function get_strings($ids)  {
        global $DB;

        // convert $ids to an array, if necessary
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_filter($ids);
        }

        // return strings, if any
        if (empty($ids)) {
            return array();
        } else {
            list($select, $params) = $DB->get_in_or_equal($ids);
            return $DB->get_records_select('hotpot_strings', "id $select", $params, '', 'id,string');
        }
    }

    /**
     * get_question_text
     *
     * @param xxx $question
     * @return xxx
     */
    static public function get_question_text($question)   {
        global $DB;

        if (empty($question->text)) {
            // JMatch, JMix and JQuiz
            return $question->name;
        } else {
            // JCloze and JCross
            return $DB->get_field('hotpot_strings', 'string', array('id' => $question->text));
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Hotpot API                                                                 //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * @return moodle_url of this hotpot's view page
     */
    public function view_url($cm=null) {
        if (is_null($cm)) {
            $cm = $this->cm;
        }
        return new moodle_url('/mod/'.$cm->modname.'/view.php', array('id' => $cm->id));
    }

    /**
     * @return moodle_url of this hotpot's view page
     */
    public function report_url($mode='', $cm=null) {
        if (is_null($cm)) {
            $cm = $this->cm;
        }
        $params = array('id' => $cm->id);
        if ($mode) {
            $params['mode'] = $mode;
        }
        return new moodle_url('/mod/hotpot/report.php', $params);
    }

    /**
     * @return moodle_url of this hotpot's attempt page
     */
    public function attempt_url($framename='', $cm=null) {
        if (is_null($cm)) {
            $cm = $this->cm;
        }
        $params = array('id' => $cm->id);
        if ($framename) {
            $params['framename'] = $framename;
        }
        return new moodle_url('/mod/hotpot/attempt.php', $params);
    }

    /**
     * @return moodle_url of this hotpot's attempt page
     */
    public function submit_url($attempt=null) {
        if (is_null($attempt)) {
            $attempt = $this->attempt;
        }
        return new moodle_url('/mod/hotpot/submit.php', array('id' => $attempt->id));
    }

    /**
     * @return moodle_url of the review page for an attempt at this hotpot
     */
    public function review_url($attempt=null) {
        if (is_null($attempt)) {
            $attempt = $this->attempt;
        }
        return new moodle_url('/mod/hotpot/review.php', array('id' => $attempt->id));
    }

    /**
     * @return moodle_url of this course's hotpot index page
     */
    public function index_url($course=null) {
        if (is_null($course)) {
            $course = $this->course;
        }
        return new moodle_url('/mod/hotpot/index.php', array('id' => $course->id));
    }

    /**
     * @return moodle_url of this hotpot's course page
     */
    public function course_url($course=null) {
        if (is_null($course)) {
            $course = $this->course;
        }
        $params = array('id' => $course->id);
        $sectionnum = 0;
        if (isset($course->coursedisplay) && defined('COURSE_DISPLAY_MULTIPAGE')) {
            // Moodle >= 2.3
            if ($course->coursedisplay==COURSE_DISPLAY_MULTIPAGE) {
                $courseid = $course->id;
                $sectionid = $this->cm->section;
                if ($modinfo = get_fast_modinfo($this->course)) {
                    $sections = $modinfo->get_section_info_all();
                    foreach ($sections as $section) {
                        if ($section->id==$sectionid) {
                            $sectionnum = $section->section;
                            break;
                        }
                    }
                }
                unset($modinfo, $sections, $section);
            }
        }
        if ($sectionnum) {
            $params['section'] = $sectionnum;
        }
        return new moodle_url('/course/view.php', $params);
    }

    /**
     * @return moodle_url of this hotpot's course grade page
     */
    public function grades_url($course=null) {
        if (is_null($course)) {
            $course = $this->course;
        }
        return new moodle_url('/grade/index.php', array('id' => $course->id));
    }

    /**
     * @return source object representing the source file for this HotPot
     */
    public function get_source() {
        global $CFG, $DB;
        if (empty($this->source)) {
            // get sourcetype e.g. hp_6_jcloze_xml
            $sourcefile = $this->get_sourcefile();
            if (! $sourcetype = clean_param($this->sourcetype, PARAM_SAFEDIR)) {
                if (! $sourcetype = hotpot::get_sourcetype($sourcefile)) {
                    return null;
                }
                $DB->set_field('hotpot', 'sourcetype', $sourcetype, array('id' => $this->id));
                $this->sourcetype = $sourcetype;
            }

            $dir = str_replace('_', '/', $sourcetype);
            require_once($CFG->dirroot.'/mod/hotpot/source/'.$dir.'/class.php');

            $classname = 'hotpot_source_'.$sourcetype;
            $this->source = new $classname($sourcefile, $this);
        }
        return $this->source;
    }

    /**
     * Returns the localized description of the grade method
     *
     * @return string
     */
    public function format_grademethod() {
        $options = $this->available_grademethods_list();
        if (array_key_exists($this->grademethod, $options)) {
            return $options[$this->grademethod];
        } else {
            return $this->grademethod; // shouldn't happen
        }
    }

    /**
     * Returns the localized description of the attempt status
     *
     * @return string
     */
    static public function format_status($status) {
        $options = self::available_statuses_list();
        if (array_key_exists($status, $options)) {
            return $options[$status];
        } else {
            return $status; // shouldn't happen
        }
    }

    /**
     * Returns a formatted version of the $time
     *
     * @param in $time the time to format
     * @param string $format time format string
     * @param string $notime return value if $time==0
     * @return string
     */
    static public function format_time($time, $format=null, $notime='&nbsp;') {
        if ($time>0) {
            return format_time($time, $format);
        } else {
            return $notime;
        }
    }

    /**
     * Returns a formatted version of an (attempt) $record's score
     *
     * @param object $record from the Moodle database
     * @param string $noscore return value if $record->score is not set
     * @return string
     */
    static function format_score($record, $default='&nbsp;') {
        if (isset($record->score)) {
            return $record->score;
        } else {
            return $default;
        }
    }

    /**
     * Returns the stored_file object for this HotPot's source file
     *
     * @return stored_file
     */
    public function get_sourcefile() {
        global $CFG, $DB;
        $fs = get_file_storage();

        $filename = basename($this->sourcefile);
        $filepath = dirname($this->sourcefile);

        // require leading and trailing slash on $filepath
        if (substr($filepath, 0, 1)=='/' && substr($filepath, -1)=='/') {
            // do nothing - $filepath is valid
        } else {
            // fix filepath - shouldn't happen !!
            // maybe leftover from a messy upgrade
            if ($filepath=='.' || $filepath=='') {
                $filepath = '/';
            } else {
                $filepath = '/'.ltrim($filepath, '/');
                $filepath = rtrim($filepath, '/').'/';
            }
            $this->sourcefile = $filepath.$filename;
            $DB->set_field('hotpot', 'sourcefile', $this->sourcefile, array('id' => $this->id));
        }

        if ($file = $fs->get_file($this->context->id, 'mod_hotpot', 'sourcefile', 0, $filepath, $filename)) {
            return $file;
        }

        // the source file is missing, probably this HotPot
        // has recently been upgraded/imported from Moodle 1.9
        // so we are going to try to create the missing stored file

        $file_record = array(
            'contextid'=>$this->context->id, 'component'=>'mod_hotpot', 'filearea'=>'sourcefile',
            'sortorder'=>1, 'itemid'=>0, 'filepath'=>$filepath, 'filename'=>$filename
        );

        $coursecontext  = hotpot_get_context(CONTEXT_COURSE, $this->course->id);
        $filehash = sha1('/'.$coursecontext->id.'/course/legacy/0'.$filepath.$filename);

        if ($file = $fs->get_file_by_hash($filehash)) {
            // file exists in legacy course files
            if ($file = $fs->create_file_from_storedfile($file_record, $file)) {
                return $file;
            }
        }

        $oldfilepath = $CFG->dataroot.'/'.$this->course->id.$filepath.$filename;
        if (file_exists($oldfilepath)) {
            // file exists on server's filesystem
            if ($file = $fs->create_file_from_pathname($file_record, $oldfilepath)) {
                return $file;
            }
        }

        // source file not found - shouldn't happen !!
        throw new moodle_exception('sourcefilenotfound', 'hotpot', '', $this->sourcefile);
    }

    /**
     * Returns the output format to be used for an attempt at this HotPot
     * If the outputformat is not given, the "best" outupt format is returned
     * which is the one with the same name as "sourcetype" for this HotPot
     *
     * @return string $subtype
     */
    public function get_outputformat() {
        if ($this->outputformat) {
            return clean_param($this->outputformat, PARAM_SAFEDIR);
        }
        if ($source = $this->get_source()) {
            return $source->get_best_outputformat();
        }
        return ''; // shouldn't happen !!
    }

    /**
     * can_attempt
     *
     * @return xxx
     */
    function can_attempt() {
        if (is_null($this->canattempt)) {
            $this->canattempt = has_capability('mod/hotpot:attempt', $this->context);
        }
        return $this->canattempt;
    }

    /**
     * can_deleteattempts
     *
     * @return xxx
     */
    function can_deleteattempts() {
        return $this->can_deletemyattempts() || $this->can_deleteallattempts();
    }

    /**
     * can_deleteallattempts
     *
     * @return xxx
     */
    function can_deleteallattempts() {
        if (is_null($this->candeleteallattempts)) {
            $this->candeleteallattempts = has_capability('mod/hotpot:deleteallattempts', $this->context);
        }
        return $this->candeleteallattempts;
    }

    /**
     * can_deletemyattempts
     *
     * @return xxx
     */
    function can_deletemyattempts() {
        if (is_null($this->candeletemyattempts)) {
            $this->candeletemyattempts = has_capability('mod/hotpot:deletemyattempts', $this->context);
        }
        return $this->candeletemyattempts;
    }

    /**
     * can_manage
     *
     * @return xxx
     */
    function can_manage() {
        if (is_null($this->canmanage)) {
            $this->canmanage = has_capability('mod/hotpot:manage', $this->context);
        }
        return $this->canmanage;
    }

    /**
     * can_preview
     *
     * @return xxx
     */
    function can_preview() {
        if (is_null($this->canpreview)) {
            $this->canpreview = has_capability('mod/hotpot:preview', $this->context);
        }
        return $this->canpreview;
    }

    /**
     * can_reviewallattempts
     *
     * @return xxx
     */
    function can_reviewattempts() {
        return $this->can_reviewmyattempts() || $this->can_reviewallattempts();
    }

    /**
     * can_reviewallattempts
     *
     * @return xxx
     */
    function can_reviewallattempts() {
        if (is_null($this->canreviewallattempts)) {
            $this->canreviewallattempts = has_capability('mod/hotpot:reviewallattempts', $this->context);
        }
        return $this->canreviewallattempts;
    }

    /**
     * can_reviewmyattempts
     *
     * @return xxx
     */
    function can_reviewmyattempts() {
        if (is_null($this->canreviewmyattempts)) {
            $this->canreviewmyattempts = has_capability('mod/hotpot:reviewmyattempts', $this->context);
        }
        return $this->canreviewmyattempts;
    }

    /**
     * can_reviewattempt
     *
     * @param object $attempt (optional, default=null) record from "hotpot_attempts" table
     * @return integer $reviewoptions currently available for this user at this attempt
     */
    function can_reviewhotpot() {
        if ($this->can_reviewallattempts()) {
            // teacher can view always review everything
            return (self::REVIEW_DURINGATTEMPT | self::REVIEW_AFTERATTEMPT | self::REVIEW_AFTERCLOSE);
        }
        if ($this->can_reviewmyattempts()) {
            if ($this->timeclose && $this->timeclose > $this->time) {
                // quiz is still open
                if ($reviewoptions = ($this->reviewoptions & self::REVIEW_DURINGATTEMPT)) {
                    return $reviewoptions;
                }
                if ($reviewoptions = ($this->reviewoptions & self::REVIEW_AFTERATTEMPT)) {
                    return $reviewoptions;
                }
            } else {
                // quiz is already closed
                if ($reviewoptions = $this->reviewoptions & self::REVIEW_AFTERCLOSE) {
                    return $reviewoptions;
                }
            }
        }
        return 0; // review not available (to this user)
    }

    /**
     * can_reviewattempt
     *
     * @param object $attempt (optional, default=null) record from "hotpot_attempts" table
     * @return integer $reviewoptions currently available for this user at this attempt
     */
    function can_reviewattempt($attempt=null) {
        if ($this->can_reviewattempts()) {
            if ($attempt===null && isset($this->attempt)) {
                $attempt = $this->attempt;
            }
            if ($attempt) {
                if ($reviewoptions = ($this->reviewoptions & self::REVIEW_DURINGATTEMPT)) {
                    // during attempt
                    if ($attempt->status==self::STATUS_INPROGRESS) {
                        return $reviewoptions;
                    }
                }
                if ($reviewoptions = ($this->reviewoptions & self::REVIEW_AFTERATTEMPT)) {
                    // after attempt (but before quiz closes)
                    if ($attempt->status==self::STATUS_COMPLETED) {
                        return $reviewoptions;
                    }
                    if ($attempt->status==self::STATUS_ABANDONED) {
                        return $reviewoptions;
                    }
                    if ($attempt->status==self::STATUS_TIMEDOUT) {
                        return $reviewoptions;
                    }
                    if ($attempt->status==self::STATUS_INPROGRESS) {
                        return $reviewoptions;
                    }
                }
                if ($reviewoptions = ($this->reviewoptions & self::REVIEW_AFTERCLOSE)) {
                    // after the quiz closes
                    if ($this->timeclose < $this->time) {
                        return $reviewoptions;
                    }
                }
            }
        }
        return 0;
    }


    /**
     * can_view
     *
     * @return xxx
     */
     function can_view() {
        if (is_null($this->canview)) {
            $this->canview = has_capability('mod/hotpot:view', $this->context);
        }
        return $this->canview;
     }

    /**
     * can_start
     *
     * @param xxx $canstart (optional, default=null)
     * @return xxx
     */
    function can_start($canstart=null)  {
        if (is_null($canstart)) {
            if (is_null($this->canstart)) {
                // set automatically
                if (has_capability('mod/hotpot:preview', $this->context)) {
                    $this->canstart = true;
                } else if (! $this->can_attempt()) {
                    $this->canstart = false;
                } else if ($this->require_isopen()) {
                    $this->canstart = false;
                } else if ($this->require_notclosed()) {
                    $this->canstart = false;
                } else if ($this->require_entrycm()) {
                    $this->canstart = false;
                } else if ($this->require_delay('delay1')) {
                    $this->canstart = false;
                } else if ($this->require_delay('delay2')) {
                    $this->canstart = false;
                } else if ($this->require_moreattempts(true)) {
                    $this->canstart = false;
                } else { // no errors so far
                    $this->canstart = true;
                }
            }
            // get
            return $this->canstart;
        } else {
            // set manually
            $this->canstart = $canstart;
        }
    }

    /**
     * Returns the subtype to be used to get a renderer for an attempt at this HotPot
     *
     * @return string $subtype
     */
    public function get_attempt_renderer_subtype() {
        if ($outputformat = $this->get_outputformat()) {
            return 'attempt_'.$outputformat;
        } else {
            return ''; // shouldn't happen !!
        }
    }

    /**
     * set_preferred_pagelayout
     *
     * @param xxx $PAGE
     */
    public function set_preferred_pagelayout($PAGE)  {
        // page layouts are defined in theme/xxx/config.php

        switch ($this->navigation) {

            case self::NAVIGATION_ORIGINAL:
            case self::NAVIGATION_NONE:
                // $PAGE->set_pagelayout('popup');
                $PAGE->set_pagelayout('embedded');
                break;

            case self::NAVIGATION_FRAME:
            case self::NAVIGATION_EMBED:
                $framename = optional_param('framename', '', PARAM_ALPHA);
                if ($framename=='top') {
                    $PAGE->set_pagelayout('frametop');
                }
                if ($framename=='main') {
                    $PAGE->set_pagelayout('embedded');
                }
                break;

            case self::NAVIGATION_TOPBAR:
                $PAGE->set_pagelayout('login'); // no nav menu
                break;
        }
    }

    /**
     * to_stdclass
     *
     * @return xxx
     */
    public function to_stdclass() {
        $stdclass = new stdclass();
        $vars = get_object_vars($this);
        foreach ($vars as $name => $value) {
            if (is_object($this->$name) || is_array($this->$name)) {
                continue;
            }
            $stdclass->$name = $value;
        }
        // extra fields required for grades
        if (isset($this->course) && is_object($this->course)) {
            $stdclass->course = $this->course->id;
        }
        if (isset($this->cm) && is_object($this->cm)) {
            $stdclass->cmidnumber = $this->cm->id;
        }
        $stdclass->modname = 'hotpot';
        return $stdclass;
    }

    /**
     * Returns the subtype to be used to get a report renderer for this HotPot
     *
     * @return string $mode
     * @return string $subtype
     */
    public function get_report_renderer_subtype($mode) {
        if ($mode=='') {
            $mode = 'overview';
        }
        return 'report_'.$mode;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // Internal methods (implementation details)                                  //
    ////////////////////////////////////////////////////////////////////////////////

    /**
     * This function will "include" all the files matching $classfilename for a given a plugin type
     * (e.g. hotpotsource), and return a list of classes that were included
     *
     * @param string $plugintype one of the plugintypes specified in mod/hotpot/db/subplugins.php
     */
    static public function get_classes($plugintype, $classfilename='class.php', $prefix='', $suffix='') {
        global $CFG;

        // initialize array to hold class names
        $classes = array();

        // get list of all subplugins
        $subplugins = array();
        include($CFG->dirroot.'/mod/hotpot/db/subplugins.php');

        // extract the $plugintype we are interested in
        $types = array();
        if (isset($subplugins[$plugintype])) {
            $types[$plugintype] = $subplugins[$plugintype];
        }
        unset($subplugins);

        // we are not interested in these directories
        $ignored = array('CVS', '_vti_cnf', 'simpletest', 'db', 'yui', 'phpunit');

        // get all the subplugins for this $plugintype
        reset($types);
        while ($type = key($types)) {
            $dir = current($types);
            $fulldir = $CFG->dirroot.'/'.$dir;
            if (is_dir($fulldir) && file_exists($fulldir.'/'.$classfilename)) {

                // include the class
                require_once($fulldir.'/'.$classfilename);

                // extract class name, e.g. hotpot_source_hp_6_jcloze_xml
                // from $subdir, e.g. mod/hotpot/file/h6/6/jcloze/xml
                // by removing leading "mod/" and converting all "/" to "_"
                $classes[] = $prefix.str_replace('/', '_', substr($dir, 4)).$suffix;

                // get subplugins in this $dir
                $items = new DirectoryIterator($fulldir);
                foreach ($items as $item) {
                    if (substr($item, 0, 1)=='.' || in_array($item, $ignored)) {
                        continue;
                    }
                    if ($item->isDir()) {
                        $types[$type.$item] = $dir.'/'.$item;
                    }
                }
            }
            next($types);
        }
        sort($classes);
        return $classes;
    }

    /**
     * get_report_modes
     *
     * @return xxx
     */
    function get_report_modes() {
        $modes = array('overview', 'scores', 'responses', 'analysis');
        if ($this->clickreporting) {
            $modes[] = 'clicktrail';
        }
        return $modes;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // methods to access database records connected to this HotPot                //
    ////////////////////////////////////////////////////////////////////////////////

    /*
     * create an db record for an attempt at this HotPot activity
     *
     * @return int the id of the newly created attempt record
     */
    function create_attempt() {
        global $DB, $USER;
        if (empty($this->attempt)) {

            // get max attempt number so far
            $sql = "SELECT MAX(attempt) FROM {hotpot_attempts} WHERE hotpotid = ? AND userid = ?";
            if ($max_attempt = $DB->get_field_sql($sql, array($this->id, $USER->id))) {
                $max_attempt ++;
            } else {
                $max_attempt = 1;
            }

            // create attempt record
            $this->attempt = new stdClass();
            $this->attempt->hotpotid       = $this->id;
            $this->attempt->userid         = $USER->id;
            $this->attempt->starttime      = 0;
            $this->attempt->endtime        = 0;
            $this->attempt->score          = 0;
            $this->attempt->penalties      = 0;
            $this->attempt->attempt        = $max_attempt;
            $this->attempt->timestart      = $this->time;
            $this->attempt->timefinish     = 0;
            $this->attempt->status         = self::STATUS_INPROGRESS;
            $this->attempt->clickreportid  = 0;
            $this->attempt->timemodified   = $this->time;

            // insert attempt record into database
            if (! $this->attempt->id = $DB->insert_record('hotpot_attempts', $this->attempt)) {
                throw new moodle_exception('error_insertrecord', 'hotpot', '', 'hotpot_attempts');
            }

            // set previous "in progress" attempt(s) to adandoned
            $select = 'hotpotid = ? AND userid = ? AND attempt < ? AND status = ?';
            $params = array($this->id, $USER->id, $max_attempt, self::STATUS_INPROGRESS);
            if ($attempts = $DB->get_records_select('hotpot_attempts', $select, $params)) {
                foreach ($attempts as $attempt) {
                    $attempt->timemodified = $this->time;
                    $attempt->status = self::STATUS_ABANDONED;
                    $DB->update_record('hotpot_attempts', $attempt);
                }
            }

        }
        return $this->attempt->id;
    }

    /**
     * count_distinct_clickreportids
     *
     * @return xxx
     */
    public function count_distinct_clickreportids() {
        $clickreportids = array();
        if ($this->get_attempts()) {
            foreach ($this->attempts as $attempt) {
                $clickreportids[$attempt->clickreportid] = true;
            }
        }
        return count($clickreportids);
    }

    /**
     * get_attempts
     *
     * @param array $selected ids of attempts to be deleted (optional, default=null)
     * @param int $userid (optional, default=0)
     * @return xxx
     */
    public function get_attempts($selected=null, $userid=0)  {
        global $DB, $USER;

        if (is_null($selected)) {
            if (is_null($this->attempts)) {
                $conditions = array('hotpotid' => $this->id, 'userid' => $USER->id);
                $this->attempts = $DB->get_records('hotpot_attempts', $conditions, 'timemodified DESC');
            }
        } else {
            $ids = array();
            foreach ($selected as $id => $flag) {
                if ($flag) {
                    $ids[] = $id;
                }
            }
            if (count($ids)) {
                list($filter, $params) = $DB->get_in_or_equal($ids);
                $select = "id $filter AND hotpotid = ?";
                $params[] = $this->id;
                if ($userid) {
                    $select .= " AND userid = ?";
                    $params[] = $userid;
                }
                $this->attempts = $DB->get_records_select('hotpot_attempts', $select, $params, 'timemodified DESC');
            }
        }

        if (empty($this->attempts)) {
            return 0;
        } else {
            return count($this->attempts);
        }
    }

    /**
     * delete_attempts
     *
     * @param xxx $selected
     */
    public function delete_attempts($selected, $onlymyattempts=true) {
        global $DB, $USER;

        if ($this->can_deleteallattempts()) {
            $userid = 0; // i.e. any user
        } else if ($this->can_deletemyattempts()) {
            $userid = $USER->id;
            $onlymyattempts = true;
        } else {
            return; // user is not allowed to delete attempts
        }

        if ($onlymyattempts) {
            // get attempts for this user only
            $this->get_attempts();
        } else {
            // get attempts for any users
            $this->get_attempts($selected, $userid);
        }

        if (empty($selected) || ! $this->attempts) {
            return; // nothing to do
        }

        $ids = array();
        $userids = array();
        foreach ($selected as $id => $delete) {
            if ($delete && array_key_exists($id, $this->attempts)) {
                $userid = $this->attempts[$id]->userid;
                if ($this->can_deleteallattempts() || ($this->can_deletemyattempts() && $userid==$USER->id)) {
                    $ids[] = $id;
                    $userids[$userid] = true;
                    unset($this->attempts[$id]);
                }
            }
        }

        if (count($ids)) {
            $DB->delete_records_list('hotpot_attempts',  'id',        $ids);
            $DB->delete_records_list('hotpot_details',   'attemptid', $ids);
            $DB->delete_records_list('hotpot_responses', 'attemptid', $ids);

            $userids = array_keys($userids);
            $stdclass = $this->to_stdclass();

            foreach ($userids as $userid) {
                hotpot_update_grades($stdclass, $userid);
            }
        }
    }

    /**
     * require_access
     *
     * @return xxx
     */
    function require_access()  {
        if (! $error = $this->require_subnet()) {
            if (! $error = $this->require_password()) {
                $error = false;
            }
        }
        return $error;
    }

    /**
     * require_subnet
     *
     * @return xxx
     */
    function require_subnet()  {
        if (! $this->subnet) {
            return false;
        }
        if (address_in_subnet(getremoteaddr(), $this->subnet)) {
            return false;
        }
        // user's IP address is missing or does not match required subnet mask
        return get_string('subnetwrong', 'quiz');
    }

    /**
     * require_password
     *
     * @return xxx
     */
    function require_password()  {
        global $SESSION;
        $error = '';

        // does this hotpot require a password?
        if ($this->password) {

            // has password not already been given?
            if (empty($SESSION->hotpot_passwordchecked[$this->id])) {

                // get password, if any, that was entered
                // (strcmp returns 0 if strings are identical)
                $password = optional_param('hotpotpassword', '', PARAM_RAW);
                if (strcmp($this->password, $password)) {

                    // password is missing or invalid

                    if ($this->entrypage) {
                        $url = $this->view_url();
                    } else {
                        $url = $this->attempt_url();
                    }
                    $error = get_string('requirepasswordmessage', 'quiz');
                    $error .= html_writer::start_tag('form', array('id'=>'hotpotpasswordform', 'method'=>'post', 'action'=>$url));
                    $error .= html_writer::start_tag('fieldset');
                    $error .= html_writer::tag('b', get_string('password')).' ';
                    $error .= html_writer::empty_tag('input', array('name'=>'hotpotpassword', 'type'=>'password', 'value'=>$password)).' ';
                    $error .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('ok')));
                    if ($password) {
                        // previously entered password was invalid
                        $error .= html_writer::empty_tag('br');
                        $error .= get_string('passworderror', 'quiz');
                    }
                    $error .= html_writer::end_tag('fieldset');
                    $error .= html_writer::end_tag('form')."\n";
                } else {
                    // newly entered password was correct
                    if (empty($SESSION->hotpot_passwordchecked)) {
                        $SESSION->hotpot_passwordchecked = array();
                    }
                    $SESSION->hotpot_passwordchecked[$this->id] = true;
                }
            }
        }
        if ($error) {
            return $error;
        } else {
            return false;
        }
    }

    /**
     * require_isopen
     *
     * @return xxx
     */
    public function require_isopen() {
        if ($this->timeopen && $this->timeopen > $this->time) {
            // unit/quiz is not yet open
            return get_string('notavailable', 'mod_hotpot', userdate($this->timeopen));
        }
        return false;
    }

    /**
     * require_notclosed
     *
     * @return xxx
     */
    public function require_notclosed() {
        if ($this->timeclose && $this->timeclose < $this->time) {
            // unit/quiz is already closed
            return get_string('closed', 'mod_hotpot', userdate($this->timeclose));
        }
        return false;
    }

    /**
     * require_entrycm
     *
     * @return xxx
     */
    public function require_entrycm() {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot.'/lib/gradelib.php');

        if (! $cm = $this->get_cm('entry')) {
            return false;
        }

        // set up url to view this activity
        $href = new moodle_url('/mod/'.$cm->modname.'/view.php', array('id' => $cm->id));

        if ($this->entrygrade) {
            if ($grades = grade_get_grades($cm->course, 'mod', $cm->modname, $cm->instance, $USER->id)) {
                $grade = 0;
                if (isset($grades->items[0]) && $grades->items[0]->grademax > 0) {
                    // this activity has a grade item
                    if (isset($grades->items[0]->grades[$USER->id])) {
                        $grade = $grades->items[0]->grades[$USER->id]->grade;
                    } else {
                        $grade = 0;
                    }
                    if ($grade < $this->entrygrade) {
                        // either this user has not attempted the entry activity
                        // or their grade so far on the entry activity is too low
                        $a = (object)array(
                            'usergrade' => intval($grade),
                            'entrygrade' => $this->entrygrade,
                            'entryactivity' => html_writer::tag('a', format_string(urldecode($cm->name)), array('href' => $href))
                        );
                        return get_string('entrygradewarning', 'mod_hotpot', $a);
                    }
                }
            }
        } else {
            // no grade, so test for "completion"
            switch ($cm->modname) {
                case 'resource':
                    $table = 'log';
                    $select = 'cmid = ? AND userid = ? AND action = ?';
                    $params = array($cm->id, $this->userid, 'view');
                    break;
                case 'lesson':
                    $table = 'lesson_grades';
                    $select = 'userid = ? AND lessonid = ? AND completed > ?';
                    $params = array($this->userid, $cm->instance, 0);
                    break;
                default:
                    $table = '';
                    $select = '';
                    $params = array();
            }
            if ($table && $select && ! $DB->record_exists_select($table, $select, $params)) {
                // user has not viewed or completed this activity yet
                $a = html_writer::tag('a', format_string(urldecode($cm->name)), array('href' => $href->out()));
                return get_string('entrycompletionwarning', 'mod_hotpot', $a);
            }
        }

        return false;
    }

    /**
     * require_exitgrade
     *
     * @return xxx
     */
    function require_exitgrade() {
        if ($this->exitcm==0 || $this->exitgrade==0 || empty($this->attempt)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * require_delay
     *
     * @param xxx $delay
     * @return xxx
     */
    public function require_delay($delay)  {
        if (! $this->$delay) {
            return false;
        }

        if (! $countattempts = $this->get_attempts()) {
            return false;
        }

        // check to see if we require a delay or not
        switch ($delay) {
            case 'delay1': $require_delay = ($countattempts==1); break;
            case 'delay2': $require_delay = ($countattempts>=2); break;
            default: $require_delay = false;
        }

        if (! $require_delay) {
            return false;
        }

        // get most recent attempt
        $lastattempt = reset($this->attempts);

        $nextattempttime = $lastattempt->timemodified + ($this->$delay);
        if ($this->time >= $nextattempttime) {
            // $delay has expired, so let them through
            return false;
        } else {
            // $delay has not expired yet
            $time = html_writer::tag('strong', userdate($nextattempttime));
            return get_string('temporaryblocked', 'quiz').' '.$time;
        }

    }

    /**
     * require_moreattempts
     *
     * @param xxx $shorterror (optional, default=false)
     * @return xxx
     */
    public function require_moreattempts($shorterror=false)  {
        if (! $this->attemptlimit) {
            return false;
        }

        if (! $countattempts = $this->get_attempts()) {
            return false;
        }

        if ($this->attemptlimit > $countattempts) {
            return false;
        }

        // maximum number of unit/quiz attempts reached
        if ($shorterror) {
            return get_string('nomoreattempts', 'mod_hotpot');
        } else {
            $attemptlimitstr = hotpot_textlib('moodle_strtolower', get_string('attemptlimit', 'mod_hotpot'));
            $msg = html_writer::tag('b', format_string($this->name))." ($attemptlimitstr = $this->attemptlimit)";
            return html_writer::tag('p', get_string('nomoreattempts', 'mod_hotpot')).html_writer::tag('p', $msg);
        }
    }

    /**
     * get_cm
     *
     * @param xxx $type
     * @return xxx
     */
    public function get_cm($type)  {
        // gets the next, previous or specific Moodle activity

        // get entry/exit cm id
        $cm_field = $type.'cm';
        $cmid = $this->$cm_field;

        if ($cmid==self::ACTIVITY_NONE) {
            return false;
        }

        if (! $modinfo = get_fast_modinfo($this->course)) {
            return false; // no modinfo - shouldn't happen !!
        }

        if (method_exists($modinfo, 'get_cm')) {
            if (! $modinfo->get_cm($this->cm->id)) {
                return false; // target cm not found - shouldn't happen !!
            }
        } else {
            if (! isset($modinfo->cms[$this->cm->id])) {
                return false; // target cm not found - shouldn't happen !!
            }
        }

        // set default search values
        $id = 0;
        $modname = '';
        $sectionnum = -1;

        // restrict search values
        if ($cmid>0) {
            $id = $cmid;
        } else {
            if ($cmid==self::ACTIVITY_COURSE_HOTPOT || $cmid==self::ACTIVITY_SECTION_HOTPOT) {
                $modname = 'hotpot';
            }
            if ($cmid==self::ACTIVITY_SECTION_ANY || $cmid==self::ACTIVITY_SECTION_HOTPOT) {
                $sectionnum = $modinfo->get_cm($this->cm->id)->sectionnum;
            }
        }

        // get cm ids (reverse order if necessary)
        $cmids = array_keys($modinfo->cms);
        if ($type=='entry') {
            $cmids = array_reverse($cmids);
        }

        // search for next, previous or specific course module
        $found = false;
        foreach ($cmids as $cmid) {
            if (method_exists($modinfo, 'get_cm')) {
                $cm = $modinfo->get_cm($cmid);
            } else {
                $cm = $modinfo->cms[$cmid];
            }
            if ($id && $cm->id!=$id) {
                continue; // wrong activity
            }
            if ($sectionnum>=0) {
                if ($type=='entry') {
                    if ($cm->sectionnum>$sectionnum) {
                        continue; // later section
                    }
                    if ($cm->sectionnum<$sectionnum) {
                        return false; // previous section
                    }
                } else { // exit (=next)
                    if ($cm->sectionnum<$sectionnum) {
                        continue; // earlier section
                    }
                    if ($cm->sectionnum>$sectionnum) {
                        return false; // later section
                    }
                }
            }
            if ($modname && $cm->modname!=$modname) {
                continue; // wrong module
            }
            if ($cm->modname=='label') {
                continue; // skip labels
            }
            if ($found || $cm->id==$id) {
                if (class_exists('\core_availability\info_module')) {
                    // Moodle >= 2.7
                    $is_visible = \core_availability\info_module::is_user_visible($cm);
                } else {
                    // Moodle <= 2.6
                    $is_visible = coursemodule_visible_for_user($cm);
                }
                if ($is_visible) {
                    return $cm;
                }
                if ($cm->id==$id) {
                    // required cm is not visible to this user
                    return false;
                }
            }
            if ($cm->id==$this->cm->id) {
                $found = true;
            }
        }

        // next cm not found
        return false;
    }

    /**
     * get_attempt
     *
     * @return xxx
     */
    public function get_attempt() {
        return $this->attempt;
    }

    /**
     * get_gradeitem
     *
     * @return xxx
     */
    public function get_gradeitem() {
        global $CFG, $USER;
        require_once($CFG->dirroot.'/lib/gradelib.php');

        if (is_null($this->gradeitem)) {
            $this->gradeitem = false;
            if ($grades = grade_get_grades($this->course->id, 'mod', 'hotpot', $this->id, $USER->id)) {
                if (isset($grades->items[0]) && $grades->items[0]->grademax > 0) {
                    // this activity has a grade item
                    if (isset($grades->items[0]->grades[$USER->id])) {
                        $this->gradeitem = $grades->items[0]->grades[$USER->id];
                        // grade->grade is the adjusted grade, for a true percent
                        // we need to shift and scale according to grademin and grademax
                        $percent = $this->gradeitem->grade;
                        if ($grades->items[0]->grademax > 0) {
                            $percent = (($percent - $grades->items[0]->grademin) / $grades->items[0]->grademax);
                        }
                        $this->gradeitem->percent = round($percent * 100);
                    }
                }
            }
        }
        return $this->gradeitem;
    }

    /**
     * update_completion_state
     *
     * @param object $completion
     * @return void, but may updated completion status
     */
    public function update_completion_state($completion) {
        if ($this->completionmingrade > 0.0 || $this->completionpass || $this->completioncompleted) {
            if ($completion->is_enabled($this->cm) && ($this->cm->completion==COMPLETION_TRACKING_AUTOMATIC)) {
                $completion->update_state($this->cm);
            }
        }
    }
}
