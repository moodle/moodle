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
 * build an Excel Writer format object from format attributes.
 *
 * @param objectref &$workbook the current excel workbook
 * @param int $size font size
 * @param boolean $bold font weight
 * @param int $color a color index
 * @param int $fgcolor a color index
 * @param int $numfmt the index of the number format
 * @return an Excel format instance.
 */
defined('MOODLE_INTERNAL') || die();

function report_trainingsessions_build_xls_format(&$workbook, $size, $bold, $color, $fgcolor, $numfmt = null) {

    $format = $workbook->add_format();

    if ($size != null) {
        $format->set_size($size);
    }

    if ($color != null) {
        $format->set_color($color);
    }

    if ($fgcolor != null) {
        $format->set_fg_color($fgcolor);
    }

    if ($bold != null) {
        $format->set_bold(1);
    }

    if ($numfmt != null) {
        $format->set_num_format($numfmt);
    }

    return $format;
}

/**
 * Sets up a set of Excel format mappings
 *
 * Supported formats :
 * T : Big Title
 * TT : section caption
 * b : bolded paragraph
 * a : body text
 * n : numeric (normal)
 * f : formula
 * t : date/time format
 * d : time/duration format
 * plus some other size specific variants.
 *
 * @param object $workbook
 * @return array of usable formats keyed by a label
 */
function report_trainingsessions_xls_formats(&$workbook) {

    // Size constants.
    $sizettl = 20;
    $sizehd1 = 14;
    $sizehd2 = 12;
    $sizehd3 = 9;
    $sizebdy = 9;

    // Color constants.
    $colorttl = 1;
    $colorhd1 = null;
    $colorhd2 = null;
    $colorhd3 = null;
    $colorbdy = null;

    // Foreground color constants.
    $fgcolorttl = 4;
    $fgcolorhd1 = 31;
    $fgcolorhd2 = null;
    $fgcolorhd3 = null;
    $fgcolorbdy = null;

    // Numeric format constants.
    $timefmt = '[h]:mm:ss';
    $datefmt = 'aaaa/mm/dd hh:mm';

    // Weight constants.
    $notbold = null;
    $bold = 1;

    // Title formats.
    $xlsformats['T'] = report_trainingsessions_build_xls_format($workbook, $sizettl, $bold, $colorbdy, $fgcolorbdy);
    $xlsformats['TT'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $bold, $colorttl, $fgcolorttl);

    // Text formats.
    $xlsformats['a0'] = report_trainingsessions_build_xls_format($workbook, $sizehd1, $bold, $colorttl, $fgcolorttl);
    $xlsformats['a1'] = report_trainingsessions_build_xls_format($workbook, $sizehd1, $notbold, $colorhd1, $fgcolorhd1);
    $xlsformats['a2'] = report_trainingsessions_build_xls_format($workbook, $sizehd2, $notbold, $colorhd2, $fgcolorhd2);
    $xlsformats['a3'] = report_trainingsessions_build_xls_format($workbook, $sizehd3, $notbold, $colorhd3, $fgcolorhd3);
    $xlsformats['b'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $bold, $colorbdy, $fgcolorbdy);
    $xlsformats['a'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy);

    // Number formats.
    $xlsformats['n'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy);
    $xlsformats['n.1'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, '0.0');
    $xlsformats['n.2'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

    // Formula formatting (same as numbers).
    $xlsformats['f'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy);
    // Duration variant.
    $xlsformats['fd'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, $timefmt);
    // Time date variant.
    $xlsformats['ft'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, $datefmt);

    // Time/duration formats.
    $xlsformats['d1'] = report_trainingsessions_build_xls_format($workbook, $sizehd1, $notbold, $colorhd1, $fgcolorhd1, $timefmt);
    $xlsformats['d2'] = report_trainingsessions_build_xls_format($workbook, $sizehd2, $notbold, $colorhd2, $fgcolorhd2, $timefmt);
    $xlsformats['d3'] = report_trainingsessions_build_xls_format($workbook, $sizehd3, $notbold, $colorhd3, $fgcolorhd3, $timefmt);
    $xlsformats['d'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, $timefmt);

    // Date/time formats.
    $xlsformats['t'] = report_trainingsessions_build_xls_format($workbook, $sizebdy, $notbold, $colorbdy, $fgcolorbdy, $datefmt);

    // Line-height formats (applying heights for different line types without any of the rest of the formatting).
    $xlsformats['_TT'] = report_trainingsessions_build_xls_format($workbook, $sizehd1, $notbold, $colorbdy, $fgcolorbdy);
    $xlsformats['_1'] = report_trainingsessions_build_xls_format($workbook, $sizehd1, $notbold, $colorbdy, $fgcolorbdy);
    $xlsformats['_2'] = report_trainingsessions_build_xls_format($workbook, $sizehd2, $notbold, $colorbdy, $fgcolorbdy);
    $xlsformats['_3'] = report_trainingsessions_build_xls_format($workbook, $sizehd3, $notbold, $colorbdy, $fgcolorbdy);

    return $xlsformats;
}

/**
 * initializes a new worksheet with static formats
 * @param int $userid
 * @param int $startrow
 * @param array $xlsformats
 * @param object $workbook
 * @return the initialized worksheet.
 */
function report_trainingsessions_init_worksheet($userid, $startrow, &$xlsformats, &$workbook, $purpose = 'usertimes') {
    global $DB;

    $config = get_config('report_trainingsessions');

    if (!empty($config->xlsexportlocale)) {
        // We may nbeed sometime to force the export locale for other Excel locales.
        moodle_setlocale($config->xlsexportlocale);
    }

    $user = $DB->get_record('user', array('id' => $userid));

    if (($purpose == 'usertimes') || ($purpose == 'allcourses')) {
        if ($config->csv_iso) {
            $sheettitle = mb_convert_encoding(fullname($user), 'ISO-8859-1', 'UTF-8');
        } else {
            $sheettitle = fullname($user);
        }
    } else {
        if ($config->csv_iso) {
            $sheettitle = mb_convert_encoding(fullname($user), 'ISO-8859-1', 'UTF-8');
            $sheettitle .= ' ('.get_string('sessions', 'report_trainingsessions').')';
        } else {
            $sheettitle = fullname($user).' ('.get_string('sessions', 'report_trainingsessions').')';
        }
    }

    $worksheet = $workbook->add_worksheet($sheettitle);
    if ($purpose == 'usertimes') {
        $worksheet->set_column(0, 0, 24);
        $worksheet->set_column(1, 1, 64);
        $worksheet->set_column(2, 2, 12);
        $worksheet->set_column(3, 3, 4);
    } else if ($purpose == 'allcourses') {
        $worksheet->set_column(0, 0, 50);
        $worksheet->set_column(1, 1, 50);
        $worksheet->set_column(2, 2, 12);
        $worksheet->set_column(3, 3, 4);
    } else {
        $worksheet->set_column(0, 0, 30);
        $worksheet->set_column(1, 1, 30);
        $worksheet->set_column(2, 2, 20);
        $worksheet->set_column(3, 3, 10);
    }
    $worksheet->set_column(4, 4, 12);
    $worksheet->set_column(5, 5, 4);
    $worksheet->set_column(6, 6, 12);
    $worksheet->set_column(7, 7, 4);
    $worksheet->set_column(8, 8, 12);
    $worksheet->set_column(9, 9, 4);
    $worksheet->set_column(10, 10, 12);
    $worksheet->set_column(11, 11, 4);
    $worksheet->set_column(12, 12, 12);
    $worksheet->set_column(13, 13, 4);

    if ($purpose == 'usertimes' || $purpose == 'allcourses') {
        $worksheet->set_row($startrow - 1, 12, $xlsformats['TT']);
        $worksheet->write_string($startrow - 1, 0, get_string('firstaccess', 'report_trainingsessions'), $xlsformats['TT']);
        $worksheet->write_string($startrow - 1, 1, get_string('item', 'report_trainingsessions'), $xlsformats['TT']);
        $worksheet->write_string($startrow - 1, 2, get_string('elapsed', 'report_trainingsessions'), $xlsformats['TT']);
        if (!empty($config->showhits)) {
            $worksheet->write_string($startrow - 1, 3, get_string('hits', 'report_trainingsessions'), $xlsformats['TT']);
        }
    } else {
        $worksheet->write_string($startrow - 1, 0, get_string('sessionstart', 'report_trainingsessions'), $xlsformats['TT']);
        $worksheet->write_string($startrow - 1, 1, get_string('sessionend', 'report_trainingsessions'), $xlsformats['TT']);
        $worksheet->write_string($startrow - 1, 2, get_string('duration', 'report_trainingsessions'), $xlsformats['TT']);
    }

    return $worksheet;
}

/**
 * a raster for xls printing of a report structure header
 * with all the relevant data about a user.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @version    moodle 2.x
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function report_trainingsessions_print_header_xls(&$worksheet, $userid, $courseid, &$data, $xlsformats) {
    global $CFG, $DB;

    $config = get_config('report_trainingsessions');

    $cols = report_trainingsessions_get_summary_cols();

    $user = $DB->get_record('user', array('id' => $userid));
    if ($courseid) {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $row = 0;

    $worksheet->set_row(0, 40, $xlsformats['T']);
    $worksheet->write_string($row, 0, get_string('sessionreports', 'report_trainingsessions'), $xlsformats['T']);
    $worksheet->merge_cells($row, 0, 0, 12);
    $row++;
    $worksheet->write_string($row, 0, get_string('user').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, fullname($user));
    $row++;
    $worksheet->write_string($row, 0, get_string('idnumber').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, $user->idnumber);
    $row++;
    $worksheet->write_string($row, 0, get_string('email').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, $user->email);
    $row++;
    $worksheet->write_string($row, 0, get_string('city').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, $user->city);
    $row++;
    $worksheet->write_string($row, 0, get_string('institution').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, $user->institution);
    $row++;

    $timeformat = get_string('profileinfotimeformat', 'report_trainingsessions');

    // Add some custom info from profile.
    if (!empty($config->extrauserinfo1)) {
        $fieldname = $DB->get_field('user_info_field', 'name', array('id' => $config->extrauserinfo1)).':';
        $fieldtype = $DB->get_field('user_info_field', 'datatype', array('id' => $config->extrauserinfo1));
        $info = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => $config->extrauserinfo1));
        $worksheet->write_string($row, 0, $fieldname.' :', $xlsformats['b']);
        if ($fieldtype == 'datetime') {
            // Possible alternatives : write in real date cell or in text.
            // $worksheet->write_date($row, 1, $info);

            $info = strftime($timeformat, $info);
            $worksheet->write_string($row, 1, $info);
        } else {
            $worksheet->write_string($row, 1, $info);
        }
        $row++;
    }

    if (!empty($config->extrauserinfo2)) {
        $fieldname = $DB->get_field('user_info_field', 'name', array('id' => $config->extrauserinfo2)).':';
        $fieldtype = $DB->get_field('user_info_field', 'datatype', array('id' => $config->extrauserinfo2));
        $info = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => $config->extrauserinfo2));
        $worksheet->write_string($row, 0, $fieldname.' :', $xlsformats['b']);
        if ($fieldtype == 'datetime') {
            // Possible alternatives : write in real date cell or in text.
            // $worksheet->write_date($row, 1, $info);

            $info = strftime($timeformat, $info);
            $worksheet->write_string($row, 1, $info);
        } else {
            $worksheet->write_string($row, 1, $info);
        }
        $row++;
    }

    if ($courseid) {
        $worksheet->write_string($row, 0, get_string('course', 'report_trainingsessions').' :', $xlsformats['b']);
        $worksheet->write_string($row, 1, format_string($course->fullname));
        $row++;
    }
    $worksheet->write_string($row, 0, get_string('from').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, userdate($data->from));
    $row++;
    $worksheet->write_string($row, 0, get_string('to').' :', $xlsformats['b']);
    $worksheet->write_string($row, 1, userdate(time()));
    $row++;

    if ($courseid) {
        $usergroups = groups_get_all_groups($courseid, $userid, 0, 'g.id, g.name');

        // Print group status.
        $worksheet->write_string($row, 0, get_string('groups').' :', $xlsformats['b']);
        $str = '';
        if (!empty($usergroups)) {
            foreach ($usergroups as $group) {
                $str = $group->name;
                if ($group->id == groups_get_course_group($course)) {
                    $str = "[$str]";
                }
                $groupnames[] = format_string($str);
            }
            $str = implode(', ', $groupnames);
        }

        $worksheet->write_string($row, 1, $str);
        $row++;

        $context = context_course::instance($courseid);
        $worksheet->write_string($row, 0, get_string('roles').' :', $xlsformats['b']);
        $roles = get_user_roles($context, $userid);
        $rolenames = array();
        foreach ($roles as $role) {
            $rolenames[] = $role->shortname;
        }
        $worksheet->write_string($row, 1, strip_tags(implode(",", $rolenames)));

        $row++;
    }

    // Print completion bar.
    if (empty($data->items)) {
        $completed = 0;
    } else {
        $completed = (0 + @$data->done) / $data->items;
    }
    $remaining = 1 - $completed;
    $completedpc = ceil($completed * 100);
    $remainingpc = 100 - $completedpc;

    $worksheet->write_string($row, 0, get_string('done', 'report_trainingsessions'), $xlsformats['b']);
    $celldata = (0 + @$data->done).' '.get_string('over', 'report_trainingsessions').' ';
    $celldata .= (0 + @$data->items).' ('.$completedpc.' %)';
    $worksheet->write_string($row, 1, $celldata);

    if (in_array('elapsed', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('elapsed', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->elapsed), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('extelapsed', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('extelapsed', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->extelapsed), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('extother', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('extother', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->extother), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('elapsedlastweek', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('elapsedlastweek', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->elapsedlastweek), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('extelapsedlastweek', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('extelapsedlastweek', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->extelapsedlastweek), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('extotherlastweek', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('extotherlastweek', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->extotherlastweek), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('coursetime', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('coursetime', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->courseelapsed), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('activityelapsed', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('activitytime', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->activityelapsed), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }
    if (in_array('otherelapsed', $cols)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('othertime', 'report_trainingsessions').' :', $xlsformats['b']);
        $elapsed = report_trainingsessions_format_time((0 + @$data->otherelapsed + @$data->courseelapsed), 'xlsd');
        $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
    }

    if (!empty($config->showhits)) {
        $row++;
        $worksheet->write_string($row, 0, get_string('hits', 'report_trainingsessions').' :', $xlsformats['b']);
        $worksheet->write_number($row, 1, (0 + @$data->events), $xlsformats['n']);
    }

    return $row;
}

function report_trainingsessions_count_header_rows($courseid) {
    global $CFG, $DB;

    $config = get_config('report_trainingsessions');

    $cols = report_trainingsessions_get_summary_cols();

    $row = 12;

    if ($courseid) {
        $row++;
    }

    if ($courseid) {
        $row += 2;
    }

    if (in_array('elapsed', $cols)) {
        $row++;
    }
    if (in_array('extelapsed', $cols)) {
        $row++;
    }
    if (in_array('extother', $cols)) {
        $row++;
    }
    if (in_array('elapsedlastweek', $cols)) {
        $row++;
    }
    if (in_array('extelapsedlastweek', $cols)) {
        $row++;
    }
    if (in_array('extotherlastweek', $cols)) {
        $row++;
    }
    if (in_array('coursetime', $cols)) {
        $row++;
    }
    if (in_array('activityelapsed', $cols)) {
        $row++;
    }
    if (in_array('otherelapsed', $cols)) {
        $row++;
    }
    if (!empty($config->showhits)) {
        $row++;
    }

    if (!empty($config->extrauserinfo1)) {
        $row++;
    }

    if (!empty($config->extrauserinfo2)) {
        $row++;
    }

    return $row;
}

/**
 * a raster for xls printing of a report structure.
 *
 */
function report_trainingsessions_print_xls(&$worksheet, &$structure, &$aggregate, &$done, &$row, &$xlsformats, $level = 1) {

    $config = get_config('report_trainingsessions');

    if (empty($structure)) {
        $str = get_string('nostructure', 'report_trainingsessions');
        $worksheet->write_string($row, 1, $str);
        return;
    }

    // Makes a blank dataobject.
    if (!isset($dataobject)) {
        $dataobject = new StdClass;
        $dataobject->elapsed = 0;
        $dataobject->events = 0;
    }

    if (is_array($structure)) {
        // Recurse in sub structures.
        foreach ($structure as $element) {
            if (isset($element->instance) && empty($element->instance->visible)) {
                // Non visible items should not be displayed.
                continue;
            }
            $res = report_trainingsessions_print_xls($worksheet, $element, $aggregate, $done, $row, $xlsformats, $level);
            $dataobject->elapsed += $res->elapsed;
            $dataobject->events += $res->events;
        }
    } else {
        // Prints a single row.
        $format = (isset($xlsformats['a'.$level])) ? $xlsformats['a'.$level] : $xlsformats['a'];

        if (!isset($element->instance) || !empty($element->instance->visible)) {
            // Non visible items should not be displayed.
            if (!empty($structure->name)) {

                // Write element name.
                $indent = str_pad('', 3 * $level, ' ');
                $str = $indent.shorten_text(strip_tags($structure->name), 85);
                $worksheet->write_string($row, 1, $str, $format);

                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $done++;
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }

                // Saves the current row for post writing aggregates.
                $thisrow = $row;
                $row++;
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_xls($worksheet, $structure->subs, $aggregate, $done,
                                                             $row, $xlsformats, $level + 1);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }

                // Firstaccess.
                $fa = @$aggregate[$structure->type][$structure->id]->firstaccess;
                if (!empty($fa)) {
                    $worksheet->write_date($thisrow, 0, (float)$fa, $xlsformats['t']);
                }

                // Elapsed.
                $convertedelapsed = report_trainingsessions_format_time($dataobject->elapsed, 'xlsd');
                $worksheet->write_time($thisrow, 2, $convertedelapsed, $xlsformats['d']);

                if (!empty($config->showhits)) {
                    $worksheet->write_number($thisrow, 3, $dataobject->events, $xlsformats['n']);
                }
            } else {
                // It is only a structural module that should not impact on level.
                if (isset($structure->id) && !empty($aggregate[$structure->type][$structure->id])) {
                    $dataobject = $aggregate[$structure->type][$structure->id];
                }
                if (!empty($structure->subs)) {
                    $res = report_trainingsessions_print_xls($worksheet, $structure->subs, $aggregate, $done,
                                                             $row, $xlsformats, $level);
                    $dataobject->elapsed += $res->elapsed;
                    $dataobject->events += $res->events;
                }
            }
        }
    }
    return $dataobject;
}

/**
 * Public wrapper for unified API.
 */
function report_trainingsessions_print_usersessions(&$worksheet, $userid, $row, $from, $to, &$course, &$xlsformats) {

    // Get data.
    $logs = use_stats_extract_logs($from, $to, $userid, $course);
    $aggregate = use_stats_aggregate_logs($logs, $from, $to);

    report_trainingsessions_print_sessions_xls($worksheet, $row, $aggregate['sessions'], $course, $xlsformats);
}

/**
 * Print session table in an initialied worksheet
 *
 * @param object $worksheet
 * @param int $row
 * @param array $sessions
 * @param object $course
 * @param object $xlsformats
 */
function report_trainingsessions_print_sessions_xls(&$worksheet, $row, $sessions, $courseorid, &$xlsformats) {
    global $CFG;

    if (is_object($courseorid)) {
        $courseid = $courseorid->id;
    } else {
        $courseid = $courseorid;
    }

    $hasltc = false;
    if (file_exists($CFG->dirroot.'/report/learningtimecheck/lib.php')) {
        $config = get_config('report_traningsessions');
        if (!empty($config->enablelearningtimecheckcoupling)) {
            require_once($CFG->dirroot.'/report/learningtimecheck/lib.php');
            $ltcconfig = get_config('report_learningtimecheck');
            $hasltc = true;
        }
    }

    $totalelapsed = 0;

    if (!empty($sessions)) {
        foreach ($sessions as $session) {

            if (empty($session->courses) || ($courseid && !array_key_exists($courseid, $session->courses))) {
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

                if ($hasltc && !empty($config->enablelearningtimecheckcoupling)) {

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

                $worksheet->write_date($row, 0, @$s->sessionstart, $xlsformats['t']);
                if (!empty($s->sessionend)) {
                    $worksheet->write_date($row, 1, @$s->sessionend, $xlsformats['t']);
                }
                $worksheet->write_string($row, 2, format_time(0 + @$s->elapsed), $xlsformats['TT']);
                $elapsed = report_trainingsessions_format_time(0 + @$s->elapsed, 'xlsd');
                $worksheet->write_time($row, 3, $elapsed, $xlsformats['d']);
                $totalelapsed += 0 + @$s->elapsed;

                $row++;
            }
        }
    }
    return $totalelapsed;
}

/**
 * a raster for Excel printing of a report structure.
 *
 * @param ref $worksheet a buffer for accumulating output
 * @param object $aggregate aggregated logs to explore.
 */
function report_trainingsessions_print_allcourses_xls(&$worksheet, &$aggregate, $row, &$xlsformats) {
    global $CFG, $COURSE, $DB;

    $config = get_config('report_trainingsessions');

    $output = array();
    $courses = array();
    $courseids = array();
    $return = new StdClass;
    $return->elapsed = 0;
    $return->events = 0;
    if (!empty($aggregate['coursetotal'])) {
        foreach ($aggregate['coursetotal'] as $cid => $cdata) {
            if ($cid != 0) {
                if (!in_array($cid, $courseids)) {
                    $fields = 'id,idnumber,shortname,fullname,category';
                    if (!$courses[$cid] = $DB->get_record('course', array('id' => $cid), $fields)) {
                        // This course has gone away.
                        continue;
                    }
                    $courseids[$cid] = '';
                }

                $output[0 + @$courses[$cid]->category][$cid] = $cdata;
                $catids[0 + @$courses[$cid]->category] = '';
            } else {
                if (!isset($output[0][SITEID])) {
                    $output[0][SITEID] = new StdClass();
                    $output[0][SITEID]->elapsed = 0;
                    $output[0][SITEID]->events = 0;
                }
                $output[0][SITEID]->elapsed += $cdata->elapsed;
                $output[0][SITEID]->events += $cdata->events;
            }
            $return->elapsed += $cdata->elapsed;
            $return->events += $cdata->events;
        }

        $coursecats = $DB->get_records_list('course_categories', 'id', array_keys($catids));
    }

    if (!empty($output)) {

        $elapsedstr = get_string('elapsed', 'report_trainingsessions');
        $hitsstr = get_string('hits', 'report_trainingsessions');
        $coursestr = get_string('course');

        if (isset($output[0])) {
            $worksheet->write_string($row, 0, get_string('site'), $xlsformats['TT']);
            $row++;
            $worksheet->write_string($row, 0, $elapsedstr, $xlsformats['a']);
            $elapsed = report_trainingsessions_format_time($output[0][SITEID]->elapsed, 'xlsd');
            $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
            $row++;
            if (!empty($config->showhits)) {
                $worksheet->write_string($row, 0, $hitsstr, $xlsformats['a']);
                $worksheet->write_number($row, 1, $output[0][SITEID]->events, $xlsformats['n']);
                $row++;
            }
        }

        foreach ($output as $catid => $catdata) {
            if ($catid == 0) {
                continue;
            }
            $worksheet->write_string($row, 0, $coursecats[$catid]->name, $xlsformats['TT']);
            $row++;
            $worksheet->write_string($row, 0, $coursestr, $xlsformats['TT']);
            $worksheet->write_string($row, 1, $elapsedstr, $xlsformats['TT']);
            $worksheet->write_string($row, 2, $hitsstr, $xlsformats['TT']);
            $row++;

            foreach ($catdata as $cid => $cdata) {
                $ccontext = context_course::instance($cid);
                if (has_capability('report/trainingsessions:view', $ccontext)) {
                    $worksheet->write_string($row, 0, $courses[$cid]->fullname, $xlsformats['a']);
                    $elapsed = report_trainingsessions_format_time($cdata->elapsed, 'xlsd');
                    $worksheet->write_time($row, 1, $elapsed, $xlsformats['d']);
                    if (!empty($config->showhits)) {
                        $worksheet->write_number($row, 2, $cdata->events, $xlsformats['n']);
                    }
                    $row++;
                } else {
                    $worksheet->write_string($row, 0, $courses[$cid]->fullname, $xlsformats['a']);
                    $label = get_string('nopermissiontoview', 'report_trainingsessions');
                    $worksheet->write_string($row, 2, $label, $xlsformats['a']);
                }
            }
        }
    }

    return $return;
}

/**
 * prints a raw data row in the worksheet
 *
 * @param object $worksheet
 * @param array $data
 * @param array $dataformats
 * @param int $row
 * @param array $xlsformats predefined set of formats
 */
function report_trainingsessions_print_rawline_xls(&$worksheet, $data, $dataformats, $row, &$xlsformats) {

    for ($i = 0; $i < count($data); $i++) {

        if (!array_key_exists($dataformats[$i], $xlsformats)) {
            throw new Exception('Unknown XLS format '.$dataformats[$i]);
        }

        $celldata = $data[$i];

        if ($dataformats[$i] == 'f') {
            if ($celldata) {
                $celldata = str_replace('{row}', ($row + 1), $celldata);
                $worksheet->write_formula($row, $i, $celldata, $xlsformats['f']);
                continue;
            }
        }

        if ($dataformats[$i] == 'n') {
            if ($celldata !== null && $celldata !== '') {
                $worksheet->write_number($row, $i, $celldata, $xlsformats['n']);
            }
            continue;
        }

        if ($dataformats[$i] == 'n.1') {
            if ($celldata !== null && $celldata !== '') {
                $worksheet->write_number($row, $i, $celldata, $xlsformats['n.1']);
            }
            continue;
        }

        if ($dataformats[$i] == 'n.2') {
            if ($celldata !== null && $celldata !== '') {
                $worksheet->write_number($row, $i, $celldata, $xlsformats['n.2']);
            }
            continue;
        }

        if ($dataformats[$i] == 'd') {
            if ($data[$i]) {
                $celldata = report_trainingsessions_format_time($data[$i], 'xlsd');
                if ($celldata !== null && $celldata !== '') {
                    $worksheet->write_time($row, $i, $celldata, $xlsformats['d']);
                }
                continue;
            } else {
                continue;
            }
        }

        if ($dataformats[$i] == 't') {
            if ($data[$i]) {
                if ($celldata !== null && $celldata !== '') {
                    $worksheet->write_date($row, $i, $data[$i], $xlsformats['t']);
                }
                continue;
            } else {
                continue;
            }
        }

        $worksheet->write_string($row, $i, $celldata, $xlsformats[$dataformats[$i]]);
    }
    return ++$row;
}

/**
 * prints a data row with column aggregators in the worksheet
 *
 * @param object $worksheet
 * @param array $dataformats
 * @param array $sumline
 * @param int $row
 * @param array $xlsformats predefined set of formats
 */
function report_trainingsessions_print_sumline_xls(&$worksheet, &$dataformats, $sumline, $minrow, $maxrow, &$xlsformats) {

    $config = get_config('report_trainingsessions');

    if (empty($sumline)) {
        return;
    }

    $sumline = str_replace(';', ',', $sumline); // Accept semi-colons too.
    $sumline = explode(',', $sumline);

    $i = 0;
    foreach ($sumline as $sum) {

        $col = chr(ord('A') + $i);

        switch ($sum) {
            case 'm': {
                $formula = $config->xlsmeanformula;
                $formula = str_replace('{col}', $col, $formula);
                $formula = str_replace('{minrow}', $minrow, $formula);
                $formula = str_replace('{maxrow}', $maxrow, $formula);
                if ($dataformats[$i][0] == 'd') {
                    $worksheet->write_formula($maxrow, $i, $formula, $xlsformats['fd']);
                } else if ($dataformats[$i][0] == 't') {
                    $worksheet->write_formula($maxrow, $i, $formula, $xlsformats['ft']);
                } else {
                    $worksheet->write_formula($maxrow, $i, $formula, $xlsformats['f']);
                }
                break;
            }
            case 's': {
                $formula = $config->xlssumformula;
                $formula = str_replace('{col}', $col, $formula);
                $formula = str_replace('{minrow}', $minrow, $formula);
                $formula = str_replace('{maxrow}', $maxrow, $formula);
                $worksheet->write_formula($maxrow, $i, $formula, $xlsformats['f']);
                break;
            }
            default:
        }

        $i++;
    }
    return $maxrow + 1;
}
