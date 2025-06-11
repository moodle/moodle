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
 * Renderer functions shared between multiple renderers.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

defined('MOODLE_INTERNAL') || die();

use context_course;
// BEGIN LSU Enhancement add Kaltura my media.
use context_system;
// END LSU Enhancement add Kaltura my media.
use core_component;
use html_writer;
use moodle_url;
use stdClass;
use theme_snap\local;
use theme_snap\renderables\login_alternative_methods;
use single_button;

require_once($CFG->dirroot.'/grade/querylib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->libdir.'/badgeslib.php');
require_once($CFG->dirroot.'/repository/lib.php');

class shared extends \renderer_base {

    /**
     * Taken from /format/renderer.php
     * Generate a summary of the activites in a section
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return stdClass
     */
    public static function section_activity_summary($section, $course, $mods) {
        global $CFG;

        require_once($CFG->libdir.'/completionlib.php');

        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return '';
        }

        // Generate array with count of activities in this section.
        $sectionmods = array();
        $total = 0;
        $complete = 0;
        $cancomplete = isloggedin() && !isguestuser();
        $completioninfo = new \completion_info($course);
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];
            if (isset($sectionmods[$thismod->modname])) {
                $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                $sectionmods[$thismod->modname]['count']++;
            } else {
                $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                $sectionmods[$thismod->modname]['count'] = 1;
            }
            if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE &&
                !$thismod->deletioninprogress) {
                $total++;
                $completiondata = $completioninfo->get_data($thismod, true);
                if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                    $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                    $complete++;
                }
            }
        }

        if (empty($sectionmods)) {
            // No sections.
            return '';
        }

        // Output section activities summary.
        $o = '';
        $o .= "<div class='section-summary-activities mdl-right'>";
        foreach ($sectionmods as $mod) {
            $o .= "<span class='activity-count'>";
            $o .= $mod['name'].': '.$mod['count'];
            $o .= "</span>";
        }
        $o .= "</div>";

        $a = false;

        // Output section completion data.
        if ($total > 0) {
            $a = new stdClass;
            $a->complete = $complete;
            $a->total = $total;
            $a->percentage = ($complete / $total) * 100;

            $o .= "<div class='section-summary-activities mdl-right'>";
            $o .= "<span class='activity-count'>".get_string('progresstotal', 'completion', $a)."</span>";
            $o .= "</div>";
        }

        $retobj = (object) array (
            'output' => $o,
            'progress' => $a,
            'complete' => $complete,
            'total' => $total
        );

        return $retobj;
    }

    /**
     * Add the Javascript to enable drag and drop upload to a course page
     *
     * @param object $course The currently displayed course
     * @param array $modnames The list of enabled (visible) modules on this site
     * @return void
     */
    protected static function dndupload_add_to_course($course, $modnames) {
        global $CFG, $PAGE;

        $showstatus = optional_param('notifyeditingon', false, PARAM_BOOL);

        // Get all handlers.
        $handler = new \dndupload_handler($course, $modnames);
        $jsdata = $handler->get_js_data();
        if (empty($jsdata->types) && empty($jsdata->filehandlers)) {
            return; // No valid handlers - don't enable drag and drop.
        }

        // Adding file handlers straight to footer, explanation below.
        $json = json_encode($handler->get_js_data());
        $script = <<<EOF
            <script>
                var themeSnapCourseFileHandlers = $json;
            </script>
EOF;

        if (!isset($CFG->additionalhtmlfooter)) {
            $CFG->additionalhtmlfooter = '';
        }
        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
        if (has_capability('moodle/course:ignorefilesizelimits', $PAGE->context)) {
            $maxbytes = 0;
        }
        // Note, we have to put the file handlers into the footer instead of passing them into the amd module as an
        // argument. If you pass large amounts of data into the amd arguments then it throws a debug error.
        $CFG->additionalhtmlfooter .= $script;

        // Add the javascript to the page.
        $PAGE->requires->strings_for_js([
            'addfilehere',
            'dndworkingfiletextlink',
            'dndworkingfilelink',
            'dndworkingfiletext',
            'dndworkingfile',
            'dndworkingtextlink',
            'dndworkingtext',
            'dndworkinglink',
            'namedfiletoolarge',
            'actionchoice',
            'servererror',
            'upload',
            'cancel'
        ], 'moodle');
        $PAGE->requires->strings_for_js([
            'modulename'
        ], 'mod_label');
        $vars = array(
            array('courseid' => $course->id,
                'maxbytes' => $maxbytes,
                'showstatus' => $showstatus)
        );

        $PAGE->requires->js('/course/dndupload.js');
        $PAGE->requires->js_call_amd('theme_snap/dndupload-lazy', 'init', $vars);
    }


    /**
     * Include the relevant javascript and language strings for the resource
     * toolbox YUI module
     *
     * @param integer $id The ID of the course being applied to
     * @param array $usedmodules An array containing the names of the modules in use on the page
     * @param array $enabledmodules An array containing the names of the enabled (visible) modules on this site
     * @param stdClass $config An object containing configuration parameters for ajax modules including:
     *          * resourceurl   The URL to post changes to for resource changes
     *          * sectionurl    The URL to post changes to for section changes
     *          * pageparams    Additional parameters to pass through in the post
     * @return bool
     */
    protected static function include_course_ajax($course, $usedmodules = array(), $enabledmodules = null, $config = null) {
        global $CFG, $PAGE, $COURSE;

        // Only include course AJAX for supported formats.
        if (!course_ajax_enabled($course)) {
            return false;
        }

        // Require various strings for the command toolbox.
        $PAGE->requires->strings_for_js([
            'afterresource',
            'aftersection',
            'confirmdeletesection',
            'deletechecktype',
            'deletechecktypename',
            'edittitle',
            'edittitleinstructions',
            'emptydragdropregion',
            'groupsnone',
            'groupsvisible',
            'groupsseparate',
            //'markthistopic',TODO: Review how to address this string deprecation.
            //'markedthistopic', TODO: Review how to address this string deprecation.
            'moveleft',
            'movesection',
            'movecoursemodule',
            'movecoursesection',
            'movecontent',
            'tocontent',
            'totopofsection',
            'unknownerror',
            'ok',
            'cancel',
            'highlight'
        ], 'moodle');

        $PAGE->requires->strings_for_js([
            'action:changeassetvisibility',
            'action:changesectionvisibility',
            'action:duplicateasset',
            'action:highlightsectionvisibility',
            'action:sectiontoc',
            'error:failedtochangesectionvisibility',
            'error:failedtohighlightsection',
            'error:failedtochangeassetvisibility',
            'error:failedtoduplicateasset',
            'error:failedtotoc',
            'deleteassetconfirm',
            'deletesectionconfirm',
            'deletingsection'
        ], 'theme_snap');

        // Include section-specific strings for formats which support sections.
        if (course_format_uses_sections($course->format)) {
            $PAGE->requires->strings_for_js(array(
                'showfromothers',
                'hidefromothers',
            ), 'format_' . $course->format);
        }

        // For confirming resource deletion we need the name of the module in question.
        foreach ($usedmodules as $module => $modname) {
            $PAGE->requires->string_for_js('pluginname', $module);
        }

        if ($COURSE->id !== SITEID) {
            // Load drag and drop upload AJAX.
            require_once($CFG->dirroot . '/course/dnduploadlib.php');
            self::dndupload_add_to_course($course, $enabledmodules);
        }

        return true;
    }

    /**
     * Javascript required by both standard header layout and flexpage layout
     *
     * @return void
     */
    public static function page_requires_js() {
        global $CFG, $PAGE, $COURSE, $USER, $OUTPUT;

        $PAGE->requires->jquery();
        $PAGE->requires->js_amd_inline("require(['theme_boost/loader']);");
        $PAGE->requires->strings_for_js(array(
            'coursecontacts',
            'debugerrors',
            'problemsfound',
            'error:coverimageexceedsmaxbytes',
            'error:coverimageresolutionlow',
            'forumtopic',
            'forumauthor',
            'forumpicturegroup',
            'forumreplies',
            'forumlastpost',
            'loading',
            'more',
            'moving',
            'movingcount',
            'movehere',
            'movefailed',
            'movingdropsectionhelp',
            'movingstartedhelp',
            'notpublished',
            'visibility',
            'snapfeedsblocktitle',
            'imageproperties',
            'coverimagedesc',
            'coverimagecropperdesc',
            'browserepositories',
            'selectimage',
            'deleteimage',
            'confirmdeletefile',
            'coverimagesettingswarning',
        ), 'theme_snap');

        $PAGE->requires->strings_for_js([
            'ok',
            'cancel',
            'error',
            'unknownerror',
            'closebuttontitle',
            'modhide',
            'modshow',
            'hiddenoncoursepage',
            'showoncoursepage',
            'switchrolereturn',
            'show',
            'hide',
            'groupsseparate',
            'groupsvisible',
            'groupsnone'
        ], 'moodle');

        $PAGE->requires->strings_for_js([
            'printbook'
        ], 'booktool_print');

        $PAGE->requires->strings_for_js([
            'progresstotal'
        ], 'completion');

        // Are we viewing /course/view.php or /course/section.php - note, this is different from just checking the page type.
        // We only ever want to load course.js when on site page or view.php - no point in loading it when on
        // course settings page, etc.
        $courseviewpage = in_array(local::current_url_path(), ['/course/view.php', '/course/section.php']);
        $pagehascoursecontent = ($PAGE->pagetype === 'site-index' || $courseviewpage);

        $cancomplete = isloggedin() && !isguestuser();
        $unavailablesections = [];
        $unavailablemods = [];
        if ($cancomplete) {
            $completioninfo = new \completion_info($COURSE);
            if ($completioninfo->is_enabled()) {
                $modinfo = get_fast_modinfo($COURSE);
                $sections = $modinfo->get_section_info_all();
                foreach ($sections as $number => $section) {
                    if ($PAGE->url->get_path() === '/course/section.php' && optional_param('id', -1, PARAM_INT) == $section->id) {
                        $sectionnum = $section->sectionnum;
                    }
                    $ci = new \core_availability\info_section($section);
                    $information = '';
                    if (!$ci->is_available($information, true)) {
                        $unavailablesections[] = $number;
                    }
                }
                foreach ($modinfo as $mod) {
                    $ci = new \core_availability\info_module($mod);
                    if (!$ci->is_available($information, true)) {
                        $unavailablemods[] = $mod->id;
                    }
                }

            }
        }

        list ($unavailablesections, $unavailablemods) = local::conditionally_unavailable_elements($COURSE);

        $coursevars = (object) [
            'id' => $COURSE->id,
            'shortname' => $COURSE->shortname,
            'contextid' => $PAGE->context->id,
            'categoryid' => !empty($PAGE->category->id) ? $PAGE->category->id : false,
            'ajaxurl' => '/course/rest.php',
            'unavailablesections' => $unavailablesections,
            'unavailablemods' => $unavailablemods,
            'enablecompletion' => isloggedin() && $COURSE->enablecompletion,
            'format' => $COURSE->format,
            'partialrender' => !empty(get_config('theme_snap', 'coursepartialrender')),
            'toctype' => get_config('theme_snap', 'leftnav'),
        ];

        if (!empty($sectionnum)) {
            $coursevars->sectionnum = $sectionnum;
        }

        $forcepwdchange = (bool) get_user_preferences('auth_forcepasswordchange', false);
        $conversationbadgecountenabled = isloggedin() && $PAGE->theme->settings->messagestoggle == 1;

        $userid = $USER->id;
        $inalternativerole = $OUTPUT->in_alternative_role();
        // Bring pre contents scss branding variables, to pass them to Snap init.
        $pre = file_get_contents($CFG->dirroot . '/theme/snap/scss/pre.scss');
        $lines = preg_split("/\r\n|\n|\r/", $pre);
        $brandcolors = [];
        foreach ($lines as $line) {
            if (strpos($line, '$brand-primary:') === 0) {
                $branding = [];
                preg_match("/#.*;\$/", $line, $branding);
                $brandcolors['primary'] = $branding[0];
                continue;
            }
            if (strpos($line, '$brand-success:') === 0) {
                $branding = [];
                preg_match("/#.*;\$/", $line, $branding);
                $brandcolors['success'] = $branding[0];
                continue;
            }
            if (strpos($line, '$brand-warning:') === 0) {
                $branding = [];
                preg_match("/#.*;\$/", $line, $branding);
                $brandcolors['warning'] = $branding[0];
                continue;
            }
            if (strpos($line, '$brand-danger:') === 0) {
                $branding = [];
                preg_match("/#.*;\$/", $line, $branding);
                $brandcolors['danger'] = $branding[0];
                continue;
            }
            if (strpos($line, '$brand-info:') === 0) {
                $branding = [];
                preg_match("/#.*;\$/", $line, $branding);
                $brandcolors['info'] = $branding[0];
                continue;
            }

            $brandprimary = array_key_exists('primary', $brandcolors);
            $brandsuccess = array_key_exists('success', $brandcolors);
            $brandwarning = array_key_exists('warning', $brandcolors);
            $branddanger = array_key_exists('danger', $brandcolors);
            $brandinfo = array_key_exists('info', $brandcolors);

            if ($brandprimary && $brandsuccess && $brandwarning && $branddanger && $brandinfo) {
                break;
            }
        }
        // Bring grading settings constants with percentage, to pass them to Snap init.
        $gradingconstants = [];
        $gradingconstants['gradepercentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
        $gradingconstants['gradepercentagereal'] = GRADE_DISPLAY_TYPE_PERCENTAGE_REAL;
        $gradingconstants['gradepercentageletter'] = GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER;
        $gradingconstants['gradereal'] = GRADE_DISPLAY_TYPE_REAL;
        $gradingconstants['graderealpercentage'] = GRADE_DISPLAY_TYPE_REAL_PERCENTAGE;
        $gradingconstants['graderealletter'] = GRADE_DISPLAY_TYPE_REAL_LETTER;
        $localplugins = core_component::get_plugin_list('local');
        // Check if the plugins are installed to pass them as parameters to accessibility.js AMD module.
        $localjoulegrader = array_key_exists('joulegrader', $localplugins);
        $blockreports = array_key_exists('reports', core_component::get_plugin_list('block'));
        $allyreport = (\core_component::get_component_directory('report_allylti') !== null);
        $localcatalogue = array_key_exists('catalogue', $localplugins);

        // Loading Filepicker for the "Change cover image" feature.
        if (($PAGE->pagelayout == 'coursecategory' || $PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'frontpage') &&
            (has_capability('moodle/course:changesummary', context_course::instance($COURSE->id)) ||
            has_capability('moodle/category:manage', context_course::instance($COURSE->id)))) {
            $args = new stdClass();
            $args->accepted_types = array('.jpeg', '.png', '.gif');
            $args->return_types = 2;
            initialise_filepicker($args);
        }

        $initvars = [$coursevars, $pagehascoursecontent, get_max_upload_file_size($CFG->maxbytes), $forcepwdchange,
                     $conversationbadgecountenabled, $userid, $inalternativerole, $brandcolors,
                     $gradingconstants];
        $initaxvars = [$localjoulegrader, $allyreport, $blockreports, $localcatalogue];
        $alternativelogins = new login_alternative_methods();
        if ($alternativelogins->potentialidps) {
            $loginvars = [get_config('theme_snap', 'enabledlogin'), get_config('theme_snap', 'enabledloginorder')];
        } else {
            $enabledlogin = \theme_snap\output\core_renderer::ENABLED_LOGIN_MOODLE;
            $loginvars = [$enabledlogin, null];
        }
        $PAGE->requires->js_call_amd('theme_snap/snap', 'snapInit', $initvars);
        if (!empty($CFG->calendar_adminseesall) && is_siteadmin()) {
            $PAGE->requires->js_call_amd('theme_snap/adminevents', 'init');
        }
        $PAGE->requires->js_call_amd('theme_snap/login_render-lazy', 'loginRender', $loginvars);
        // Does the page have editable course content?
        if ($pagehascoursecontent && $PAGE->user_allowed_editing()) {
            $canmanage = has_capability('moodle/course:manageactivities', context_course::instance($COURSE->id))
                || has_capability('moodle/course:update', context_course::instance($COURSE->id));
            if ($canmanage && $COURSE->format !== 'tiles' ||
                ($canmanage && !empty($USER->editing) && $COURSE->format == 'tiles')) {
                $modinfo = get_fast_modinfo($COURSE);
                $modnamesused = $modinfo->get_used_module_names();

                // Temporarily change edit mode to on for course ajax to be included.
                $originaleditstate = !empty($USER->editing) ? $USER->editing : false;
                $USER->editing = true;
                self::include_course_ajax($COURSE, $modnamesused);
                $USER->editing = $originaleditstate;
            }
        }
    }

    /**
     * Is the gradebook accessible - i.e. are there any reports accessible to this user
     * @return bool
     */
    public static function gradebook_accessible($context) {
        global $COURSE;

        // Ask if user has not capabilities and if course is set to not to show the grades to students.
        if ((!has_capability('gradereport/grader:view', $context)) && ($COURSE->showgrades == 0)) {
            return false;
        }

        // Find all enabled reports.
        $reports = core_component::get_plugin_list('gradereport');
        foreach (array_keys($reports) as $report) {
            if (!component_callback('gradereport_'.$report, 'is_enabled', array(), true)) {
                unset($reports[$report]);
            }
        }

        // Reduce reports list down to just those accessible to user.
        foreach ($reports as $plugin => $plugindir) {
            // Remove ones we can't see.
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                unset($reports[$plugin]);
            }
        }
        return !empty($reports);
    }

    /**
     * generates a string list of links based on links array
     * structure of links array should be
     * array(
     *      array(
     *          'link'=>[url in a string]
     *          'title'=>[mandatory - anyold string title]
     *      )
     * )
     * note - couldn't use html_writer::alist function as it does not support sub lists
     *
     * @author Guy Thomas
     * @param array $links
     * @return string;
     */
    public static function render_appendices(array $links) {
        global $CFG, $COURSE;

        $o = '';
        foreach ($links as $item) {
            $item = (object) $item;
            // Make sure item link is the correct type of url.
            if (stripos($item->link, 'http') !== 0) {
                $item->link = $CFG->wwwroot.'/'.$item->link;
            }
            // Generate linkhtml.
            $attributes = $item->attributes ?? null;
            $o .= '<li>';
            $o .= html_writer::link($item->link, $item->title, $attributes);
            $o .= '</li>';
        }
        return $o;
    }

    /**
     * generate list of course tools
     *
     * @author Guy Thomas
     * @date 2014-04-23
     * @return string
     */
    public static function appendices() {
        global $CFG, $COURSE, $OUTPUT, $DB;

        $links = [];
        $localplugins = core_component::get_plugin_list('local');
        $coursecontext = context_course::instance($COURSE->id);

        // Course enrolment link.
        /** @var \enrol_plugin[] $plugins */
        $plugins   = enrol_get_plugins(true);
        $instances = enrol_get_instances($COURSE->id, true);
        $selfenrol = false;
        // These plugins may allow self (un)enroll links to be shown.
        $allowedenrollplugins = [];
        $allowedenrollplugins['self'] = true;
        $allowedenrollplugins['manual'] = true;
        foreach ($instances as $instance) { // Need to check enrolment methods for self enrol.
            if (isset($allowedenrollplugins[$instance->enrol])) { // Will show links for methods which allow it.
                $plugin = $plugins[$instance->enrol];
                if (is_enrolled($coursecontext)) {
                    // Prepare unenrolment link.
                    $enrolurl = $plugin->get_unenrolself_link($instance);
                    if ($enrolurl) {
                        $selfenrol = true;
                        $iconurl = $OUTPUT->image_url('i/unenrolme', 'theme_snap');
                        $enrolicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
                        $enrolstr = $enrolicon . get_string('unenrolme', 'theme_snap');
                        break;
                    }
                } else {
                    if ($plugin->show_enrolme_link($instance)) {
                        // Prepare enrolment link.
                        $selfenrol = true;
                        $enrolurl = new moodle_url('/enrol/index.php', ['id' => $COURSE->id]);
                        $iconurl = $OUTPUT->image_url('i/enrolme', 'theme_snap');
                        $enrolicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
                        $enrolstr = $enrolicon . get_string('enrolme', 'theme_snap');
                        break;
                    }
                }
            }
        }

        // Course settings.
        if (has_capability('moodle/course:update', $coursecontext)) {
            $iconurl = $OUTPUT->image_url('gear', 'theme');
            $coverimageurl = local::course_coverimage_url($COURSE->id);
            if (!empty($coverimageurl)) {
                $iconurl = $coverimageurl;
            }
            $settingsicon = '<img src="'.$iconurl.'" class="snap-cover-icon svg-icon" alt="" role="presentation">';

            $links[] = array(
                'link' => 'course/edit.php?id='.$COURSE->id,
                'title' => $settingsicon.get_string('editcoursesettings', 'theme_snap'),
            );
        }

        $iconurl = $OUTPUT->image_url('joule_grader', 'theme');
        $gradebookicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';

        // Joule grader if installed.
        if (array_key_exists('joulegrader', $localplugins)) {
            if (has_capability('local/joulegrader:grade', $coursecontext)
                || has_capability('local/joulegrader:view', $coursecontext)
            ) {
                $links[] = array(
                    'link' => 'local/joulegrader/view.php?courseid='.$COURSE->id,
                    'title' => $gradebookicon.'Open Grader',
                );
            }
        }

        // Gradebook.
        if (self::gradebook_accessible($coursecontext)) {
            $iconurl = $OUTPUT->image_url('gradebook', 'theme');
            $gradebookicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            // Gradebook.
            $links[] = array(
                'link' => 'grade/index.php?id='.$COURSE->id,
                'title' => $gradebookicon.get_string('gradebook', 'grades')
            );
        }

        // Participants.
        if (course_can_view_participants($coursecontext)) {

            // Get count of course users.
            $usercount = \theme_snap\local::count_enrolled_users($coursecontext, '', 0, true);

            // Build icon.
            $participanticons = '';
            if (!empty($usercount)) {
                // Get subset of users for icon.
                $usersubset = get_enrolled_users($coursecontext,
                        '', 0, 'u.*', 'picture desc, lastaccess desc', 0, 4, true);
                foreach ($usersubset as $user) {
                    $userpicture = new \user_picture($user);
                    $userpicture->link = false;
                    $userpicture->size = 100;
                    $participanticons .= $OUTPUT->render($userpicture);
                }
            } else {
                // Default icon when 0 participants.
                $iconurl = $OUTPUT->image_url('u/f1');
                $participanticons = '<img src="'.$iconurl.'" alt="" role="presentation">';
            }

            $participanticons = '<div class="snap-participant-icons">'.$participanticons.'</div>';
            $links[] = array(
                'link' => 'user/index.php?id='.$COURSE->id.'&mode=1',
                'title' => $participanticons.$usercount.' '.get_string('participants')
            );
        }

        // Joule reports if installed.
        if (array_key_exists('reports', core_component::get_plugin_list('block'))) {
            $iconurl = $OUTPUT->image_url('joule_reports', 'theme');
            $reportsicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            if (has_capability('block/reports:viewown', $coursecontext, null, false)
                || has_capability('block/reports:view', $coursecontext)
            ) {
                $links[] = array(
                    'link' => $CFG->wwwroot.'/blocks/reports/view.php?action=dashboard&courseid='.$COURSE->id,
                    'title' => $reportsicon.'Open Reports'
                );
            }
        }

        // New Open reports if installed and visible.
        if (array_key_exists('reports', core_component::get_plugin_list('block'))
                && !empty($CFG->block_reports_enable_dashboardce)) {
            $iconurl = $OUTPUT->image_url('open_reports_ce', 'theme');
            $reportsicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            if (has_capability('block/reports:viewown', $coursecontext, null, false)
                || has_capability('block/reports:view', $coursecontext)
            ) {
                $links[] = array(
                    'link' => $CFG->wwwroot.'/blocks/reports/view.php?action=dashboardce&courseid='.$COURSE->id,
                    'title' => $reportsicon.'Open Reports ('.get_string('experimental',
                            'block_reports').')'
                );
            }
        }

        // Personalised Learning Designer.
        if (array_key_exists('pld', $localplugins) && has_capability('local/pld:editcourserules', $coursecontext)) {
            $iconurl = $OUTPUT->image_url('pldnew', 'theme');
            $pldicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            $pldname = get_string('pldinitials', 'local_pld');
            $links[] = array(
                'link' => 'local/pld/view.php?newpld=1&courseid='.$COURSE->id,
                'title' => $pldicon.$pldname,
                'attributes' => ['id' => 'ct-pld']
            );
        }

        // Personalised Learning Designer (Legacy).
        if ((!isset($CFG->local_pld_legacy) || $CFG->local_pld_legacy)) {
            if (array_key_exists('pld', $localplugins) && has_capability('local/pld:editcourserules', $coursecontext)) {
                $iconurl = $OUTPUT->image_url('pld', 'theme');
                $pldicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
                $pldname = get_string('pldlegacy', 'local_pld');
                $links[] = array(
                    'link' => 'local/pld/view.php?courseid='.$COURSE->id,
                    'title' => $pldicon.$pldname,
                    'attributes' => ['id' => 'ct-pld-legacy']
                );
            }
        }

        // Competencies if enabled.
        if (get_config('core_competency', 'enabled') && has_capability('moodle/competency:competencyview', $coursecontext)) {
            $iconurl = $OUTPUT->image_url('competencies', 'theme');
            $competenciesicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            $links[] = array(
                'link'  => 'admin/tool/lp/coursecompetencies.php?courseid='.$COURSE->id,
                'title' => $competenciesicon.get_string('competencies', 'core_competency')
            );
        }

        // Outcomes if enabled.
        if (!empty($CFG->core_outcome_enable)) {
            $iconurl = $OUTPUT->image_url('outcomes', 'theme');
            $outcomesicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';

            if (has_capability('moodle/grade:edit', $coursecontext)) {
                $links[] = array(
                    'link'  => 'outcome/course.php?contextid='.$coursecontext->id,
                    'title' => $outcomesicon.get_string('outcomes', 'outcome'),
                );
            } else if (!is_guest($coursecontext)) {
                $outcomesets = new \core_outcome\model\outcome_set_repository();
                if ($outcomesets->course_has_any_outcome_sets($COURSE->id)) {
                    $links[] = array(
                        'link'  => 'outcome/course.php?contextid='.$coursecontext->id.
                            '&action=report_course_user_performance_table',
                        'title' => $outcomesicon.get_string('outcomes', 'outcome'),
                    );
                }
            }
        }

        // Course badges.
        if (!empty($CFG->enablebadges) && !empty($CFG->badges_allowcoursebadges)) {
            // Match capabilities used by badges subsystem.
            $badgecaps = array(
                'moodle/badges:earnbadge',
                'moodle/badges:viewbadges',
                'moodle/badges:viewawarded',
                'moodle/badges:createbadge',
                'moodle/badges:awardbadge',
                'moodle/badges:configuremessages',
                'moodle/badges:configuredetails',
                'moodle/badges:deletebadge',
            );
            $canviewbadges = has_any_capability($badgecaps, $coursecontext);
            if (!is_guest($coursecontext) && $canviewbadges) {
                $iconurl = $OUTPUT->image_url('badges', 'theme');
                $badgesicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
                $links[] = array(
                    'link' => 'badges/view.php?type=' . BADGE_TYPE_COURSE . '&id=' . $COURSE->id,
                    'title' => $badgesicon.get_string('badges', 'badges')
                );
            }
        }

        // Mediasite. (GT Mod - core component check needs to be first in evaluation or capability check error will
        // occur when the module is not installed).
        if ( \core_component::get_component_directory('mod_mediasite') !== null &&
            $COURSE->id != SITEID && has_capability('mod/mediasite:courses7', $coursecontext) &&
            is_callable('mr_on') &&
            mr_on("mediasite", "_MR_MODULES")) {
            require_once($CFG->dirroot . "/mod/mediasite/mediasitesite.php");
            $iconurl = $OUTPUT->image_url('icon', 'mediasite');
            $badgesicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            $courseconfig = $DB->get_record('mediasite_course_config', array('course' => $COURSE->id));
            if (!empty($courseconfig->mediasite_courses_enabled) && $courseconfig->mediasite_site) {
                $site = new \Sonicfoundry\MediasiteSite($courseconfig->mediasite_site);
                $url = new moodle_url(
                    '/mod/mediasite/courses7.php',
                    array('id' => $COURSE->id, 'siteid' => $courseconfig->mediasite_site)
                );
                $links[] = array(
                    'link' => $url->out_as_local_url(false),
                    'title' => $badgesicon . $site->get_integration_catalog_title()
                );
            } else {
                require_once($CFG->dirroot.'/mod/mediasite/navigation.php');
                foreach (get_mediasite_sites(true, false) as $site) {
                    $url = new moodle_url('/mod/mediasite/courses7.php', array('id' => $COURSE->id, 'siteid' => $site->id));
                    $links[] = array(
                        'link' => $url->out_as_local_url(false),
                        'title' => $badgesicon . $site->integration_catalog_title
                    );
                }
            }
        }

        // Begin LSU Enhancement fix quickmail icon not showing up for students in course.
        if ( \core_component::get_component_directory('block_quickmail') !== null) {

            // Check course config
            $courseconfig = $DB->get_records_menu('block_quickmail_config', ['coursesid' => $COURSE->id], '', 'name,value');

            // Get the master block config for Quickmail.
            $blockconfig = get_config('moodle', 'block_quickmail_allowstudents');

            // Determine Quickmail allowstudents for this course.
            if ((int) $blockconfig < 0) {
                $courseallowstudents = 0;
            } else {
                $courseallowstudents = array_key_exists('allowstudents', $courseconfig) ?
                    $courseconfig['allowstudents'] :
                    $blockconfig;
            }

            // Show QM icon and link for those who cansend OR students.
            if (has_capability('block/quickmail:cansend', $coursecontext) OR $courseallowstudents == 1) {
                // Set the icon appropriate for the version.
                if ($CFG->version > 2017051500.00) {
                    $iconurl = $OUTPUT->image_url('t/email', 'core');
                } else {
                    $iconurl = $OUTPUT->pix_url('t/email', 'core');
                }

                // Build the HTML for the icon.
                $quickmailicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
                // Build the link and add it to the array of links.
                $links[] = array(
                    'link' => 'blocks/quickmail/qm.php?courseid='.$COURSE->id,
                    'title' => $quickmailicon.get_string('pluginname', 'block_quickmail'),
                 );
            }
        }
        // End LSU Enhancement fix quickmail icon no showing up for students in course.

        $config = get_config('tool_ally');
        $configured = !empty($config) && !empty($config->key) && !empty($config->adminurl) && !empty($config->secret);
        $runningbehattest = defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING;
        $configured = $configured || $runningbehattest;

        if ( \core_component::get_component_directory('report_allylti') !== null &&
            $COURSE->id != SITEID && has_capability('report/allylti:viewcoursereport', $coursecontext) && $configured) {

            $url = new moodle_url('/report/allylti/launch.php', [
                    'reporttype' => 'course',
                    'report' => 'admin',
                    'course' => $COURSE->id]
            );

            $iconurl = $OUTPUT->image_url('i/ally_logo', 'theme_snap');
            $allyicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
            $links[] = [
                'link' => $url->out_as_local_url(false),
                'title' => $allyicon . get_string('coursereport', 'report_allylti'),
                'attributes' => ['target' => '_blank']
            ];
        }

        // Add enrol link as the last item in the dashboard links.
        if ($selfenrol) {
            $links[] = [
                'link'  => $enrolurl->out_as_local_url(false),
                'title' => $enrolstr,
            ];
        }

        // Output course tools section.
        $coursetools = get_string('coursetools', 'theme_snap');
        $iconurl = $OUTPUT->image_url('course_dashboard', 'theme');
        $coursetoolsicon = '<img src="'.$iconurl.'" class="svg-icon" alt="" role="presentation">';
        $coursehomealttext = get_string('tilesformatcoursehomealttext', 'theme_snap');

        if ($COURSE->format === 'tiles') {
            $courseurl = new moodle_url('/course/view.php', ['id' => $COURSE->id]);

            $o = '<div id="coursetools-header-tiles">';
            $o .= '<h2>' . $coursetoolsicon . $coursetools . '</h2>';
            $o .= '<div><a href="' . $courseurl . '">
                       <i class="icon fa fa-home fa-fw fa-2x"
                        title="'.$coursehomealttext.'" aria-label="'.$coursehomealttext.'"></i>
                   </a></div>';
            $o .= '</div>';
        } else {
            $o = '<h2>' . $coursetoolsicon . $coursetools . '</h2>';
        }

        if ($downloaditem = self::get_download_content_link()) {
            $links[] = $downloaditem;
        }
        $o .= self::print_student_dashboard();
        $o .= '<ul id="coursetools-list">' .self::render_appendices($links). '</ul>';

        return $o;
    }

    /**
     * Course tools.
     *
     * @param bool $forceshow - force the tools section to be shown.
     * @return string
     */
    public static function course_tools($forceshow = false) {
        global $PAGE, $DB, $USER, $COURSE;

        $output = '';

        $showtools = $forceshow;

        if (!$showtools && stripos($PAGE->bodyclasses, 'format-singleactivity') !== false ) {
            // Display course tools in single activity mode, but only on main page.
            // Current test for main page is based on the pagetype matching a regex.
            // Would be nice if there was something more direct to test.
            if (preg_match('/^mod-.*-view$/', $PAGE->pagetype)) {
                $showtools = true;
            } else if ($PAGE->cm && $PAGE->cm->modname === 'hsuforum') {
                $mod = $DB->get_record('hsuforum', ['id' => $PAGE->cm->instance]);
                $showtools = $mod->type === 'single' && $PAGE->pagetype === 'mod-hsuforum-discuss';
            }
        }

        if ($showtools) {
            if (!empty($USER->editing) && $COURSE->format == 'tiles') {
                $output = '<section id="coursetools" class="clearfix editing-tiles" tabindex="-1">';
            } else {
                $output = '<section id="coursetools" class="clearfix" tabindex="-1">';
            }
            $output .= self::appendices();
            $output .= '</section>';
        }

        return $output;
    }

    /**
     * User dashboard.
     * Shown to users in the course dashboard, initially their progress and grade.
     * Progress and Grade use a progress.js circle.
     *
     * @return string
     */
    public static function print_student_dashboard() {
        global $USER, $COURSE, $OUTPUT;

        $coursecontext = context_course::instance($COURSE->id);
        $output = '';

        // Don't output for teachers.
        if (has_capability('moodle/grade:viewall', $coursecontext)) {
            return $output;
        }
        // Don't output if gradebook is not accessible for this user.
        if (!self::gradebook_accessible($coursecontext)) {
            return $output;
        }

        $userpicture = new \user_picture($USER);
        $userpicture->link = false;
        $userpicture->alttext = false;
        $userpicture->class = 'userpicture snap-icon'; // Icon class for margin.
        $userpicture->size = 100;
        $userpic = $OUTPUT->render($userpicture);

        $userboard  = '<div id="snap-student-dashboard" class="row clearfix">';
        $userboard .= '<div class="col-xs-6">';
        $userboard .= '<h4 class="h6">' .s(fullname($USER)). '</h4>';
        $userboard .= $userpic;
        $userboard .= '</div>';

        // User progress.
        if ($COURSE->enablecompletion) {
            $progress = local::course_completion_progress($COURSE);
            $userboard .= '<div class="col-xs-3 text-center snap-student-dashboard-progress">';
            $userboard .= '<h4 class="h6">' .get_string('progress', 'theme_snap'). '</h6>';
            $userboard .= '<div class="js-progressbar-circle snap-progress-circle" value="'
                .round($progress->progress ?? 0). '"></div>';
            $userboard .= '</div>';
        }

        // User grade.
        if (has_capability('gradereport/overview:view', $coursecontext)) {
            $grade = local::course_grade($COURSE);
            $coursegrade = '-';
            $gradeitem = \grade_item::fetch_course_item($COURSE->id);
            $displayformat = $gradeitem->get_displaytype();
            // If the display grade form is set as a letter, a letter will appear in the user grade dashboard.
            if (!empty($grade->coursegrade) &&
                (($displayformat == GRADE_DISPLAY_TYPE_REAL) ||
                ($displayformat == GRADE_DISPLAY_TYPE_REAL_PERCENTAGE) ||
                ($displayformat == GRADE_DISPLAY_TYPE_REAL_LETTER) ||
                ($displayformat == GRADE_DISPLAY_TYPE_LETTER) ||
                ($displayformat == GRADE_DISPLAY_TYPE_LETTER_REAL) ||
                ($displayformat == GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE))) {
                $coursegrade = current(explode(' ', $grade->coursegrade['value']));
            } else if (!empty($grade->coursegrade['percentage'])) {
                $coursegrade = current(explode(' ', $grade->coursegrade['percentage']));
            }

            $moodleurl = new moodle_url('/grade/report/user/index.php', ['id' => $COURSE->id, 'userid' => $USER->id]);

            $userboard .= '<div class="col-xs-3 text-center snap-student-dashboard-grade">';
            $userboard .= '<h4 class="h6">' . get_string('gradenoun') . '</h6>';
            $userboard .= '<a href="' . $moodleurl . '">';
            $userboard .= '<div class="js-progressbar-circle snap-progress-circle snap-progressbar-link" value="';
            $userboard .= s($coursegrade) . '"gradeformat="' . $displayformat . '" ></div>';
            $userboard .= '</a>';
            $userboard .= '</div>';
        }

        $userboard .= '</div><!- close .snap-user-dashboard ->';
        $userboard .= '<br>';

        $output .= $userboard;
        return $output;

    }

    /**
     * @param array $link
     * @return array
     */
    private static function get_download_content_link(): array {
        global $COURSE, $USER, $OUTPUT;
        $coursecontext = context_course::instance($COURSE->id);
        $link = [];
        if (\core\content::can_export_context($coursecontext, $USER)) {
            $linkattr = \core_course\output\content_export_link::get_attributes($coursecontext);
            $iconurl = $OUTPUT->image_icon('fp/download_content', 'theme', 'theme_snap',
                ['class' => 'iconlarge svg-icon', 'role' => 'presentation']);
            $link = [
                'link' => $linkattr->url,
                'title' => $iconurl . $linkattr->displaystring,
                'attributes' => $linkattr->elementattributes
            ];
        }
        return $link;
    }
}
