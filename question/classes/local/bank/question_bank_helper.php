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

namespace core_question\local\bank;

use cm_info;
use context;
use context_course;
use core\task\manager;
use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/questionlib.php');
require_once($CFG->dirroot . '/course/modlib.php');

/**
 * Helper class for qbank sharing.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_helper {
    /** @var string the type of qbank module that users create */
    public const TYPE_STANDARD = 'standard';

    /**
     * The type of shared bank module that the system creates.
     * These are created in course restores when no target context can be found,
     * and also for when a question category cannot be deleted safely due to questions being in use.
     *
     * @var string
     */
    public const TYPE_SYSTEM = 'system';

    /** @var string The type of shared bank module that the system creates for previews. Not used for any other purpose. */
    public const TYPE_PREVIEW = 'preview';

    /** @var array Shared bank types */
    public const SHARED_TYPES = [self::TYPE_STANDARD, self::TYPE_SYSTEM, self::TYPE_PREVIEW];

    /**
     * User preferences record key to store recently viewed question banks.
     */
    protected const RECENTLY_VIEWED = 'recently_viewed_open_banks';

    /**
     * Category delimiter used by the SQL to group concatenate question category data i.e.
     * category_id<->category_name<->context_id.
     */
    private const CATEGORY_DELIMITER = '<->';

    /**
     * Category separator used by the SQL for group concatenation of those category triplets
     * from above.
     */
    private const CATEGORY_SEPARATOR = '<,>';

    /**
     * Maximum length for the question bank name database field.
     */
    public const BANK_NAME_MAX_LENGTH = 255;

    /**
     * Modules that share questions via FEATURE_PUBLISHES_QUESTIONS.
     *
     * @return array
     */
    public static function get_activity_types_with_shareable_questions(): array {
        static $sharedmods;

        if (!empty($sharedmods)) {
            return $sharedmods;
        }

        $manager = \core_plugin_manager::instance();
        $plugins = $manager->get_enabled_plugins('mod');

        $sharedmods = array_filter(
            array_keys($plugins),
            static fn ($plugin) => plugin_supports('mod', $plugin, FEATURE_PUBLISHES_QUESTIONS) &&
                question_module_uses_questions($plugin)
        );

        return array_values($sharedmods);
    }

    /**
     * Get module types that do not share questions. They will have FEATURE_USES_QUESTIONS set to false or won't have it defined.
     *
     * @return array
     */
    public static function get_activity_types_with_private_questions(): array {
        static $privatemods;

        if (!empty($privatemods)) {
            return $privatemods;
        }

        $manager = \core_plugin_manager::instance();
        $plugins = $manager->get_enabled_plugins('mod');

        $privatemods = array_filter(
            array_keys($plugins),
            static fn ($plugin) => !plugin_supports('mod', $plugin, FEATURE_PUBLISHES_QUESTIONS) &&
                question_module_uses_questions($plugin)
        );

        return array_values($privatemods);
    }

    /**
     * Get records for activity modules that do publish questions, and optionally get their question categories too.
     *
     * @param array $incourseids array of course ids where you want instances included. Leave empty if you want from all courses.
     * @param array $notincourseids array of course ids where you do not want instances included.
     * @param array $havingcap current user must have at least one of these capabilities on each bank context.
     * @param bool $getcategories optionally return the categories belonging to these banks.
     * @param int $currentbankid optionally include the bank id you want included as the first result from the method return.
     * it will only be included if the other parameters allow it.
     * @param ?context $filtercontext Optional context to use for all string filtering, useful for performance when calling with
     *      parameters that will get banks across multiple contexts.
     * @param string $search Optional term to search question bank instances by name
     * @param int $limit The number of results to return (default 0 = no limit)
     * @return stdClass[]
     */
    public static function get_activity_instances_with_shareable_questions(
        array $incourseids = [],
        array $notincourseids = [],
        array $havingcap = [],
        bool $getcategories = false,
        int $currentbankid = 0,
        ?context $filtercontext = null,
        string $search = '',
        int $limit = 0,
    ): array {
        return self::get_bank_instances(true,
            $incourseids,
            $notincourseids,
            $getcategories,
            $currentbankid,
            $havingcap,
            $filtercontext,
            $search,
            $limit,
        );
    }

    /**
     * Get records for activity modules that don't publish questions, and optionally get their question categories too.
     *
     * @param array $incourseids array of course ids where you want instances included. Leave empty if you want from all courses.
     * @param array $notincourseids array of course ids where you do not want instances included.
     * @param array $havingcap current user must have at least one of these capabilities on each bank context.
     * @param bool $getcategories optionally return the categories belonging to these banks.
     * @param int $currentbankid optionally include the bank id you want included as the first result from the method return.
     * it will only be included if the other parameters allow it.
     * @param ?context $filtercontext Optional context to use for all string filtering, useful for performance when calling with
     *       parameters that will get banks across multiple contexts.
     * @return stdClass[]
     */
    public static function get_activity_instances_with_private_questions(
        array $incourseids = [],
        array $notincourseids = [],
        array $havingcap = [],
        bool $getcategories = false,
        int $currentbankid = 0,
        ?context $filtercontext = null,
    ): array {
        return self::get_bank_instances(false,
            $incourseids,
            $notincourseids,
            $getcategories,
            $currentbankid,
            $havingcap,
            $filtercontext,
        );
    }

    /**
     * Private method to build the SQL and get records from the DB. Called from public API methods
     * {@see self::get_activity_instances_with_shareable_questions()}
     * {@see self::get_activity_instances_with_private_questions()}
     *
     * @param bool $isshared true if you want instances that publish questions false if you want instances that don't
     * @param array $incourseids array of course ids where you want instances included. Leave empty if you want from all courses.
     * @param array $notincourseids array of course ids where you do not want instances included.
     * @param bool $getcategories optionally return the categories belonging to these banks.
     * @param int $currentbankid optionally include the bank id you want included as the first result from the method return.
     *  it will only be included if the other parameters allow it.
     * @param array $havingcap current user must have at least one of these capabilities on each bank context.
     * @param ?context $filtercontext Optional context to use for all string filtering, useful for performance when calling with
     *     parameters that will get banks across multiple contexts.
     * @param string $search Optional term to search question bank instances by name
     * @param int $limit The number of results to return (default 0 = no limit)
     * @return stdClass[]
     */
    private static function get_bank_instances(
        bool $isshared,
        array $incourseids = [],
        array $notincourseids = [],
        bool $getcategories = false,
        int $currentbankid = 0,
        array $havingcap = [],
        ?context $filtercontext = null,
        string $search = '',
        int $limit = 0,
    ): array {
        global $DB;

        $pluginssql = [];
        $params = [];

        // Build the SELECT portion of the SQL and include question category joins as required.
        if ($getcategories) {
            $concat = $DB->sql_concat('qc.id',
                "'" . self::CATEGORY_DELIMITER . "'",
                'qc.name',
                "'" . self::CATEGORY_DELIMITER . "'",
                'qc.contextid'
            );
            $groupconcat = $DB->sql_group_concat($concat, self::CATEGORY_SEPARATOR);
            $select = "SELECT cm.id, cm.course, {$groupconcat} AS cats";
            $catsql = ' JOIN {context} c ON c.instanceid = cm.id AND c.contextlevel = ' . CONTEXT_MODULE .
                ' JOIN {question_categories} qc ON qc.contextid = c.id AND qc.parent <> 0';
        } else {
            $select = 'SELECT cm.id, cm.course';
            $catsql = '';
        }

        if ($isshared) {
            $plugins = self::get_activity_types_with_shareable_questions();
        } else {
            $plugins = self::get_activity_types_with_private_questions();
        }

        if (empty($plugins)) {
            return [];
        }

        // Build the joins for all modules of the type requested i.e. those that do or do not share questions.
        foreach ($plugins as $key => $plugin) {
            $moduleid = $DB->get_field('modules', 'id', ['name' => $plugin]);
            $sql = "JOIN {{$plugin}} p{$key} ON p{$key}.id = cm.instance
                    AND cm.module = {$moduleid} AND cm.deletioninprogress = 0";
            if ($plugin === self::get_default_question_bank_activity_name()) {
                $sql .= " AND p{$key}.type <> '" . self::TYPE_PREVIEW . "'";
            }
            if (!empty($search)) {
                $sql .= " AND " . $DB->sql_like("p{$key}.name", ":search{$key}", false);
                $params["search{$key}"] = "%{$search}%";
            }
            $pluginssql[] = $sql;
        }
        $pluginssql = implode(' ', $pluginssql);

        // Build the SQL to filter out any requested course ids.
        if (!empty($notincourseids)) {
            [$notincoursesql, $notincourseparams] = $DB->get_in_or_equal($notincourseids, SQL_PARAMS_NAMED, 'param', false);
            $notincoursesql = "AND cm.course {$notincoursesql}";
            $params = array_merge($params, $notincourseparams);
        } else {
            $notincoursesql = '';
        }

        // Build the SQL to include ONLY records belonging to the requested courses.
        if (!empty($incourseids)) {
            [$incoursesql, $incourseparams] = $DB->get_in_or_equal($incourseids, SQL_PARAMS_NAMED);
            $incoursesql = " AND cm.course {$incoursesql}";
            $params = array_merge($params, $incourseparams);
        } else {
            $incoursesql = '';
        }

        // Optionally order the results by the requested bank id.
        if (!empty($currentbankid)) {
            $orderbysql = " ORDER BY CASE WHEN cm.id = :currentbankid THEN 0 ELSE 1 END ASC, cm.id DESC ";
            $params['currentbankid'] = $currentbankid;
        } else {
            $orderbysql = '';
        }

        $sql = "{$select}
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                {$pluginssql}
                {$catsql}
                WHERE 1=1 {$notincoursesql} {$incoursesql}
                GROUP BY cm.id, cm.course
                {$orderbysql}";

        $rs = $DB->get_recordset_sql($sql, $params, limitnum: $limit);
        $banks = [];

        foreach ($rs as $cm) {
            // If capabilities have been supplied as a method argument then ensure the viewing user has at least one of those
            // capabilities on the module itself.
            if (!empty($havingcap)) {
                $context = \context_module::instance($cm->id);
                if (!(new question_edit_contexts($context))->have_one_cap($havingcap)) {
                    continue;
                }
            }
            // Populate the raw record.
            $banks[] = self::get_formatted_bank($cm, $currentbankid, filtercontext: $filtercontext);
        }
        $rs->close();

        return $banks;
    }

    /**
     * Get a list of recently viewed question banks that implement FEATURE_PUBLISHES_QUESTIONS.
     * If any of the stored contexts don't exist anymore then update the user preference record accordingly.
     *
     * @param int $userid of the user to get recently viewed banks for.
     * @param int $notincourseid if supplied don't return any in this course id
     * @param ?context $filtercontext Optional context to use for all string filtering, useful for performance when calling with
     *       parameters that will get banks across multiple contexts.
     * @return cm_info[]
     */
    public static function get_recently_used_open_banks(
        int $userid,
        int $notincourseid = 0,
        ?context $filtercontext = null,
    ): array {
        $prefs = get_user_preferences(self::RECENTLY_VIEWED, null, $userid);
        $contextids = !empty($prefs) ? explode(',', $prefs) : [];
        if (empty($contextids)) {
            return $contextids;
        }
        $invalidcontexts = [];
        $banks = [];

        foreach ($contextids as $contextid) {
            if (!$context = context::instance_by_id($contextid, IGNORE_MISSING)) {
                $invalidcontexts[] = $context;
                continue;
            }
            if ($context->contextlevel !== CONTEXT_MODULE) {
                throw new \moodle_exception('Invalid question bank contextlevel: ' . $context->contextlevel);
            }
            [, $cm] = get_module_from_cmid($context->instanceid);
            if (!empty($notincourseid) && $notincourseid == $cm->course) {
                continue;
            }
            $record = self::get_formatted_bank($cm, filtercontext: $filtercontext);
            $banks[] = $record;
        }

        if (!empty($invalidcontexts)) {
            $tostore = array_diff($contextids, $invalidcontexts);
            $tostore = implode(',', $tostore);
            set_user_preference(self::RECENTLY_VIEWED, $tostore, $userid);
        }

        return $banks;
    }

    /**
     * Mark a user as having viewed a question bank in the user_preferences table with key {@see self::RECENTLY_VIEWED}
     *
     * @param context $bankcontext add this bank context to the viewing user's list of recently viewed.
     * @return void
     */
    public static function add_bank_context_to_recently_viewed(context $bankcontext): void {

        [, $cm] = get_module_from_cmid($bankcontext->instanceid);

        if (!plugin_supports('mod', $cm->modname, FEATURE_PUBLISHES_QUESTIONS)) {
            return;
        }

        $userprefs = get_user_preferences(self::RECENTLY_VIEWED);
        $recentlyviewed = !empty($userprefs) ? explode(',', $userprefs) : [];
        $recentlyviewed = array_combine($recentlyviewed, $recentlyviewed);
        $tostore = [];
        $tostore[] = $bankcontext->id;
        if (!empty($recentlyviewed[$bankcontext->id])) {
            unset($recentlyviewed[$bankcontext->id]);
        }
        $tostore = array_merge($tostore, array_values($recentlyviewed));
        $tostore = array_slice($tostore, 0, 5);
        set_user_preference(self::RECENTLY_VIEWED, implode(',', $tostore));
    }

    /**
     * Populate the raw record with data for use in rendering.
     *
     * @param stdClass $cm raw course_modules record to populate data from.
     * @param int $currentbankid set an 'enabled' flag on the instance that matched this id.
     *     Used in qbank_bulkmove/bulk_move.mustache
     * @param ?context $filtercontext Optional context in which to apply filters.
     *
     * @return stdClass
     */
    private static function get_formatted_bank(stdClass $cm, int $currentbankid = 0, ?context $filtercontext = null): stdClass {

        $cminfo = cm_info::create($cm);
        $concatedcats = !empty($cm->cats) ? explode(self::CATEGORY_SEPARATOR, $cm->cats) : [];
        $categories = array_map(static function($concatedcategory) use ($cminfo, $currentbankid) {
            $values = explode(self::CATEGORY_DELIMITER, $concatedcategory);
            $cat = new stdClass();
            $cat->id = $values[0];
            $cat->name = $values[1];
            $cat->contextid = $values[2];
            $cat->enabled = $cminfo->id == $currentbankid ? 'enabled' : 'disabled';
            return $cat;
        }, $concatedcats);

        $bank = new stdClass();
        $filteroptions = ['escape' => false];
        if (!is_null($filtercontext)) {
            $filteroptions['context'] = $filtercontext;
        }
        $bank->name = $cminfo->get_formatted_name($filteroptions);
        $bank->modid = $cminfo->id;
        $bank->contextid = $cminfo->context->id;
        if (!isset($filteroptions['context'])) {
            $filteroptions['context'] = context_course::instance($cminfo->get_course()->id);
        }
        $bank->coursenamebankname = format_string($cminfo->get_course()->shortname, true, $filteroptions) . " - {$bank->name}";
        $bank->cminfo = $cminfo;
        $bank->questioncategories = $categories;
        return $bank;
    }

    /**
     * Get the system type qbank instance for this course, optionally create it if it does not yet exist.
     * {@see self::TYPE_SYSTEM}
     *
     * @param stdClass $course the course to get the default system type bank for.
     * @param bool $createifnotexists create a default bank if it does not exist.
     * @return cm_info|null
     */
    public static function get_default_open_instance_system_type(stdClass $course, bool $createifnotexists = false): ?cm_info {

        $modinfo = get_fast_modinfo($course);
        $qbanks = $modinfo->get_instances_of(self::get_default_question_bank_activity_name());
        $systembank = null;

        if ($systembankids = self::get_qbank_ids_of_type_in_course($course, self::TYPE_SYSTEM)) {
            // We should only ever have 1 of these.
            $systembankid = reset($systembankids);
            // Filter the course modinfo qbanks by the systembankid.
            $systembanks = array_filter($qbanks, static fn($bank) => $bank->id === $systembankid);
            $systembank = !empty($systembanks) ? reset($systembanks) : null;
        }

        if (!$systembank && $createifnotexists) {
            $systembank = self::create_default_open_instance(
                $course,
                self::get_bank_name_string('systembank', 'question'),
                self::TYPE_SYSTEM,
            );
        }

        return $systembank;
    }

    /**
     * Get the bank that is used for preview purposes only, optionally create it if it does not yet exist.
     * {@see \qbank_columnsortorder\column_manager::get_questionbank()}
     *
     * @param bool $createifnotexists create a default bank if it does not exist.
     * @return cm_info|null
     */
    public static function get_preview_open_instance_type(bool $createifnotexists = false): ?cm_info {

        $site = get_site();
        $modinfo = get_fast_modinfo($site);
        $qbanks = $modinfo->get_instances_of(self::get_default_question_bank_activity_name());
        $previewbank = null;

        if ($previewbankids = self::get_qbank_ids_of_type_in_course($site, self::TYPE_PREVIEW)) {
            // We should only ever have 1 of these.
            $previewbankid = reset($previewbankids);
            // Filter the course modinfo qbanks by the previewbankid.
            $previewbanks = array_filter($qbanks, static fn($bank) => $bank->id === $previewbankid);
            $previewbank = !empty($previewbanks) ? reset($previewbanks) : null;
        }

        if (!$previewbank && $createifnotexists) {
            $previewbank = self::create_default_open_instance(
                $site,
                self::get_bank_name_string('previewbank', 'question'),
                self::TYPE_PREVIEW
            );
        }

        return $previewbank;
    }

    /**
     * Get course module ids from qbank instances on a course that are of the sub-type provided.
     *
     * @param stdClass $course the course to search
     * @param string $subtype the subtype of the qbank module {@see self::SHARED_TYPES}
     * @return int[]
     */
    private static function get_qbank_ids_of_type_in_course(stdClass $course, string $subtype): array {
        global $DB;

        if (!in_array($subtype, self::SHARED_TYPES)) {
            throw new \moodle_exception('Invalid question bank type: ' . $subtype);
        }

        $modinfo = get_fast_modinfo($course);
        $defaultyactivityname = self::get_default_question_bank_activity_name();
        $qbanks = $modinfo->get_instances_of($defaultyactivityname);

        if (!empty($qbanks)) {
            $sql = "SELECT cm.id
                      FROM {course_modules} cm
                      JOIN {modules} m ON m.id = cm.module
                      JOIN {{$defaultyactivityname}} q ON q.id = cm.instance AND cm.module = m.id
                     WHERE cm.course = :course
                       AND q.type = :type";

            return $DB->get_fieldset_sql($sql, ['type' => $subtype, 'course' => $course->id]);
        }

        return [];
    }

    /**
     * Create a bank on the course from default options.
     *
     * @param stdClass $course the course that the new module is being created in
     * @param string $bankname name of the new module
     * @param string $type {@see self::TYPES}
     * @return cm_info
     */
    public static function create_default_open_instance(
        stdClass $course,
        string $bankname,
        string $type = self::TYPE_STANDARD
    ): cm_info {
        global $DB;

        if (!in_array($type, self::SHARED_TYPES)) {
            throw new \RuntimeException('invalid type');
        }

        // Preview bank must be created at site course.
        if ($type === self::TYPE_PREVIEW) {
            if ($qbank = self::get_preview_open_instance_type()) {
                return $qbank;
            }
            $course = get_site();
        }

        // We can only have one of these types per course.
        if ($type === self::TYPE_SYSTEM && $qbank = self::get_default_open_instance_system_type($course)) {
            return $qbank;
        }

        $module = $DB->get_record('modules', ['name' => self::get_default_question_bank_activity_name()], '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        // STANDARD type needs capability checks.
        if ($type === self::TYPE_STANDARD) {
            require_capability('moodle/course:manageactivities', $context);
            if (!course_allowed_module($course, $module->name)) {
                throw new \moodle_exception('moduledisable');
            }
        }

        if (strlen($bankname) > self::BANK_NAME_MAX_LENGTH) {
            throw new \coding_exception(
                'The provided bankname is too long for the database field.',
                'Use question_bank_helper::get_bank_name_string to get a suitably truncated name.',
            );
        }

        $data = new stdClass();
        $data->section = 0;
        $data->visible = 0;
        $data->course = $course->id;
        $data->module = $module->id;
        $data->modulename = $module->name;
        $data->groupmode = $course->groupmode;
        $data->groupingid = $course->defaultgroupingid;
        $data->id = '';
        $data->instance = '';
        $data->coursemodule = '';
        $data->downloadcontent = DOWNLOAD_COURSE_CONTENT_ENABLED;
        $data->visibleoncoursepage = 0;
        $data->name = $bankname;
        $data->type = in_array($type, self::SHARED_TYPES) ? $type : self::TYPE_STANDARD;
        $data->showdescription = $type === self::TYPE_STANDARD ? 0 : 1;

        $mod = add_moduleinfo($data, $course);

        // Have to set this manually as the system because this bank type is not intended to be created directly by a user.
        if ($type === self::TYPE_SYSTEM) {
            $DB->set_field($module->name, 'intro', get_string('systembankdescription', 'question'), ['id' => $mod->instance]);
            $DB->set_field($module->name, 'introformat', FORMAT_HTML, ['id' => $mod->instance]);
        }

        return get_fast_modinfo($course)->get_cm($mod->coursemodule);
    }

    /**
     * Get the url that shows the banks list of a course.
     *
     * @param int $courseid of the course to get the url for.
     * @param bool $createdefault Pass true if you want the URL to create a default qbank instance when referred.
     * @return moodle_url
     */
    public static function get_url_for_qbank_list(int $courseid, bool $createdefault = false): moodle_url {
        $url = new moodle_url('/question/banks.php', ['courseid' => $courseid]);
        if ($createdefault) {
            $url->param('createdefault', true);
        }
        return $url;
    }

    /**
     * This task should only ever be called once, on install/upgrade. But we may need to warn the user on some pages
     * that some banks may not have been transferred yet if it failed or hasn't yet completed.
     *
     * @return bool
     */
    public static function has_bank_migration_task_completed_successfully(): bool {
        $defaultbank = self::get_default_question_bank_activity_name();
        $task = manager::get_adhoc_tasks("\\mod_{$defaultbank}\\task\\transfer_question_categories");
        return empty($task);
    }

    /**
     * Get the activity plugin name that will be the type used for default bank creation and management.
     *
     * @return string
     */
    public static function get_default_question_bank_activity_name(): string {
        global $CFG;
        return $CFG->corequestion_defaultqbankmod ?? 'qbank';
    }

    /**
     * Get the requested language string, with parameters truncated to ensure the result fits in the database.
     *
     * Since we may be generating a question bank name based on an existing course or category name, we need to ensure
     * that the resulting string isn't longer than the maximum module name.
     *
     * @param string $identifier The string identifier
     * @param string $component The string component
     * @param mixed|null $params The string parameters (a single string, array or object as accepted by get_string)
     * @return string The string truncated to a length that will fit in the database.
     */
    public static function get_bank_name_string(string $identifier, string $component, mixed $params = null): string {
        if (is_object($params)) {
            $shortparams = (array) $params;
        } else {
            $shortparams = $params;
        }
        $bankname = get_string($identifier, $component, $shortparams);
        if (!is_null($shortparams)) {
            $trimlength = self::BANK_NAME_MAX_LENGTH - 4;
            while (\core_text::strlen($bankname) > self::BANK_NAME_MAX_LENGTH && $trimlength > 0) {
                // Gradually shorten the string parameters until the resulting string is short enough.
                if (is_array($shortparams)) {
                    $shortparams = array_map(fn($param) => shorten_text(trim($param), $trimlength), $shortparams);
                } else {
                    $shortparams = shorten_text(trim($shortparams), $trimlength);
                }
                $bankname = get_string($identifier, $component, $shortparams);
                $trimlength -= 10;
            }
        }
        // As a failsafe, limit the length of the final string in case the lang string is too long.
        return shorten_text($bankname, self::BANK_NAME_MAX_LENGTH);
    }
}
