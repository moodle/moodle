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
 * Glossary module external API.
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/glossary/lib.php');

/**
 * Glossary module external functions.
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Costantino Cito <ccito@cvaconsulting.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_glossary_external extends external_api {

    /**
     * Get the browse modes from the display format.
     *
     * This returns some of the terms that can be used when reporting a glossary being viewed.
     *
     * @param  string $format The display format of the glossary.
     * @return array Containing some of all of the following: letter, cat, date, author.
     */
    public static function get_browse_modes_from_display_format($format) {
        global $DB;

        $dp = $DB->get_record('glossary_formats', array('name' => $format), '*', IGNORE_MISSING);
        $formats = glossary_get_visible_tabs($dp);

        // Always add 'letter'.
        $modes = array('letter');

        if (in_array('category', $formats)) {
            $modes[] = 'cat';
        }
        if (in_array('date', $formats)) {
            $modes[] = 'date';
        }
        if (in_array('author', $formats)) {
            $modes[] = 'author';
        }

        return $modes;
    }

    /**
     * Get the return value of an entry.
     *
     * @param bool $includecat Whether the definition should include category info.
     * @return external_definition
     */
    public static function get_entry_return_structure($includecat = false) {
        $params = array(
            'id' => new external_value(PARAM_INT, 'The entry ID'),
            'glossaryid' => new external_value(PARAM_INT, 'The glossary ID'),
            'userid' => new external_value(PARAM_INT, 'Author ID'),
            'userfullname' => new external_value(PARAM_TEXT, 'Author full name'),
            'userpictureurl' => new external_value(PARAM_URL, 'Author picture'),
            'concept' => new external_value(PARAM_RAW, 'The concept'),
            'definition' => new external_value(PARAM_RAW, 'The definition'),
            'definitionformat' => new external_format_value('definition'),
            'definitiontrust' => new external_value(PARAM_BOOL, 'The definition trust flag'),
            'attachment' => new external_value(PARAM_BOOL, 'Whether or not the entry has attachments'),
            'attachments' => new external_multiple_structure(
                new external_single_structure(array(
                    'filename' => new external_value(PARAM_FILE, 'File name'),
                    'mimetype' => new external_value(PARAM_RAW, 'Mime type'),
                    'fileurl'  => new external_value(PARAM_URL, 'File download URL')
                )), 'attachments', VALUE_OPTIONAL
            ),
            'timecreated' => new external_value(PARAM_INT, 'Time created'),
            'timemodified' => new external_value(PARAM_INT, 'Time modified'),
            'teacherentry' => new external_value(PARAM_BOOL, 'The entry was created by a teacher, or equivalent.'),
            'sourceglossaryid' => new external_value(PARAM_INT, 'The source glossary ID'),
            'usedynalink' => new external_value(PARAM_BOOL, 'Whether the concept should be automatically linked'),
            'casesensitive' => new external_value(PARAM_BOOL, 'When true, the matching is case sensitive'),
            'fullmatch' => new external_value(PARAM_BOOL, 'When true, the matching is done on full words only'),
            'approved' => new external_value(PARAM_BOOL, 'Whether the entry was approved'),
        );

        if ($includecat) {
            $params['categoryid'] = new external_value(PARAM_INT, 'The category ID. This may be' .
                ' \''. GLOSSARY_SHOW_NOT_CATEGORISED . '\' when the entry is not categorised', VALUE_DEFAULT,
                GLOSSARY_SHOW_NOT_CATEGORISED);
            $params['categoryname'] = new external_value(PARAM_RAW, 'The category name. May be empty when the entry is' .
                ' not categorised, or the request was limited to one category.', VALUE_DEFAULT, '');
        }

        return new external_single_structure($params);
    }

    /**
     * Fill in an entry object.
     *
     * This adds additional required fields for the external function to return.
     *
     * @param  stdClass $entry   The entry.
     * @param  context  $context The context the entry belongs to.
     * @return void
     */
    public static function fill_entry_details($entry, $context, $useridfield = 'userdataid', $userfieldprefix = 'userdata') {
        global $PAGE;
        $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);

        // Format concept and definition.
        $entry->concept = external_format_string($entry->concept, $context->id);
        list($entry->definition, $entry->definitionformat) = external_format_text($entry->definition, $entry->definitionformat,
            $context->id, 'mod_glossary', 'entry', $entry->id);

        // Author details.
        $user = new stdclass();
        $user = user_picture::unalias($entry, null, $useridfield, $userfieldprefix);
        $userpicture = new user_picture($user);
        $userpicture->size = 1;
        $entry->userfullname = fullname($user, $canviewfullnames);
        $entry->userpictureurl = $userpicture->get_url($PAGE)->out(false);

        // Fetch attachments.
        $entry->attachment = !empty($entry->attachment) ? 1 : 0;
        $entry->attachments = array();
        if ($entry->attachment) {
            $fs = get_file_storage();
            if ($files = $fs->get_area_files($context->id, 'mod_glossary', 'attachment', $entry->id, 'filename', false)) {
                foreach ($files as $file) {
                    $filename = $file->get_filename();
                    $fileurl = moodle_url::make_webservice_pluginfile_url($context->id, 'mod_glossary', 'attachment',
                        $entry->id, '/', $filename);
                    $entry->attachments[] = array(
                        'filename' => $filename,
                        'mimetype' => $file->get_mimetype(),
                        'fileurl'  => $fileurl->out(false)
                    );
                }
            }
        }
    }

    /**
     * Describes the parameters for get_glossaries_by_courses.
     *
     * @return external_external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_glossaries_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'course id'),
                    'Array of course IDs', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of glossaries in a provided list of courses.
     *
     * If no list is provided all glossaries that the user can view will be returned.
     *
     * @param array $courseids the course IDs.
     * @return array of glossaries
     * @since Moodle 3.1
     */
    public static function get_glossaries_by_courses($courseids = array()) {
        $params = self::validate_parameters(self::get_glossaries_by_courses_parameters(), array('courseids' => $courseids));

        $warnings = array();
        $courses = array();
        $courseids = $params['courseids'];

        if (empty($courseids)) {
            $courses = enrol_get_my_courses();
            $courseids = array_keys($courses);
        }

        // Array to store the glossaries to return.
        $glossaries = array();
        $modes = array();

        // Ensure there are courseids to loop through.
        if (!empty($courseids)) {
            list($courses, $warnings) = external_util::validate_courses($courseids, $courses);

            // Get the glossaries in these courses, this function checks users visibility permissions.
            $glossaries = get_all_instances_in_courses('glossary', $courses);
            foreach ($glossaries as $glossary) {
                $context = context_module::instance($glossary->coursemodule);
                $glossary->name = external_format_string($glossary->name, $context->id);
                list($glossary->intro, $glossary->introformat) = external_format_text($glossary->intro, $glossary->introformat,
                    $context->id, 'mod_glossary', 'intro', null);

                // Make sure we have a number of entries per page.
                if (!$glossary->entbypage) {
                    $glossary->entbypage = $CFG->glossary_entbypage;
                }

                // Add the list of browsing modes.
                if (!isset($modes[$glossary->displayformat])) {
                    $modes[$glossary->displayformat] = self::get_browse_modes_from_display_format($glossary->displayformat);
                }
                $glossary->browsemodes = $modes[$glossary->displayformat];
            }
        }

        $result = array();
        $result['glossaries'] = $glossaries;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_glossaries_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_glossaries_by_courses_returns() {
        return new external_single_structure(array(
            'glossaries' => new external_multiple_structure(
                new external_single_structure(array(
                    'id' => new external_value(PARAM_INT, 'Glossary id'),
                    'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                    'course' => new external_value(PARAM_INT, 'Course id'),
                    'name' => new external_value(PARAM_RAW, 'Glossary name'),
                    'intro' => new external_value(PARAM_RAW, 'The Glossary intro'),
                    'introformat' => new external_format_value('intro'),
                    'allowduplicatedentries' => new external_value(PARAM_INT, 'If enabled, multiple entries can have the' .
                        ' same concept name'),
                    'displayformat' => new external_value(PARAM_TEXT, 'Display format type'),
                    'mainglossary' => new external_value(PARAM_INT, 'If enabled this glossary is a main glossary.'),
                    'showspecial' => new external_value(PARAM_INT, 'If enabled, participants can browse the glossary by' .
                        ' special characters, such as @ and #'),
                    'showalphabet' => new external_value(PARAM_INT, 'If enabled, participants can browse the glossary by' .
                        ' letters of the alphabet'),
                    'showall' => new external_value(PARAM_INT, 'If enabled, participants can browse all entries at once'),
                    'allowcomments' => new external_value(PARAM_INT, 'If enabled, all participants with permission to' .
                        ' create comments will be able to add comments to glossary entries'),
                    'allowprintview' => new external_value(PARAM_INT, 'If enabled, students are provided with a link to a' .
                        ' printer-friendly version of the glossary. The link is always available to teachers'),
                    'usedynalink' => new external_value(PARAM_INT, 'If site-wide glossary auto-linking has been enabled' .
                        ' by an administrator and this checkbox is ticked, the entry will be automatically linked' .
                        ' wherever the concept words and phrases appear throughout the rest of the course.'),
                    'defaultapproval' => new external_value(PARAM_INT, 'If set to no, entries require approving by a' .
                        ' teacher before they are viewable by everyone.'),
                    'approvaldisplayformat' => new external_value(PARAM_TEXT, 'When approving glossary items you may wish' .
                        ' to use a different display format'),
                    'globalglossary' => new external_value(PARAM_INT, ''),
                    'entbypage' => new external_value(PARAM_INT, 'Entries shown per page'),
                    'editalways' => new external_value(PARAM_INT, 'Always allow editing'),
                    'rsstype' => new external_value(PARAM_INT, 'To enable the RSS feed for this activity, select either' .
                        ' concepts with author or concepts without author to be included in the feed'),
                    'rssarticles' => new external_value(PARAM_INT, 'This setting specifies the number of glossary entry' .
                        ' concepts to include in the RSS feed. Between 5 and 20 generally acceptable'),
                    'assessed' => new external_value(PARAM_INT, 'Aggregate type'),
                    'assesstimestart' => new external_value(PARAM_INT, 'Restrict rating to items created after this'),
                    'assesstimefinish' => new external_value(PARAM_INT, 'Restrict rating to items created before this'),
                    'scale' => new external_value(PARAM_INT, 'Scale ID'),
                    'timecreated' => new external_value(PARAM_INT, 'Time created'),
                    'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                    'completionentries' => new external_value(PARAM_INT, 'Number of entries to complete'),
                    'section' => new external_value(PARAM_INT, 'Section'),
                    'visible' => new external_value(PARAM_INT, 'Visible'),
                    'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                    'groupingid' => new external_value(PARAM_INT, 'Grouping ID'),
                    'browsemodes' => new external_multiple_structure(
                        new external_value(PARAM_ALPHA, 'Modes of browsing allowed')
                    )
                ), 'Glossaries')
            ),
            'warnings' => new external_warnings())
        );
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_glossary_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary instance ID'),
            'mode' => new external_value(PARAM_ALPHA, 'The mode in which the glossary is viewed'),
        ));
    }

    /**
     * Notify that the course module was viewed.
     *
     * @param int $id The glossary instance ID.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function view_glossary($id, $mode) {
        global $DB;

        $params = self::validate_parameters(self::view_glossary_parameters(), array(
            'id' => $id,
            'mode' => $mode
        ));
        $id = $params['id'];
        $mode = $params['mode'];
        $warnings = array();

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Trigger module viewed event.
        glossary_view($glossary, $course, $cm, $context, $mode);

        return array(
            'status' => true,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function view_glossary_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'True on success'),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function view_entry_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
        ));
    }

    /**
     * Notify that the entry was viewed.
     *
     * @param int $id The entry ID.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function view_entry($id) {
        global $DB, $USER;

        $params = self::validate_parameters(self::view_entry_parameters(), array('id' => $id));
        $id = $params['id'];
        $warnings = array();

        // Fetch and confirm.
        $entry = $DB->get_record('glossary_entries', array('id' => $id), '*', MUST_EXIST);
        $glossary = $DB->get_record('glossary', array('id' => $entry->glossaryid), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        if (empty($entry->approved) && $entry->userid != $USER->id && !has_capability('mod/glossary:approve', $context)) {
            throw new invalid_parameter_exception('invalidentry');
        }

        // Trigger view.
        glossary_entry_view($entry, $context);

        return array(
            'status' => true,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function view_entry_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_BOOL, 'True on success'),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_entries_by_letter_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'letter' => new external_value(PARAM_ALPHA, 'A letter, or either keywords: \'ALL\' or \'SPECIAL\'.'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes the non-approved entries created by' .
                    ' the user. When true, also includes the ones that the user has the permission to approve.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Browse a glossary entries by letter.
     *
     * @param int $id The glossary ID.
     * @param string $letter A letter, or a special keyword.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_entries_by_letter($id, $letter, $from = 0, $limit = 20, $options = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entries_by_letter_parameters(), array(
            'id' => $id,
            'letter' => $letter,
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $letter = $params['letter'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the mode.
        $modes = self::get_browse_modes_from_display_format($glossary->displayformat);
        if (!in_array('letter', $modes)) {
            throw new invalid_parameter_exception('invalidbrowsemode');
        }

        // Preparing the query.
        $where = '1 = 1';
        $params = array();

        if ($letter != 'ALL' && $letter != 'SPECIAL' && ($letterstrlen = core_text::strlen($letter))) {
            $params['hookup'] = core_text::strtoupper($letter);
            $where = $DB->sql_substr('upper(concept)', 1, $letterstrlen) . ' = :hookup';
        }
        if ($letter == 'SPECIAL') {
            $alphabet = explode(',', get_string('alphabet', 'langconfig'));
            list($nia, $aparams) = $DB->get_in_or_equal($alphabet, SQL_PARAMS_NAMED, 'a', false);
            $params = array_merge($params, $aparams);
            $where = $DB->sql_substr("upper(concept)", 1, 1) . " $nia";
        }

        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $userfields = user_picture::fields('u', null, 'userdataid', 'userdata');

        $sqlselectcount = "SELECT COUNT('x')";
        $sqlselect = "SELECT ge.*, $userfields";
        $sql = "  FROM {glossary_entries} ge
             LEFT JOIN {user} u ON ge.userid = u.id
                 WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)
                   AND $approvedsql
                   AND $where";
        $sqlorder = " ORDER BY ge.concept";

        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;
        $params['myid'] = $USER->id;

        // Fetching the entries.
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $entries = $DB->get_records_sql($sqlselect . $sql . $sqlorder, $params, $from, $limit);
        foreach ($entries as $key => $entry) {
            self::fill_entry_details($entry, $context);
        }

        return array(
            'count' => $count,
            'entries' => $entries,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_entries_by_letter_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records matching the request.'),
            'entries' => new external_multiple_structure(
                self::get_entry_return_structure()
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_entries_by_date_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'order' => new external_value(PARAM_ALPHA, 'Order the records by: \'CREATION\' or \'UPDATE\'.',
                VALUE_DEFAULT, 'UPDATE'),
            'sort' => new external_value(PARAM_ALPHA, 'The direction of the order: \'ASC\' or \'DESC\'', VALUE_DEFAULT, 'DESC'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes the non-approved entries created by' .
                    ' the user. When true, also includes the ones that the user has the permission to approve.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Browse a glossary entries by date.
     *
     * @param int $id The glossary ID.
     * @param string $order The way to order the records.
     * @param string $sort The direction of the order.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_entries_by_date($id, $order = 'UPDATE', $sort = 'DESC', $from = 0, $limit = 20,
            $options = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entries_by_date_parameters(), array(
            'id' => $id,
            'order' => core_text::strtoupper($order),
            'sort' => core_text::strtoupper($sort),
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $order = $params['order'];
        $sort = $params['sort'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        if (!in_array($order, array('CREATION', 'UPDATE'))) {
            throw new invalid_parameter_exception('invalidorder');
        } else if (!in_array($sort, array('ASC', 'DESC'))) {
            throw new invalid_parameter_exception('invalidsort');
        }

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the mode.
        $modes = self::get_browse_modes_from_display_format($glossary->displayformat);
        if (!in_array('date', $modes)) {
            throw new invalid_parameter_exception('invalidbrowsemode');
        }

        // Preparing the query.
        $params = array();
        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $userfields = user_picture::fields('u', null, 'userdataid', 'userdata');

        $sqlselectcount = "SELECT COUNT('x')";
        $sqlselect = "SELECT ge.*, $userfields";
        $sql = "  FROM {glossary_entries} ge
             LEFT JOIN {user} u ON ge.userid = u.id
                 WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)
                   AND $approvedsql";

        $sqlorder = ' ORDER BY ';
        if ($order == 'CREATION') {
            $sqlorder .= 'ge.timecreated';
        } else {
            $sqlorder .= 'ge.timemodified';
        }
        $sqlorder .= ' ' . $sort;

        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;
        $params['myid'] = $USER->id;

        // Fetching the entries.
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $entries = $DB->get_records_sql($sqlselect . $sql . $sqlorder, $params, $from, $limit);
        foreach ($entries as $key => $entry) {
            self::fill_entry_details($entry, $context);
        }

        return array(
            'count' => $count,
            'entries' => $entries,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_entries_by_date_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records matching the request.'),
            'entries' => new external_multiple_structure(
                self::get_entry_return_structure()
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_categories_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20)
        ));
    }

    /**
     * Get the categories of a glossary.
     *
     * @param int $id The glossary ID.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_categories($id, $from = 0, $limit = 20) {
        global $DB;

        $params = self::validate_parameters(self::get_categories_parameters(), array(
            'id' => $id,
            'from' => $from,
            'limit' => $limit
        ));
        $id = $params['id'];
        $from = $params['from'];
        $limit = $params['limit'];
        $warnings = array();

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Fetch the categories.
        $count = $DB->count_records('glossary_categories', array('glossaryid' => $id));
        $categories = $DB->get_records('glossary_categories', array('glossaryid' => $id), 'name ASC', '*', $from, $limit);
        foreach ($categories as $category) {
            $category->name = external_format_string($category->name, $context->id);
        }

        return array(
            'count' => $count,
            'categories' => $categories,
            'warnings' => array(),
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_categories_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records.'),
            'categories' => new external_multiple_structure(
                new external_single_structure(array(
                    'id' => new external_value(PARAM_INT, 'The category ID'),
                    'glossaryid' => new external_value(PARAM_INT, 'The glossary ID'),
                    'name' => new external_value(PARAM_RAW, 'The name of the category'),
                    'usedynalink' => new external_value(PARAM_BOOL, 'Whether the category is automatically linked'),
                ))
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_entries_by_category_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID.'),
            'categoryid' => new external_value(PARAM_INT, 'The category ID. Use \'' . GLOSSARY_SHOW_ALL_CATEGORIES . '\' for all' .
                ' categories, or \'' . GLOSSARY_SHOW_NOT_CATEGORISED . '\' for uncategorised entries.'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes the non-approved entries created by' .
                    ' the user. When true, also includes the ones that the user has the permission to approve.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Browse a glossary entries by category.
     *
     * @param int $id The glossary ID.
     * @param int $categoryid The category ID.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_entries_by_category($id, $categoryid, $from = 0, $limit = 20, $options = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entries_by_category_parameters(), array(
            'id' => $id,
            'categoryid' => $categoryid,
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $categoryid = $params['categoryid'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the mode.
        $modes = self::get_browse_modes_from_display_format($glossary->displayformat);
        if (!in_array('cat', $modes)) {
            throw new invalid_parameter_exception('invalidbrowsemode');
        }

        // Validate the category.
        if (in_array($categoryid, array(GLOSSARY_SHOW_ALL_CATEGORIES, GLOSSARY_SHOW_NOT_CATEGORISED))) {
            // All good.
        } else if ($DB->count_records('glossary_categories', array('id' => $categoryid, 'glossaryid' => $id)) < 1) {
            throw new invalid_parameter_exception('invalidcategory');
        }

        // Preparing the query.
        $params = array();
        $glossarysql = '(ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)';
        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;

        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        $params['myid'] = $USER->id;
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $userfields = user_picture::fields('u', null, 'userdataid', 'userdata');
        $sqlfields = "ge.*, gec.categoryid, $userfields";
        $sqlorderfields = 'ge.concept';

        if ($categoryid === GLOSSARY_SHOW_ALL_CATEGORIES) {
            $sqlfields .= ', gc.name AS categoryname';
            $sqlorderfields = 'gc.name, ge.concept';
            $sql = "  FROM {glossary_entries} ge
                      JOIN {glossary_entries_categories} gec ON ge.id = gec.entryid
                      JOIN {glossary_categories} gc ON gc.id = gec.categoryid
                 LEFT JOIN {user} u ON ge.userid = u.id
                     WHERE $glossarysql
                       AND $approvedsql";

        } else if ($categoryid === GLOSSARY_SHOW_NOT_CATEGORISED) {
            $sql = "  FROM {glossary_entries} ge
                 LEFT JOIN {glossary_entries_categories} gec ON ge.id = gec.entryid
                 LEFT JOIN {user} u ON ge.userid = u.id
                     WHERE $glossarysql
                       AND $approvedsql
                       AND gec.categoryid IS NULL";

        } else {
            $sql = "  FROM {glossary_entries} ge
                      JOIN {glossary_entries_categories} gec
                        ON gec.entryid = ge.id
                       AND gec.categoryid = :categoryid
                 LEFT JOIN {user} u ON ge.userid = u.id
                     WHERE $glossarysql
                       AND $approvedsql";
            $params['categoryid'] = $categoryid;
        }

        $sqlselectcount = "SELECT COUNT('x')";
        $sqlselect = "SELECT $sqlfields";
        $sqlorder = ' ORDER BY ' . $sqlorderfields;

        // Fetching the entries.
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $entries = $DB->get_records_sql($sqlselect . $sql . $sqlorder, $params, $from, $limit);
        foreach ($entries as $key => $entry) {
            self::fill_entry_details($entry, $context);
            if ($entry->categoryid === null) {
                $entry->categoryid = GLOSSARY_SHOW_NOT_CATEGORISED;
            }
            if (isset($entry->categoryname)) {
                $entry->categoryname = external_format_string($entry->categoryname, $context->id);
            }
        }

        return array(
            'count' => $count,
            'entries' => $entries,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_entries_by_category_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records matching the request.'),
            'entries' => new external_multiple_structure(
                self::get_entry_return_structure(true)
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_authors_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes self even if all of their entries' .
                    ' require approval. When true, also includes authors only having entries pending approval.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Get the authors of a glossary.
     *
     * @param int $id The glossary ID.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_authors($id, $from = 0, $limit = 20, $options = array()) {
        global $DB, $PAGE, $USER;

        $params = self::validate_parameters(self::get_authors_parameters(), array(
            'id' => $id,
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Fetch the authors.
        $params = array();
        $userfields = user_picture::fields('u', null);

        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        $params['myid'] = $USER->id;
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $sqlselectcount = "SELECT COUNT(DISTINCT(u.id))";
        $sqlselect = "SELECT DISTINCT(u.id), $userfields";
        $sql = "  FROM {user} u
                  JOIN {glossary_entries} ge
                    ON ge.userid = u.id
                   AND (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)
                   AND $approvedsql";
        $ordersql = " ORDER BY u.lastname, u.firstname";

        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;

        $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $users = $DB->get_recordset_sql($sqlselect . $sql . $ordersql, $params, $from, $limit);
        $authors = array();
        foreach ($users as $user) {
            $userpicture = new user_picture($user);
            $userpicture->size = 1;

            $author = new stdClass();
            $author->id = $user->id;
            $author->fullname = fullname($user, $canviewfullnames);
            $author->pictureurl = $userpicture->get_url($PAGE)->out(false);
            $authors[] = $author;
        }
        $users->close();

        return array(
            'count' => $count,
            'authors' => $authors,
            'warnings' => array(),
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_authors_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records.'),
            'authors' => new external_multiple_structure(
                new external_single_structure(array(
                    'id' => new external_value(PARAM_INT, 'The user ID'),
                    'fullname' => new external_value(PARAM_NOTAGS, 'The fullname'),
                    'pictureurl' => new external_value(PARAM_URL, 'The picture URL'),
                ))
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_entries_by_author_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'letter' => new external_value(PARAM_ALPHA, 'First letter of firstname or lastname, or either keywords:'
                . ' \'ALL\' or \'SPECIAL\'.'),
            'field' => new external_value(PARAM_ALPHA, 'Search and order using: \'FIRSTNAME\' or \'LASTNAME\'', VALUE_DEFAULT,
                'LASTNAME'),
            'sort' => new external_value(PARAM_ALPHA, 'The direction of the order: \'ASC\' or \'DESC\'', VALUE_DEFAULT, 'ASC'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes the non-approved entries created by' .
                    ' the user. When true, also includes the ones that the user has the permission to approve.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Browse a glossary entries by author.
     *
     * @param int $id The glossary ID.
     * @param string $letter A letter, or a special keyword.
     * @param string $field The field to search from.
     * @param string $sort The direction of the order.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_entries_by_author($id, $letter, $field = 'LASTNAME', $sort = 'ASC', $from = 0, $limit = 20,
            $options = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entries_by_author_parameters(), array(
            'id' => $id,
            'letter' => $letter,
            'field' => core_text::strtoupper($field),
            'sort' => core_text::strtoupper($sort),
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $letter = $params['letter'];
        $field = $params['field'];
        $sort = $params['sort'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        if (!in_array($field, array('FIRSTNAME', 'LASTNAME'))) {
            throw new invalid_parameter_exception('invalidfield');
        } else if (!in_array($sort, array('ASC', 'DESC'))) {
            throw new invalid_parameter_exception('invalidsort');
        }

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the mode.
        $modes = self::get_browse_modes_from_display_format($glossary->displayformat);
        if (!in_array('author', $modes)) {
            throw new invalid_parameter_exception('invalidbrowsemode');
        }

        // Preparing the query.
        $where = '1 = 1';
        $params = array();

        if ($field == 'FIRSTNAME') {
            $usernamefield = $DB->sql_fullname('u.firstname' , 'u.lastname');
        } else {
            $usernamefield = $DB->sql_fullname('u.lastname' , 'u.firstname');
        }

        if ($letter != 'ALL' && $letter != 'SPECIAL' && ($letterstrlen = core_text::strlen($letter))) {
            $params['hookup'] = core_text::strtoupper($letter);
            $where = $DB->sql_substr("upper($usernamefield)", 1, $letterstrlen) . ' = :hookup';
        }
        if ($letter == 'SPECIAL') {
            $alphabet = explode(',', get_string('alphabet', 'langconfig'));
            list($nia, $aparams) = $DB->get_in_or_equal($alphabet, SQL_PARAMS_NAMED, 'a', false);
            $params = array_merge($params, $aparams);
            $where = $DB->sql_substr("upper($usernamefield)", 1, 1) . " $nia";
        }

        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        $params['myid'] = $USER->id;
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $userfields = user_picture::fields('u', null, 'userdataid', 'userdata');

        $sqlselectcount = "SELECT COUNT('x')";
        $sqlselect = "SELECT ge.*, $userfields";
        $sql = "  FROM {glossary_entries} ge
                  JOIN {user} u ON ge.userid = u.id
                 WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)
                   AND $approvedsql
                   AND $where";
        $sqlorder = " ORDER BY $usernamefield $sort, ge.concept";

        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;

        // Fetching the entries.
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $entries = $DB->get_records_sql($sqlselect . $sql . $sqlorder, $params, $from, $limit);
        foreach ($entries as $key => $entry) {
            self::fill_entry_details($entry, $context);
        }

        return array(
            'count' => $count,
            'entries' => $entries,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_entries_by_author_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records matching the request.'),
            'entries' => new external_multiple_structure(
                self::get_entry_return_structure()
            ),
            'warnings' => new external_warnings()
        ));
    }

    /**
     * Returns the description of the external function parameters.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_entries_by_author_id_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'Glossary entry ID'),
            'authorid' => new external_value(PARAM_INT, 'The author ID'),
            'order' => new external_value(PARAM_ALPHA, 'Order by: \'CONCEPT\', \'CREATION\' or \'UPDATE\'', VALUE_DEFAULT,
                'CONCEPT'),
            'sort' => new external_value(PARAM_ALPHA, 'The direction of the order: \'ASC\' or \'DESC\'', VALUE_DEFAULT, 'ASC'),
            'from' => new external_value(PARAM_INT, 'Start returning records from here', VALUE_DEFAULT, 0),
            'limit' => new external_value(PARAM_INT, 'Number of records to return', VALUE_DEFAULT, 20),
            'options' => new external_single_structure(array(
                'includenotapproved' => new external_value(PARAM_BOOL, 'When false, includes the non-approved entries created by' .
                    ' the user. When true, also includes the ones that the user has the permission to approve.', VALUE_DEFAULT, 0)
            ), 'An array of options', VALUE_DEFAULT, array())
        ));
    }

    /**
     * Browse a glossary entries by author.
     *
     * @param int $id The glossary ID.
     * @param int $authorid The author ID.
     * @param string $order The way to order the results.
     * @param string $sort The direction of the order.
     * @param int $from Start returning records from here.
     * @param int $limit Number of records to return.
     * @param array $options Array of options.
     * @return array of warnings and status result
     * @since Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_entries_by_author_id($id, $authorid, $order = 'CONCEPT', $sort = 'ASC', $from = 0, $limit = 20,
            $options = array()) {
        global $DB, $USER;

        $params = self::validate_parameters(self::get_entries_by_author_id_parameters(), array(
            'id' => $id,
            'authorid' => $authorid,
            'order' => core_text::strtoupper($order),
            'sort' => core_text::strtoupper($sort),
            'from' => $from,
            'limit' => $limit,
            'options' => $options,
        ));
        $id = $params['id'];
        $authorid = $params['authorid'];
        $order = $params['order'];
        $sort = $params['sort'];
        $from = $params['from'];
        $limit = $params['limit'];
        $options = $params['options'];
        $warnings = array();

        if (!in_array($order, array('CONCEPT', 'CREATION', 'UPDATE'))) {
            throw new invalid_parameter_exception('invalidorder');
        } else if (!in_array($sort, array('ASC', 'DESC'))) {
            throw new invalid_parameter_exception('invalidsort');
        }

        // Fetch and confirm.
        $glossary = $DB->get_record('glossary', array('id' => $id), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($glossary, 'glossary');
        $context = context_module::instance($cm->id);
        self::validate_context($context);

        // Validate the mode.
        $modes = self::get_browse_modes_from_display_format($glossary->displayformat);
        if (!in_array('author', $modes)) {
            throw new invalid_parameter_exception('invalidbrowsemode');
        }

        // Preparing the query.
        $params = array();

        $approvedsql = '(ge.approved <> 0 OR ge.userid = :myid)';
        $params['myid'] = $USER->id;
        if (!empty($options['includenotapproved']) && has_capability('mod/glossary:approve', $context)) {
            $approvedsql = '1 = 1';
        }

        $userfields = user_picture::fields('u', null, 'userdataid', 'userdata');

        $sqlselectcount = "SELECT COUNT('x')";
        $sqlselect = "SELECT ge.*, $userfields";
        $sql = "  FROM {glossary_entries} ge
                  JOIN {user} u ON ge.userid = u.id
                 WHERE (ge.glossaryid = :gid1 OR ge.sourceglossaryid = :gid2)
                   AND $approvedsql
                   AND ge.userid = :uid";
        $params['uid'] = $authorid;
        $params['gid1'] = $glossary->id;
        $params['gid2'] = $glossary->id;

        $sqlorder = ' ORDER BY ';
        if ($order == 'CREATION') {
            $sqlorder .= 'ge.timecreated';

        } else if ($order == 'UPDATE') {
            $sqlorder .= 'ge.timemodified';

        } else {
            $sqlorder .= 'ge.concept';
        }
        $sqlorder .= ' ' . $sort;

        // Fetching the entries.
        $count = $DB->count_records_sql($sqlselectcount . $sql, $params);
        $entries = $DB->get_records_sql($sqlselect . $sql . $sqlorder, $params, $from, $limit);
        foreach ($entries as $key => $entry) {
            self::fill_entry_details($entry, $context);
        }

        return array(
            'count' => $count,
            'entries' => $entries,
            'warnings' => $warnings
        );
    }

    /**
     * Returns the description of the external function return value.
     *
     * @return external_description
     * @since Moodle 3.1
     */
    public static function get_entries_by_author_id_returns() {
        return new external_single_structure(array(
            'count' => new external_value(PARAM_INT, 'The total number of records matching the request.'),
            'entries' => new external_multiple_structure(
                self::get_entry_return_structure()
            ),
            'warnings' => new external_warnings()
        ));
    }
}
