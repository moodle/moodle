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
 * json data format
 *
 * @package     report_trainingsessions
 * @category    report
 * @copyright   Valery Fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function report_trainingsessions_print_userinfo(&$csvbuffer, $user) {

    $str = '#'."\n";
    $str .= '# ln: '.$user->lastname."\n";
    $str .= '# fn: '.$user->firstname."\n";
    $str .= '# ID: '.$user->idnumber."\n";
    $str .= '#'."\n";

    $csvbuffer .= $str;
}

function report_trainingsessions_print_header(&$csvbuffer) {

    $config = get_config('report_trainingsessions');

    $headerline = array();
    $headerline[] = 'section';
    $headerline[] = 'plugin';
    $headerline[] = 'firstaccess';
    $headerline[] = 'elapsed';
    if (!empty($config->showhits)) {
        $headerline[] = 'events';
    }

    $csvbuffer .= implode($config->csvseparator, $headerline)."\n";
}

function report_trainingsessions_print_course_structure(&$csvbuffer, &$structure, &$aggregate) {
    static $currentstructure = '';

    $config = get_config('report_trainingsessions');

    if (empty($structure)) {
        $csvbuffer = get_string('nostructure', 'report_trainingsessions');
        return;
    }

    if (is_array($structure)) {
        // Recurse in sub structures.
        foreach ($structure as $element) {
            if (isset($element->instance) && empty($element->instance->visible)) {
                // Non visible items should not be displayed.
                continue;
            }
            if (!empty($config->hideemptymodules) && empty($element->elapsed) && empty($element->events)) {
                // Discard empty items.
                continue;
            }
            report_trainingsessions_print_course_structure($csvbuffer, $element, $aggregate);
        }
    } else {
        // Prints a single row.
        if (!isset($structure->instance) || !empty($structure->instance->visible)) {
            // Non visible items should not be displayed.
            if (!empty($structure->name)) {
                // Write element title.
                // TODO : Check how to force spanning on title.
                $dataline = array();
                if (($structure->plugintype == 'page') || ($structure->plugintype == 'section')) {
                    $currentstructure = $structure->name;
                } else {
                    // True activity.
                    $dataline = array();
                    $dataline[0] = $currentstructure;
                    $dataline[1] = shorten_text(get_string('pluginname', $structure->type), 40);
                    if (!empty($config->showhits)) {
                        $firstaccess = @$aggregate[$structure->type][$structure->id]->firstaccess;
                        $dataline[2] = report_trainingsessions_format_time($firstaccess, 'xls');
                        $elapsed = @$aggregate[$structure->type][$structure->id]->elapsed;
                        $dataline[3] = report_trainingsessions_format_time($elapsed, 'html');
                        $dataline[4] = $structure->events;
                    } else {
                        $firstaccess = @$aggregate[$structure->type][$structure->id]->firstaccess;
                        $dataline[2] = report_trainingsessions_format_time($firstaccess, 'xls');
                        $elapsed = @$aggregate[$structure->type][$structure->id]->elapsed;
                        $dataline[3] = report_trainingsessions_format_time($elapsed, 'html');
                    }

                    $csvbuffer .= implode($config->csvseparator, $dataline)."\n";
                }

                if (!empty($structure->subs)) {
                    report_trainingsessions_print_course_structure($csvbuffer, $structure->subs, $aggregate);
                }
            }
        }
    }
}

/**
 * A raster for printing in raw format with all the relevant data about a user.
 * @param int $courseid the course to compile reports in
 * @param arrayref &$cols the course to compile reports in
 * @param objectref &$user user to compile info for
 * @param objectref &$data input data to aggregate. Provides time information as 'elapsed" and 'weekelapsed' members.
 * @param string &$rawstr the output buffer reference. Column names come from outside.
 * @param int $from compilation start time
 * @param int $to compilation end time
 * @return void. $rawstr is appended by reference.
 */
function report_trainingsessions_print_global_raw($courseid, &$cols, &$user, &$aggregate, &$weekaggregate, &$rawstr) {
    global $COURSE, $DB;

    $config = get_config('report_trainingsessions');

    $colsdata = report_trainingsessions_map_summary_cols($cols, $user, $aggregate, $weekaggregate, $courseid);

    // Add grades.
    report_trainingsessions_add_graded_data($colsdata, $user->id, $aggregate);

    if (!empty($config->csv_iso)) {
        $rawstr .= mb_convert_encoding(implode(';', $colsdata)."\n", 'ISO-8859-1', 'UTF-8');
    } else {
        $rawstr .= implode(';', $colsdata)."\n";
    }
}

function report_trainingsessions_print_global_header(&$csvbuffer) {

    $config = get_config('report_trainingsessions');

    $colskeys = report_trainingsessions_get_summary_cols();

    report_trainingsessions_add_graded_columns($colskeys, $footitles);

    if (!empty($config->csv_iso)) {
        $csvbuffer = mb_convert_encoding(implode(';', $colskeys)."\n", 'ISO-8859-1', 'UTF-8');
    } else {
        $csvbuffer = implode(';', $colskeys)."\n";
    }
}

function report_trainingsessions_print_session_header(&$csvbuffer) {

    $colheads = array(
        'sessionstart',
        'sessionend',
        'elapsedsecs',
        'elapsedsecs'
    );

    $csvbuffer = implode(';', $colheads)."\n";
}

/**
 * print session table in an initialied worksheet
 * @param object $worksheet
 * @param int $row
 * @param array $sessions
 * @param object $course
 * @param object $xlsformats
 */
function report_trainingsessions_print_usersessions(&$csvbuffer, $userid, $courseorid, $from, $to, $id) {
    global $CFG, $DB;

    if (is_object($courseorid)) {
        $course = $courseorid;
    } else {
        $course = $DB->get_record('course', array('id' => $courseorid));
    }

    // Get data.
    $logs = use_stats_extract_logs($from, $to, $userid, $course);
    $aggregate = use_stats_aggregate_logs($logs, $from, $to);

    if (report_trainingsessions_supports_feature('calculation/coupling')) {
        $config = get_config('report_traningsessions');
        if (!empty($config->enablelearningtimecheckcoupling)) {
            require_once($CFG->dirroot.'/report/learningtimecheck/lib.php');
            $ltcconfig = get_config('report_learningtimecheck');
        }
    }

    $totalelapsed = 0;

    if (!empty($sessions)) {
        foreach ($sessions as $session) {

            if ($courseid && !array_key_exists($courseid, $session->courses)) {
                // Omit all sessions not visiting this course.
                continue;
            }

            // Fix eventual missing session end.
            if (!isset($session->sessionend) && empty($session->elapsed)) {
                // This is a "not true" session reliquate. Ignore it.
                continue;
            }

            // Fix all incoming sessions. possibly cropped by threshold effect.
            $session->sessionend = $session->sessionstart + $session->elapsed;

            $daysessions = report_trainingsessions_splice_session($session);

            foreach ($daysessions as $s) {

                if (!empty($config->enablelearningtimecheckcoupling)) {

                    if (!empty($ltcconfig->checkworkingdays) || !empty($ltcconfig->checkworkinghours)) {
                        if (!empty($ltcconfig->checkworkingdays)) {
                            if (!report_learningtimecheck_is_valid($fakecheck)) {
                                continue;
                            }
                        }

                        if (!empty($ltcconfig->checkworkinghours)) {
                            if (!report_learningtimecheck_check_day($fakecheck, $ltcconfig)) {
                                continue;
                            }

                            report_learningtimecheck_crop_session($s, $ltcconfig);
                            if ($s->sessionstart && $s->sessionend) {
                                // Segment was not invalidated, possibly shorter than original.
                                $s->elapsed = $s->sessionend - $s->sessionstart;
                            } else {
                                // Croping results concluded into an invalid segment.
                                continue;
                            }
                        }
                    }
                }

                $dataline[] = report_trainingsessions_format_time(@$s->sessionstart, 'html');
                if (!empty($s->sessionend)) {
                    $dataline[] = report_trainingsessions_format_time(@$s->sessionend, 'html');
                } else {
                    $dataline[] = '';
                }
                $dataline[] = $s->elapsed;
                $dataline[] = report_trainingsessions_format_time(0 + @$s->elapsed, 'html');
                $totalelapsed += 0 + @$s->elapsed;

                $csvbuffer .= implode(';', $dataline)."\n";
            }
        }
    }
    return $totalelapsed;
}