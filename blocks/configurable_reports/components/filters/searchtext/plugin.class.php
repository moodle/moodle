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
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_searchtext
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_searchtext extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('filter_searchtext', 'block_configurable_reports');
        $this->reporttypes = ['searchtext', 'sql'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return empty($data->idnumber) ? get_string('filter_searchtext_summary', 'block_configurable_reports') : $data->idnumber;
    }

    /**
     * Execute
     *
     * @param string $finalelements
     * @param object $data
     * @return string|array
     */
    public function execute($finalelements, $data) {

        // For backwards compatibility and filters without idnumber, includes old method of matching without idnumber.
        if (!empty($data->idnumber)) {
            $filtersearchtext = optional_param('filter_searchtext_' . $data->idnumber, '', PARAM_RAW);
        } else {
            $filtersearchtext = optional_param('filter_searchtext', '', PARAM_RAW);
        }

        if ($this->report->type !== 'sql') {
            return [$filtersearchtext];
        }

        if ($filtersearchtext) {
            if (!empty($data->idnumber)) {
                $filtermatch = "FILTER_SEARCHTEXT_{$data->idnumber}";
            } else {
                $filtermatch = "FILTER_SEARCHTEXT";
            }

            $finalelements = $this->sql_replace($filtersearchtext, $filtermatch, $finalelements);
        }

        return $finalelements;
    }

    /**
     * Print filter
     *
     * @param MoodleQuickForm $mform
     * @param bool|object $formdata
     * @return void
     */
    public function print_filter(MoodleQuickForm $mform, $formdata = false): void {

        // For backwards compatibility and filters without idnumber, includes old method of matching without idnumber.
        if (!empty($formdata->idnumber)) {
            $filtername = 'filter_searchtext_' . $formdata->idnumber;
        } else {
            $filtername = 'filter_searchtext';
        }
        if (isset($formdata->label)) {
            $filterlabel = $formdata->label;
        } else {
            $filterlabel = get_string('filter', 'block_configurable_reports');
        }
        $filtersearchtext = optional_param($filtername, '', PARAM_RAW);
        $mform->addElement('text', $filtername, $filterlabel);
        $mform->setType($filtername, PARAM_RAW);
        $mform->setDefault($filtername, $filtersearchtext);
    }

    /**
     * sql_replace
     *
     * @param string $filtersearchtext
     * @param string $filterstrmatch
     * @param string $finalelements
     * @return array|mixed|string|string[]
     */
    private function sql_replace($filtersearchtext, $filterstrmatch, $finalelements) {

        // TODO Check if this is a duplicate of the same function in plugin_fuserfield.
        $operators = ['=', '<', '>', '<=', '>=', '~', 'in'];

        if (preg_match("/%%$filterstrmatch:([^%]+)%%/i", $finalelements, $output)) {
            [$field, $operator] = preg_split('/:/', $output[1]);

            if (!in_array($operator, $operators, true)) {
                throw new moodle_exception('nosuchoperator');
            }

            if ($operator === '~') {
                $searchitem = trim(str_replace("'", "''", $filtersearchtext));
                $replace = " AND " . $field . " LIKE '%" . $searchitem . "%'";
            } else if ($operator === 'in') {
                $processeditems = [];
                // Accept comma-separated values, allowing for '\,' as a literal comma.
                foreach (preg_split("/(?<!\\\\),/", $filtersearchtext) as $searchitem) {
                    // Strip leading/trailing whitespace and quotes (we'll add our own quotes later).
                    $searchitem = trim($searchitem);
                    $searchitem = trim($searchitem, '"\'');

                    // We can also safely remove escaped commas now.
                    $searchitem = str_replace('\\,', ',', $searchitem);

                    // Escape and quote strings...
                    if (!is_numeric($searchitem)) {
                        $searchitem = "'" . addslashes($searchitem) . "'";
                    }
                    $processeditems[] = "$field like $searchitem";
                }
                // Despite the name, by not actually using in() we can support wildcards, and maybe be more portable as well.
                $replace = " AND (" . implode(" OR ", $processeditems) . ")";
            } else {
                $replace = ' AND ' . $field . ' ' . $operator . ' ' . $filtersearchtext;
            }
            $finalelements = str_replace("%%$filterstrmatch:" . $output[1] . '%%', $replace, $finalelements);
        }

        return $finalelements;
    }

}
