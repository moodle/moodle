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
 * Base class for conditional availability information (for module or section).
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for conditional availability information (for module or section).
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class info {
    /** @var \stdClass Course */
    protected $course;

    /** @var \course_modinfo Modinfo (available only during some functions) */
    protected $modinfo = null;

    /** @var bool Visibility flag (eye icon) */
    protected $visible;

    /** @var string Availability data as JSON string */
    protected $availability;

    /** @var tree Availability configuration, decoded from JSON; null if unset */
    protected $availabilitytree;

    /** @var array|null Array of information about current restore if any */
    protected static $restoreinfo = null;

    /**
     * Constructs with item details.
     *
     * @param \stdClass $course Course object
     * @param int $visible Value of visible flag (eye icon)
     * @param string $availability Availability definition (JSON format) or null
     * @throws \coding_exception If data is not valid JSON format
     */
    public function __construct($course, $visible, $availability) {
        // Set basic values.
        $this->course = $course;
        $this->visible = (bool)$visible;
        $this->availability = $availability;
    }

    /**
     * Obtains the course associated with this availability information.
     *
     * @return \stdClass Moodle course object
     */
    public function get_course() {
        return $this->course;
    }

    /**
     * Gets context used for checking capabilities for this item.
     *
     * @return \context Context for this item
     */
    abstract public function get_context();

    /**
     * Obtains the modinfo associated with this availability information.
     *
     * Note: This field is available ONLY for use by conditions when calculating
     * availability or information.
     *
     * @return \course_modinfo Modinfo
     * @throws \coding_exception If called at incorrect times
     */
    public function get_modinfo() {
        if (!$this->modinfo) {
            throw new \coding_exception(
                    'info::get_modinfo available only during condition checking');
        }
        return $this->modinfo;
    }

    /**
     * Gets the availability tree, decoding it if not already done.
     *
     * @return tree Availability tree
     */
    public function get_availability_tree() {
        if (is_null($this->availabilitytree)) {
            if (is_null($this->availability)) {
                throw new \coding_exception(
                        'Cannot call get_availability_tree with null availability');
            }
            $this->availabilitytree = $this->decode_availability($this->availability, true);
        }
        return $this->availabilitytree;
    }

    /**
     * Decodes availability data from JSON format.
     *
     * This function also validates the retrieved data as follows:
     * 1. Data that does not meet the API-defined structure causes a
     *    coding_exception (this should be impossible unless there is
     *    a system bug or somebody manually hacks the database).
     * 2. Data that meets the structure but cannot be implemented (e.g.
     *    reference to missing plugin or to module that doesn't exist) is
     *    either silently discarded (if $lax is true) or causes a
     *    coding_exception (if $lax is false).
     *
     * @param string $availability Availability string in JSON format
     * @param boolean $lax If true, throw exceptions only for invalid structure
     * @return tree Availability tree
     * @throws \coding_exception If data is not valid JSON format
     */
    protected function decode_availability($availability, $lax) {
        // Decode JSON data.
        $structure = json_decode($availability);
        if (is_null($structure)) {
            throw new \coding_exception('Invalid availability text', $availability);
        }

        // Recursively decode tree.
        return new tree($structure, $lax);
    }

    /**
     * Determines whether this particular item is currently available
     * according to the availability criteria.
     *
     * - This does not include the 'visible' setting (i.e. this might return
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * Depending on options selected, a description of the restrictions which
     * mean the student can't view it (in HTML format) may be stored in
     * $information. If there is nothing in $information and this function
     * returns false, then the activity should not be displayed at all.
     *
     * This function displays debugging() messages if the availability
     * information is invalid.
     *
     * @param string $information String describing restrictions in HTML format
     * @param bool $grabthelot Performance hint: if true, caches information
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid If set, specifies a different user ID to check availability for
     * @param \course_modinfo $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo()
     * @return bool True if this item is available to the user, false otherwise
     */
    public function is_available(&$information, $grabthelot = false, $userid = 0,
            \course_modinfo $modinfo = null) {
        global $USER;

        // Default to no information.
        $information = '';

        // Do nothing if there are no availability restrictions.
        if (is_null($this->availability)) {
            return true;
        }

        // Resolve optional parameters.
        if (!$userid) {
            $userid = $USER->id;
        }
        if (!$modinfo) {
            $modinfo = get_fast_modinfo($this->course, $userid);
        }
        $this->modinfo = $modinfo;

        // Get availability from tree.
        try {
            $tree = $this->get_availability_tree();
            $result = $tree->check_available(false, $this, $grabthelot, $userid);
        } catch (\coding_exception $e) {
            $this->warn_about_invalid_availability($e);
            $this->modinfo = null;
            return false;
        }

        // See if there are any messages.
        if ($result->is_available()) {
            $this->modinfo = null;
            return true;
        } else {
            // If the item is marked as 'not visible' then we don't change the available
            // flag (visible/available are treated distinctly), but we remove any
            // availability info. If the item is hidden with the eye icon, it doesn't
            // make sense to show 'Available from <date>' or similar, because even
            // when that date arrives it will still not be available unless somebody
            // toggles the eye icon.
            if ($this->visible) {
                $information = $tree->get_result_information($this, $result);
            }

            $this->modinfo = null;
            return false;
        }
    }

    /**
     * Checks whether this activity is going to be available for all users.
     *
     * Normally, if there are any conditions, then it may be hidden depending
     * on the user. However in the case of date conditions there are some
     * conditions which will definitely not result in it being hidden for
     * anyone.
     *
     * @return bool True if activity is available for all
     */
    public function is_available_for_all() {
        global $CFG;
        if (is_null($this->availability) || empty($CFG->enableavailability)) {
            return true;
        } else {
            try {
                return $this->get_availability_tree()->is_available_for_all();
            } catch (\coding_exception $e) {
                $this->warn_about_invalid_availability($e);
                return false;
            }
        }
    }

    /**
     * Obtains a string describing all availability restrictions (even if
     * they do not apply any more). Used to display information for staff
     * editing the website.
     *
     * The modinfo parameter must be specified when it is called from inside
     * get_fast_modinfo, to avoid infinite recursion.
     *
     * This function displays debugging() messages if the availability
     * information is invalid.
     *
     * @param \course_modinfo $modinfo Usually leave as null for default
     * @return string Information string (for admin) about all restrictions on
     *   this item
     */
    public function get_full_information(\course_modinfo $modinfo = null) {
        // Do nothing if there are no availability restrictions.
        if (is_null($this->availability)) {
            return '';
        }

        // Resolve optional parameter.
        if (!$modinfo) {
            $modinfo = get_fast_modinfo($this->course);
        }
        $this->modinfo = $modinfo;

        try {
            $result = $this->get_availability_tree()->get_full_information($this);
            $this->modinfo = null;
            return $result;
        } catch (\coding_exception $e) {
            $this->warn_about_invalid_availability($e);
            return false;
        }
    }

    /**
     * In some places we catch coding_exception because if a bug happens, it
     * would be fatal for the course page GUI; instead we just show a developer
     * debug message.
     *
     * @param \coding_exception $e Exception that occurred
     */
    protected function warn_about_invalid_availability(\coding_exception $e) {
        $name = $this->get_thing_name();
        $htmlname = $this->format_info($name, $this->course);
        // Because we call format_info here, likely in the middle of building dynamic data for the
        // activity, there could be a chance that the name might not be available.
        if ($htmlname === '') {
            // So instead use the numbers (cmid) from the tag.
            $htmlname = preg_replace('~[^0-9]~', '', $name);
        }
        $htmlname = html_to_text($htmlname, 75, false);
        $info = 'Error processing availability data for &lsquo;' . $htmlname
                 . '&rsquo;: ' . s($e->a);
        debugging($info, DEBUG_DEVELOPER);
    }

    /**
     * Called during restore (near end of restore). Updates any necessary ids
     * and writes the updated tree to the database. May output warnings if
     * necessary (e.g. if a course-module cannot be found after restore).
     *
     * @param string $restoreid Restore identifier
     * @param int $courseid Target course id
     * @param \base_logger $logger Logger for any warnings
     * @param int $dateoffset Date offset to be added to any dates (0 = none)
     * @param \base_task $task Restore task
     */
    public function update_after_restore($restoreid, $courseid, \base_logger $logger,
            $dateoffset, \base_task $task) {
        $tree = $this->get_availability_tree();
        // Set static data for use by get_restore_date_offset function.
        self::$restoreinfo = array('restoreid' => $restoreid, 'dateoffset' => $dateoffset,
                'task' => $task);
        $changed = $tree->update_after_restore($restoreid, $courseid, $logger,
                $this->get_thing_name());
        if ($changed) {
            // Save modified data.
            if ($tree->is_empty()) {
                // If the tree is empty, but the tree has changed, remove this condition.
                $this->set_in_database(null);
            } else {
                $structure = $tree->save();
                $this->set_in_database(json_encode($structure));
            }
        }
    }

    /**
     * Gets the date offset (amount by which any date values should be
     * adjusted) for the current restore.
     *
     * @param string $restoreid Restore identifier
     * @return int Date offset (0 if none)
     * @throws coding_exception If not in a restore (or not in that restore)
     */
    public static function get_restore_date_offset($restoreid) {
        if (!self::$restoreinfo) {
            throw new coding_exception('Only valid during restore');
        }
        if (self::$restoreinfo['restoreid'] !== $restoreid) {
            throw new coding_exception('Data not available for that restore id');
        }
        return self::$restoreinfo['dateoffset'];
    }

    /**
     * Gets the restore task (specifically, the task that calls the
     * update_after_restore method) for the current restore.
     *
     * @param string $restoreid Restore identifier
     * @return \base_task Restore task
     * @throws coding_exception If not in a restore (or not in that restore)
     */
    public static function get_restore_task($restoreid) {
        if (!self::$restoreinfo) {
            throw new coding_exception('Only valid during restore');
        }
        if (self::$restoreinfo['restoreid'] !== $restoreid) {
            throw new coding_exception('Data not available for that restore id');
        }
        return self::$restoreinfo['task'];
    }

    /**
     * Obtains the name of the item (cm_info or section_info, at present) that
     * this is controlling availability of. Name should be formatted ready
     * for on-screen display.
     *
     * @return string Name of item
     */
    abstract protected function get_thing_name();

    /**
     * Stores an updated availability tree JSON structure into the relevant
     * database table.
     *
     * @param string $availabilty New JSON value
     */
    abstract protected function set_in_database($availabilty);

    /**
     * In rare cases the system may want to change all references to one ID
     * (e.g. one course-module ID) to another one, within a course. This
     * function does that for the conditional availability data for all
     * modules and sections on the course.
     *
     * @param int|\stdClass $courseorid Course id or object
     * @param string $table Table name e.g. 'course_modules'
     * @param int $oldid Previous ID
     * @param int $newid New ID
     * @return bool True if anything changed, otherwise false
     */
    public static function update_dependency_id_across_course(
            $courseorid, $table, $oldid, $newid) {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $modinfo = get_fast_modinfo($courseorid);
        $anychanged = false;
        foreach ($modinfo->get_cms() as $cm) {
            $info = new info_module($cm);
            $changed = $info->update_dependency_id($table, $oldid, $newid);
            $anychanged = $anychanged || $changed;
        }
        foreach ($modinfo->get_section_info_all() as $section) {
            $info = new info_section($section);
            $changed = $info->update_dependency_id($table, $oldid, $newid);
            $anychanged = $anychanged || $changed;
        }
        $transaction->allow_commit();
        if ($anychanged) {
            get_fast_modinfo($courseorid, 0, true);
        }
        return $anychanged;
    }

    /**
     * Called on a single item. If necessary, updates availability data where
     * it has a dependency on an item with a particular id.
     *
     * @param string $table Table name e.g. 'course_modules'
     * @param int $oldid Previous ID
     * @param int $newid New ID
     * @return bool True if it changed, otherwise false
     */
    protected function update_dependency_id($table, $oldid, $newid) {
        // Do nothing if there are no availability restrictions.
        if (is_null($this->availability)) {
            return false;
        }
        // Pass requirement on to tree object.
        $tree = $this->get_availability_tree();
        $changed = $tree->update_dependency_id($table, $oldid, $newid);
        if ($changed) {
            // Save modified data.
            $structure = $tree->save();
            $this->set_in_database(json_encode($structure));
        }
        return $changed;
    }

    /**
     * Converts legacy data from fields (if provided) into the new availability
     * syntax.
     *
     * Supported fields: availablefrom, availableuntil, showavailability
     * (and groupingid for sections).
     *
     * It also supports the groupmembersonly field for modules. This part was
     * optional in 2.7 but now always runs (because groupmembersonly has been
     * removed).
     *
     * @param \stdClass $rec Object possibly containing legacy fields
     * @param bool $section True if this is a section
     * @param bool $modgroupmembersonlyignored Ignored option, previously used
     * @return string|null New availability value or null if none
     */
    public static function convert_legacy_fields($rec, $section, $modgroupmembersonlyignored = false) {
        // Do nothing if the fields are not set.
        if (empty($rec->availablefrom) && empty($rec->availableuntil) &&
                (empty($rec->groupmembersonly)) &&
                (!$section || empty($rec->groupingid))) {
            return null;
        }

        // Handle legacy availability data.
        $conditions = array();
        $shows = array();

        // Groupmembersonly condition (if enabled) for modules, groupingid for
        // sections.
        if (!empty($rec->groupmembersonly) ||
                (!empty($rec->groupingid) && $section)) {
            if (!empty($rec->groupingid)) {
                $conditions[] = '{"type":"grouping"' .
                        ($rec->groupingid ? ',"id":' . $rec->groupingid : '') . '}';
            } else {
                // No grouping specified, so allow any group.
                $conditions[] = '{"type":"group"}';
            }
            // Group members only condition was not displayed to students.
            $shows[] = 'false';
        }

        // Date conditions.
        if (!empty($rec->availablefrom)) {
            $conditions[] = '{"type":"date","d":">=","t":' . $rec->availablefrom . '}';
            $shows[] = !empty($rec->showavailability) ? 'true' : 'false';
        }
        if (!empty($rec->availableuntil)) {
            $conditions[] = '{"type":"date","d":"<","t":' . $rec->availableuntil . '}';
            // Until dates never showed to students.
            $shows[] = 'false';
        }

        // If there are some conditions, return them.
        if ($conditions) {
            return '{"op":"&","showc":[' . implode(',', $shows) . '],' .
                    '"c":[' . implode(',', $conditions) . ']}';
        } else {
            return null;
        }
    }

    /**
     * Adds a condition from the legacy availability condition.
     *
     * (For use during restore only.)
     *
     * This function assumes that the activity either has no conditions, or
     * that it has an AND tree with one or more conditions.
     *
     * @param string|null $availability Current availability conditions
     * @param \stdClass $rec Object containing information from old table
     * @param bool $show True if 'show' option should be enabled
     * @return string New availability conditions
     */
    public static function add_legacy_availability_condition($availability, $rec, $show) {
        if (!empty($rec->sourcecmid)) {
            // Completion condition.
            $condition = '{"type":"completion","cm":' . $rec->sourcecmid .
                    ',"e":' . $rec->requiredcompletion . '}';
        } else {
            // Grade condition.
            $minmax = '';
            if (!empty($rec->grademin)) {
                $minmax .= ',"min":' . sprintf('%.5f', $rec->grademin);
            }
            if (!empty($rec->grademax)) {
                $minmax .= ',"max":' . sprintf('%.5f', $rec->grademax);
            }
            $condition = '{"type":"grade","id":' . $rec->gradeitemid . $minmax . '}';
        }

        return self::add_legacy_condition($availability, $condition, $show);
    }

    /**
     * Adds a condition from the legacy availability field condition.
     *
     * (For use during restore only.)
     *
     * This function assumes that the activity either has no conditions, or
     * that it has an AND tree with one or more conditions.
     *
     * @param string|null $availability Current availability conditions
     * @param \stdClass $rec Object containing information from old table
     * @param bool $show True if 'show' option should be enabled
     * @return string New availability conditions
     */
    public static function add_legacy_availability_field_condition($availability, $rec, $show) {
        if (isset($rec->userfield)) {
            // Standard field.
            $fieldbit = ',"sf":' . json_encode($rec->userfield);
        } else {
            // Custom field.
            $fieldbit = ',"cf":' . json_encode($rec->shortname);
        }
        // Value is not included for certain operators.
        switch($rec->operator) {
            case 'isempty':
            case 'isnotempty':
                $valuebit = '';
                break;

            default:
                $valuebit = ',"v":' . json_encode($rec->value);
                break;
        }
        $condition = '{"type":"profile","op":"' . $rec->operator . '"' .
                $fieldbit . $valuebit . '}';

        return self::add_legacy_condition($availability, $condition, $show);
    }

    /**
     * Adds a condition to an AND group.
     *
     * (For use during restore only.)
     *
     * This function assumes that the activity either has no conditions, or
     * that it has only conditions added by this function.
     *
     * @param string|null $availability Current availability conditions
     * @param string $condition Condition text '{...}'
     * @param bool $show True if 'show' option should be enabled
     * @return string New availability conditions
     */
    protected static function add_legacy_condition($availability, $condition, $show) {
        $showtext = ($show ? 'true' : 'false');
        if (is_null($availability)) {
            $availability = '{"op":"&","showc":[' . $showtext .
                    '],"c":[' . $condition . ']}';
        } else {
            $matches = array();
            if (!preg_match('~^({"op":"&","showc":\[(?:true|false)(?:,(?:true|false))*)' .
                    '(\],"c":\[.*)(\]})$~', $availability, $matches)) {
                throw new \coding_exception('Unexpected availability value');
            }
            $availability = $matches[1] . ',' . $showtext . $matches[2] .
                    ',' . $condition . $matches[3];
        }
        return $availability;
    }

    /**
     * Tests against a user list. Users who cannot access the activity due to
     * availability restrictions will be removed from the list.
     *
     * Note this only includes availability restrictions (those handled within
     * this API) and not other ways of restricting access.
     *
     * This test ONLY includes conditions which are marked as being applied to
     * user lists. For example, group conditions are included but date
     * conditions are not included.
     *
     * The function operates reasonably efficiently i.e. should not do per-user
     * database queries. It is however likely to be fairly slow.
     *
     * @param array $users Array of userid => object
     * @return array Filtered version of input array
     */
    public function filter_user_list(array $users) {
        global $CFG;
        if (is_null($this->availability) || !$CFG->enableavailability) {
            return $users;
        }
        $tree = $this->get_availability_tree();
        $checker = new capability_checker($this->get_context());

        // Filter using availability tree.
        $this->modinfo = get_fast_modinfo($this->get_course());
        $filtered = $tree->filter_user_list($users, false, $this, $checker);
        $this->modinfo = null;

        // Include users in the result if they're either in the filtered list,
        // or they have viewhidden. This logic preserves ordering of the
        // passed users array.
        $result = array();
        $canviewhidden = $checker->get_users_by_capability($this->get_view_hidden_capability());
        foreach ($users as $userid => $data) {
            if (array_key_exists($userid, $filtered) || array_key_exists($userid, $canviewhidden)) {
                $result[$userid] = $users[$userid];
            }
        }

        return $result;
    }

    /**
     * Gets the capability used to view hidden activities/sections (as
     * appropriate).
     *
     * @return string Name of capability used to view hidden items of this type
     */
    abstract protected function get_view_hidden_capability();

    /**
     * Obtains SQL that returns a list of enrolled users that has been filtered
     * by the conditions applied in the availability API, similar to calling
     * get_enrolled_users and then filter_user_list. As for filter_user_list,
     * this ONLY filters out users with conditions that are marked as applying
     * to user lists. For example, group conditions are included but date
     * conditions are not included.
     *
     * The returned SQL is a query that returns a list of user IDs. It does not
     * include brackets, so you neeed to add these to make it into a subquery.
     * You would normally use it in an SQL phrase like "WHERE u.id IN ($sql)".
     *
     * The function returns an array with '' and an empty array, if there are
     * no restrictions on users from these conditions.
     *
     * The SQL will be complex and may be slow. It uses named parameters (sorry,
     * I know they are annoying, but it was unavoidable here).
     *
     * @param bool $onlyactive True if including only active enrolments
     * @return array Array of SQL code (may be empty) and params
     */
    public function get_user_list_sql($onlyactive) {
        global $CFG;
        if (is_null($this->availability) || !$CFG->enableavailability) {
            return array('', array());
        }

        // Get SQL for the availability filter.
        $tree = $this->get_availability_tree();
        list ($filtersql, $filterparams) = $tree->get_user_list_sql(false, $this, $onlyactive);
        if ($filtersql === '') {
            // No restrictions, so return empty query.
            return array('', array());
        }

        // Get SQL for the view hidden list.
        list ($viewhiddensql, $viewhiddenparams) = get_enrolled_sql(
                $this->get_context(), $this->get_view_hidden_capability(), 0, $onlyactive);

        // Result is a union of the two.
        return array('(' . $filtersql . ') UNION (' . $viewhiddensql . ')',
                array_merge($filterparams, $viewhiddenparams));
    }

    /**
     * Formats the $cm->availableinfo string for display. This includes
     * filling in the names of any course-modules that might be mentioned.
     * Should be called immediately prior to display, or at least somewhere
     * that we can guarantee does not happen from within building the modinfo
     * object.
     *
     * @param \renderable|string $inforenderable Info string or renderable
     * @param int|\stdClass $courseorid
     * @return string Correctly formatted info string
     */
    public static function format_info($inforenderable, $courseorid) {
        global $PAGE, $OUTPUT;

        // Use renderer if required.
        if (is_string($inforenderable)) {
            $info = $inforenderable;
        } else {
            $renderable = new \core_availability\output\availability_info($inforenderable);
            $info = $OUTPUT->render($renderable);
        }

        // Don't waste time if there are no special tags.
        if (strpos($info, '<AVAILABILITY_') === false) {
            return $info;
        }

        // Handle CMNAME tags.
        $modinfo = get_fast_modinfo($courseorid);
        $context = \context_course::instance($modinfo->courseid);
        $info = preg_replace_callback('~<AVAILABILITY_CMNAME_([0-9]+)/>~',
                function($matches) use($modinfo, $context) {
                    $cm = $modinfo->get_cm($matches[1]);
                    $modulename = format_string($cm->get_name(), true, ['context' => $context]);
                    // We make sure that we add a data attribute to the name so we can change it later if the
                    // original module name changes.
                    if ($cm->has_view() && $cm->get_user_visible()) {
                        // Help student by providing a link to the module which is preventing availability.
                        return \html_writer::link($cm->get_url(), $modulename, ['data-cm-name-for' => $cm->id]);
                    } else {
                        return \html_writer::span($modulename, '', ['data-cm-name-for' => $cm->id]);
                    }
                }, $info);
        $info = preg_replace_callback('~<AVAILABILITY_FORMAT_STRING>(.*?)</AVAILABILITY_FORMAT_STRING>~s',
                function($matches) use ($context) {
                    $decoded = htmlspecialchars_decode($matches[1], ENT_NOQUOTES);
                    return format_string($decoded, true, ['context' => $context]);
                }, $info);
        $info = preg_replace_callback('~<AVAILABILITY_CALLBACK type="([a-z0-9_]+)">(.*?)</AVAILABILITY_CALLBACK>~s',
                function($matches) use ($modinfo, $context) {
                    // Find the class, it must have already been loaded by now.
                    $fullclassname = 'availability_' . $matches[1] . '\condition';
                    if (!class_exists($fullclassname, false)) {
                        return '<!-- Error finding class ' . $fullclassname .' -->';
                    }
                    // Load the parameters.
                    $params = [];
                    $encodedparams = preg_split('~<P/>~', $matches[2], 0);
                    foreach ($encodedparams as $encodedparam) {
                        $params[] = htmlspecialchars_decode($encodedparam, ENT_NOQUOTES);
                    }
                    return $fullclassname::get_description_callback_value($modinfo, $context, $params);
                }, $info);

        return $info;
    }

    /**
     * Used in course/lib.php because we need to disable the completion tickbox
     * JS (using the non-JS version instead, which causes a page reload) if a
     * completion tickbox value may affect a conditional activity.
     *
     * @param \stdClass $course Moodle course object
     * @param int $cmid Course-module id
     * @return bool True if this is used in a condition, false otherwise
     */
    public static function completion_value_used($course, $cmid) {
        // Access all plugins. Normally only the completion plugin is going
        // to affect this value, but it's potentially possible that some other
        // plugin could also rely on the completion plugin.
        $pluginmanager = \core_plugin_manager::instance();
        $enabled = $pluginmanager->get_enabled_plugins('availability');
        $componentparams = new \stdClass();
        foreach ($enabled as $plugin => $info) {
            // Use the static method.
            $class = '\availability_' . $plugin . '\condition';
            if ($class::completion_value_used($course, $cmid)) {
                return true;
            }
        }
        return false;
    }
}
