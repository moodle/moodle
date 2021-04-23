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

namespace tool_brickfield\local\tool;

use tool_brickfield\manager;

/**
 * Class printable.
 *
 * @package tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class printable extends tool {

    /**
     * Provide a name for this tool, suitable for display on pages.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolname(): string {
        return get_string('printable:toolname', 'tool_brickfield');
    }

    /**
     * Provide a short name for this tool, suitable for menus and selectors.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolshortname(): string {
        return get_string('printable:toolshortname', 'tool_brickfield');
    }

    /**
     * Provide a lowercase name identifying this plugin. Should really be the same as the directory name.
     * @return string
     */
    public function pluginname(): string {
        return 'printable';
    }

    /**
     * Return the data for renderer / template display.
     * @return \stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    protected function fetch_data(): \stdClass {
        global $DB;

        $filter = $this->get_filter();
        if (!$filter->has_course_filters()) {
            return (object)[
                'valid' => false,
                'error' => get_string('error:nocoursespecified', 'tool_brickfield'),
            ];
        } else if (!$filter->validate_filters()) {
            return (object)[
                'valid' => false,
                'error' => $filter->get_errormessage(),
            ];
        }

        $data = (object)[
            'valid' => true,
            'error' => '',
        ];

        $config = get_config(manager::PLUGINNAME);
        $perpage = isset($config->perpagefix) ? $config->perpagefix : $config->perpage;

        list($wheresql, $params) = $filter->get_course_sql();

        $combo = [];
        $sqlcombo = 'SELECT distinct '.$DB->sql_concat_join("''", ['area.component', 'area.contextid']).' as tmpid,
                    sum(res.errorcount)
               FROM {' . manager::DB_AREAS . '} area
         INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
         INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
              WHERE 1=1 ' . $wheresql . '
            group by area.component, area.contextid';
        $combodata = $DB->get_records_sql_menu($sqlcombo, $params);

        $combo['total'] = count($combodata);
        $combo['failed'] = 0;
        foreach ($combodata as $count) {
            if ($count != 0) {
                $combo['failed']++;
            }
        }
        $combo['passed'] = ($combo['total'] - $combo['failed']);

        $data->combodata = $combo;

        $sql = 'SELECT che.checkgroup, SUM(res.errorcount) as errorinstances
              FROM {' . manager::DB_AREAS . '} area
        INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
        INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
        INNER JOIN {' . manager::DB_CHECKS . '} che ON che.id = res.checkid
             WHERE 1=1 ' . $wheresql . ' GROUP BY che.checkgroup
          ORDER BY che.checkgroup';

        $groupdata = $DB->get_records_sql($sql, $params);

        $data->groupdata = $groupdata;

        // Adding check displaynames from language strings.

        $wheresql = ' and area.courseid = ?';
        $params = [$filter->courseid];

        $sql4 = 'SELECT area.component, sum(res.errorcount) as errorsum
               FROM {' . manager::DB_AREAS . '} area
        INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
        INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
             WHERE 1=1 ' . $wheresql . ' GROUP BY area.component
          ORDER BY errorsum DESC';
        $toptargetdataraw = $DB->get_records_sql($sql4, $params, 0, 5);
        $toptargetdata = [];
        foreach ($toptargetdataraw as $top) {
            $top->component = tool::get_module_label($top->component);
            if ($top->errorsum != 0) {
                $toptargetdata[] = $top;
            }
        }
        $data->toptargetdata = $toptargetdata;

        $sql3 = 'SELECT che.shortname, sum(res.errorcount) as checkcount
              FROM {' . manager::DB_AREAS . '} area
        INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
        INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
        INNER JOIN {' . manager::DB_CHECKS . '} che ON che.id = res.checkid
             WHERE 1=1 ' . $wheresql . ' AND res.errorcount >= ? GROUP BY che.shortname
          ORDER BY checkcount DESC';
        $params[] = 1;
        $checkcountdata = $DB->get_records_sql($sql3, $params, 0, 5);
        foreach ($checkcountdata as $key => &$cdata) {
            $cdata->checkname = self::get_check_description($key);
        }
        $data->checkcountdata = $checkcountdata;

        $sqltar = 'SELECT distinct '.$DB->sql_concat_join("''", ['area.component', 'area.contextid']).' as tmpid,
                component, SUM(errorcount) as errorsum
               FROM {' . manager::DB_AREAS . '} area
         INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
         INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
              WHERE 1=1 '.$wheresql.'
           GROUP BY area.component, area.contextid';
        $targetdata = $DB->get_records_sql($sqltar, $params);

        $tarlabels = [];
        $combotar = [];
        foreach ($targetdata as $tar) {
            if (!array_key_exists($tar->component, $combotar)) {
                $combotar[$tar->component] = [];
                $combotar[$tar->component]['total'] = 0;
                $combotar[$tar->component]['failed'] = 0;
                $tarlabels[$tar->component] = tool::get_module_label($tar->component);
            }
            $combotar[$tar->component]['total']++;
            if ($tar->errorsum > 0) {
                $combotar[$tar->component]['failed']++;
            }
        }
        ksort($combotar);
        $data->combotardata = $combotar;
        $data->tarlabels = $tarlabels;

        $errorsql = 'SELECT err.id as errid, res.id as resid, area.component, area.itemid, area.cmid,
                   che.shortname, err.linenumber as errline, err.htmlcode
              FROM {' . manager::DB_AREAS . '} area
        INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
        INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
        INNER JOIN {' . manager::DB_CHECKS . '} che ON che.id = res.checkid
        INNER JOIN {' . manager::DB_ERRORS . '} err ON res.id = err.resultid WHERE 1=1 ' . $wheresql;
        $errordata = $DB->get_records_sql($errorsql, $params, 0, $perpage);

        foreach ($errordata as $err) {
            $err->shortname = self::get_check_description($err->shortname);
            // Truncating HTML with base64 image data, to avoid page overstretching.
            $base64detected = parent::base64_img_detected($err->htmlcode);
            if ($base64detected) {
                $err->htmlcode = parent::truncate_base64($err->htmlcode);
            }
        }

        $data->errordata = $errordata;
        $data->errordetailscount = $perpage;

        if ($filter->categoryid != 0) {
            $data->countdata = count($filter->courseids);
        } else {
            $countsql = 'select count(distinct courseid) from {' . manager::DB_AREAS . '}';
            $countdata = $DB->count_records_sql($countsql, []);
            $data->countdata = $countdata;
        }

        return $data;
    }

    /**
     * Get the HTML output for display.
     * @return mixed
     */
    public function get_output() {
        global $PAGE;

        $data = $this->get_data();
        $filter = $this->get_filter();

        $renderer = $PAGE->get_renderer('tool_brickfield', 'printable');
        if ($filter->target == 'pdf') {
            $renderer->pdf_renderer($data, $filter);
            return '';
        } else if ($filter->target == 'html') {
            $output = $renderer->header();
            return $output . $renderer->display($data, $filter);
        } else {
            return $renderer->display($data, $filter);
        }
    }
}
