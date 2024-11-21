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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace Containers;

use Helpers\ArrayHelper;
use Helpers\FunctionHelper;
use DataExtractor;


class ColumnsContainer extends BaseContainer {

    static protected $columns = array();
    static protected $modifiers = array();
    static protected $mode = DataExtractor::MYSQL_MODE;
    static protected $infoTemplate = "SELECT d.data FROM {user_info_data} AS d INNER JOIN {user_info_field} f ON d.fieldid = f.id WHERE f.id = :%s AND d.userid = u.id";
    static protected $gradebookTemplate = "SELECT %s FROM {grade_grades} AS g WHERE g.userid = u.id AND g.itemid = :%s";
    static protected $columnNumber = 0;

    protected static function init($mode = DataExtractor::MYSQL_MODE) {

        if (static::$columns && static::$mode === $mode) {
            return;
        }

        static::$mode = $mode;
        $activityNames = \get_modules_names();

        $modifiers = array(
            1 => 'SUM',
            2 => 'COUNT',
            3 => 'DISTINCT',
            4 => 'AVG',
            5 => 'MAX',
            6 => 'MIN',
            7 => array(
                DataExtractor::MYSQL_MODE => 'TIME_TO_SEC',
                DataExtractor::POSTGRES_MODE => function($value, $params) {
                    return "extract ('epoch' from TO_TIMESTAMP($value, 'HH24:MI:SS')::TIME)";
                }),
            8 => array(
                DataExtractor::MYSQL_MODE => 'SEC_TO_TIME',
                DataExtractor::POSTGRES_MODE => ''
            ),
            9 => 'ROUND',
            10 => 'LOWER',
            11 => array(
                DataExtractor::MYSQL_MODE => function($value, $params, DataExtractor $extractor) {
                    $params = !empty($params) ? $params : array('separator' => ', ');
                    if (!empty($params['order'])) {
                        $params['order'] = OrdersContainer::release($params['order'], $extractor);
                    } else {
                        $params['order'] = '';
                    }

                    if (!empty($params['distinct'])) {
                        $distinct = 'DISTINCT ';
                    } else {
                        $distinct = '';
                    }

                    return "GROUP_CONCAT({$distinct}{$value} SEPARATOR '{$params['separator']}')";
                },
                DataExtractor::POSTGRES_MODE => function($value, $params, DataExtractor $extractor) {
                    $params = !empty($params) ? $params : array('separator' => ', ');
                    if (!empty($params['order'])) {
                        $params['order'] = OrdersContainer::release($params['order'], $extractor);
                    } else {
                        $params['order'] = '';
                    }

                    if (!empty($params['distinct'])) {
                        $distinct = 'DISTINCT ';
                    } else {
                        $distinct = '';
                    }

                    return "string_agg({$distinct}$value::character varying, '" . $params['separator'] . "')";
                }
            ),
            12 => array(
                DataExtractor::MYSQL_MODE => 'WEEKDAY',
                DataExtractor::POSTGRES_MODE => function($value, $params) {
                    return "extract(dow from $value::timestamp)";
                }
            ),
            13 => array(
                DataExtractor::MYSQL_MODE => function($value, $params) {
                    return "(DAYOFWEEK($value) - 1)";
                },
                DataExtractor::POSTGRES_MODE => function($value, $params) {
                    return "EXTRACT(DOW FROM $value)";
                }
            ),
            14 => array(
                DataExtractor::MYSQL_MODE => function($value, $params) {
                    return "DATE_FORMAT($value, '%a')";
                },
                DataExtractor::POSTGRES_MODE => function($value, $params) {
                    return "to_char($value, 'Day')";
                }
            ),
            15 => function($value, $params) {
                return "(CASE WHEN $value IS NOT NULL THEN $value ELSE 0 END)";
            },
            17 => array(
                DataExtractor::MYSQL_MODE => function($value, $params = array()) {

                    $format = isset($params['format'])? $params['format'] : '%Y-%m-%d %T';
                    return "FROM_UNIXTIME($value, '$format')";
                },
                DataExtractor::POSTGRES_MODE => "to_timestamp"
            ),
            18 => array(
                DataExtractor::MYSQL_MODE => "MONTH",
                DataExtractor::POSTGRES_MODE => function($value, $params) {
                    return "date_part('month', $value)";
                }
            ),
            19 => function($value, $params) {
                return "(CASE WHEN $value IS NULL THEN 0 ELSE $value END)";
            },
            20 => function($value, $params) {
                return "DATE_ADD($value, INTERVAL " . $params['value'] . " " . $params['type'] . ")";
            },
            21 => function($value, $params) {
                return $value . $params["operator"] . $params["value"];
            },
            22 => function($value, $params) {
                $points = isset($params['points'])? $params['points'] : 2;
                return "ROUND($value, $points)";
            },
            23 => function($value, $params) {
                if (!empty($params['bound'])) {
                    return "(CASE WHEN $value < " . $params['bound'] . " THEN 0 ELSE 1 END)";
                } else {
                    return "(CASE WHEN $value IS NULL THEN 0 ELSE 1 END)";
                }
            },
            24 => function($value, $params) {
                $statuses = implode(',', $params['complete_options']);
                return "CASE WHEN (SELECT COUNT($value) FROM {course_modules_completion} cmc WHERE cmc.coursemoduleid = cm.id) THEN ((SELECT COUNT($value) FROM {course_modules_completion} cmc WHERE cmc.completionstate IN(" . $statuses . ") AND cmc.coursemoduleid = cm.id) / (SELECT COUNT($value) FROM {course_modules_completion} cmc WHERE cmc.coursemoduleid = cm.id) * 100) ELSE 0 END";
            },
            25 => function($value, $params) {
                return "lil.timepoint + $value * 3600";
            },
            26 => "DAYOFMONTH",
            27 => array(
                DataExtractor::MYSQL_MODE => function($value, $params = array('separator' => ',', 'count' => 1)) {
                    $params['separator'] = isset($params['separator'])? $params['separator'] : ',';
                    $params['count'] = isset($params['count'])? $params['count'] : 1;
                    return "SUBSTRING_INDEX($value, '" . $params['separator'] . "', " . $params['count'] . ")";
                },
                DataExtractor::POSTGRES_MODE => function($value, $params = array('separator' => ',', 'count' => 1)) {
                    return "split_part($value, '" . $params['separator'] . "', " . $params['count'] . ")";
                }
            ),
            28 => array(
                DataExtractor::MYSQL_MODE => function($value, $params = array()) {
                    $params['separator'] = isset($params['separator'])? $params['separator'] : '_';
                    $params['count'] = isset($params['count'])? $params['count'] : 1;

                    return
                        "REPLACE (SUBSTRING(
                            SUBSTRING_INDEX($value, '" . $params['separator'] . "', " . $params['count'] . "),
                            CHAR_LENGTH(
                                SUBSTRING_INDEX($value, '{$params['separator']}', " . ($params['count'] - 1) . ")
                            ) + 1
                        ), '{$params['separator']}', '')";
                },
                DataExtractor::POSTGRES_MODE => function($value, $params = array('separator' => ',', 'count' => 1)) {
                    return "split_part($value, '" . $params['separator'] . "', " . $params['count'] . ")";
                }
            ),
            29 => function($value, $params = array()) {
                    $params['separator'] = isset($params['separator'])? $params['separator'] : '_';
                    return "POSITION('{$params['separator']}' IN $value)";
            },
            30 => function($value, $params = array()) {

                $params['sub'] = isset($params['sub'])? $params['sub'] : '$init';
                $sql = "SUBSTRING({$params['sub']}, $value";

                if (!empty($params['length'])) {
                    $sql .= ', ' . $params['length'];
                }

                $sql .= ')';
                return $sql;
            },
            31 => function($value, $params = array()) {

                $params['type'] = isset($params['type'])? $params['type'] : 'BOTH';
                $params['sub'] = isset($params['sub'])? $params['sub'] : ' ';

                return "TRIM({$params['type']} '{$params['sub']}' FROM $value)";
            },
            32 => function($value, $params = array()) {
                $params['needle']  = isset($params['needle'])? $params['needle'] : "'_'";
                $params['replace'] = isset($params['replace'])? $params['replace'] : "' '";
                $params['string']  = isset($params['string'])? $params['string'] : $value;

                return "REPLACE({$params['string']}, {$params['needle']}, {$params['replace']})";
            },
            33 => "COALESCE",
        );

        $columns = array(
            1  => array("name" => 'id', "sql" => "id"),
            2  => array("name" => "firstname", "sql" => "firstname"),
            3  => array("name" => "lastname", "sql" => "lastname"),
            4  => array("name" => "timecreated", "sql" => "timecreated"),
            5  => array("name" => "grade", "sql" => "grade"),
            6  => array("name" => "timespend", "sql" => "timespend"),
            7  => array("name" => "visits", "sql" => "visits"),
            8  => array("name" => "fullname", "sql" => "fullname"),
            9  => array("name" => "timecompleted", "sql" => "timecompleted"),
            10 => array("name" => "userid", "sql" => "userid"),
            11 => array("name" => "courseid", "sql" => "courseid"),
            12 => array("name" => "courses", "sql" => "courses"),
            13 => array("name" => "users", "sql" => "users"),
            14 => array("name" => "activities", "sql" => "activities"),
            15 => array("name" => "grade", "sql" => "ROUND(AVG(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END), 2)", "prepend" => false),
            16 => array("name" => "name", "sql" => "name"),
            17 => array("name" => "questions", "sql" => "questions"),
            18 => array("name" => "timespend", "sql" => "qa.timefinish - qa.timestart", "prepend" => false),
            19 => array("name" => "timemodified", "sql" => "timemodified"),
            20 => array("name" => "quizid", "sql" => "quizid"),
            21 => array("name" => "quizzesgrade", "sql" => "(qa.sumgrades/q.sumgrades)*100", "prepend" => false),
            22 => array("name" => "email", "sql" => "email") ,
            23 => array("name" => "timestart", "sql" => "timestart"),
            24 => array("name" => "state", "sql" => "state"),
            25 => array("name" => "teacher", "sql" => "teacher"),
            26 => array("name" => "fullname", "sql" => "CONCAT_WS(' ', u.firstname, u.lastname)", "prepend" => false),
            27 => array("name" => "instanceid", "sql" => "instanceid"),
            28 => array("name" => "enrol", "sql" => "enrol"),
            29 => array("name" => "instance", "sql" => "instance"),
            30 => array("name" => "scormid", "sql" => "scormid"),
            31 => array("name" => "value", "sql" => "value"),
            32 => array("name" => "attempt", "sql" => "attempt"),
            33 => array("name" => "iteminstance", "sql" => "iteminstance"),
            36 => array("name" => "quiz", "sql" => "quiz"),
            37 => array("name" => "id", "sql" => "CONCAT(u.id, '_', a.id)", "prepend" => false),
            38 => array("name" => "duedate", "sql" => "duedate"),
            39 => array("name" => "status", "sql" => "status"),
            40 => array("name" => "id", "sql" => "CONCAT(u.id, '_',b.id)", "prepend" => false),
            41 => array("name" => "dateissued", "sql" => "dateissued"),
            42 => array("name" => "dateexpire", "sql" => "dateexpire"),
            43 => array("name" => "id", "sql" => "CONCAT(u.id, '_', ct.id, '_', c.id)", "prepend" => false),
            44 => array("name" => "certificateid", "sql" => "certificateid"),
            45 => array("name" => "activityname", "sql" => '(' . $activityNames . ')', "prepend" => false),
            46 => array("name" => "itemmodule", "sql" => "itemmodule"),
            47 => array("name" => "useragent", "sql" => "useragent"),
            48 => array("name" => "userlang", "sql" => "userlang"),
            49 => array("name" => "useros", "sql" => "useros"),
            50 => array("name" => "param", "sql" => "param"),
            51 => array("name" => "certificates", "sql" => "certificates"),
            52 => array("name" => "enrols", "sql" => "enrols"),
            53 => array("name" => "completions", "sql" => "completions"),
            54 => array("name" => "course", "sql" => "course"),
            55 => array("name" => "lastlogin", "sql" => "lastlogin"),
            56 => array("name" => "lastaccess", "sql" => "lastaccess"),
            57 => array("name" => "discussions", "sql" => "discussions"),
            58 => array("name" => "posts", "sql" => "posts"),
            59 => array("name" => "avgmonth", "sql" => "avgmonth"),
            60 => array("name" => "avgweek", "sql" => "avgweek"),
            61 => array("name" => "avgday", "sql" => "avgday"),
            62 => array("name" => "forum", "sql" => "forum"),
            63 => array("name" => "avgmonth", "sql" => array(
                DataExtractor::MYSQL_MODE => "COUNT(DISTINCT fp.id)/(PERIOD_DIFF(DATE_FORMAT(NOW(), '%Y%m'), DATE_FORMAT(FROM_UNIXTIME(MAX(cm.added)), '%Y%m')) + 1)",
                DataExtractor::POSTGRES_MODE => "COUNT(DISTINCT fp.id)/(abs(extract(year from  age(now(), to_timestamp(MAX(cm.added))))::int * 12 + extract(month from  age(now(), to_timestamp(MAX(cm.added))))::int) + 1)"
            ), "prepend" => false),
            64 => array("name" => "avgweek", "sql" => array(
                DataExtractor::MYSQL_MODE => "COUNT(DISTINCT fp.id)/CEILING((DATEDIFF(DATE_FORMAT (NOW(), '%Y%m%d'),DATE_FORMAT(FROM_UNIXTIME(MAX(cm.added)), '%Y%m%d')) + 1)/7)",
                DataExtractor::POSTGRES_MODE => "COUNT(DISTINCT fp.id)/((CEILING(EXTRACT(epoch from age(now(), to_timestamp(MAX(cm.added)))) / 604800)::int) + 1)"
            ), "prepend" => false),
            65 => array("name" => "avgday", "sql" => array(
                DataExtractor::MYSQL_MODE => "COUNT(DISTINCT fp.id)/(DATEDIFF(DATE_FORMAT(NOW(), '%Y%m%d'),DATE_FORMAT(FROM_UNIXTIME(MAX(cm.added)), '%Y%m%d')) + 1)",
                DataExtractor::POSTGRES_MODE => "COUNT(DISTINCT fp.id)/((CEILING(EXTRACT(epoch from age(now(), to_timestamp(MAX(cm.added)))) / 86400)::int) + 1)"
            ), "prepend" => false),
            66 => array("name" => "address", "sql" => "address"),
            67 => array("name" => "username", "sql" => "username"),
            68 => array("name" => "city", "sql" => "city"),
            69 => array("name" => "institution", "sql" => "institution"),
            70 => array("name" => "department", "sql" => "department"),
            71 => array("name" => "country", "sql" => "country"),
            72 => array("name" => "idnumber", "sql" => "idnumber"),
            73 => array("name" => "firstaccess", "sql" => "firstaccess"),
            74 => array("name" => "category", "sql" => "category"),
            75 => array("name" => "element", "sql" => "element"),
            76 => array("name" => "roleid", "sql" => "roleid"),
            77 => array("name" => "enrolid", "sql" => "enrolid"),
            78 => array("name" => "module", "sql" => "module"),
            79 => array("name" => "userlang", "sql" => "userlang"),
            80 => array("name" => "useros", "sql" => "useros"),
            81 => array("name" => "cohortid", "sql" => "cohortid"),
            82 => array("name" => "coursemoduleid", "sql" => "coursemoduleid"),
            83 => array("name" => "completionstate", "sql" => "completionstate"),
            84 => array("name" => "assignment", "sql" => "assignment"),
            85 => array("name" => "badgeid", "sql" => "badgeid"),
            86 => array("name" => "visible", "sql" => "visible"),
            87 => array("name" => "timecompleted", "sql" => "timecompleted"),
            88 => array("name" => "timepoint", "sql" => "timepoint"),
            89 => array("name" => "trackid", "sql" => "trackid"),
            90 => array("name" => "itemid", "sql" => "itemid"),
            91 => array("name" => "itemtype", "sql" => "itemtype"),
            92 => array("name" => "finalgrade", "sql" => "finalgrade"),
            93 => array("name" => "discussion", "sql" => "discussion"),
            94 => array("name" => "contextid", "sql" => "contextid"),
            95 => array("name" => "contextlevel", "sql" => "contextlevel"),
            96 => array("name" => "page", "sql" => "page"),
            97 => array("name" => "shortname", "sql" => "shortname"),
            98 => array("name" => "format", "sql" => "format"),
            99 => array("name" => "startdate", "sql" => "startdate"),
            100 => array("name" => "completed", "sql" => "completed"),
            101 => array("name" => "id", "sql" => "CONCAT(u.id, '_', l.id)", "prepend" => false),
            102 => array("name" => "lessonid", "sql" => "lessonid"),
            104 => array("name" => "available", "sql" => "available"),
            105 => array("name" => "deadline", "sql" => "deadline"),
            106 => array("name" => "filename", "sql" => "filename"),
            107 => array("name" => "filesize", "sql" => "filesize"),
            108 => array("name" => "id", "sql" => "CONCAT(u.id, '_', c.id, '_', cm.id)", "prepend" => false),
            109 => array("name" => "id", "sql" => "CONCAT(u.id, '_', c.id)", "prepend" => false),
            110 => array("name" => "sessions", "sql" => "sessions"),
            111 => array("name" => "id", "sql" => "CONCAT(u.id, '_', q.id, '_', c.id)", "prepend" => false),
            112 => array("name" => "id", "sql" => "CONCAT(u.id, '_', s.id, '_', c.id)", "prepend" => false),
            113 => array("name" => "day", "sql" => "FROM_UNIXTIME(log.timecreated,'%m/%d/%Y')", "prepend" => false),
            114 => array("name" => "target", "sql" => "target"),
            115 => array("name" => "action", "sql" => "action"),
            116 => array("name" => "day", "sql" => "FROM_UNIXTIME(qa.timefinish,'%m/%d/%Y')", "prepend" => false),
            117 => array("name" => "timefinish", "sql" => "timefinish"),
            118 => array("name" => "day", "sql" => "FROM_UNIXTIME(ass.timemodified,'%m/%d/%Y')", "prepend" => false),
            119 => array("name" => "currentlogin", "sql" => "currentlogin"),
            120 => array("name" => "id", "sql" => "CONCAT(c.id, '_', CASE WHEN t.id IS NOT NULL THEN t.id ELSE 0 END)", "prepend" => false),
            121 => array("name" => "auth", "sql" => "auth"),
            122 => array("name" => "suspended", "sql" => "suspended"),
            123 => array("name" => "lang", "sql" => "lang"),
            124 => array("name" => "icq", "sql" => "icq"),
            125 => array("name" => "skype", "sql" => "skype"),
            126 => array("name" => "yahoo", "sql" => "yahoo"),
            127 => array("name" => "aim", "sql" => "aim"),
            128 => array("name" => "msn", "sql" => "msn"),
            129 => array("name" => "phone1", "sql" => "phone1"),
            130 => array("name" => "phone2", "sql" => "phone2"),
            131 => array("name" => "address", "sql" => "address"),
            132 => array("name" => "url", "sql" => "url"),
            133 => array("name" => "timeadded", "sql" => "timeadded"),
            134 => array("name" => "id", "sql" => "CONCAT(co.id, '_', u.id)", "prepend" => false),
            135 => array("name" => "deleted", "sql" => "deleted"),
            136 => array("name" => "grade", "sql" => "ROUND((CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END), 2)", "prepend" => false),
            137 => array("name" => "logid", "sql" => "logid"),
            138 => array("name" => "day", "sql" => "day"),
            139 => array("name" => "notcompleted", "sql" => "COUNT(ue.userid) - COUNT(cc.userid)", "prepend" => false),
            140 => array("name" => "pages", "sql" => "pages"),
            141 => array("name" => "month", "sql" => "month"),
            142 => array("name" => "hour", "sql" => "hour"),
            143 => array("name" => "logsuser", "sql" => "COUNT(log.id)/COUNT(DISTINCT ra.userid)", "prepend" => false),
            144 => array("name" => "quizattempts", "sql" => "COUNT(qa.id)/COUNT(DISTINCT ra.userid)", "prepend" => false),
            145 => array("name" => "assignattemts", "sql" => "COUNT(ass.id)/COUNT(DISTINCT ra.userid)", "prepend" => false),
            146 => array("name" => "badges", "sql" => "badges"),
            147 => array("name" => "gradetime", "sql" => "asg.timemodified - ass.timemodified", "prepend" => false),
            148 => array("name" => "grader", "sql" => "grader", "prepend" => false),
            149 => array("name" => "timeaccess", "sql" => "timeaccess"),
            150 => array("name" => "groupid", "sql" => "groupid"),
            151 => array("name" => "confirmed", "sql" => "confirmed"),
            152 => array("name" => "data", "sql" => "data"),
            153 => array("name" => "fieldid", "sql" => "fieldid"),
            154 => array("name" => "lastmodified", "sql" => "lastmodified"),
            155 => array("name" => "survey", "sql" => "survey"),
            156 => array("name" => "id", "sql" => "CONCAT(u.id, '_',sw.id)", "prepend" => false),
            157 => array("name" => "id", "sql" => "CONCAT(u.id, '_',qs.id)", "prepend" => false),
            158 => array("name" => "qid", "sql" => "qid"),
            159 => array("name" => "courses", "sql" => "coursecount"),
            160 => array("name" => "responsesummary", "sql" => "responsesummary"),
            161 => array("name" => "rightanswer", "sql" => "rightanswer"),
            162 => array("name" => "questionusageid", "sql" => "questionusageid"),
            163 => array("name" => "uniqueid", "sql" => "uniqueid"),
            164 => array("name" => "questionid", "sql" => "questionid"),
            165 => array("name" => "attempts", "sql" => "attempts"),
            166 => array("name" => "highnestgrade", "sql" => "highnestgrade"),
            167 => array("name" => "lowestnestgrade", "sql" => "lowestnestgrade"),
            168 => array(
                "name" => "barrier",
                "sql" => array(
                    DataExtractor::MYSQL_MODE => "(CASE WHEN ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2)>0 then concat(10*floor((ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2) - 1)/10) + 1, '-', 10*floor((ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2) - 1)/10) + 10) ELSE 0 END)",
                    DataExtractor::POSTGRES_MODE => "CASE WHEN ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2)>0 then concat(10*floor((ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2) - 1)/10) + 1, '-', 10*floor((ROUND(CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END, 2) - 1)/10) + 10) ELSE 0::TEXT END"
                ),
                "prepend" => false
            ),
            169 => array("name" => "competencyid", "sql" => "competencyid"),
            170 => array("name" => "partialrightanswers", "sql" => "CASE WHEN qas.state LIKE '%partial' THEN 1 ELSE 0 END", "prepend" => false),
            171 => array("name" => "rightanswers", "sql" => "CASE WHEN qas.state LIKE '%right' THEN 1 ELSE 0 END", "prepend" => false),
            172 => array("name" => "notanswered", "sql" => "CASE WHEN qas.state LIKE '%todo' OR qas.state LIKE '%gaveup' THEN 1 ELSE 0 END", "prepend" => false),
            173 => array("name" => "uniquecolumn", "sql" => "1", "prepend" => false),
            174 => array("name" => "questionattemptid", "sql" => "questionattemptid"),
            175 => array("name" => "wrong", "sql" => "CASE WHEN qas.state LIKE '%wrong' THEN 1 ELSE 0 END", "prepend" => false),
            176 => array("name" => "grade", "sql" => "CASE WHEN g.rawgrademax > 0 THEN (g.finalgrade/g.rawgrademax)*100 ELSE g.finalgrade END", "prepend" => false),
            177 => array("name" => "attemptnumber", "sql" => "attemptnumber"),
            178 => array("name" => "latest", "sql" => "latest"),
            179 => array("name" => "sequencenumber", "sql" => "sequencenumber"),
            180 => array("name" => "preview", "sql" => "preview"),
            181 => array("name" => "type", "sql" => "type"),
            182 => array("name" => "added", "sql" => "added"),
            183 => array("name" => "enablecompletion", "sql" => "enablecompletion"),
            184 => array("name" => "usercreated", "sql" => "usercreated"),
            185 => array("name" => "section", "sql" => "section"),
            186 => array("name" => "proficiency", "sql" => "proficiency"),
            187 => array("name" => "id", "sql" => "CONCAT(u.id, '_', r.id)", "prepend" => false),
            188 => array("name" => "itemname", "sql" => "itemname"),
            189 => array("name" => "countrows", "sql" => "countrows"),
            190 => array("name" => "user_action", "sql" => "CONCAT(u_action.firstname, ' ', u_action.lastname)", "prepend" => false),
            191 => array("name" => "relateduserid", "sql" => "relateduserid"),
            192 => array("name" => "last_change", "sql" => "last_change"),
            193 => array("name" => "objectid", "sql" => "objectid"),
            194 => array("name" => "gradepass", "sql" => "gradepass"),
            195 => array("name" => "criteriatype", "sql" => "criteriatype"),
            196 => array("name" => "numassigns1", "sql" => "numassigns1"),
            197 => array("name" => "numassigns2", "sql" => "numassigns2"),
            198 => array("name" => "numassigns3", "sql" => "numassigns3"),
            199 => array("name" => "uniqvisits", "sql" => "COUNT(DISTINCT lit.id)", "prepend" => false),
            200 => array("name" => "timeend", "sql" => "timeend"),
            201 => array("name" => "objecttable", "sql" => "objecttable"),
            202 => array("name" => "activityname", "sql" => "(CASE WHEN l.objecttable = 'forum_discussions' THEN (SELECT name FROM {forum_discussions} WHERE id = l.objectid) ELSE (CASE WHEN l.objecttable = 'forum_posts' THEN (SELECT subject FROM {forum_posts} WHERE id = l.objectid) ELSE '' END) END)", "prepend" => false),
            203 => array("name" => "origin", "sql" => "origin"),
            204 => array("name" => "ip", "sql" => "ip"),
            205 => array("name" => "contextinstanceid", "sql" => "contextinstanceid"),
            206 => array("name" => "component", "sql" => "component"),
            207 => array("name" => "cost", "sql" => "cost"),
            208 => array("name" => "currency", "sql" => "currency"),
            209 => array("name" => "description", "sql" => "description"),
            210 => array("name" => "scale", "sql" => "scale"),
            211 => array("name" => "scaleid", "sql" => "scaleid"),
            212 => array("name" => "outcomeid", "sql" => "outcomeid"),
            213 => array("name" => "modules", "sql" => "modules"),
            214 => array("name" => "completion", "sql" => "completion"),
            215 => array("name" => "timeopen", "sql" => "timeopen"),
            216 => array("name" => "timeclose", "sql" => "timeclose"),
            217 => array("name" => "forums", "sql" => "forums"),
            218 => array("name" => "week", "sql" => "week"),
            219 => array("name" => "subject", "sql" => "subject"),
            220 => array("name" => "created", "sql" => "created"),
            221 => array("name" => "parent", "sql" => "parent"),
            222 => array("name" => "middlename", "sql" => "middlename"),
            223 => array("name" => "lastip", "sql" => "lastip"),
            224 => array("name" => "plugin", "sql" => "plugin"),
            225 => array("name" => "version", "sql" => "version"),
            226 => array("name" => "tagid", "sql" => "tagid"),
            227 => array("name" => "enddate", "sql" => "enddate"),
            228 => array("name" => "rawgrademax", "sql" => "rawgrademax"),
            229 => array("name" => "rawgrademin", "sql" => "rawgrademin"),
            230 => array("name" => "path", "sql" => "path"),
            231 => array("name" => "depth", "sql" => "depth"),
            232 => array("name" => "productid", "sql" => "productid"),
            233 => array("name" => "expiration", "sql" => "expiration"),
            234 => array("name" => "role", "sql" => "role"),
            235 => array("name" => "modified", "sql" => "modified"),
            236 => array("name" => "enrolment_date", "sql" => "CASE WHEN MIN(ue.timestart) > 0 THEN MIN(ue.timestart) ELSE MIN(ue.timecreated) END"),
            237 => array("name" => "containertype", "sql" => "containertype"),
            238 => array("name" => "vendorid", "sql" => "vendorid")
        );

        static::$modifiers = array_map(function($modifier) use ($mode) {

            if (is_array($modifier)) {
              $modifier = $modifier[$mode];
            }
            return $modifier;

        }, $modifiers);

        static::$columns = array_map(function($column) use ($mode) {
            if (is_array($column['sql'])) {
                $column['sql'] = $column['sql'][$mode];
            }

            return $column;
        }, $columns);

    }

    public static function get($request, DataExtractor $extractor = null, $params = array()) {

        static::init($extractor->getMode());

        $selected = ($flag = ArrayHelper::is_indexed_array($request))? $request : array($request);
        $columns = static::$columns;

        $result = array_map(function($column) use ($columns, $extractor) {

            if (!is_array($column)) {
                return array('sql' => is_numeric($column)? $column : "'$column'");
            } else if (!empty($column['infofield'])) {
                $value = static::createColumnFromInfo($column, $extractor);
            } elseif (!empty($column['gradebookfield'])) {
                $value = static::createColumnFromGradebook($column, $extractor);
            } else {
                if (isset($column['id']['if'])) {  // this is conditional query type

                    $cond = FiltersContainer::release($column['id']['if'], $extractor);
                    $trueStatement = static::release(array($column['id']['values'][0]), $extractor, array('name' => false));

                    if (isset($column['id']['values'][1])) {
                        $elseStatement = ' ELSE ' . static::release(array($column['id']['values'][1]), $extractor, array('name' => false));
                    } else {
                        $elseStatement = '';
                    }

                    $value = array('sql' => "CASE WHEN $cond THEN $trueStatement $elseStatement END");
                } else if (isset($column['id']['expression'])) {

                    $sql = array_reduce($column['id']['expression'], function($sql, $cur) use ($extractor) {
                        return $sql . (is_array($cur) ? static::release(array($cur), $extractor, array('name' => false)) : $cur);
                    }, '');

                    $value = compact('sql');

                } else if (isset($column['id']['concat'])) {

                    $sql = 'CONCAT(' . implode(',', array_map(function($cur) use ($extractor) {
                        return (is_array($cur) ? static::release(array($cur), $extractor, array('name' => false)) : "'$cur'");
                    }, $column['id']['concat'])) . ')';

                    $value = compact('sql');

                } else if (is_array($column['id'])) {
                    $value = array('sql' => '(' . $extractor->construct($column['id']) . ')');
                } else if (is_numeric($column['id'])) {
                    $value = static::getById($column['id'], $extractor);
                } else {
                    $value = array('sql' => "'{$column['id']}'");
                }

                if (!(isset($value['prepend']) && $value['prepend'] === false) && !empty($column['table'])) {
                    $tableName = is_numeric($column['table'])? TablesContainer::getById($column['table'], $extractor)['name'] : $column['table'];
                    $value['sql'] =  $tableName  . '.' . $value['sql'];
                }

            }

            if (isset($column['modifier'])) {
                $initial = $value['sql'];
                $value['sql'] = array_reduce($column['modifier'], function($carry, $modifier) use($extractor, $initial) {
                    return static::applyModifier($modifier, $carry, $extractor, $initial);
                }, $value['sql']);
            }

            if (isset($column['name'])) {
                $value['name'] = is_numeric($column['name'])? static::getById($column['name'], $extractor)['name'] : $column['name'];
            }

            return $value;

        }, $selected);

        return $flag? $result : $result[0];
    }

    public static function construct($columns, DataExtractor $extractor, $params = array()) {

        $processed = array_map(function($column) use ($params) {
            $sql = $column['sql'];

            if ((!isset($params['name']) || $params['name']) && !empty($column['name'])) {
                $sql .= ' AS ' . $column['name'];
            }

            return $sql;
        }, $columns);
        $sep = $extractor->getSeparator();

        return implode("," . $sep . " ", $processed) . $sep;

    }

    public static function getById($id, $mode) {

        if ($mode instanceof DataExtractor) {
            $mode = $mode->getMode();
        }

        static::init($mode);

        return static::$columns[$id];
    }

    public static function applyModifier($id, $value, DataExtractor $extractor, $initial = null) {
        $initial = $initial? $initial : $value;
        $modifier = is_array($id)? $id : compact('id');

        if (empty(static::$modifiers[$modifier['id']])) {
            return $value;
        }

        $selected = static::$modifiers[$modifier['id']];

        if (FunctionHelper::is_anonym_function($selected)) {
            $params = isset($modifier['params'])? $modifier['params'] : array();
            $result = $selected($value, $params, $extractor);
        } else {
            $result = $selected . '(' . $value . ')';
        }

        return str_ireplace(array('$init', '$value'), array($initial, $value), $result);
    }

    protected static function createColumnFromInfo($infoField, DataExtractor $extractor) {
        $columnName = 'infocol' . (static::$columnNumber++);
        $column = array("sql" => '(' . sprintf(static::$infoTemplate, $columnName) . ')', "name" => $infoField['name']);
        $extractor->setArguments($columnName, $infoField['id']);

        return $column;
    }

    protected static function createColumnFromGradebook($gradebookField, DataExtractor $extractor) {
        $columnInstance = 'gradebookinstance' . (static::$columnNumber);
        static::$columnNumber++;

        $column = array("sql" => '(' . sprintf(static::$gradebookTemplate, static::$columns[136]['sql'], $columnInstance) . ')', "name" => $gradebookField['name']);
        $extractor->setArguments($columnInstance, $gradebookField['id']);

        return $column;
    }
}
