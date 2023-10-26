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

namespace tool_brickfield;

/**
 * Class manager
 * @package tool_brickfield
 * @copyright  2021 Brickfield Education Labs https://www.brickfield.ie
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * Defines the waiting for analysis status.
     */
    const STATUS_WAITING = 0;

    /**
     * Defined the analysis in progress status.
     */
    const STATUS_INPROGRESS = -1;

    /**
     * Defines the analysis has completed status.
     */
    const STATUS_CHECKED = 1;

    /**
     * Defines summary error value.
     */
    const SUMMARY_ERROR = 0;

    /**
     * Defines summary failed value.
     */
    const SUMMARY_FAILED = 1;

    /**
     * Defines summary percent value.
     */
    const SUMMARY_PERCENT = 2;

    /**
     * Default bulk record limit.
     */
    const BULKRECORDLIMIT = 1000;

    /**
     * Name of this plugin.
     */
    const PLUGINNAME = 'tool_brickfield';

    /**
     * Areas table name.
     */
    const DB_AREAS = self::PLUGINNAME . '_areas';

    /**
     * Cacheacts table name.
     */
    const DB_CACHEACTS = self::PLUGINNAME . '_cache_acts';

    /**
     * Cachecheck table name.
     */
    const DB_CACHECHECK = self::PLUGINNAME . '_cache_check';

    /**
     * Checks table name.
     */
    const DB_CHECKS = self::PLUGINNAME . '_checks';

    /**
     * Content table name.
     */
    const DB_CONTENT = self::PLUGINNAME . '_content';

    /**
     * Errors table name.
     */
    const DB_ERRORS = self::PLUGINNAME . '_errors';

    /**
     * Process table name.
     */
    const DB_PROCESS = self::PLUGINNAME . '_process';

    /**
     * Results table name.
     */
    const DB_RESULTS = self::PLUGINNAME . '_results';

    /**
     * Schedule table name.
     */
    const DB_SCHEDULE = self::PLUGINNAME . '_schedule';

    /**
     * Summary table name.
     */
    const DB_SUMMARY = self::PLUGINNAME . '_summary';

    /** @var string The URL to find help at. */
    private static $helpurl = 'https://www.brickfield.ie/moodle-help-311';


    /** @var  array Statically stores the database checks records. */
    static protected $checks;

    /**
     * Returns the URL used for registration.
     *
     * @return \moodle_url
     */
    public static function registration_url(): \moodle_url {
        return accessibility::get_plugin_url('registration.php');
    }

    /**
     * Returns an appropriate message about the current registration state.
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function registration_message(): string {
        $firstline = get_string('notregistered', self::PLUGINNAME);
        if (has_capability('moodle/site:config', \context_system::instance())) {
            $secondline = \html_writer::link(self::registration_url(), get_string('registernow', self::PLUGINNAME));
        } else {
            $secondline = get_string('contactadmin', self::PLUGINNAME);
        }
        return $firstline . '<br />' . $secondline;
    }

    /**
     * Get the help page URL.
     * @return string
     * @throws dml_exception
     */
    public static function get_helpurl(): string {
        return self::$helpurl;
    }

    /**
     * Return an array of system checks available, and store them statically.
     *
     * @return array
     * @throws \dml_exception
     */
    public static function get_checks(): array {
        global $DB;
        if (self::$checks === null) {
            self::$checks = $DB->get_records(self::DB_CHECKS, [] , 'id');
        }
        return self::$checks;
    }

    /**
     * Find all available areas.
     *
     * @return area_base[]
     * @throws \ReflectionException
     */
    public static function get_all_areas(): array {
        return array_filter(
            array_map(
                function($classname) {
                    $reflectionclass = new \ReflectionClass($classname);
                    if ($reflectionclass->isAbstract()) {
                        return false;
                    }
                    $instance = new $classname();

                    if ($instance->is_available()) {
                        return $instance;
                    } else {
                        return null;
                    }
                },
                array_keys(\core_component::get_component_classes_in_namespace('tool_brickfield', 'local\areas'))
            )
        );
    }

    /**
     * Calculate contenthash of a given content string
     *
     * @param string|null $content
     * @return string
     */
    public static function get_contenthash(?string $content = null): string {
        return sha1($content ?? '');
    }

    /**
     * Does the current area content need to be scheduled for check?
     *
     * It does not need to be scheduled if:
     * - it is the current content
     * OR
     * - there is already schedule
     *
     * @param int $areaid
     * @param string $contenthash
     * @return bool
     * @throws \dml_exception
     */
    protected static function content_needs_scheduling(int $areaid, string $contenthash): bool {
        global $DB;
        return ! $DB->get_field_sql('SELECT 1 FROM {' . self::DB_CONTENT . '} '.
            'WHERE areaid = ?
            AND (status = 0 OR (iscurrent = 1 AND contenthash = ?))',
            [$areaid, $contenthash], IGNORE_MULTIPLE);
    }

    /**
     * Schedule an area for analysis if there has been changes.
     *
     * @param \stdClass $arearecord record with the fields from the {tool_brickfield_areas} table
     *    as returned by area_base::find_relevant_areas().
     *    It also contains the 'content' property with the current area content
     * @throws \dml_exception
     */
    protected static function schedule_area_if_necessary(\stdClass $arearecord) {
        global $DB;

        $contenthash = static::get_contenthash($arearecord->content);
        $searchparams = array_diff_key((array)$arearecord, ['content' => 1, 'reftable' => 1, 'refid' => 1]);
        if ($dbrecord = $DB->get_record(self::DB_AREAS, $searchparams)) {
            if ( ! static::content_needs_scheduling($dbrecord->id, $contenthash)) {
                // This is already the latest content record or there is already scheduled record, nothing to do.
                return;
            }
        } else {
            $dbrecord = (object)array_diff_key((array)$arearecord, ['content' => 1]);
            $dbrecord->id = $DB->insert_record(self::DB_AREAS, $dbrecord);
        }
        // Schedule the area for the check. Note that we do not record the contenthash, we will calculate it again
        // during the actual check.
        $DB->insert_record(self::DB_CONTENT,
            (object)['areaid' => $dbrecord->id, 'contenthash' => '', 'timecreated' => time(),
                'status' => self::STATUS_WAITING]);
    }

    /**
     * Asks all area providers if they have any areas that might have changed as a result of an event and schedules them
     *
     * @param \core\event\base $event
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function find_new_or_updated_areas(\core\event\base $event) {
        foreach (static::get_all_areas() as $area) {
            if ($records = $area->find_relevant_areas($event)) {
                foreach ($records as $record) {
                    static::schedule_area_if_necessary($record);
                }
                $records->close();
            }
        }
    }

    /**
     * Returns the current content of the area.
     *
     * @param \stdClass $arearecord record from the tool_brickfield_areas table
     * @return array|null array where the first element is the value of the field and the second element
     *    is the 'format' for this field if it is present. If the record was not found null is returned.
     * @throws \ddl_exception
     * @throws \ddl_table_missing_exception
     * @throws \dml_exception
     */
    protected static function get_area_content(\stdClass $arearecord): array {
        global $DB;
        if ($arearecord->type == area_base::TYPE_FIELD) {
            $tablename = $arearecord->tablename;
            $fieldname = $arearecord->fieldorarea;
            $itemid = $arearecord->itemid;

            if (!$DB->get_manager()->table_exists($tablename)) {
                return [];
            }
            if (!$DB->get_manager()->field_exists($tablename, $fieldname)) {
                return [];
            }
            $fields = $fieldname;
            if ($DB->get_manager()->field_exists($tablename, $fieldname . 'format')) {
                $fields .= ',' . $fieldname . 'format';
            }
            if ($record = $DB->get_record($tablename, ['id' => $itemid], $fields)) {
                return array_values((array)$record);
            }
        }
        return [];
    }

    /**
     * Asks all area providers if they have any areas that might have changed per courseid and schedules them.
     *
     * @param int $courseid
     * @throws \ReflectionException
     * @throws \coding_exception
     * @throws \ddl_exception
     * @throws \ddl_table_missing_exception
     * @throws \dml_exception
     */
    public static function find_new_or_updated_areas_per_course(int $courseid) {
        $totalcount = 0;
        foreach (static::get_all_areas() as $area) {
            if ($records = $area->find_course_areas($courseid)) {
                foreach ($records as $record) {
                    $totalcount++;
                    static::schedule_area_if_necessary($record);
                }
                $records->close();
            }
            // For a site course request, also process the site level areas.
            if (($courseid == SITEID) && ($records = $area->find_system_areas())) {
                foreach ($records as $record) {
                    $totalcount++;
                    // Currently, the courseid in the area table is null if there is a category id.
                    if (!empty($record->categoryid)) {
                        $record->courseid = null;
                    }
                    static::schedule_area_if_necessary($record);
                }
                $records->close();
            }
        }
        // Need to run for total count of areas.
        static::check_scheduled_areas($totalcount);
    }

    /**
     * Finds all areas that are waiting to be checked, performs checks. Returns true if there were records processed, false if not.
     * To be called from scheduled task
     *
     * @param int $batch
     * @return bool
     * @throws \coding_exception
     * @throws \ddl_exception
     * @throws \ddl_table_missing_exception
     * @throws \dml_exception
     */
    public static function check_scheduled_areas(int $batch = 0): bool {
        global $DB;

        $processingtime = 0;
        $resultstime = 0;

        $config = get_config(self::PLUGINNAME);
        if ($batch == 0) {
            $batch = $config->batch;
        }
        // Creating insert array for courseid cache reruns.
        $recordsfound = false;
        $batchinserts = [];
        echo("Batch amount is ".$batch.", starttime ".time()."\n");
        $rs = $DB->get_recordset_sql('SELECT a.*, ch.id AS contentid
            FROM {' . self::DB_AREAS. '} a
            JOIN {' . self::DB_CONTENT . '} ch ON ch.areaid = a.id
            WHERE ch.status = ?
            ORDER BY a.id, ch.timecreated, ch.id',
            [self::STATUS_WAITING], 0, $batch);

        foreach ($rs as $arearecord) {
            $recordsfound = true;
            $DB->set_field(self::DB_CONTENT, 'status', self::STATUS_INPROGRESS, ['id' => $arearecord->contentid]);
            $content = static::get_area_content($arearecord);
            if ($content[0] == null) {
                $content[0] = '';
            }
            accessibility::run_check($content[0], $arearecord->contentid, $processingtime, $resultstime);

            // Set all content 'iscurrent' fields for this areaid to 0.
            $DB->set_field(self::DB_CONTENT, 'iscurrent', 0, ['areaid' => $arearecord->id]);
            // Update this content record to be the current record.
            $DB->update_record(self::DB_CONTENT,
                (object)['id' => $arearecord->contentid, 'status' => self::STATUS_CHECKED, 'timechecked' => time(),
                    'contenthash' => static::get_contenthash($content[0]), 'iscurrent' => 1]);

            // If full caching has been run, then queue, if not in queue already.
            if (($arearecord->courseid != null) && static::is_okay_to_cache() &&
                !isset($batchinserts[$arearecord->courseid])) {
                $batchinserts[$arearecord->courseid] = ['courseid' => $arearecord->courseid, 'item' => 'cache'];
            }
        }

        if (count($batchinserts) > 0) {
            $DB->insert_records(self::DB_PROCESS, $batchinserts);
        }

        mtrace('Total time in htmlchecker: ' . $processingtime . ' secs.');
        mtrace('Total time in results: ' . $resultstime . ' secs.');
        return $recordsfound;
    }

    /**
     * Return true if analysis hasn't been disabled.
     * @return bool
     * @throws \dml_exception
     */
    public static function is_okay_to_cache(): bool {
        return (analysis::type_is_byrequest());
    }

    /**
     * Finds all areas that are waiting to be deleted, performs deletions.
     *
     * @param int $batch limit, can be called from runcli.php
     * To be called from scheduled task
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function check_scheduled_deletions(int $batch = 0) {
        global $DB;

        $config = get_config(self::PLUGINNAME);
        if ($batch == 0) {
            $batch = $config->batch;
        }

        // Creating insert array for courseid cache reruns.
        $batchinserts = [];

        $rs = $DB->get_recordset(self::DB_PROCESS, ['contextid' => -1, 'timecompleted' => 0], '', '*', 0, $batch);

        foreach ($rs as $record) {

            if ($record->item == "core_course") {
                $tidyparams = ['courseid' => $record->courseid];
                static::delete_summary_data($record->courseid); // Delete cache too.
            } else if ($record->item == "course_categories") {
                $tidyparams = ['component' => 'core_course', 'categoryid' => $record->innercontextid];
            } else if ($record->item == "course_sections") {
                // Locate course sections, using innercontextid, contextid set to -1 for delete.
                $tidyparams = ['courseid' => $record->courseid, 'component' => 'core_course',
                    'tablename' => $record->item, 'itemid' => $record->innercontextid];
            } else if ($record->item == "lesson_pages") {
                // Locate lesson pages, using innercontextid, contextid set to -1 for delete.
                $tidyparams = ['courseid' => $record->courseid, 'component' => 'mod_lesson',
                    'tablename' => $record->item, 'itemid' => $record->innercontextid];
            } else if ($record->item == "book_chapters") {
                // Locate book chapters, using innercontextid, contextid set to -1 for delete.
                $tidyparams = ['courseid' => $record->courseid, 'component' => 'mod_book',
                    'tablename' => $record->item, 'itemid' => $record->innercontextid];
            } else if ($record->item == "question") {
                // Locate question areas, using innercontextid, contextid set to -1 for delete.
                $tidyparams = [
                    'courseid' => $record->courseid, 'component' => 'core_question',
                    'tablename' => $record->item, 'itemid' => $record->innercontextid
                ];
            } else {
                // Locate specific module instance, using innercontextid, contextid set to -1 for delete.
                $tidyparams = ['courseid' => $record->courseid, 'component' => $record->item,
                    'itemid' => $record->innercontextid];
            }

            $areas = $DB->get_records(self::DB_AREAS, $tidyparams);
            foreach ($areas as $area) {
                static::delete_area_tree($area);
            }

            $DB->delete_records(self::DB_PROCESS, ['id' => $record->id]);

            // If full caching has been run, then queue, if not in queue already.
            if ($record->courseid != null && static::is_okay_to_cache() && !isset($batchinserts[$record->courseid])) {
                $batchinserts[$record->courseid] = ['courseid' => $record->courseid, 'item' => 'cache'];
            }
        }
        $rs->close();

        if (count($batchinserts) > 0) {
            $DB->insert_records(self::DB_PROCESS, $batchinserts);
        }
    }

    /**
     * Checks all queued course updates, and finds all relevant areas.
     *
     * @param int $batch limit
     * To be called from scheduled task
     * @throws \ReflectionException
     * @throws \dml_exception
     */
    public static function check_course_updates(int $batch = 0) {
        global $DB;

        if ($batch == 0) {
            $config = get_config(self::PLUGINNAME);
            $batch = $config->batch;
        }

        $recs = $DB->get_records(self::DB_PROCESS, ['item' => 'coursererun'], '', 'DISTINCT courseid', 0, $batch);

        foreach ($recs as $record) {
            static::find_new_or_updated_areas_per_course($record->courseid);
            $DB->delete_records(self::DB_PROCESS, ['courseid' => $record->courseid, 'item' => 'coursererun']);
            static::store_result_summary($record->courseid);
        }
    }

    /**
     * Finds all records for a given content area and performs deletions.
     *
     * To be called from scheduled task
     * @param \stdClass $area
     * @throws \dml_exception
     */
    public static function delete_area_tree(\stdClass $area) {
        global $DB;

        $contents = $DB->get_records(self::DB_CONTENT, ['areaid' => $area->id]);
        foreach ($contents as $content) {
            $results = $DB->get_records(self::DB_RESULTS, ['contentid' => $content->id]);
            foreach ($results as $result) {
                $DB->delete_records(self::DB_ERRORS, ['resultid' => $result->id]);
            }
            $DB->delete_records(self::DB_RESULTS, ['contentid' => $content->id]);
        }

        // Also, delete all child areas, if existing.
        $childparams = ['type' => $area->type, 'reftable' => $area->tablename,
            'refid' => $area->itemid];
        $childareas = $DB->get_records(self::DB_AREAS, $childparams);
        foreach ($childareas as $childarea) {
            static::delete_area_tree($childarea);
        }

        $DB->delete_records(self::DB_CONTENT, ['areaid' => $area->id]);
        $DB->delete_records(self::DB_AREAS, ['id' => $area->id]);
    }

    /**
     * Finds all records which are no longer current and performs deletions.
     *
     * To be called from scheduled task.
     */
    public static function delete_historical_data() {
        global $DB;

        $config = get_config(self::PLUGINNAME);

        if ($config->deletehistoricaldata) {
            $contents = $DB->get_records(self::DB_CONTENT, ['iscurrent' => 0, 'status' => self::STATUS_CHECKED]);
            foreach ($contents as $content) {
                $results = $DB->get_records(self::DB_RESULTS, ['contentid' => $content->id]);
                foreach ($results as $result) {
                    $DB->delete_records(self::DB_ERRORS, ['resultid' => $result->id]);
                }
                $DB->delete_records(self::DB_RESULTS, ['contentid' => $content->id]);
                $DB->delete_records(self::DB_CONTENT, ['id' => $content->id]);
            }
        }
    }

    /**
     * Finds all summary cache records for a given courseid and performs deletions.
     * To be called from scheduled task.
     *
     * @param int $courseid
     * @throws \dml_exception
     */
    public static function delete_summary_data(int $courseid) {
        global $DB;

        if ($courseid == null) {
            mtrace('Attempting to run delete_summary_data with no courseid, returning');
            return;
        }

        $DB->delete_records(self::DB_SUMMARY, ['courseid' => $courseid]);
        $DB->delete_records(self::DB_CACHECHECK, ['courseid' => $courseid]);
        $DB->delete_records(self::DB_CACHEACTS, ['courseid' => $courseid]);
    }

    /**
     * Finds all results required to display accessibility report and stores them in the database.
     *
     * To be called from scheduled task.
     * @param int|null $courseid
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function store_result_summary(int $courseid = null) {
        global $DB;

        if (static::is_okay_to_cache() && ($courseid == null)) {
            mtrace('Attempting to run update cache with no courseid, returning');
            return;
        }

        $extrasql = !$courseid ? "" : "AND courseid = ?";
        $coursesqlval = !$courseid ? [] : [$courseid];

        // Count of failed activities and count of errors by check.
        $errorsql = "SELECT areas.courseid, chx.checkgroup,
                COUNT(DISTINCT (".$DB->sql_concat('areas.contextid', 'areas.component').")) AS failed,
                SUM(res.errorcount) AS errors
                FROM {" . self::DB_AREAS . "} areas
                INNER JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = areas.id
                INNER JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
                INNER JOIN {" . self::DB_CHECKS . "} chx ON chx.id = res.checkid
                WHERE res.errorcount > ? AND ch.iscurrent = ? ". $extrasql ." GROUP BY courseid, chx.checkgroup";

        $recordserrored = $DB->get_recordset_sql($errorsql,  array_merge([0, 1], $coursesqlval));

        // Count of failed activities by course.
        $failsql = "SELECT areas.courseid,
                COUNT(DISTINCT (".$DB->sql_concat('areas.contextid', 'areas.component').")) AS failed,
                SUM(res.errorcount) AS errors
                FROM {" . self::DB_AREAS . "} areas
                INNER JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = areas.id
                INNER JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
                WHERE res.errorcount > ? AND ch.iscurrent = ? ". $extrasql ." GROUP BY courseid";

        $recordsfailed = $DB->get_recordset_sql($failsql, array_merge([0, 1], $coursesqlval));

        $extrasql = !$courseid ? "" : "WHERE courseid = ?";
        // Count of activities per course.
        $countsql = "SELECT courseid, COUNT(DISTINCT (".$DB->sql_concat('areas.contextid', 'areas.component').")) AS activities
                FROM {" . self::DB_AREAS . "} areas ". $extrasql ." GROUP BY areas.courseid";

        $recordscount = $DB->get_records_sql($countsql, $coursesqlval);

        $final = [];
        $values = [];

        foreach ($recordscount as $countrecord) {
            $final[$countrecord->courseid] = array_pad(array(), 8,
                    [self::SUMMARY_ERROR => 0, self::SUMMARY_FAILED => 0, self::SUMMARY_PERCENT => 100]
                ) + [
                    "activitiespassed" => $countrecord->activities,
                    "activitiesfailed" => 0,
                    "activities" => $countrecord->activities
                ];
        }

        foreach ($recordsfailed as $failedrecord) {
            $final[$failedrecord->courseid]["activitiespassed"] -= $failedrecord->failed;
            $final[$failedrecord->courseid]["activitiesfailed"] += $failedrecord->failed;
        }

        foreach ($recordserrored as $errorrecord) {
            $final[$errorrecord->courseid][$errorrecord->checkgroup][self::SUMMARY_ERROR] = $errorrecord->errors;
            $final[$errorrecord->courseid][$errorrecord->checkgroup][self::SUMMARY_FAILED] = $errorrecord->failed;
            $final[$errorrecord->courseid][$errorrecord->checkgroup][self::SUMMARY_PERCENT] = round(100 * (1 -
                    ($final[$errorrecord->courseid][$errorrecord->checkgroup][self::SUMMARY_FAILED]
                        / $final[$errorrecord->courseid]["activities"])));
        }

        foreach ($recordscount as $course) {
            if (!$course->courseid) {
                continue;
            }
            $element = [
                'courseid' => $course->courseid,
                'status' => self::STATUS_CHECKED,
                'activities' => $final[$course->courseid]["activities"],
                'activitiespassed' => $final[$course->courseid]["activitiespassed"],
                'activitiesfailed' => $final[$course->courseid]["activitiesfailed"],
                'errorschecktype1' => $final[$course->courseid][area_base::CHECKGROUP_FORM][self::SUMMARY_ERROR],
                'errorschecktype2' => $final[$course->courseid][area_base::CHECKGROUP_IMAGE][self::SUMMARY_ERROR],
                'errorschecktype3' => $final[$course->courseid][area_base::CHECKGROUP_LAYOUT][self::SUMMARY_ERROR],
                'errorschecktype4' => $final[$course->courseid][area_base::CHECKGROUP_LINK][self::SUMMARY_ERROR],
                'errorschecktype5' => $final[$course->courseid][area_base::CHECKGROUP_MEDIA][self::SUMMARY_ERROR],
                'errorschecktype6' => $final[$course->courseid][area_base::CHECKGROUP_TABLE][self::SUMMARY_ERROR],
                'errorschecktype7' => $final[$course->courseid][area_base::CHECKGROUP_TEXT][self::SUMMARY_ERROR],
                'failedchecktype1' => $final[$course->courseid][area_base::CHECKGROUP_FORM][self::SUMMARY_FAILED],
                'failedchecktype2' => $final[$course->courseid][area_base::CHECKGROUP_IMAGE][self::SUMMARY_FAILED],
                'failedchecktype3' => $final[$course->courseid][area_base::CHECKGROUP_LAYOUT][self::SUMMARY_FAILED],
                'failedchecktype4' => $final[$course->courseid][area_base::CHECKGROUP_LINK][self::SUMMARY_FAILED],
                'failedchecktype5' => $final[$course->courseid][area_base::CHECKGROUP_MEDIA][self::SUMMARY_FAILED],
                'failedchecktype6' => $final[$course->courseid][area_base::CHECKGROUP_TABLE][self::SUMMARY_FAILED],
                'failedchecktype7' => $final[$course->courseid][area_base::CHECKGROUP_TEXT][self::SUMMARY_FAILED],
                'percentchecktype1' => $final[$course->courseid][area_base::CHECKGROUP_FORM][self::SUMMARY_PERCENT],
                'percentchecktype2' => $final[$course->courseid][area_base::CHECKGROUP_IMAGE][self::SUMMARY_PERCENT],
                'percentchecktype3' => $final[$course->courseid][area_base::CHECKGROUP_LAYOUT][self::SUMMARY_PERCENT],
                'percentchecktype4' => $final[$course->courseid][area_base::CHECKGROUP_LINK][self::SUMMARY_PERCENT],
                'percentchecktype5' => $final[$course->courseid][area_base::CHECKGROUP_MEDIA][self::SUMMARY_PERCENT],
                'percentchecktype6' => $final[$course->courseid][area_base::CHECKGROUP_TABLE][self::SUMMARY_PERCENT],
                'percentchecktype7' => $final[$course->courseid][area_base::CHECKGROUP_TEXT][self::SUMMARY_PERCENT]
            ];
            $resultid = $DB->get_field(self::DB_SUMMARY, 'id', ['courseid' => $course->courseid]);
            if ($resultid) {
                $element['id'] = $resultid;
                $DB->update_record(self::DB_SUMMARY, (object)$element);
                continue;
            }
            array_push($values, $element);
        }

        $DB->insert_records(self::DB_SUMMARY, $values);

        $extrasql = !$courseid ? "WHERE courseid != ?" : "WHERE courseid = ?";
        $coursesqlval = !$courseid ? [0] : [$courseid];
        // Count of failed errors per check.
        $checkssql = "SELECT area.courseid, ".self::STATUS_CHECKED." AS status, res.checkid,
                COUNT(res.errorcount) as checkcount, SUM(res.errorcount) AS errorcount
                FROM {" . self::DB_AREAS . "} area
                INNER JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = area.id AND ch.iscurrent = 1
                INNER JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
                ".$extrasql." GROUP BY area.courseid, res.checkid";

        $checksresult = $DB->get_recordset_sql($checkssql, $coursesqlval);

        $checkvalues = [];
        foreach ($checksresult as $check) {
            if ($result = $DB->get_record(self::DB_CACHECHECK, ['courseid' => $check->courseid, 'checkid' => $check->checkid])) {
                $check->id = $result->id;
                $DB->update_record(self::DB_CACHECHECK, $check);
            } else {
                array_push($checkvalues, (array)$check);
            }
        }
        $DB->insert_records(self::DB_CACHECHECK, $checkvalues);

        // Count of failed or passed rate per activity.
        $activitysql = "SELECT courseid, ".self::STATUS_CHECKED." AS status, area.component,
                COUNT(DISTINCT area.contextid) AS totalactivities, 0 AS failedactivities,
                COUNT(DISTINCT area.contextid) AS passedactivities, 0 AS errorcount
                FROM {" . self::DB_AREAS . "} area
                ".$extrasql."
                GROUP BY area.courseid, area.component";

        $activityresults = $DB->get_recordset_sql($activitysql, $coursesqlval);

        $activityvalues = [];

        // Count of failed errors per courseid per activity.
        $activityfailedsql = "SELECT area.courseid, area.component, area.contextid, SUM(res.errorcount) AS errorcount
                FROM {" . self::DB_AREAS . "} area
                INNER JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = area.id AND ch.iscurrent = 1
                INNER JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
                ".$extrasql." AND res.errorcount != 0
                GROUP BY area.courseid, area.component, area.contextid";

        $activityfailedresults = $DB->get_recordset_sql($activityfailedsql, $coursesqlval);

        foreach ($activityresults as $activity) {
            $tmpkey = $activity->courseid.$activity->component;
            $activityvalues[$tmpkey] = $activity;
        }

        foreach ($activityfailedresults as $failed) {
            $tmpkey = $failed->courseid.$failed->component;
            $activityvalues[$tmpkey]->failedactivities ++;
            $activityvalues[$tmpkey]->passedactivities --;
            $activityvalues[$tmpkey]->errorcount += $failed->errorcount;
        }

        $activityvaluespush = [];
        foreach ($activityvalues as $value) {
            if ($result = $DB->get_record(self::DB_CACHEACTS, ['courseid' => $value->courseid, 'component' => $value->component])) {
                $value->id = $result->id;
                $DB->update_record(self::DB_CACHEACTS, $value);
            } else {
                array_push($activityvaluespush, (array)$value);
            }
        }

        $DB->insert_records(self::DB_CACHEACTS, $activityvaluespush);

        $recordserrored->close();
        $recordsfailed->close();
        $checksresult->close();
        $activityresults->close();
        $activityfailedresults->close();
    }

    /**
     * Get course module summary information for a course.
     *
     * @param   int $courseid
     * @return  stdClass[]
     */
    public static function get_cm_summary_for_course(int $courseid): array {
        global $DB;

        $sql = "
        SELECT
            area.cmid,
            sum(errorcount) as numerrors,
            count(errorcount) as numchecks
         FROM {" . self::DB_AREAS . "} area
         JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = area.id AND ch.iscurrent = 1
         JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
        WHERE area.courseid = :courseid AND component != :component
     GROUP BY cmid";

        $params = [
            'courseid' => $courseid,
            'component' => 'core_course',
        ];

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Get section summary information for a course.
     *
     * @param   int $courseid
     * @return  stdClass[]
     */
    public static function get_section_summary_for_course(int $courseid): array {
        global $DB;

        $sql = "
        SELECT
            sec.section,
            sum(errorcount) AS numerrors,
            count(errorcount) as numchecks
         FROM {" . self::DB_AREAS . "} area
         JOIN {" . self::DB_CONTENT . "} ch ON ch.areaid = area.id AND ch.iscurrent = 1
         JOIN {" . self::DB_RESULTS . "} res ON res.contentid = ch.id
         JOIN {course_sections} sec ON area.itemid = sec.id
        WHERE area.tablename = :tablename AND area.courseid = :courseid
     GROUP BY sec.section";

        $params = [
            'courseid' => $courseid,
            'tablename' => 'course_sections'
        ];

        return $DB->get_records_sql($sql, $params);
    }
}
