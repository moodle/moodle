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
 * More object oriented wrappers around parts of the Moodle question bank.
 *
 * In due course, I expect that the question bank will be converted to a
 * fully object oriented structure, at which point this file can be a
 * starting point.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../type/questiontypebase.php');


/**
 * This static class provides access to the other question bank.
 *
 * It provides functions for managing question types and question definitions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_bank {
    // TODO: This limit can be deleted if someday we move all TEXTS to BIG ones. MDL-19603
    const MAX_SUMMARY_LENGTH = 32000;

    /** @var array question type name => question_type subclass. */
    private static $questiontypes = array();

    /** @var array question type name => 1. Records which question definitions have been loaded. */
    private static $loadedqdefs = array();

    /** @var boolean nasty hack to allow unit tests to call {@link load_question()}. */
    private static $testmode = false;
    private static $testdata = array();

    private static $questionconfig = null;

    /**
     * @var array string => string The standard set of grade options (fractions)
     * to use when editing questions, in the range 0 to 1 inclusive. Array keys
     * are string becuase: a) we want grades to exactly 7 d.p., and b. you can't
     * have float array keys in PHP.
     * Initialised by {@link ensure_grade_options_initialised()}.
     */
    private static $fractionoptions = null;
    /** @var array string => string The full standard set of (fractions) -1 to 1 inclusive. */
    private static $fractionoptionsfull = null;

    /**
     * @param string $qtypename a question type name, e.g. 'multichoice'.
     * @return bool whether that question type is installed in this Moodle.
     */
    public static function is_qtype_installed($qtypename) {
        $plugindir = core_component::get_plugin_directory('qtype', $qtypename);
        return $plugindir && is_readable($plugindir . '/questiontype.php');
    }

    /**
     * Get the question type class for a particular question type.
     * @param string $qtypename the question type name. For example 'multichoice' or 'shortanswer'.
     * @param bool $mustexist if false, the missing question type is returned when
     *      the requested question type is not installed.
     * @return question_type the corresponding question type class.
     */
    public static function get_qtype($qtypename, $mustexist = true) {
        global $CFG;
        if (isset(self::$questiontypes[$qtypename])) {
            return self::$questiontypes[$qtypename];
        }
        $file = core_component::get_plugin_directory('qtype', $qtypename) . '/questiontype.php';
        if (!is_readable($file)) {
            if ($mustexist || $qtypename == 'missingtype') {
                throw new coding_exception('Unknown question type ' . $qtypename);
            } else {
                return self::get_qtype('missingtype');
            }
        }
        include_once($file);
        $class = 'qtype_' . $qtypename;
        if (!class_exists($class)) {
            throw new coding_exception("Class {$class} must be defined in {$file}.");
        }
        self::$questiontypes[$qtypename] = new $class();
        return self::$questiontypes[$qtypename];
    }

    /**
     * Load the question configuration data from config_plugins.
     * @return object get_config('question') with caching.
     */
    public static function get_config() {
        if (is_null(self::$questionconfig)) {
            self::$questionconfig = get_config('question');
        }
        return self::$questionconfig;
    }

    /**
     * @param string $qtypename the internal name of a question type. For example multichoice.
     * @return bool whether users are allowed to create questions of this type.
     */
    public static function qtype_enabled($qtypename) {
        $config = self::get_config();
        $enabledvar = $qtypename . '_disabled';
        return self::qtype_exists($qtypename) && empty($config->$enabledvar) &&
                self::get_qtype($qtypename)->menu_name() != '';
    }

    /**
     * @param string $qtypename the internal name of a question type. For example multichoice.
     * @return bool whether this question type exists.
     */
    public static function qtype_exists($qtypename) {
        return array_key_exists($qtypename, core_component::get_plugin_list('qtype'));
    }

    /**
     * @param $qtypename the internal name of a question type, for example multichoice.
     * @return string the human_readable name of this question type, from the language pack.
     */
    public static function get_qtype_name($qtypename) {
        return self::get_qtype($qtypename)->local_name();
    }

    /**
     * @return array all the installed question types.
     */
    public static function get_all_qtypes() {
        $qtypes = array();
        foreach (core_component::get_plugin_list('qtype') as $plugin => $notused) {
            try {
                $qtypes[$plugin] = self::get_qtype($plugin);
            } catch (coding_exception $e) {
                // Catching coding_exceptions here means that incompatible
                // question types do not cause the rest of Moodle to break.
            }
        }
        return $qtypes;
    }

    /**
     * Sort an array of question types according to the order the admin set up,
     * and then alphabetically for the rest.
     * @param array qtype->name() => qtype->local_name().
     * @return array sorted array.
     */
    public static function sort_qtype_array($qtypes, $config = null) {
        if (is_null($config)) {
            $config = self::get_config();
        }

        $sortorder = array();
        $otherqtypes = array();
        foreach ($qtypes as $name => $localname) {
            $sortvar = $name . '_sortorder';
            if (isset($config->$sortvar)) {
                $sortorder[$config->$sortvar] = $name;
            } else {
                $otherqtypes[$name] = $localname;
            }
        }

        ksort($sortorder);
        core_collator::asort($otherqtypes);

        $sortedqtypes = array();
        foreach ($sortorder as $name) {
            $sortedqtypes[$name] = $qtypes[$name];
        }
        foreach ($otherqtypes as $name => $notused) {
            $sortedqtypes[$name] = $qtypes[$name];
        }
        return $sortedqtypes;
    }

    /**
     * @return array all the question types that users are allowed to create,
     *      sorted into the preferred order set on the admin screen.
     */
    public static function get_creatable_qtypes() {
        $config = self::get_config();
        $allqtypes = self::get_all_qtypes();

        $qtypenames = array();
        foreach ($allqtypes as $name => $qtype) {
            if (self::qtype_enabled($name)) {
                $qtypenames[$name] = $qtype->local_name();
            }
        }

        $qtypenames = self::sort_qtype_array($qtypenames);

        $creatableqtypes = array();
        foreach ($qtypenames as $name => $notused) {
            $creatableqtypes[$name] = $allqtypes[$name];
        }
        return $creatableqtypes;
    }

    /**
     * Load the question definition class(es) belonging to a question type. That is,
     * include_once('/question/type/' . $qtypename . '/question.php'), with a bit
     * of checking.
     * @param string $qtypename the question type name. For example 'multichoice' or 'shortanswer'.
     */
    public static function load_question_definition_classes($qtypename) {
        global $CFG;
        if (isset(self::$loadedqdefs[$qtypename])) {
            return;
        }
        $file = $CFG->dirroot . '/question/type/' . $qtypename . '/question.php';
        if (!is_readable($file)) {
            throw new coding_exception('Unknown question type (no definition) ' . $qtypename);
        }
        include_once($file);
        self::$loadedqdefs[$qtypename] = 1;
    }

    /**
     * This method needs to be called whenever a question is edited.
     */
    public static function notify_question_edited($questionid) {
        question_finder::get_instance()->uncache_question($questionid);
    }

    /**
     * Load a question definition data from the database. The data will be
     * returned as a plain stdClass object.
     * @param int $questionid the id of the question to load.
     * @return object question definition loaded from the database.
     */
    public static function load_question_data($questionid) {
        return question_finder::get_instance()->load_question_data($questionid);
    }

    /**
     * Load a question definition from the database. The object returned
     * will actually be of an appropriate {@link question_definition} subclass.
     * @param int $questionid the id of the question to load.
     * @param bool $allowshuffle if false, then any shuffle option on the selected
     *      quetsion is disabled.
     * @return question_definition loaded from the database.
     */
    public static function load_question($questionid, $allowshuffle = true) {

        if (self::$testmode) {
            // Evil, test code in production, but no way round it.
            return self::return_test_question_data($questionid);
        }

        $questiondata = self::load_question_data($questionid);

        if (!$allowshuffle) {
            $questiondata->options->shuffleanswers = false;
        }
        return self::make_question($questiondata);
    }

    /**
     * Convert the question information loaded with {@link get_question_options()}
     * to a question_definintion object.
     * @param object $questiondata raw data loaded from the database.
     * @return question_definition loaded from the database.
     */
    public static function make_question($questiondata) {
        return self::get_qtype($questiondata->qtype, false)->make_question($questiondata, false);
    }

    /**
     * @return question_finder a question finder.
     */
    public static function get_finder() {
        return question_finder::get_instance();
    }

    /**
     * Only to be called from unit tests. Allows {@link load_test_data()} to be used.
     */
    public static function start_unit_test() {
        self::$testmode = true;
    }

    /**
     * Only to be called from unit tests. Allows {@link load_test_data()} to be used.
     */
    public static function end_unit_test() {
        self::$testmode = false;
        self::$testdata = array();
    }

    private static function return_test_question_data($questionid) {
        if (!isset(self::$testdata[$questionid])) {
            throw new coding_exception('question_bank::return_test_data(' . $questionid .
                    ') called, but no matching question has been loaded by load_test_data.');
        }
        return self::$testdata[$questionid];
    }

    /**
     * To be used for unit testing only. Will throw an exception if
     * {@link start_unit_test()} has not been called first.
     * @param object $questiondata a question data object to put in the test data store.
     */
    public static function load_test_question_data(question_definition $question) {
        if (!self::$testmode) {
            throw new coding_exception('question_bank::load_test_data called when ' .
                    'not in test mode.');
        }
        self::$testdata[$question->id] = $question;
    }

    protected static function ensure_fraction_options_initialised() {
        if (!is_null(self::$fractionoptions)) {
            return;
        }

        // define basic array of grades. This list comprises all fractions of the form:
        // a. p/q for q <= 6, 0 <= p <= q
        // b. p/10 for 0 <= p <= 10
        // c. 1/q for 1 <= q <= 10
        // d. 1/20
        $rawfractions = array(
            0.9000000,
            0.8333333,
            0.8000000,
            0.7500000,
            0.7000000,
            0.6666667,
            0.6000000,
            0.5000000,
            0.4000000,
            0.3333333,
            0.3000000,
            0.2500000,
            0.2000000,
            0.1666667,
            0.1428571,
            0.1250000,
            0.1111111,
            0.1000000,
            0.0500000,
        );

        // Put the None option at the top.
        self::$fractionoptions = array(
            '0.0' => get_string('none'),
            '1.0' => '100%',
        );
        self::$fractionoptionsfull = array(
            '0.0' => get_string('none'),
            '1.0' => '100%',
        );

        // The the positive grades in descending order.
        foreach ($rawfractions as $fraction) {
            $percentage = format_float(100 * $fraction, 5, true, true) . '%';
            self::$fractionoptions["{$fraction}"] = $percentage;
            self::$fractionoptionsfull["{$fraction}"] = $percentage;
        }

        // The the negative grades in descending order.
        foreach (array_reverse($rawfractions) as $fraction) {
            self::$fractionoptionsfull['' . (-$fraction)] =
                    format_float(-100 * $fraction, 5, true, true) . '%';
        }

        self::$fractionoptionsfull['-1.0'] = '-100%';
    }

    /**
     * @return array string => string The standard set of grade options (fractions)
     * to use when editing questions, in the range 0 to 1 inclusive. Array keys
     * are string becuase: a) we want grades to exactly 7 d.p., and b. you can't
     * have float array keys in PHP.
     * Initialised by {@link ensure_grade_options_initialised()}.
     */
    public static function fraction_options() {
        self::ensure_fraction_options_initialised();
        return self::$fractionoptions;
    }

    /** @return array string => string The full standard set of (fractions) -1 to 1 inclusive. */
    public static function fraction_options_full() {
        self::ensure_fraction_options_initialised();
        return self::$fractionoptionsfull;
    }

    /**
     * Return a list of the different question types present in the given categories.
     *
     * @param  array $categories a list of category ids
     * @return array the list of question types in the categories
     * @since  Moodle 3.1
     */
    public static function get_all_question_types_in_categories($categories) {
        global $DB;

        list($categorysql, $params) = $DB->get_in_or_equal($categories);
        $sql = "SELECT DISTINCT q.qtype
                           FROM {question} q
                           JOIN {question_versions} qv ON qv.questionid = q.id
                           JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                          WHERE qbe.questioncategoryid $categorysql";

        $qtypes = $DB->get_fieldset_sql($sql, $params);
        return $qtypes;
    }
}


/**
 * Class for loading questions according to various criteria.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_finder implements cache_data_source {
    /** @var question_finder the singleton instance of this class. */
    protected static $questionfinder = null;

    /**
     * @return question_finder a question finder.
     */
    public static function get_instance() {
        if (is_null(self::$questionfinder)) {
            self::$questionfinder = new question_finder();
        }
        return self::$questionfinder;
    }

    /* See cache_data_source::get_instance_for_cache. */
    public static function get_instance_for_cache(cache_definition $definition) {
        return self::get_instance();
    }

    /**
     * @return cache_application the question definition cache we are using.
     */
    protected function get_data_cache() {
        // Do not double cache here because it may break cache resetting.
        return cache::make('core', 'questiondata');
    }

    /**
     * This method needs to be called whenever a question is edited.
     */
    public function uncache_question($questionid) {
        $this->get_data_cache()->delete($questionid);
    }

    /**
     * Load a question definition data from the database. The data will be
     * returned as a plain stdClass object.
     * @param int $questionid the id of the question to load.
     * @return object question definition loaded from the database.
     */
    public function load_question_data($questionid) {
        return $this->get_data_cache()->get($questionid);
    }

    /**
     * Get the ids of all the questions in a list of categories.
     * @param array $categoryids either a categoryid, or a comma-separated list
     *      category ids, or an array of them.
     * @param string $extraconditions extra conditions to AND with the rest of
     *      the where clause. Must use named parameters.
     * @param array $extraparams any parameters used by $extraconditions.
     * @return array questionid => questionid.
     */
    public function get_questions_from_categories($categoryids, $extraconditions,
            $extraparams = array()) {
        global $DB;

        list($qcsql, $qcparams) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'qc');

        if ($extraconditions) {
            $extraconditions = ' AND (' . $extraconditions . ')';
        }
        $qcparams['readystatus'] = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $sql = "SELECT q.id, q.id AS id2
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                 WHERE qbe.questioncategoryid {$qcsql}
                       AND q.parent = 0
                       AND qv.status = :readystatus
                       {$extraconditions}";

        return $DB->get_records_sql_menu($sql, $qcparams + $extraparams);
    }

    /**
     * Get the ids of all the questions in a list of categories, with the number
     * of times they have already been used in a given set of usages.
     *
     * The result array is returned in order of increasing (count previous uses).
     *
     * @param array $categoryids an array question_category ids.
     * @param qubaid_condition $qubaids which question_usages to count previous uses from.
     * @param string $extraconditions extra conditions to AND with the rest of
     *      the where clause. Must use named parameters.
     * @param array $extraparams any parameters used by $extraconditions.
     * @return array questionid => count of number of previous uses.
     */
    public function get_questions_from_categories_with_usage_counts($categoryids,
            qubaid_condition $qubaids, $extraconditions = '', $extraparams = array()) {
        return $this->get_questions_from_categories_and_tags_with_usage_counts(
                $categoryids, $qubaids, $extraconditions, $extraparams);
    }

    /**
     * Get the ids of all the questions in a list of categories that have ALL the provided tags,
     * with the number of times they have already been used in a given set of usages.
     *
     * The result array is returned in order of increasing (count previous uses).
     *
     * @param array $categoryids an array of question_category ids.
     * @param qubaid_condition $qubaids which question_usages to count previous uses from.
     * @param string $extraconditions extra conditions to AND with the rest of
     *      the where clause. Must use named parameters.
     * @param array $extraparams any parameters used by $extraconditions.
     * @param array $tagids an array of tag ids
     * @return array questionid => count of number of previous uses.
     */
    public function get_questions_from_categories_and_tags_with_usage_counts($categoryids,
            qubaid_condition $qubaids, $extraconditions = '', $extraparams = array(), $tagids = array()) {
        global $DB;

        list($qcsql, $qcparams) = $DB->get_in_or_equal($categoryids, SQL_PARAMS_NAMED, 'qc');

        $readystatus = \core_question\local\bank\question_version_status::QUESTION_STATUS_READY;
        $select = "q.id, (SELECT COUNT(1)
                            FROM " . $qubaids->from_question_attempts('qa') . "
                           WHERE qa.questionid = q.id AND " . $qubaids->where() . "
                         ) AS previous_attempts";
        $from   = "{question} q";
        $join   = "JOIN {question_versions} qv ON qv.questionid = q.id
                   JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid";
        $from = $from . " " . $join;
        $where  = "qbe.questioncategoryid {$qcsql}
               AND q.parent = 0
               AND qv.status = '$readystatus'
               AND qv.version = (SELECT MAX(v.version)
                                  FROM {question_versions} v
                                  JOIN {question_bank_entries} be
                                    ON be.id = v.questionbankentryid
                                 WHERE be.id = qbe.id)";
        $params = $qcparams;

        if (!empty($tagids)) {
            // We treat each additional tag as an AND condition rather than
            // an OR condition.
            //
            // For example, if the user filters by the tags "foo" and "bar" then
            // we reduce the question list to questions that are tagged with both
            // "foo" AND "bar". Any question that does not have ALL of the specified
            // tags will be omitted.
            list($tagsql, $tagparams) = $DB->get_in_or_equal($tagids, SQL_PARAMS_NAMED, 'ti');
            $tagparams['tagcount'] = count($tagids);
            $tagparams['questionitemtype'] = 'question';
            $tagparams['questioncomponent'] = 'core_question';
            $where .= " AND q.id IN (SELECT ti.itemid
                                       FROM {tag_instance} ti
                                      WHERE ti.itemtype = :questionitemtype
                                            AND ti.component = :questioncomponent
                                            AND ti.tagid {$tagsql}
                                   GROUP BY ti.itemid
                                     HAVING COUNT(itemid) = :tagcount)";
            $params += $tagparams;
        }

        if ($extraconditions) {
            $extraconditions = ' AND (' . $extraconditions . ')';
        }

        return $DB->get_records_sql_menu("SELECT $select
                                                FROM $from
                                               WHERE $where $extraconditions
                                            ORDER BY previous_attempts",
                $qubaids->from_where_params() + $params + $extraparams);
    }

    /* See cache_data_source::load_for_cache. */
    public function load_for_cache($questionid) {
        global $DB;

        $sql = 'SELECT q.id, qc.id as category, q.parent, q.name, q.questiontext, q.questiontextformat,
                       q.generalfeedback, q.generalfeedbackformat, q.defaultmark, q.penalty, q.qtype,
                       q.length, q.stamp, q.timecreated, q.timemodified,
                       q.createdby, q.modifiedby, qbe.idnumber,
                       qc.contextid,
                       qv.status,
                       qv.id as versionid,
                       qv.version,
                       qv.questionbankentryid
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE q.id = :id';

        $questiondata = $DB->get_record_sql($sql, ['id' => $questionid], MUST_EXIST);
        get_question_options($questiondata);
        return $questiondata;
    }

    /* See cache_data_source::load_many_for_cache. */
    public function load_many_for_cache(array $questionids) {
        global $DB;
        list($idcondition, $params) = $DB->get_in_or_equal($questionids);
        $sql = 'SELECT q.id, qc.id as category, q.parent, q.name, q.questiontext, q.questiontextformat,
                       q.generalfeedback, q.generalfeedbackformat, q.defaultmark, q.penalty, q.qtype,
                       q.length, q.stamp, q.timecreated, q.timemodified,
                       q.createdby, q.modifiedby, qbe.idnumber,
                       qc.contextid,
                       qv.status,
                       qv.id as versionid,
                       qv.version,
                       qv.questionbankentryid
                  FROM {question} q
                  JOIN {question_versions} qv ON qv.questionid = q.id
                  JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
                  JOIN {question_categories} qc ON qc.id = qbe.questioncategoryid
                 WHERE q.id ';

        $questiondata = $DB->get_records_sql($sql . $idcondition, $params);

        foreach ($questionids as $id) {
            if (!array_key_exists($id, $questiondata)) {
                throw new dml_missing_record_exception('question', '', ['id' => $id]);
            }
            get_question_options($questiondata[$id]);
        }
        return $questiondata;
    }
}
