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
 * @package moodlecore
 * @subpackage questionbank
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This static class provides access to the other question bank.
 *
 * It provides functions for managing question types and question definitions.
 *
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_bank {
    /** @var array question type name => question_type subclass. */
    private static $questiontypes = array();

    /** @var array question type name => 1. Records which question definitions have been loaded. */
    private static $loadedqdefs = array();

    protected static $questionfinder = null;

    /** @var boolean nasty hack to allow unit tests to call {@link load_question()}. */
    private static $testmode = false;
    private static $testdata = array();

    /**
     * Get the question type class for a particular question type.
     * @param string $qtypename the question type name. For example 'multichoice' or 'shortanswer'.
     * @param boolean $mustexist if false, the missing question type is returned when
     *      the requested question type is not installed.
     * @return question_type the corresponding question type class.
     */
    public static function get_qtype($qtypename, $mustexist = true) {
        global $CFG;
        if (isset(self::$questiontypes[$qtypename])) {
            return self::$questiontypes[$qtypename];
        }
        $file = $CFG->dirroot . '/question/type/' . $qtypename . '/questiontype.php';
        if (!is_readable($file)) {
            if ($mustexist || $qtypename == 'missingtype') {
                throw new Exception('Unknown question type ' . $qtypename);
            } else {
                return self::get_qtype('missingtype');
            }
        }
        include_once($file);
        $class = 'qtype_' . $qtypename;
        self::$questiontypes[$qtypename] = new $class();
        return self::$questiontypes[$qtypename];
    }

    /**
     * @param $qtypename the internal name of a question type, for example multichoice.
     * @return string the human_readable name of this question type, from the language pack.
     */
    public static function get_qtype_name($qtypename) {
        return self::get_qtype($qtypename)->menu_name();
    }

    /**
     * @return array all the installed question types.
     */
    public static function get_all_qtypes() {
        $qtypes = array();
        $plugins = get_list_of_plugins('question/type', 'datasetdependent');
        foreach ($plugins as $plugin) {
            $qtypes[$plugin] = self::get_qtype($plugin);
        }
        return $qtypes;
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
            throw new Exception('Unknown question type (no definition) ' . $qtypename);
        }
        include_once($file);
        self::$loadedqdefs[$qtypename] = 1;
    }

    /**
     * Load a question definition from the database. The object returned
     * will actually be of an appropriate {@link question_definition} subclass.
     * @param integer $questionid the id of the question to load.
     * @return question_definition loaded from the database.
     */
    public static function load_question($questionid) {
        if (self::$testmode) {
            // Evil, test code in production, but now way round it.
            return self::return_test_question_data($questionid);
        }

        $questiondata = get_record('question', 'id', $questionid);
        if (empty($questiondata)) {
            throw new Exception('Unknown question id ' . $questionid);
        }
        get_question_options($questiondata);
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
        if (is_null(self::$questionfinder)) {
            self::$questionfinder = new question_finder();
        }
        return self::$questionfinder;
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
            throw new Exception('question_bank::return_test_data(' . $questionid .
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
            throw new Exception('question_bank::load_test_data called when not in test mode.');
        }
        self::$testdata[$question->id] = $question;
    }
}

class question_finder {
    /**
     * Get the ids of all the questions in a list of categoryies.
     * @param integer|string|array $categoryids either a categoryid, or a comma-separated list
     *      category ids, or an array of them.
     * @param string $extraconditions extra conditions to AND with the rest of the where clause.
     * @return array questionid => questionid.
     */
    public function get_questions_from_categories($categoryids, $extraconditions) {
        if (is_array($categoryids)) {
            $categoryids = implode(',', $categoryids);
        }

        if ($extraconditions) {
            $extraconditions = ' AND (' . $extraconditions . ')';
        }
        $questionids = get_records_select_menu('question',
                "category IN ($categoryids)
                 AND parent = 0
                 AND hidden = 0
                 $extraconditions", '', 'id,id AS id2');
        if (!$questionids) {
            $questionids = array();
        }
        return $questionids;
    }
}
