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
 * Dropdown menu in reportstable on overview tab.
 *
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Friederike Schwager
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pdfannotator\output;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class reportmenu implements \renderable, \templatable {

    private $url;
    private $iconclass;
    private $label;

    /**
     * Constructor of renderable for dropdown menu in reportstable.
     * @param object $report Report object
     * @param int $cmid Course module id
     * @param int $currentpage Page of the table on overviewpage
     * @param int $itemsperpage Number of entries on every page in the table
     * @param int $reportfilter Value of the filter for the answerstable
     */
    public function __construct($report, $cmid, $currentpage, $itemsperpage, $reportfilter) {
        global $CFG;
        if ($report->seen == 0) {
            $urlparams = array('action' => 'markreportasread');
            $iconclass = "icon fa fa-eye-slash fa-fw";
            $label = get_string('markasread', 'pdfannotator');
        } else {
            $urlparams = array('action' => 'markreportasunread');
            $iconclass = "icon fa fa-eye fa-fw";
            $label = get_string('markasunread', 'pdfannotator');
        }
        $urlparams['id'] = $cmid;
        if ($reportfilter == 2) {
            $urlparams['page'] = $currentpage;
        } else {
            $urlparams['page'] = '0';
        }
        $urlparams['reportid'] = $report->reportid;
        $urlparams['itemsperpage'] = $itemsperpage;
        $urlparams['reportfilter'] = $reportfilter;
        $urlparams['sesskey'] = sesskey();

        $url = new moodle_url($CFG->wwwroot . '/mod/pdfannotator/view.php', $urlparams);

        $this->url = $url;
        $this->iconclass = $iconclass;
        $this->label = $label;
    }

    /**
     * This function is required by any renderer to retrieve the data structure
     * passed into the template.
     * @param \renderer_base $output
     * @return type
     */
    public function export_for_template(\renderer_base $output) {
        $data = [];
        $data['url'] = $this->url->out();
        $data['iconclass'] = $this->iconclass;
        $data['label'] = $this->label;
        return $data;
    }

}
