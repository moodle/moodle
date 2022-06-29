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

use context_system;
use moodle_exception;
use moodle_url;
use stdClass;
use tool_brickfield\local\tool\filter;

/**
 * Provides the Brickfield Accessibility toolkit API.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward Brickfield Education Labs Ltd, https://www.brickfield.ie
 * @author     Mike Churchward (mike@brickfieldlabs.ie)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accessibility {

    /** @var string The component sub path */
    private static $pluginpath = 'tool/brickfield';

    /** @var string Supported format of topics */
    const TOOL_BRICKFIELD_FORMAT_TOPIC = 'topics';

    /** @var string Supported format of weeks */
    const TOOL_BRICKFIELD_FORMAT_WEEKLY = 'weeks';

    /**
     * Return the state of the site enable condition.
     * @return bool
     */
    public static function is_accessibility_enabled(): bool {
        global $CFG;

        if (isset($CFG->enableaccessibilitytools)) {
            return $CFG->enableaccessibilitytools;
        }

        // Enabled by default.
        return true;
    }

    /**
     * Throw an error if the toolkit is not enabled.
     * @return bool
     * @throws moodle_exception
     */
    public static function require_accessibility_enabled(): bool {
        if (!static::is_accessibility_enabled()) {
            throw new moodle_exception('accessibilitydisabled', manager::PLUGINNAME);
        }
        return true;
    }

    /**
     * Get a URL for a page within the plugin.
     *
     * This takes into account the value of the admin config value.
     *
     * @param   string $url The URL within the plugin
     * @return  moodle_url
     */
    public static function get_plugin_url(string $url = ''): moodle_url {
        $url = ($url == '') ? 'index.php' : $url;
        $pluginpath = self::$pluginpath;
        return new moodle_url("/admin/{$pluginpath}/{$url}");
    }

    /**
     * Get a file path for a file within the plugin.
     *
     * This takes into account the value of the admin config value.
     *
     * @param   string $path The path within the plugin
     * @return  string
     */
    public static function get_file_path(string $path): string {
        global $CFG;

        return implode(DIRECTORY_SEPARATOR, [$CFG->dirroot, $CFG->admin, self::$pluginpath, $path, ]);
    }

    /**
     * Get the canonicalised name of a capability.
     *
     * @param   string $capability
     * @return  string
     */
    public static function get_capability_name(string $capability): string {
        return self::$pluginpath . ':' . $capability;
    }

    /**
     * Get the relevant title.
     * @param filter $filter
     * @param int $countdata
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function get_title(filter $filter, int $countdata): string {
        global $DB;

        $tmp = new \stdClass();
        $tmp->count = $countdata;
        $langstr = 'title' . $filter->tab . 'partial';

        if ($filter->courseid != 0) {
            $thiscourse = get_fast_modinfo($filter->courseid)->get_course();
            $tmp->name = $thiscourse->fullname;
        } else {
            $langstr = 'title' . $filter->tab . 'all';
        }
        return get_string($langstr, manager::PLUGINNAME, $tmp);
    }

    /**
     * Function to be run periodically according to the scheduled task.
     * Return true if a process was completed. False if no process executed.
     * Finds all unprocessed courses for bulk batch processing and completes them.
     * @param int $batch
     * @return bool
     * @throws \ReflectionException
     * @throws \coding_exception
     * @throws \ddl_exception
     * @throws \ddl_table_missing_exception
     * @throws \dml_exception
     */
    public static function bulk_process_courses_cron(int $batch = 0): bool {
        global $PAGE;

        // Run a registration check.
        if (!(new registration())->validate()) {
            return false;
        }

        if (analysis::is_enabled()) {
            $PAGE->set_context(context_system::instance());
            mtrace("Starting cron for bulk_process_courses");
            // Do regular processing. True if full deployment type isn't selected as well.
            static::bulk_processing($batch);
            mtrace("Ending cron for bulk_process_courses");
            return true;
        } else {
            mtrace('Content analysis is currently disabled in settings.');
            return false;
        }
    }

    /**
     * Bulk processing.
     * @param int $batch
     * @return bool
     */
    protected static function bulk_processing(int $batch = 0): bool {
        manager::check_course_updates();
        mtrace("check_course_updates completed at " . time());
        $recordsprocessed = manager::check_scheduled_areas($batch);
        mtrace("check_scheduled_areas completed at " . time());
        manager::check_scheduled_deletions();
        mtrace("check_scheduled_deletions completed at " . time());
        manager::delete_historical_data();
        mtrace("delete_historical_data completed at " . time());
        return $recordsprocessed;
    }

    /**
     * Function to be run periodically according to the scheduled task.
     * Finds all unprocessed courses for cache processing and completes them.
     */
    public static function bulk_process_caches_cron() {
        global $DB;

        // Run a registration check.
        if (!(new registration())->validate()) {
            return;
        }

        if (analysis::is_enabled()) {
            mtrace("Starting cron for bulk_process_caches");
            // Monitor ongoing caching requests.
            $fields = 'DISTINCT courseid';
            $reruns = $DB->get_records(manager::DB_PROCESS, ['item' => 'cache'], '', $fields);
            foreach ($reruns as $rerun) {
                mtrace("Running rerun caching for Courseid " . $rerun->courseid);
                manager::store_result_summary($rerun->courseid);
                mtrace("rerun cache completed at " . time());
                $DB->delete_records(manager::DB_PROCESS, ['courseid' => $rerun->courseid, 'item' => 'cache']);
            }
            mtrace("Ending cron for bulk_process_caches at " . time());
        } else {
            mtrace('Content analysis is currently disabled in settings.');
        }
    }

    /**
     * This function runs the checks on the html item
     *
     * @param string $html The html string to be analysed; might be NULL.
     * @param int $contentid The content area ID
     * @param int $processingtime
     * @param int $resultstime
     */
    public static function run_check(string $html, int $contentid, int &$processingtime, int &$resultstime) {
        global $DB;

        // Change the limit if 10,000 is not appropriate.
        $bulkrecordlimit = manager::BULKRECORDLIMIT;
        $bulkrecordcount = 0;

        $checkids = static::checkids();
        $checknameids = array_flip($checkids);

        $testname = 'brickfield';

        $stime = time();

        // Swapping in new library.
        $htmlchecker = new local\htmlchecker\brickfield_accessibility($html, $testname, 'string');
        $htmlchecker->run_check();
        $tests = $htmlchecker->guideline->get_tests();
        $report = $htmlchecker->get_report();
        $processingtime += (time() - $stime);

        $records = [];
        foreach ($tests as $test) {
            $records[$test]['count'] = 0;
            $records[$test]['errors'] = [];
        }

        foreach ($report['report'] as $a) {
            if (!isset($a['type'])) {
                continue;
            }
            $type = $a['type'];
            $records[$type]['errors'][] = $a;
            if (!isset($records[$type]['count'])) {
                $records[$type]['count'] = 0;
            }
            $records[$type]['count']++;
        }

        $stime = time();
        $returnchecks = [];
        $errors = [];

        // Build up records for inserting.
        foreach ($records as $key => $rec) {
            $recordres = new stdClass();
            // Handling if checkid is unknown.
            $checkid = (isset($checknameids[$key])) ? $checknameids[$key] : 0;
            $recordres->contentid = $contentid;
            $recordres->checkid = $checkid;
            $recordres->errorcount = $rec['count'];

            // Build error inserts if needed.
            if ($rec['count'] > 0) {
                foreach ($rec['errors'] as $tmp) {
                    $error = new stdClass();
                    $error->resultid = 0;
                    $error->linenumber = $tmp['lineNo'];
                    $error->htmlcode = $tmp['html'];
                    $error->errordescription = $tmp['title'];
                    // Add contentid and checkid so that we can query for the results record id later.
                    $error->contentid = $contentid;
                    $error->checkid = $checkid;
                    $errors[] = $error;
                }
            }
            $returnchecks[] = $recordres;
            $bulkrecordcount++;

            // If we've hit the bulk limit, write the results records and reset.
            if ($bulkrecordcount > $bulkrecordlimit) {
                $DB->insert_records(manager::DB_RESULTS, $returnchecks);
                $bulkrecordcount = 0;
                $returnchecks = [];
                // Get the results id value for each error record and write the errors.
                foreach ($errors as $key2 => $error) {
                    $errors[$key2]->resultid = $DB->get_field(manager::DB_RESULTS, 'id',
                        ['contentid' => $error->contentid, 'checkid' => $error->checkid]);
                    unset($errors[$key2]->contentid);
                    unset($errors[$key2]->checkid);
                }
                $DB->insert_records(manager::DB_ERRORS, $errors);
                $errors = [];
            }
        }

        // Write any leftover records.
        if ($bulkrecordcount > 0) {
            $DB->insert_records(manager::DB_RESULTS, $returnchecks);
            // Get the results id value for each error record and write the errors.
            foreach ($errors as $key => $error) {
                $errors[$key]->resultid = $DB->get_field(manager::DB_RESULTS, 'id',
                    ['contentid' => $error->contentid, 'checkid' => $error->checkid]);
                unset($errors[$key]->contentid);
                unset($errors[$key]->checkid);
            }
            $DB->insert_records(manager::DB_ERRORS, $errors);
        }

        $resultstime += (time() - $stime);
    }

    /**
     * This function runs one specified check on the html item
     *
     * @param string $html The html string to be analysed; might be NULL.
     * @param int $contentid The content area ID
     * @param int $errid The error ID
     * @param string $check The check name to run
     * @param int $processingtime
     * @param int $resultstime
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function run_one_check(
        string $html = null,
        int $contentid,
        int $errid,
        string $check,
        int &$processingtime,
        int &$resultstime
    ) {
        global $DB;

        $stime = time();

        $checkdata = $DB->get_record(manager::DB_CHECKS, ['shortname' => $check], 'id,shortname,severity');

        $testname = 'brickfield';

        // Swapping in new library.
        $htmlchecker = new local\htmlchecker\brickfield_accessibility($html, $testname, 'string');
        $htmlchecker->run_check();
        $report = $htmlchecker->get_test($check);
        $processingtime += (time() - $stime);

        $record = [];
        $record['count'] = 0;
        $record['errors'] = [];

        foreach ($report as $a) {
            $a->html = $a->get_html();
            $record['errors'][] = $a;
            $record['count']++;
        }

        // Build up record for inserting.
        $recordres = new stdClass();
        // Handling if checkid is unknown.
        $checkid = (isset($checkdata->id)) ? $checkdata->id : 0;
        $recordres->contentid = $contentid;
        $recordres->checkid = $checkid;
        $recordres->errorcount = $record['count'];
        if ($exists = $DB->get_record(manager::DB_RESULTS, ['contentid' => $contentid, 'checkid' => $checkid])) {
            $resultid = $exists->id;
            $DB->set_field(manager::DB_RESULTS, 'errorcount', $record['count'], ['id' => $resultid]);
            // Remove old error records for specific resultid, if existing.
            $DB->delete_records(manager::DB_ERRORS, ['id' => $errid]);
        } else {
            $resultid = $DB->insert_record(manager::DB_RESULTS, $recordres);
        }
        $errors = [];

        // Build error inserts if needed.
        if ($record['count'] > 0) {
            // Reporting all found errors for this check, so need to ignore existing other error records.
            foreach ($record['errors'] as $tmp) {
                // Confirm if error is reported separately.
                if ($DB->record_exists_select(manager::DB_ERRORS,
                    'resultid = ? AND ' . $DB->sql_compare_text('htmlcode', 255) . ' = ' . $DB->sql_compare_text('?', 255),
                    [$resultid, html_entity_decode($tmp->html)])) {
                    continue;
                }
                $error = new stdClass();
                $error->resultid = $resultid;
                $error->linenumber = $tmp->line;
                $error->htmlcode = html_entity_decode($tmp->html);
                $errors[] = $error;
            }

            $DB->insert_records(manager::DB_ERRORS, $errors);
        }

        $resultstime += (time() - $stime);
    }

    /**
     * Returns all of the id's and shortnames of all of the checks.
     * @param int $status
     * @return array
     * @throws \dml_exception
     */
    public static function checkids(int $status = 1): array {
        global $DB;

        $checks = $DB->get_records_menu(manager::DB_CHECKS, ['status' => $status], 'id ASC', 'id,shortname');
        return $checks;
    }

    /**
     * Returns an array of translations from htmlchecker of all of the checks, and their descriptions.
     * @return array
     * @throws \dml_exception
     */
    public static function get_translations(): array {
        global $DB;

        $htmlchecker = new local\htmlchecker\brickfield_accessibility('test', 'brickfield', 'string');
        $htmlchecker->run_check();
        ksort($htmlchecker->guideline->translations);

        // Need to limit to active checks.
        $activechecks = $DB->get_fieldset_select(manager::DB_CHECKS, 'shortname', 'status = :status', ['status' => 1]);

        $translations = [];
        foreach ($htmlchecker->guideline->translations as $key => $trans) {
            if (in_array($key, $activechecks)) {
                $translations[$key] = $trans;
            }
        }

        return $translations;
    }

    /**
     * Returns an array of all of the course id's for a given category.
     * @param int $categoryid
     * @return array|null
     * @throws \dml_exception
     */
    public static function get_category_courseids(int $categoryid): ?array {
        global $DB;

        if (!$DB->record_exists('course_categories', ['id' => $categoryid])) {
            return null;
        }

        $sql = "SELECT {course}.id
              FROM {course}, {course_categories}
             WHERE {course}.category = {course_categories}.id
               AND (
                " . $DB->sql_like('path', ':categoryid1') . "
             OR " . $DB->sql_like('path', ':categoryid2') . "
        )";
        $params = ['categoryid1' => "%/$categoryid/%", 'categoryid2' => "%/$categoryid"];
        $courseids = $DB->get_fieldset_sql($sql, $params);

        return $courseids;
    }

    /**
     * Get summary data for this site.
     * @param int $id
     * @return \stdClass
     * @throws \dml_exception
     */
    public static function get_summary_data(int $id): \stdClass {
        global $CFG, $DB;

        $summarydata = new \stdClass();
        $summarydata->siteurl = (substr($CFG->wwwroot, -1) !== '/') ? $CFG->wwwroot . '/' : $CFG->wwwroot;
        $summarydata->moodlerelease = (preg_match('/^(\d+\.\d.*?)[. ]/', $CFG->release, $matches)) ? $matches[1] : $CFG->release;
        $summarydata->numcourses = $DB->count_records('course') - 1;
        $summarydata->numusers = $DB->count_records('user', array('deleted' => 0));
        $summarydata->numfiles = $DB->count_records('files');
        $summarydata->numfactivities = $DB->count_records('course_modules');
        $summarydata->mobileservice = (int)$CFG->enablemobilewebservice === 1 ? true : false;
        $summarydata->usersmobileregistered = $DB->count_records('user_devices');
        $summarydata->contenttyperesults = static::get_contenttyperesults($id);
        $summarydata->contenttypeerrors = static::get_contenttypeerrors();
        $summarydata->percheckerrors = static::get_percheckerrors();
        return $summarydata;
    }

    /**
     * Get content type results.
     * @param int $id
     * @return \stdClass
     */
    private static function get_contenttyperesults(int $id): \stdClass {
        global $DB;
        $sql = 'SELECT component, COUNT(id) AS count
                  FROM {' . manager::DB_AREAS . '}
              GROUP BY component';
        $components = $DB->get_recordset_sql($sql);
        $contenttyperesults = new \stdClass();
        $contenttyperesults->id = $id;
        $contenttyperesults->contenttype = new \stdClass();
        foreach ($components as $component) {
            $componentname = $component->component;
            $contenttyperesults->contenttype->$componentname = $component->count;
        }
        $components->close();
        $contenttyperesults->summarydatastorage = static::get_summary_data_storage();
        $contenttyperesults->datachecked = time();
        return $contenttyperesults;
    }


    /**
     * Get per check errors.
     * @return stdClass
     * @throws dml_exception
     */
    private static function get_percheckerrors(): stdClass {
        global $DB;

        $sql = 'SELECT ' . $DB->sql_concat_join("'_'", ['courseid', 'checkid']) . ' as tmpid,
                       ca.courseid, ca.status, ca.checkid, ch.shortname, ca.checkcount, ca.errorcount
                  FROM {' . manager::DB_CACHECHECK . '} ca
            INNER JOIN {' . manager::DB_CHECKS . '} ch on ch.id = ca.checkid
              ORDER BY courseid, checkid ASC';

        $combo = $DB->get_records_sql($sql);

        return (object) [
            'percheckerrors' => $combo,
        ];
    }

    /**
     * Get content type errors.
     * @return stdClass
     * @throws dml_exception
     */
    private static function get_contenttypeerrors(): stdClass {
        global $DB;

        $fields = 'courseid, status, activities, activitiespassed, activitiesfailed,
                    errorschecktype1, errorschecktype2, errorschecktype3, errorschecktype4,
                    errorschecktype5, errorschecktype6, errorschecktype7,
                    failedchecktype1, failedchecktype2, failedchecktype3, failedchecktype4,
                    failedchecktype5, failedchecktype6, failedchecktype7,
                    percentchecktype1, percentchecktype2, percentchecktype3, percentchecktype4,
                    percentchecktype5, percentchecktype6, percentchecktype7';
        $combo = $DB->get_records(manager::DB_SUMMARY, null, 'courseid ASC', $fields);

        return (object) [
            'typeerrors' => $combo,
        ];
    }

    /**
     * Get summary data storage.
     * @return array
     * @throws dml_exception
     */
    private static function get_summary_data_storage(): array {
        global $DB;

        $fields = $DB->sql_concat_join("''", ['component', 'courseid']) . ' as tmpid,
                 courseid, component, errorcount, totalactivities, failedactivities, passedactivities';
        $combo = $DB->get_records(manager::DB_CACHEACTS, null, 'courseid, component ASC', $fields);
        return $combo;
    }
}
