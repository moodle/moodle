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

/** LearnerScript Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
ini_set("memory_limit", "-1");
ini_set('max_execution_time', 6000);
use \block_learnerscript\Spout\Common\Type;
use \block_learnerscript\Spout\Writer\WriterFactory;

require_once $CFG->dirroot . '/blocks/learnerscript/lib.php';
require_once $CFG->libdir . '/adminlib.php';
function export_report($reportclass) {
	global $DB, $CFG, $SESSION;
	$reportdata = $reportclass->finalreport;
	$writer = WriterFactory::create(Type::CSV); // for XLSX files
	require_once $CFG->dirroot . '/lib/excellib.class.php';
	$table = $reportdata->table;
	$filename = $reportdata->name . "_" . Date('d M Y H:i:s', time()) . '.csv';
	$writer->openToBrowser($filename); // stream data directly to the browser
	$filter = array('Filters');
	$writer->addRow($filter);
	if(isset($reportclass->selectedfilters) && !empty($reportclass->selectedfilters)){
		foreach ($reportclass->selectedfilters as $k => $filter) {
			$k = substr($k, 0, -1);
	        $writer->addRow([$k, $filter]);
	    }
	}
	$head = array();
	if (!empty($table->head)) {
		foreach ($table->head as $key => $heading) {
			$head[] = $heading;
		}
		$writer->addRow($head);
	}
	$data = array();
	if(!empty($table->data)) {
		foreach ($table->data as $key => $value) {
			$data[] = array_map(function ($v) {
				return trim(strip_tags($v));
			}, $value);
		}
	}

	$writer->addRows($data); // add a row at a time
	$writer->close();
}
