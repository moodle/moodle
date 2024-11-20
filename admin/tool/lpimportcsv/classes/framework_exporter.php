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
 * This file contains the csv exporter for a competency framework.
 *
 * @package   tool_lpimportcsv
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lpimportcsv;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

use core_competency\api;
use stdClass;
use csv_export_writer;

/**
 * Export Competency framework.
 *
 * @package   tool_lpimportcsv
 * @copyright 2015 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class framework_exporter {

    /** @var $framework \core_competency\competency_framework */
    protected $framework = null;

    /** @var $error string */
    protected $error = '';

    /**
     * Constructor
     * @param int $frameworkid The framework id
     */
    public function __construct($frameworkid) {
        $this->framework = api::read_framework($frameworkid);
    }

    /**
     * Export all the competencies from this framework to a csv file.
     */
    public function export() {
        global $CFG;
        require_once($CFG->libdir . '/csvlib.class.php');

        $writer = new csv_export_writer();
        $filename = clean_param($this->framework->get('shortname') . '-' . $this->framework->get('idnumber'), PARAM_FILE);
        $writer->set_filename($filename);

        $headers = framework_importer::list_required_headers();

        $writer->add_data($headers);

        // Order and number of columns must match framework_importer::list_required_headers().
        $row = array(
            '',
            $this->framework->get('idnumber'),
            $this->framework->get('shortname'),
            $this->framework->get('description'),
            $this->framework->get('descriptionformat'),
            $this->framework->get_scale()->compact_items(),
            $this->framework->get('scaleconfiguration'),
            '',
            '',
            '',
            '',
            '',
            true,
            implode(',', $this->framework->get('taxonomies'))
        );
        $writer->add_data($row);

        $filters = array('competencyframeworkid' => $this->framework->get('id'));
        $competencies = api::list_competencies($filters);
        // Index by id so we can lookup parents.
        $indexed = array();
        foreach ($competencies as $competency) {
            $indexed[$competency->get('id')] = $competency;
        }
        foreach ($competencies as $competency) {
            $parentidnumber = '';
            if ($competency->get('parentid') > 0) {
                $parent = $indexed[$competency->get('parentid')];
                $parentidnumber = $parent->get('idnumber');
            }

            $scalevalues = '';
            $scaleconfig = '';
            if ($competency->get('scaleid') !== null) {
                $scalevalues = $competency->get_scale()->compact_items();
                $scaleconfig = $competency->get('scaleconfiguration');
            }

            $ruleconfig = $competency->get('ruleconfig');
            if ($ruleconfig === null) {
                $ruleconfig = "null";
            }

            $allrelated = $competency->get_related_competencies();

            $relatedidnumbers = array();
            foreach ($allrelated as $onerelated) {
                $relatedidnumbers[] = str_replace(',', '%2C', $onerelated->get('idnumber'));
            }
            $relatedidnumbers = implode(',', $relatedidnumbers);

            // Order and number of columns must match framework_importer::list_required_headers().
            $row = array(
                $parentidnumber,
                $competency->get('idnumber'),
                $competency->get('shortname'),
                $competency->get('description'),
                $competency->get('descriptionformat'),
                $scalevalues,
                $scaleconfig,
                $competency->get('ruletype'),
                $competency->get('ruleoutcome'),
                $ruleconfig,
                $relatedidnumbers,
                $competency->get('id'),
                false,
                ''
            );

            $writer->add_data($row);
        }

        $writer->download_file();
    }
}
