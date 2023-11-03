<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
namespace block_learnerscript\output;

defined('MOODLE_INTERNAL') || die();

use block_learnerscript\form\basicparams_form;
use block_learnerscript\local\ls;
use block_learnerscript\local\schedule;
use html_table;
use html_writer;
use moodle_url;
use plugin_renderer_base;
use tabobject;
use stdclass;

/**
 * Block Report Dashboard renderer.
 * @package   block_learnerscript
 */
class renderer extends plugin_renderer_base {
	/**
	 * [generate_report_page description]
	 * @param  [type] $reportclass [description]
	 * @return [type]              [description]
	 */
	public function render_index_page(index_page $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('core_group/index', $data);
	}
	public function render_reporttable(reporttable $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/reporttable', $data);
	}
	public function render_plotoption(\block_learnerscript\output\plotoption $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/plotoption', $data);
	}
	public function render_design(\block_learnerscript\output\design $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/design', $data);
	}
	public function render_scheduledusers(\block_learnerscript\output\scheduledusers $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/scheduledusers', $data);
	}
	public function render_plottabs(\block_learnerscript\output\plottabs $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/plottabs', $data);
	}
	public function render_filtertoggleform(\block_learnerscript\output\filtertoggleform $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/filtertoggleform', $data);
	}
	public function viewreport($report, $context, $reportclass) {
		global $PAGE, $CFG, $USER, $OUTPUT;
		$calcbutton = false;
                $reportid = $report->id;
		$ls = new ls();

		if ($report->type !== 'statistics') {
			$plots = $ls->get_components_data($report->id, 'plot');
			$components = $ls->cr_unserialize($reportclass->config->components);
			$calcbutton = false;
			if (!empty($components['calculations']['elements'])) {
				$calcbutton = true;
			}
			if (has_capability('block/learnerscript:managereports', $context) ||
				(has_capability('block/learnerscript:manageownreports', $context)) && $report->ownerid == $USER->id) {
				$plotoptions = new \block_learnerscript\output\plotoption($plots, $report->id, $calcbutton,'viewreport');
				echo $this->render($plotoptions);
			}
			$debug = optional_param('debug', false, PARAM_BOOL);
			if ($debug) {
				$debugsql = true;
			}
		}

		if (!empty($reportclass->basicparams)) {
			$basicparamsform = new basicparams_form(null, $reportclass);
            $basicparamsform->set_data($reportclass->params);
			echo $basicparamsform->display();
		}
		$plottabscontent = '';
        $plotdata = (new ls)->cr_listof_reporttypes($report->id, false, false);
        if (!empty($plotdata)) {
            $params = '';
            unset($_GET['id']);
            foreach ($_GET as $k => $param) {
                if (is_numeric($param)) {
                    $params .= ", $k: $param";
                } else {
                    $params .= ", $k: '$param'";
                }
            }
            if(empty($reportclass->basicparams) || !empty($reportclass->params)){
                $enableplots = 0;
            }else{
                $enableplots = 1;
            }
            $plottabs = new \block_learnerscript\output\plottabs($plotdata, $report->id, $params, $enableplots);
            $plottabscontent = $this->render($plottabs);
        }

		echo '<div id="viewreport' . $report->id .'">';
		$filterform = $reportclass->print_filters(true);
		$filterform = new \block_learnerscript\output\filtertoggleform($filterform, $plottabscontent);
		echo $this->render($filterform);

		if($calcbutton){
			echo '<div class="reportcalculation'.$report->id.'" style="display:none;"></div>';
		}
        $plotreportcontainer = '';
        if ($report->disabletable == 1 && empty($plotdata)){
            $plotreportcontainer = '<div class="alert alert-info">' . get_string('nodataavailable', 'block_learnerscript') . '</div>';
        }
        echo "<div class='plotgraphcontainer hide pull-right' data-reportid=".$report->id.">" . html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('t/dockclose'), 'alt' => get_string('closegraph', 'block_learnerscript'), 'title' => get_string('closegraph', 'block_learnerscript'), 'class' => 'icon')) . "</div><div id='plotreportcontainer$reportid' class='ls-report_graph_container'>$plotreportcontainer</div>";

		// $plotdata = (new ls)->cr_listof_reporttypes($report->id, false, false);
		if (!empty($plotdata)) {
			// $params = '';
			// unset($_GET['id']);
			// foreach ($_GET as $k => $param) {
			// 	if (is_numeric($param)) {
			// 		$params .= ", $k: $param";
			// 	} else {
			// 		$params .= ", $k: '$param'";
			// 	}
			// }
			// if(empty($reportclass->basicparams) || !empty($reportclass->params)){
			// 	$enableplots = 0;
			// }else{
			// 	$enableplots = 1;
			// }
			// $plottabs = new \block_learnerscript\output\plottabs($plotdata, $report->id, $params, $enableplots, $filterform);
			// echo $this->render($plottabs);
		}

		$export = explode(',', $reportclass->config->export);
		if($report->disabletable == 0){
			echo '<div id="reportcontainer'.$report->id.'"></div>';
		}
		echo '</div>';
	}
	/**
	 * Scheduled reports data to display in html table
	 * @param  integer $reportid ReportID
	 * @param  integer $courseid CourseID
	 * @param  boolean $table    Table Head(true)/ Table Body (false)
	 * @param  integer $start
	 * @param  integer $length
	 * @param  string $search
	 * @return  if $table => true, table head content
	 *                     $table=> false, object with scheduled reports
	 *                     if  records not found, dispalying info message.
	 */
	public function schedulereportsdata($reportid, $courseid = 1, $table = true, $start = 0, $length = 5, $search = '') {
		global $DB, $CFG, $PAGE, $OUTPUT;

		$scheduledreports = (new schedule)->schedulereports($reportid, $table, $start, $length, $search);
		$frequencyselect = (new schedule)->get_options();
		if ($table) {
			if (!$scheduledreports['totalschreports']) {
				$return = html_writer::tag('center', get_string('noschedule', 'block_learnerscript'), array('class' => 'alert alert-info'));
			} else {
				$table = new html_table();
				$table->head = array(get_string('role', 'block_learnerscript'),
					get_string('exportformat', 'block_learnerscript'),
					get_string('schedule', 'block_learnerscript'),
					get_string('action'));
				$table->size = array('40%', '15%', '35%', '10%');
				$table->align = array('left', 'center', 'left', 'center');
				$table->id = 'scheduledtimings';
				$table->attributes['data-reportid'] = $reportid;
				$table->attributes['data-courseid'] = $courseid;
				$return = html_writer::table($table);
			}
		} else {
			$data = array();
			foreach ($scheduledreports['schreports'] as $sreport) {
				$line = array();

		        switch ($sreport->role) {
		            case 'admin':         $originalrole = 'Admin'; break;
		            case 'manager':         $originalrole = get_string('manager', 'role'); break;
		            case 'coursecreator':   $originalrole = get_string('coursecreators'); break;
		            case 'editingteacher':  $originalrole = get_string('defaultcourseteacher'); break;
		            case 'teacher':         $originalrole = get_string('noneditingteacher'); break;
		            case 'student':         $originalrole = get_string('defaultcoursestudent'); break;
		            case 'guest':           $originalrole = get_string('guest'); break;
		            case 'user':            $originalrole = get_string('authenticateduser'); break;
		            case 'frontpage':       $originalrole = get_string('frontpageuser', 'role'); break;
		            // We should not get here, the role UI should require the name for custom roles!
		            default:                $originalrole = $sreport->role; break;
		        }

				$line[] = $originalrole;
				$line[] = strtoupper($sreport->exportformat);
				$line[] = (new schedule)->get_formatted($sreport->frequency, $sreport->schedule);
				$buttons = array();
				$buttons[] = html_writer::link(new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid, 'courseid' => $courseid, 'scheduleid' => $sreport->id, 'sesskey' => sesskey())), html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('t/edit'), 'alt' => get_string('edit'), 'class' => 'iconsmall', 'title' => 'Edit')));
				$buttons[] = html_writer::link(new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid, 'courseid' => $courseid, 'scheduleid' => $sreport->id, 'sesskey' => sesskey(), 'delete' => 1)), html_writer::empty_tag('img', array('src' => $OUTPUT->image_url('t/delete'), 'alt' => get_string('delete'), 'class' => 'iconsmall', 'title' => 'Delete')));
				$line[] = implode(' ', $buttons);
				$data[] = $line;
			}
			$return = array(
				"recordsTotal" => $scheduledreports['totalschreports'],
				"recordsFiltered" => $scheduledreports['totalschreports'],
				"data" => $data,
			);
		}
		return $return;
	}

	public function viewschusers($reportid, $scheduleid, $schuserslist, $stable) {
		if ($stable->table) {
			$viewschuserscount = (new schedule)->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
			if ($viewschuserscount > 0) {
				$table = new html_table();
				$table->head = array(get_string('name'),
					get_string('email'));
				$table->size = array('50%', '50%');
				$table->id = 'scheduledusers';
				$table->attributes['data-reportid'] = $reportid;
				$table->attributes['data-courseid'] = isset($courseid) ? $courseid : SITEID;
				$return = html_writer::table($table);
			} else {
				$return = "<div class='alert alert-info'>" . get_string('usersnotfound', 'block_learnerscript') . "</div>";
			}
		} else {
			$schedulingdata = (new schedule)->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
			$data = array();
			foreach ($schedulingdata['schedulingdata'] as $sdata) {
				$line = array();
				$line[] = $sdata->fullname;
				$line[] = $sdata->email;
				$data[] = $line;
			}
			$return = array(
				"recordsTotal" => $schedulingdata['viewschuserscount'],
				"recordsFiltered" => $schedulingdata['viewschuserscount'],
				"data" => $data,
			);

		}

		return $return;
	}
	function print_tabs($reportclass, $currenttab) {
		global $COURSE;
		$top = array();
		$top[] = new tabobject('viewreport', new moodle_url('/blocks/learnerscript/viewreport.php',
			array('id' => $reportclass->config->id, 'courseid' => $COURSE->id)),
			get_string('viewreport', 'block_learnerscript'));
		$components = array('permissions');
		foreach ($reportclass->components as $comptab) {
			if (!in_array($comptab, $components)) {
				continue;
			}
			$top[] = new tabobject($comptab, new moodle_url('/blocks/learnerscript/editcomp.php',
				array('id' => $reportclass->config->id,
					'comp' => $comptab,
					'courseid' => $COURSE->id)),
				get_string($comptab, 'block_learnerscript'));
		}
		$top[] = new tabobject('report', new moodle_url('/blocks/learnerscript/editreport.php',
			array('id' => $reportclass->config->id,
				'courseid' => $COURSE->id)),
			get_string('report', 'block_learnerscript'));
		$top[] = new tabobject('schedulereport', new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php',
			array('id' => $reportclass->config->id,
				'courseid' => $COURSE->id)),
			get_string('schedulereport', 'block_learnerscript'));

		$top[] = new tabobject('managereports', new moodle_url('/blocks/learnerscript/managereport.php'),
			get_string('managereports', 'block_learnerscript'));

		$tabs = array($top);
		print_tabs($tabs, $currenttab);
	}

	public function render_component_form($reportid, $component, $pname) {
		GLOBAL $CFG, $DB, $PAGE;

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			print_error(get_string('noreportexists', 'block_learnerscript'));
		}
		require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $component . '/' . $pname . '/plugin.class.php';
		$pluginclassname = 'block_learnerscript\lsreports\plugin_' . $pname;
		$pluginclass = new $pluginclassname($report);

		require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $component . '/component.class.php';
		$componentclassname = 'component_' . $component;
		$compclass = new $componentclassname($report->id);

		require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $component . '/' . $pname . '/form.php';
		$classname = $pname . '_form';

		$formurlparams = array('id' => $reportid, 'comp' => $component, 'pname' => $pname);
		if ($cid) {
			$formurlparams['cid'] = $cid;
		}
		$formurl = new moodle_url('/blocks/learnerscript/editplugin.php', $formurlparams);
		$editform = new $classname($formurl, compact('comp', 'cid', 'id', 'pluginclass', 'compclass', 'report', 'reportclass'));
		$html = $editform->render();
		$headcode = $PAGE->start_collecting_javascript_requirements();

		$loadpos = strpos($headcode, 'M.yui.loader');
		$cfgpos = strpos($headcode, 'M.cfg');
		$script .= substr($headcode, $loadpos, $cfgpos - $loadpos);
		// And finally the initalisation calls for those libraries
		$endcode = $PAGE->requires->get_end_code();
		$script .= preg_replace('/<\/?(script|link)[^>]*>/', '', $endcode);

		return array('html' => $html, 'script' => $script);
	}
	public function render_lsconfig(\block_learnerscript\output\lsconfig $page) {
		$data = $page->export_for_template($this);
		return parent::render_from_template('block_learnerscript/lsconfig', $data);
	}
}