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
 * Class errors.
 *
 * @package tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class errors extends tool {

    /**
     * Provide a name for this tool, suitable for display on pages.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolname(): string {
        return get_string('errors:toolname', 'tool_brickfield');
    }

    /**
     * Provide a short name for this tool, suitable for menus and selectors.
     * @return mixed|string
     * @throws \coding_exception
     */
    public static function toolshortname(): string {
        return get_string('errors:toolshortname', 'tool_brickfield');
    }

    /**
     * Provide a lowercase name identifying this plugin. Should really be the same as the directory name.
     * @return string
     */
    public function pluginname(): string {
        return 'errors';
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
        if (!$filter->validate_filters()) {
            return (object)[
                'valid' => false,
                'error' => $filter->get_errormessage(),
            ];

        }

        $data = (object)[
            'valid' => true,
            'error' => '',
        ];

        list($wheresql, $params) = $filter->get_course_sql();
        $sql = 'SELECT err.id as errid, res.id as resid, area.*,
                   res.checkid, err.linenumber as errline, err.htmlcode
              FROM {' . manager::DB_AREAS . '} area
        INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
        INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
        INNER JOIN {' . manager::DB_ERRORS . '} err  ON res.id = err.resultid
        WHERE 1=1 ' . $wheresql .'
          ORDER BY area.courseid, area.component ASC';

        $errordata = $DB->get_records_sql($sql, $params, ($filter->page * $filter->perpage), $filter->perpage);

        // Adding check displaynames and component names from language strings.
        $checks = $DB->get_records_menu(manager::DB_CHECKS, ['status' => 1], '', 'id, shortname');
        foreach ($errordata as $value) {
            $value->shortname = $checks[$value->checkid];
            $value->checkdesc = self::get_check_description($value->shortname);
            // Truncating HTML with base64 image data, to avoid page overstretching.
            $base64detected = parent::base64_img_detected($value->htmlcode);
            if ($base64detected) {
                $value->htmlcode = parent::truncate_base64($value->htmlcode);
            }
        }

        $countsql = 'SELECT COUNT(err.id)
            FROM {' . manager::DB_AREAS . '} area
      INNER JOIN {' . manager::DB_CONTENT . '} ch ON ch.areaid = area.id AND ch.iscurrent = 1
      INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id
      INNER JOIN {' . manager::DB_ERRORS . '} err ON res.id = err.resultid
      WHERE 1=1 ' . $wheresql;

        if (($filter->courseid == 0)
            && ($filter->categoryid == 0)) {
            $countsql = 'SELECT COUNT(err.id)
                      FROM {' . manager::DB_CONTENT . '} ch
                INNER JOIN {' . manager::DB_RESULTS . '} res ON res.contentid = ch.id AND ch.iscurrent = 1
                INNER JOIN {' . manager::DB_ERRORS . '} err ON res.id = err.resultid
                WHERE 1=1 ' . $wheresql;
        }

        $errortotal = $DB->count_records_sql($countsql, $params);

        $data->errordata = $errordata;
        $data->errortotal = $errortotal;

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
     * Errors needs to use perpage for pages.
     *
     * @param int $perpage
     * @return int
     */
    public function perpage_limits(int $perpage): int {
        $config = get_config(manager::PLUGINNAME);
        return $config->perpage;
    }
}
