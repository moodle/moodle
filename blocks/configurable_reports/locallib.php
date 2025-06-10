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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * cr_print_js_function
 *
 * @return void
 */
function cr_print_js_function() {
    echo '<script>
        function printDiv(id) {
            let cdiv, tmpw;

            cdiv = document.getElementById(id);
            tmpw = window.open(" ", "Print");

            tmpw.document.open();
            tmpw.document.write(\'<html><body>\');
            tmpw.document.write(cdiv.innerHTML);
            tmpw.document.write(\'</body></html>\');
            tmpw.document.close();

            setTimeout(function() {
                tmpw.print();
                tmpw.close();
            }, 1000);
        }
    </script>';
}

/**
 * cr_add_jsdatatables
 *
 * @param string $cssid
 * @param moodle_page $page
 * @return void
 */
function cr_add_jsdatatables(string $cssid, moodle_page $page) {
    $data = [];
    $data['selector'] = $cssid;

    $page->requires->string_for_js('thousandssep', 'langconfig');
    $page->requires->strings_for_js(
        [
            'datatables_sortascending',
            'datatables_sortdescending',
            'datatables_first',
            'datatables_last',
            'datatables_next',
            'datatables_previous',
            'datatables_emptytable',
            'datatables_info',
            'datatables_infoempty',
            'datatables_infofiltered',
            'datatables_lengthmenu',
            'datatables_loadingrecords',
            'datatables_processing',
            'datatables_search',
            'datatables_zerorecords',
        ],
        'block_configurable_reports'
    );

    $page->requires->js_call_amd('block_configurable_reports/main', 'add_jsdatatables', [$data]);
}

/**
 * cr_add_jsordering
 *
 * @param string $cssid
 * @param moodle_page|null $page
 */
function cr_add_jsordering(string $cssid, moodle_page $page = null) {
    global $OUTPUT;

    if (!empty($page)) {
        $data = [];
        $data['selector'] = $cssid;
        if (method_exists($OUTPUT, 'image_url')) {
            $data['background'] = $OUTPUT->image_url('normal', 'block_configurable_reports')->out();
            $data['backgroundasc'] = $OUTPUT->image_url('asc', 'block_configurable_reports')->out();
            $data['backgrounddesc'] = $OUTPUT->image_url('desc', 'block_configurable_reports')->out();
        }
        $page->requires->js_call_amd('block_configurable_reports/main', 'js_order', [$data]);
    }
}

/**
 * urlencode_recursive
 *
 * @param mixed $var
 * @return array|mixed|stdClass|string
 */
function urlencode_recursive($var) {
    if (is_object($var)) {
        $newvar = new stdClass();
        $properties = get_object_vars($var);
        foreach ($properties as $property => $value) {
            $newvar->$property = urlencode_recursive($value);
        }
    } else if (is_array($var)) {
        $newvar = [];
        foreach ($var as $property => $value) {
            $newvar[$property] = urlencode_recursive($value);
        }
    } else if (is_string($var)) {
        $newvar = urlencode($var);
    } else {
        // Nulls, integers, etc.
        $newvar = $var;
    }

    return $newvar;
}

/**
 * urldecode_recursive
 *
 * @param mixed $var
 * @return mixed
 */
function urldecode_recursive($var) {
    if (is_object($var)) {
        $newvar = new stdClass();
        $properties = get_object_vars($var);
        foreach ($properties as $property => $value) {
            $newvar->$property = urldecode_recursive($value);
        }
    } else if (is_array($var)) {
        $newvar = [];
        foreach ($var as $property => $value) {
            $newvar[$property] = urldecode_recursive($value);
        }
    } else if (is_string($var)) {
        $newvar = urldecode($var);
    } else {
        $newvar = $var;
    }

    return $newvar;
}

/**
 * cr_get_my_reports
 *
 * @param int $courseid
 * @param int $userid
 * @param bool $allcourses
 * @return array
 */
function cr_get_my_reports(int $courseid, int $userid, bool $allcourses = true): array {
    global $DB;

    if ($courseid === SITEID) {
        $context = context_system::instance();
    } else {
        $context = context_course::instance($courseid);
    }

    if (has_capability('block/configurable_reports:managereports', $context, $userid)) {
        if ($courseid === SITEID && $allcourses) {
            $reports = $DB->get_records('block_configurable_reports', null, 'name ASC');
        } else {
            $reports = $DB->get_records('block_configurable_reports', ['courseid' => $courseid], 'name ASC');
        }
    } else {
        $reports = $DB->get_records_select(
            'block_configurable_reports',
            'ownerid = ? AND courseid = ? ORDER BY name ASC',
            [$userid, $courseid]
        );
    }

    return $reports;
}

/**
 * cr_serialize
 *
 * @param mixed $data
 * @return string
 */
function cr_serialize($data): string {
    return serialize(urlencode_recursive($data));
}

/**
 * Update serialized data
 *
 * @param string $data
 * @return array
 */
function cr_unserialize(string $data): array {
    // It's needed to convert the object to stdClass to avoid __PHP_Incomplete_Class error.
    $data = preg_replace('/O:6:"object"/', 'O:8:"stdClass"', $data);
    // To make SQL queries compatible with PostgreSQL it's needed to replace " to '.
    $data = preg_replace('/THEN\+%22(.+?)%22/', 'THEN+%27${1}%27', $data);
    $data = preg_replace('/%60/', '+++', $data);

    // TODO remove unserialize.
    $data = unserialize($data);

    return (array) urldecode_recursive($data);
}

/**
 * cr_check_report_permissions
 *
 * @param object $report
 * @param int $userid
 * @param context $context
 * @return mixed
 */
function cr_check_report_permissions($report, int $userid, context $context) {
    global $CFG;

    require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
    require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

    $classn = 'report_' . $report->type;

    return (new $classn($report->id))->check_permissions($userid, $context);
}

/**
 * cr_get_report_plugins
 *
 * @param int $courseid
 * @return array
 */
function cr_get_report_plugins(int $courseid): array {
    $pluginoptions = [];
    $context = ($courseid === SITEID) ? context_system::instance() : context_course::instance($courseid);
    $plugins = get_list_of_plugins('blocks/configurable_reports/reports');

    if ($plugins) {
        foreach ($plugins as $p) {
            if ($p === 'sql' && !block_configurable_reports_can_managesqlreports($context)) {
                continue;
            }
            $pluginoptions[$p] = get_string('report_' . $p, 'block_configurable_reports');
        }
    }

    return $pluginoptions;
}

/**
 * cr_get_export_plugins
 *
 * @return array
 */
function cr_get_export_plugins(): array {

    $plugins = get_list_of_plugins('blocks/configurable_reports/export');
    $pluginoptions = [];
    if ($plugins) {
        foreach ($plugins as $p) {
            $pluginoptions[$p] = get_string('export_' . $p, 'block_configurable_reports');
        }
    }

    return $pluginoptions;
}

/**
 * cr_print_table
 *
 * @param object $table
 * @param bool $return
 * @return string|true
 */
function cr_print_table(object $table, bool $return = false) {
    global $COURSE;

    $output = '';

    if (isset($table->align)) {
        foreach ($table->align as $key => $aa) {
            if ($aa) {
                $align[$key] = ' text-align:' . fix_align_rtl($aa) . ';';  // Fix for RTL languages.
            } else {
                $align[$key] = '';
            }
        }
    }
    if (isset($table->size)) {
        foreach ($table->size as $key => $ss) {
            if ($ss) {
                $size[$key] = ' width:' . $ss . ';';
            } else {
                $size[$key] = '';
            }
        }
    }
    if (isset($table->wrap)) {
        foreach ($table->wrap as $key => $ww) {
            if ($ww) {
                $wrap[$key] = ' white-space:nowrap;';
            } else {
                $wrap[$key] = '';
            }
        }
    }

    if (empty($table->width)) {
        $table->width = '80%';
    }

    if (empty($table->tablealign)) {
        $table->tablealign = 'center';
    }

    if (!isset($table->cellpadding)) {
        $table->cellpadding = '5';
    }

    if (!isset($table->cellspacing)) {
        $table->cellspacing = '1';
    }

    if (empty($table->class)) {
        $table->class = 'generaltable';
    }

    $tableid = empty($table->id) ? '' : 'id="' . $table->id . '"';
    $output .= '<form action="send_emails.php" method="post" id="sendemail">';
    $output .= '<table width="' . $table->width . '" ';
    if (!empty($table->summary)) {
        $output .= " summary=\"$table->summary\"";
    }
    $output .= " cellpadding=\"$table->cellpadding\" cellspacing=\"$table->cellspacing\"
                class=\"$table->class boxalign$table->tablealign\" $tableid>\n";

    $countcols = 0;
    $isuserid = -1;

    if (!empty($table->head)) {
        $countcols = count($table->head);
        $output .= '<thead><tr>';
        $keys = array_keys($table->head);
        $lastkey = end($keys);
        foreach ($table->head as $key => $heading) {
            if ($heading === 'sendemail') {
                $isuserid = $key;
            }
            if (!isset($size[$key])) {
                $size[$key] = '';
            }
            if (!isset($align[$key])) {
                $align[$key] = '';
            }
            if ($key == $lastkey) {
                $extraclass = ' lastcol';
            } else {
                $extraclass = '';
            }

            $output .= '<th style="vertical-align:top;' . $align[$key] . $size[$key] . ';white-space:normal;" class="header c' .
                $key . $extraclass . '" scope="col">' . $heading . '</th>';
        }
        $output .= '</tr></thead>' . "\n";
    }

    if (!empty($table->data)) {
        $oddeven = 1;
        $keys = array_keys($table->data);
        $lastrowkey = end($keys);
        foreach ($table->data as $key => $row) {
            $oddeven = $oddeven ? 0 : 1;
            if (!isset($table->rowclass[$key])) {
                $table->rowclass[$key] = '';
            }

            if ($key == $lastrowkey) {
                $table->rowclass[$key] .= ' lastrow';
            }

            $output .= '<tr class="r' . $oddeven . ' ' . $table->rowclass[$key] . '">' . "\n";
            if ($row === 'hr' && $countcols) {
                $output .= '<td colspan="' . $countcols . '"><div class="tabledivider"></div></td>';
            } else {  // It's a normal row of data.
                $keys2 = array_keys($row);
                $lastkey = end($keys2);

                foreach ($row as $keyouter => $item) {
                    if (!isset($size[$keyouter])) {
                        $size[$keyouter] = '';
                    }
                    if (!isset($align[$keyouter])) {
                        $align[$keyouter] = '';
                    }
                    if (!isset($wrap[$keyouter])) {
                        $wrap[$keyouter] = '';
                    }
                    if ($keyouter == $lastkey) {
                        $extraclass = ' lastcol';
                    } else {
                        $extraclass = '';
                    }
                    if ($keyouter == $isuserid) {
                        $output .= '<td style="' . $align[$keyouter] . $size[$keyouter] . $wrap[$keyouter] . '" class="cell c' .
                            $keyouter .
                            $extraclass . '"><input name="userids[]" type="checkbox" value="' . s($item) . '"></td>';
                    } else {
                        $output .= '<td style="' . $align[$keyouter] . $size[$keyouter] . $wrap[$keyouter] . '" class="cell c' .
                            $keyouter .
                            $extraclass . '">' . $item . '</td>';
                    }

                }
            }
            $output .= '</tr>' . "\n";
        }
    }
    $output .= '</table>' . "\n";
    $output .= '<input type="hidden" name="courseid" value="' . $COURSE->id . '">';
    if ($isuserid != -1) {
        $output .= '<input type="submit" value="send emails">';
    }
    $output .= '</form>';

    if ($return) {
        return $output;
    }

    echo $output;

    return true;
}

/**
 * table_to_excel
 *
 * @param string $filename
 * @param object $table
 * @return void
 */
function table_to_excel(string $filename, $table) {
    global $CFG;

    require_once($CFG->dirroot . '/lib/excellib.class.php');

    if (!empty($table->head)) {
        foreach ($table->head as $key => $heading) {
            $matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading))));
        }
    }

    if (!empty($table->data)) {
        foreach ($table->data as $rkey => $row) {
            foreach ($row as $key => $item) {
                $matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item))));
            }
        }
    }

    $downloadfilename = clean_filename($filename);
    // Creating a workbook.
    $workbook = new MoodleExcelWorkbook("-");
    // Sending HTTP headers.
    $workbook->send($downloadfilename);

    // Adding the worksheet.
    $myxls = $workbook->add_worksheet($filename);

    foreach ($matrix as $ri => $col) {
        foreach ($col as $ci => $cv) {
            $myxls->write_string($ri, $ci, $cv);
        }
    }

    $workbook->close();
    exit;
}

/**
 * Returns contexts in deprecated and current modes
 *
 * @param int $context The context
 * @param int|null $id The context id
 * @param null $strictness The flags to be used
 * @return stdClass     An object instance
 */
function cr_get_context(int $context, ?int $id = null, $strictness = null) {

    if ($context === CONTEXT_SYSTEM) {
        return context_system::instance();
    }

    if ($context === CONTEXT_COURSE) {
        return context_course::instance($id, $strictness);
    }

    if ($context === CONTEXT_COURSECAT) {
        return context_coursecat::instance($id, $strictness);
    }

    if ($context === CONTEXT_BLOCK) {
        return context_block::instance($id, $strictness);
    }

    if ($context === CONTEXT_MODULE) {
        return context_module::instance($id, $strictness);
    }

    if ($context === CONTEXT_USER) {
        return context_user::instance($id, $strictness);
    }

    return get_context_instance($context, $id, $strictness);
}

/**
 * cr_make_categories_list
 *
 * @param array $list
 * @param array $parents
 * @param string $requiredcapability
 * @param int $excludeid
 * @param object $category
 * @param string $path
 * @return void
 */
function cr_make_categories_list(&$list, &$parents, $requiredcapability = '', $excludeid = 0, $category = null, $path = '') {
    global $DB;

    // For categories list use just this one function.
    if (empty($list)) {
        $list = [];
    }

    $list += core_course_category::make_categories_list($requiredcapability, $excludeid);

    // Building the list of all parents of all categories in the system is highly undesirable and hardly ever needed.
    // Usually user needs only parents for one particular category, in which case should be used:
    // coursecat::get($categoryid)->get_parents().
    if (empty($parents)) {
        $parents = [];
    }
    $all = $DB->get_records_sql('SELECT id, parent FROM {course_categories} ORDER BY sortorder');
    foreach ($all as $record) {
        if ($record->parent) {
            $parents[$record->id] = array_merge($parents[$record->parent], [$record->parent]);
        } else {
            $parents[$record->id] = [];
        }
    }
}

/**
 * cr_import_xml
 *
 * @param string $xml
 * @param object $course
 * @return bool
 */
function cr_import_xml(string $xml, object $course) {
    global $CFG, $DB, $USER;

    require_once($CFG->dirroot . '/lib/xmlize.php');
    $data = xmlize($xml, 1, 'UTF-8');

    if (isset($data['report']['@']['version'])) {
        $newreport = new stdclass;
        foreach ($data['report']['#'] as $key => $val) {
            if ($key === 'components') {
                $val[0]['#'] = base64_decode(trim($val[0]['#']));
                // Fix url_encode " and ' when importing SQL queries.
                $tempcomponents = cr_unserialize($val[0]['#']);

                if (array_key_exists('customsql', $tempcomponents)) {

                    // Set current courseid.
                    $tempcomponents['customsql']['config']->courseid = $course->id;
                    $querysql = str_replace(["\'", '\"'], ["'", '"'], $tempcomponents['customsql']['config']->querysql);
                    $tempcomponents['customsql']['config']->querysql = $querysql;
                }

                $val[0]['#'] = cr_serialize($tempcomponents);
            }
            $newreport->{$key} = trim($val[0]['#']);
        }
        $newreport->courseid = $course->id;
        $newreport->ownerid = $USER->id;
        $newreport->name .= " (" . userdate(time()) . ")";

        if (!$DB->insert_record('block_configurable_reports', $newreport)) {
            return false;
        }

        return true;
    }

    return false;
}

/**
 * cr_logging_info
 *
 * @return array
 */
function cr_logging_info(): array {

    static $uselegacyreader;
    static $useinternalreader;
    static $logtable;

    if (isset($uselegacyreader, $useinternalreader, $logtable)) {
        return [
            $uselegacyreader,
            $useinternalreader,
            $logtable,
        ];
    }

    $uselegacyreader = false; // Flag to determine if we should use the legacy reader.
    $useinternalreader = false; // Flag to determine if we should use the internal reader.
    $logtable = '';

    // Get list of readers.
    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();

    // Get preferred reader.
    if (!empty($readers)) {
        foreach ($readers as $readerpluginname => $reader) {
            // If legacy reader is preferred reader.
            if ($readerpluginname == 'logstore_legacy') {
                $uselegacyreader = true;
                $logtable = 'log';
            }

            // If sql_internal_table_reader is preferred reader.
            if ($reader instanceof \core\log\sql_internal_table_reader || $reader instanceof \core\log\sql_internal_reader) {
                $useinternalreader = true;
                $logtable = $reader->get_internal_log_table_name();
            }
        }
    }

    return [
        $uselegacyreader,
        $useinternalreader,
        $logtable,
    ];
}

/**
 * Check if the current user is allowed to manage sql reports.
 *
 * @param context $context
 * @return bool
 */
function block_configurable_reports_can_managesqlreports($context): bool {
    global $USER;
    if (has_capability('block/configurable_reports:managesqlreports', $context)) {
        $allowedusers = get_config('block_configurable_reports', 'allowedsqlusers');
        if (!empty($allowedusers)) {
            $allowedusers = explode(',', $allowedusers);
            if (in_array($USER->username, $allowedusers)) {
                return true;
            }
        } else {
            return true;
        }
    }

    return false;
}
