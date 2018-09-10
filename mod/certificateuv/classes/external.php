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
 * Certificate module external API
 *
 * @package    mod_certificate
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/certificate/locallib.php');

/**
 * Certificate module external functions
 *
 * @package    mod_certificate
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_certificateuv_external extends external_api {

    /**
     * Describes the parameters for get_certificates_by_courses.
     *
     * @return external_function_parameters
     */
    public static function get_certificates_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of certificates in a provided list of courses,
     * if no list is provided all certificates that the user can view will be returned.
     *
     * @param array $courseids the course ids
     * @return array the certificate details
     */
    public static function get_certificates_by_courses($courseids = array()) {
        global $CFG;

        $returnedcertificates = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_certificates_by_courses_parameters(), array('courseids' => $courseids));

        if (empty($params['courseids'])) {
            $params['courseids'] = array_keys(enrol_get_my_courses());
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids']);

            // Get the certificates in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $certificates = get_all_instances_in_courses("certificate", $courses);

            foreach ($certificates as $certificate) {

                $context = context_module::instance($certificate->coursemodule);

                // Entry to return.
                $module = array();

                // First, we return information that any user can see in (or can deduce from) the web interface.
                $module['id'] = $certificate->id;
                $module['coursemodule'] = $certificate->coursemodule;
                $module['course'] = $certificate->course;
                $module['name']  = external_format_string($certificate->name, $context->id);

                $viewablefields = [];
                if (has_capability('mod/certificateuv:view', $context)) {
                    list($module['intro'], $module['introformat']) =
                        external_format_text($certificate->intro, $certificate->introformat, $context->id,
                                                'mod_certificateuv', 'intro', $certificate->id);

                    // Check certificate requeriments for current user.
                    $viewablefields[] = 'requiredtime';
                    $module['requiredtimenotmet'] = 0;
                    if ($certificate->requiredtime && !has_capability('mod/certificateuv:manage', $context)) {
                        if (certificateuv_get_course_time($certificate->course) < ($certificate->requiredtime * 60)) {
                            $module['requiredtimenotmet'] = 1;
                        }
                    }
                }

                // Check additional permissions for returning optional private settings.
                if (has_capability('moodle/course:manageactivities', $context)) {

                    $additionalfields = array('emailteachers', 'emailothers', 'savecert',
                        'reportcert', 'delivery', 'certificatetype', 'orientation', 'borderstyle', 'bordercolor',
                        'printwmark', 'printdate', 'datefmt', 'printnumber', 'printgrade', 'gradefmt', 'printoutcome',
                        'printhours', 'printteacher', 'customtext', 'printsignature', 'printseal', 'timecreated', 'timemodified',
                        'section', 'visible', 'groupmode', 'groupingid');
                    $viewablefields = array_merge($viewablefields, $additionalfields);

                }

                foreach ($viewablefields as $field) {
                    $module[$field] = $certificate->{$field};
                }

                $returnedcertificates[] = $module;
            }
        }

        $result = array();
        $result['certificates'] = $returnedcertificates;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_certificates_by_courses return value.
     *
     * @return external_single_structure
     */
    public static function get_certificates_by_courses_returns() {

        return new external_single_structure(
            array(
                'certificates' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Certificate id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'Certificate name'),
                            'intro' => new external_value(PARAM_RAW, 'The Certificate intro', VALUE_OPTIONAL),
                            'introformat' => new external_format_value('intro', VALUE_OPTIONAL),
                            'requiredtimenotmet' => new external_value(PARAM_INT, 'Whether the time req is met', VALUE_OPTIONAL),
                            'emailteachers' => new external_value(PARAM_INT, 'Email teachers?', VALUE_OPTIONAL),
                            'emailothers' => new external_value(PARAM_RAW, 'Email others?', VALUE_OPTIONAL),
                            'savecert' => new external_value(PARAM_INT, 'Save certificate?', VALUE_OPTIONAL),
                            'reportcert' => new external_value(PARAM_INT, 'Report certificate?', VALUE_OPTIONAL),
                            'delivery' => new external_value(PARAM_INT, 'Delivery options', VALUE_OPTIONAL),
                            'requiredtime' => new external_value(PARAM_INT, 'Required time', VALUE_OPTIONAL),
                            'certificatetype' => new external_value(PARAM_RAW, 'Type', VALUE_OPTIONAL),
                            'orientation' => new external_value(PARAM_ALPHANUM, 'Orientation', VALUE_OPTIONAL),
                            'borderstyle' => new external_value(PARAM_RAW, 'Border style', VALUE_OPTIONAL),
                            'bordercolor' => new external_value(PARAM_RAW, 'Border color', VALUE_OPTIONAL),
                            'printwmark' => new external_value(PARAM_RAW, 'Print water mark?', VALUE_OPTIONAL),
                            'printdate' => new external_value(PARAM_RAW, 'Print date?', VALUE_OPTIONAL),
                            'datefmt' => new external_value(PARAM_INT, 'Date format', VALUE_OPTIONAL),
                            'printnumber' => new external_value(PARAM_INT, 'Print number?', VALUE_OPTIONAL),
                            'printgrade' => new external_value(PARAM_INT, 'Print grade?', VALUE_OPTIONAL),
                            'gradefmt' => new external_value(PARAM_INT, 'Grade format', VALUE_OPTIONAL),
                            'printoutcome' => new external_value(PARAM_INT, 'Print outcome?', VALUE_OPTIONAL),
                            'printhours' => new external_value(PARAM_TEXT, 'Print hours?', VALUE_OPTIONAL),
                            'printteacher' => new external_value(PARAM_INT, 'Print teacher?', VALUE_OPTIONAL),
                            'customtext' => new external_value(PARAM_RAW, 'Custom text', VALUE_OPTIONAL),
                            'printsignature' => new external_value(PARAM_RAW, 'Print signature?', VALUE_OPTIONAL),
                            'printseal' => new external_value(PARAM_RAW, 'Print seal?', VALUE_OPTIONAL),
                            'timecreated' => new external_value(PARAM_INT, 'Time created', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time modified', VALUE_OPTIONAL),
                            'section' => new external_value(PARAM_INT, 'course section id', VALUE_OPTIONAL),
                            'visible' => new external_value(PARAM_INT, 'visible', VALUE_OPTIONAL),
                            'groupmode' => new external_value(PARAM_INT, 'group mode', VALUE_OPTIONAL),
                            'groupingid' => new external_value(PARAM_INT, 'group id', VALUE_OPTIONAL),
                        ), 'Tool'
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function view_certificate_parameters() {
        return new external_function_parameters(
            array(
                'certificateid' => new external_value(PARAM_INT, 'certificate instance id')
            )
        );
    }

    /**
     * Trigger the course module viewed event and update the module completion status.
     *
     * @param int $certificateid the certificate instance id
     * @return array of warnings and status result
     * @throws moodle_exception
     */
    public static function view_certificate($certificateid) {
        global $DB;

        $params = self::validate_parameters(self::view_certificate_parameters(),
                                            array(
                                                'certificateid' => $certificateid
                                            )
        );
        $warnings = array();

        // Request and permission validation.
        $certificate = $DB->get_record('certificate', array('id' => $params['certificateid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($certificate, 'certificateuv');

        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/certificateuv:view', $context);

        $event = \mod_certificate\event\course_module_viewed::create(array(
            'objectid' => $certificate->id,
            'context' => $context,
        ));
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('certificate', $certificate);
        $event->trigger();

        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function view_certificate_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Check if the user can issue certificates.
     *
     * @param  int $certificateid certificate instance id
     * @return array array containing context related data
     */
    private static function check_can_issue($certificateid) {
        global $DB;

        $certificate = $DB->get_record('certificateuv', array('id' => $certificateid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($certificate, 'certificate');

        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('mod/certificateuv:view', $context);

        // Check if the user can view the certificate.
        if ($certificate->requiredtime && !has_capability('mod/certificateuv:manage', $context)) {
            if (certificateuv_get_course_time($course->id) < ($certificate->requiredtime * 60)) {
                $a = new stdClass();
                $a->requiredtime = $certificate->requiredtime;
                throw new moodle_exception('requiredtimenotmet', 'certificateuv', '', $a);
            }
        }
        return array($certificate, $course, $cm, $context);
    }

    /**
     * Returns a issued certificated structure
     *
     * @return external_single_structure External single structure
     */
    private static function issued_structure() {
        return new external_single_structure(
            array(
            'id' => new external_value(PARAM_INT, 'Issue id'),
            'userid' => new external_value(PARAM_INT, 'User id'),
            'certificateid' => new external_value(PARAM_INT, 'Certificate id'),
            'code' => new external_value(PARAM_RAW, 'Certificate code'),
            'timecreated' => new external_value(PARAM_INT, 'Time created'),
            'filename' => new external_value(PARAM_FILE, 'Time created'),
            'fileurl' => new external_value(PARAM_URL, 'Time created'),
            'mimetype' => new external_value(PARAM_RAW, 'mime type'),
            'grade' => new external_value(PARAM_NOTAGS, 'Certificate grade', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Add extra required information to the issued certificate
     *
     * @param stdClass $issue       issue object
     * @param stdClass $certificate certificate object
     * @param stdClass $course      course object
     * @param stdClass $cm          course module object
     * @param stdClass $context     context object
     */
    private static function add_extra_issue_data($issue, $certificate, $course, $cm, $context) {
        global $CFG;

        // Grade data.
        if ($certificate->printgrade) {
            $issue->grade = certificateuv_get_grade($certificate, $course);
        }

        // File data.
        $issue->mimetype = 'application/pdf';
        $issue->filename = certificateuv_get_certificate_filename($certificate, $cm, $course) . '.pdf';
        // We need to use a special file area to be able to download certificates (in most cases are not stored in the site).
        $issue->fileurl = moodle_url::make_webservice_pluginfile_url(
                                $context->id, 'mod_certificateuv', 'onthefly', $issue->id, '/', $issue->filename)->out(false);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function issue_certificate_parameters() {
        return new external_function_parameters(
            array(
                'certificateid' => new external_value(PARAM_INT, 'certificate instance id')
            )
        );
    }

    /**
     * Create new certificate record, or return existing record.
     *
     * @param int $certificateid the certificate instance id
     * @return array of warnings and status result
     * @throws moodle_exception
     */
    public static function issue_certificate($certificateid) {
        global $USER;

        $params = self::validate_parameters(self::issue_certificate_parameters(),
                                            array(
                                                'certificateid' => $certificateid
                                            )
        );
        $warnings = array();

        // Request and permission validation.
        list($certificate, $course, $cm, $context) = self::check_can_issue($params['certificateid']);

        $issue = certificate_get_issue($course, $USER, $certificate, $cm);
        self::add_extra_issue_data($issue, $certificate, $course, $cm, $context);

        $result = array();
        $result['issue'] = $issue;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function issue_certificate_returns() {
        return new external_single_structure(
            array(
                'issue' => self::issued_structure(),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_issued_certificates_parameters() {
        return new external_function_parameters(
            array(
                'certificateid' => new external_value(PARAM_INT, 'certificate instance id')
            )
        );
    }

    /**
     * Get the list of issued certificates for the current user.
     *
     * @param int $certificateid the certificate instance id
     * @return array of warnings and status result
     * @throws moodle_exception
     */
    public static function get_issued_certificates($certificateid) {

        $params = self::validate_parameters(self::get_issued_certificates_parameters(),
                                            array(
                                                'certificateid' => $certificateid
                                            )
        );
        $warnings = array();

        // Request and permission validation.
        list($certificate, $course, $cm, $context) = self::check_can_issue($params['certificateid']);

        $issues = certificate_get_attempts($certificate->id);

        if ($issues !== false ) {
            foreach ($issues as $issue) {
                self::add_extra_issue_data($issue, $certificate, $course, $cm, $context);

            }
        } else {
            $issues = array();
        }

        $result = array();
        $result['issues'] = $issues;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function get_issued_certificates_returns() {
        return new external_single_structure(
            array(
                'issues' => new external_multiple_structure(self::issued_structure()),
                'warnings' => new external_warnings()
            )
        );
    }

}
