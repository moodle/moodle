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
 * Glossary module external API
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/externallib.php");
/**
 * Glossary module external functions
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_glossary_external extends external_api {
    /**
     * Describes the parameters for get_glossaries_by_courses.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_glossaries_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'),
                    'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }
    /**
     * Returns a list of glossaries in a provided list of courses,
     * if no list is provided all glossaries that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array of glossaries details
     * @since Moodle 3.0
     */
    public static function get_glossaries_by_courses($courseids = array()) {
        global $CFG;
        $params = self::validate_parameters(self::get_glossaries_by_courses_parameters(), array('courseids' => $courseids));
        $warnings = array();
        if (!empty($params['courseids'])) {
            $courses = array();
            $courseids = $params['courseids'];
        } else {
            $courses = enrol_get_my_courses();
            $courseids = array_keys($courses);
        }
        // Array to store the glossaries to return.
        $arrglossaries = array();
        // Ensure there are courseids to loop through.
        if (!empty($courseids)) {
            // Array of the courses we are going to retrieve the glossaries from.
            $arraycourses = array();
            // Go through the courseids.
            foreach ($courseids as $cid) {
                // Check the user can function in this context.
                try {
                    $context = context_course::instance($cid);
                    self::validate_context($context);
                    if (has_capability('mod/glossary:view', $context)) {
                        // Check if this course was already loaded (by enrol_get_my_courses).
                        if (!isset($courses[$cid])) {
                            $courses[$cid] = get_course($cid);
                        }
                        $arraycourses[$cid] = $courses[$cid];
                    } else {
                        $warnings[] = array(
                            'item' => 'course',
                            'itemid' => $cid,
                            'warningcode' => '2',
                            'message' => get_string('missingrequiredcapability', 'webservice', 'mod/glossary:view')
                        );
                    }
                } catch (Exception $e) {
                    $warnings[] = array(
                        'item' => 'course',
                        'itemid' => $cid,
                        'warningcode' => '1',
                        'message' => 'No access rights in course context '.$e->getMessage()
                    );
                }
            }
            // Get the glossaries in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $glossaries = get_all_instances_in_courses("glossary", $arraycourses);
            foreach ($glossaries as $glossary) {
                $glossarycontext = context_module::instance($glossary->coursemodule);
                // Entry to return.
                $glossarydetails = array();
                // First, we return information that any user can see in the web interface.
                $glossarydetails['id'] = $glossary->id;
                $glossarydetails['coursemodule']      = $glossary->coursemodule;
                $glossarydetails['course']            = $glossary->course;
                $glossarydetails['name']              = $glossary->name;
                // Format intro.
                list($glossarydetails['intro'], $glossarydetails['introformat']) =
                    external_format_text($glossary->intro, $glossary->introformat,
                                            $glossarycontext->id, 'mod_glossary', 'intro', null);
                $glossarydetails['allowduplicatedentries'] = $glossary->allowduplicatedentries;
                $glossarydetails['displayformat']          = $glossary->displayformat;
                $glossarydetails['mainglossary']           = $glossary->mainglossary;
                $glossarydetails['showspecial']            = $glossary->showspecial;
                $glossarydetails['showalphabet']           = $glossary->showalphabet;
                $glossarydetails['showall']                = $glossary->showall;
                $glossarydetails['allowcomments']          = $glossary->allowcomments;
                $glossarydetails['allowprintview']         = $glossary->allowprintview;
                $glossarydetails['usedynalink']            = $glossary->usedynalink;
                $glossarydetails['defaultapproval']        = $glossary->defaultapproval;
                $glossarydetails['approvaldisplayformat']  = $glossary->approvaldisplayformat;
                $glossarydetails['globalglossary']         = $glossary->globalglossary;
                $glossarydetails['entbypage']              = $glossary->entbypage;
                $glossarydetails['editalways']             = $glossary->editalways;
                $glossarydetails['rsstype']                = $glossary->rsstype;
                $glossarydetails['rssarticles']            = $glossary->rssarticles;
                $glossarydetails['assessed']               = $glossary->assessed;
                $glossarydetails['assesstimestart']        = $glossary->assesstimestart;
                $glossarydetails['assesstimefinish']       = $glossary->assesstimefinish;
                $glossarydetails['scale']                  = $glossary->scale;
                if (has_capability('moodle/course:manageactivities', $glossarycontext)) {
                    $glossarydetails['timecreated']            = $glossary->timecreated;
                    $glossarydetails['timemodified']           = $glossary->timemodified;
                    $glossarydetails['completionentries']      = $glossary->completionentries;
                    $glossarydetails['section']                = $glossary->section;
                    $glossarydetails['visible']                = $glossary->visible;
                    $glossarydetails['groupmode']              = $glossary->groupmode;
                    $glossarydetails['groupingid']             = $glossary->groupingid;
                }
                $arrglossaries[] = $glossarydetails;
            }
        }
        $result = array();
        $result['glossaries'] = $arrglossaries;
        $result['warnings'] = $warnings;
        return $result;
    }
    /**
     * Describes the get_glossaries_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.0
     */
    public static function get_glossaries_by_courses_returns() {
        return new external_single_structure(
            array(
                'glossaries' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Glossary id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_TEXT, 'Course id'),
                            'name' => new external_value(PARAM_TEXT, 'Glossary name'),
                            'intro' => new external_value(PARAM_RAW, 'The Glossary intro'),
                            'introformat' => new external_format_value('intro'),
                            'allowduplicatedentries' => new external_value(PARAM_INT, 'If enabled, multiple entries can have the'.
                                                                                                            ' same concept name'),
                            'displayformat' => new external_value(PARAM_TEXT, 'display format type'),
                            'mainglossary'  => new external_value(PARAM_INT, 'main glossary'),
                            'showspecial'   => new external_value(PARAM_INT, 'If enabled, participants can browse the glossary by'.
                                                                                           ' special characters, such as @ and #'),
                            'showalphabet'  => new external_value(PARAM_INT, 'If enabled, participants can browse the glossary by'.
                                                                                                       ' letters of the alphabet'),
                            'showall'       => new external_value(PARAM_INT, 'If enabled, participants can browse all entries '.
                                                                                                                     ' at once'),
                            'allowcomments' => new external_value(PARAM_INT, 'If enabled, all participants with permission to '.
                                                             'create comments will be able to add comments to glossary entries'),
                            'allowprintview' => new external_value(PARAM_INT, 'If enabled, students are provided with a link to a '.
                                             ' printer-friendly version of the glossary. The link is always available to teachers'),
                            'usedynalink' => new external_value(PARAM_INT, 'If site-wide glossary auto-linking has been enabled by'.
                                            ' an administrator and this checkbox is ticked, the entry will be automatically linked'.
                                               ' wherever the concept words and phrases appear throughout the rest of the course.'),
                            'defaultapproval' => new external_value(PARAM_INT, 'If set to no, entries require approving by a '.
                                                                              'teacher before they are viewable by everyone.'),
                            'approvaldisplayformat' => new external_value(PARAM_TEXT, 'When approving glossary items you may wish'.
                                                                                             ' to use a different display format'),
                            'globalglossary' => new external_value(PARAM_INT, ''),
                            'entbypage' => new external_value(PARAM_INT, 'Entries shown per page'),
                            'editalways' => new external_value(PARAM_INT, 'Always allow editing'),
                            'rsstype' => new external_value(PARAM_INT, 'To enable the RSS feed for this activity, select either'.
                                                   ' concepts with author or concepts without author to be included in the feed'),
                            'rssarticles' => new external_value(PARAM_INT, 'This setting specifies the number of glossary entry'.
                                                  ' concepts to include in the RSS feed. Between 5 and 20 generally acceptable'),
                            'assessed' => new external_value(PARAM_INT, 'assessed'),
                            'assesstimestart' => new external_value(PARAM_RAW, 'assess time start'),
                            'assesstimefinish' => new external_value(PARAM_RAW, 'assess time finish'),
                            'scale' => new external_value(PARAM_INT, 'scale'),
                            'timecreated' => new external_value(PARAM_RAW, 'time created', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_RAW, 'time modified', VALUE_OPTIONAL),
                            'completionentries' => new external_value(PARAM_INT, 'completion entries', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'section', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'grouping id', VALUE_OPTIONAL),
                        ), 'Glossaries'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
