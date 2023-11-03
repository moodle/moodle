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
 * @author: Naveen kumar
 * @date: 2014
 */
namespace block_learnerscript\local;
use pdf;
use DateTime;
use DateTimeZone;
use context_system;
use context_course;
use csv_export_writer;
use stdClass;
use MoodleODSWorkbook;
use MoodleODSWriter;
use PHPExcel;
use PHPExcel_IOFactory;
use block_learnerscript\local\ls;
use context_helper;
use moodle_exception;

require_once($CFG->dirroot . '/calendar/lib.php');

define('REPORT_EMAIL', 1);
define('REPORT_EXPORT', 2);
define('REPORT_EXPORT_AND_EMAIL', 3);
define('REPORT_EXPORT_FORMAT_ODS', 1);
define('REPORT_EXPORT_FORMAT_EXCEL', 2);
define('REPORT_EXPORT_FORMAT_CSV', 3);
define('REPORT_EXPORT_FORMAT_PDF', 4);

global $REPORT_EXPPORT_FORMATS;
$REPORT_EXPPORT_FORMATS = array('ods' => REPORT_EXPORT_FORMAT_ODS,
	'xls' => REPORT_EXPORT_FORMAT_EXCEL,
	'csv' => REPORT_EXPORT_FORMAT_CSV,
	'pdf' => REPORT_EXPORT_FORMAT_PDF);

class schedule {

	const DAILY = 1;
	const WEEKLY = 2;
	const MONTHLY = 3;

	public function getschedule($frequency) {
		global $CFG, $DB, $USER, $OUTPUT;
		$CALENDARDAYS = calendar_get_days();
		//Daily selector
		$data = array();
		if ($frequency == 1) {
			$dailyselect = array();
			for ($i = 0; $i < 24; $i++) {
				$dailyselect[$i] = date('G:i', mktime($i, 0, 0));
			}
			$data = $dailyselect;
		} elseif ($frequency == 2) {
			//Weekly selector
			$weeklyselect = array();
			for ($i = 0; $i < 7; $i++) {
				if (class_exists('\core_calendar\type_factory')) {
					$weeklyselect[$i] = get_string(strtolower($CALENDARDAYS[$i]['shortname']), 'calendar');
				} else {
					$weeklyselect[$i] = get_string(strtolower($CALENDARDAYS[$i]), 'calendar');
				}

			}
			$data = $weeklyselect;
		} else if ($frequency == 3) {
			$monthlyselect = array();
			$dateformat = ($USER->lang == 'en') ? 'jS' : 'j';
			for ($i = 1; $i <= 31; $i++) {
				$monthlyselect[$i] = date($dateformat, mktime(0, 0, 0, 0, $i));
			}
			$data = $monthlyselect;
		}

		return $data;
	}

	/**
	 * Get available scheduler options
	 *
	 * @return array
	 */
	public static function get_options() {

		return array(0 => '--SELECT--',
			self::DAILY => 'daily',
			self::WEEKLY => 'weekly',
			self::MONTHLY => 'monthly');
	}

	public function next($schedulereport, $timestamp = null, $is_cron = true) {
		global $USER;
		if (!isset($schedulereport->frequency)) {
			return $this;
		}

		$this->changed = true;
		$frequency = $schedulereport->frequency;
		$schedule = $schedulereport->schedule;
		$usertz = $this->get_clean_timezone($USER->timezone);
		if (is_null($timestamp)) {
			$datetime = new DateTime('now', new DateTimeZone($usertz));
			$timestamp = strtotime($datetime->format('Y-m-d H:i:s'));
		}
		is_null($timestamp) ? $time = time() : $time = $timestamp;
		$timeday = date('j', $time);
		$timemonth = date('n', $time);
		$timeyear = date('Y', $time);

		switch ($frequency) {
		case self::DAILY:
			$offset = (date('G', $time) < $schedule) ? 0 : DAYSECS;
			$nextschedule = mktime(0, 0, 0, $timemonth, $timeday, $timeyear) + $offset + ($schedule * 60 * 60);
			break;
		case self::WEEKLY:
			$calendardays = calendar_get_days();
			if ($schedule <= date('w')) {
				$day = (7 - date('w')) + $schedule;
			} else {
				$day = ($schedule - date('w')) + 7;
			}
			if ((($calendardays[$schedule]['fullname'] == strtolower(strftime('%A', time()))) && (!$is_cron))) {
				$nextschedule = mktime(9, 0, 0, $timemonth, $timeday, $timeyear);
			} else {
				$nextschedule = mktime(9, 0, 0, $timemonth, $timeday + $day, $timeyear);
			}
			break;
		case self::MONTHLY:
			if (($timeday == $schedule) && (!$is_cron)) {
				$nextschedule = mktime(10, 0, 0, $timemonth, $timeday, $timeyear);
			} else {
				$offset = ($timeday >= $schedule) ? 1 : 0;
				$newmonth = $timemonth + $offset;
				if ($newmonth < 13) {
					$newyear = $timeyear;
				} else {
					$newyear = $timeyear + 1;
					$newmonth = 1;
				}

				$daysinmonth = date('t', mktime(0, 0, 0, $newmonth, 3, $newyear));
				$newday = ($schedule > $daysinmonth) ? $daysinmonth : $schedule;
				$nextschedule = mktime(10, 0, 0, $newmonth, $newday, $newyear);
			}
			break;
		}
		// Make the appropriate conversion in case the user is using a different timezone from the server.
		$datetime = new DateTime(date('Y-m-d H:i:s', $nextschedule), new DateTimeZone($usertz));
		$datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
		$nextschedule = strtotime($datetime->format('Y-m-d H:i:s'));

		return $nextschedule;
	}

	/**
	 * @method get_formatted Given scheduled report frequency and schedule data, output a human readable string.
	 *
	 * @param integer $frequent Frequency of the report scheduling
	 * @param integer $schedule The scheduled date/time (either hour of day, day or week or day of month)
	 * @param object $user User object belonging to the recipient (optional). Defaults to current user
	 * @return string schedule desription
	 */
	public function get_formatted($frequent, $schedule, $user = false) {
		global $USER;
		if (!$user) {
			$user = $USER;
		}

		$timemonth = date('n', time());
		$timeday = date('j', time());
		$timeyear = date('Y', time());
		$calendardays = calendar_get_days();
		$dateformat = ($USER->lang == 'en') ? 'jS' : 'j';
		$out = '';
		switch ($frequent) {

		case self::DAILY:
			$out .= get_string('daily', 'block_learnerscript') . ' ' . get_string('at', 'block_learnerscript') . ' ';
			$out .= strftime('%I:%M%p', mktime($schedule, 0, 0, $timemonth, $timeday, $timeyear));
			break;
		case self::WEEKLY:
			$out .= get_string('weekly', 'block_learnerscript') . ' ' . get_string('on', 'block_learnerscript') . ' ';
			if (($calendardays[$schedule]['fullname'])) {
				$out .= get_string(($calendardays[$schedule]['fullname']), 'block_learnerscript');
			}
			break;
		case self::MONTHLY:
			$out .= get_string('monthly', 'block_learnerscript') . ' ' . get_string('onthe', 'block_learnerscript') . ' ';
			$out .= date($dateformat, mktime(0, 0, 0, 0, $schedule, $timeyear));
			break;
		}
		return $out;
	}

	/**
	 * gets a list of bad timezones with the most likely proper named location zone
	 * @return array a bad timezone list key=>bad value=>replacement
	 */
	function get_bad_timezone_list() {
		$zones = array();
		//unsupported but common abbreviations
		$zones['EST'] = 'America/New_York';
		$zones['EDT'] = 'America/New_York';
		$zones['EST5EDT'] = 'America/New_York';
		$zones['CST'] = 'America/Chicago';
		$zones['CDT'] = 'America/Chicago';
		$zones['CST6CDT'] = 'America/Chicago';
		$zones['MST'] = 'America/Denver';
		$zones['MDT'] = 'America/Denver';
		$zones['MST7MDT'] = 'America/Denver';
		$zones['PST'] = 'America/Los_Angeles';
		$zones['PDT'] = 'America/Los_Angeles';
		$zones['PST8PDT'] = 'America/Los_Angeles';
		$zones['HST'] = 'Pacific/Honolulu';
		$zones['WET'] = 'Europe/London';
		$zones['GMT'] = 'Europe/London';
		$zones['EET'] = 'Europe/Kiev';
		$zones['FET'] = 'Europe/Minsk';
		$zones['CET'] = 'Europe/Amsterdam';

		//Moodle offset zones.
		$zones['-13.0'] = 'Pacific/Apia';
		$zones['-12.5'] = 'Pacific/Apia';
		$zones['-12.0'] = 'Pacific/Kwajalein';
		$zones['-11.5'] = 'Pacific/Niue';
		$zones['-11.0'] = 'Pacific/Midway';
		$zones['-10.5'] = 'Pacific/Rarotonga';
		$zones['-10.0'] = 'Pacific/Honolulu';
		$zones['-9.5'] = 'Pacific/Marquesas';
		$zones['-9.0'] = 'America/Anchorage';
		$zones['-8.5'] = 'America/Anchorage';
		$zones['-8.0'] = 'America/Los_Angeles';
		$zones['-7.5'] = 'America/Los_Angeles';
		$zones['-7.0'] = 'America/Denver';
		$zones['-6.5'] = 'America/Denver';
		$zones['-6.0'] = 'America/Chicago';
		$zones['-5.5'] = 'America/Chicago';
		$zones['-5.0'] = 'America/New_York';
		$zones['-4.5'] = 'America/Caracas';
		$zones['-4.0'] = 'America/Santiago';
		$zones['-3.5'] = 'America/St_Johns';
		$zones['-3.0'] = 'America/Sao_Paulo';
		$zones['-2.5'] = 'America/Sao_Paulo';
		$zones['-2.0'] = 'Atlantic/South_Georgia';
		$zones['-1.5'] = 'Atlantic/Cape_Verde';
		$zones['-1.0'] = 'Atlantic/Cape_Verde';
		$zones['-0.5'] = 'Europe/London';
		$zones['0.0'] = 'Europe/London';
		$zones['0.5'] = 'Europe/London';
		$zones['1.0'] = 'Europe/Amsterdam';
		$zones['1.5'] = 'Europe/Amsterdam';
		$zones['2.0'] = 'Europe/Helsinki';
		$zones['2.5'] = 'Europe/Minsk';
		$zones['3.0'] = 'Asia/Riyadh';
		$zones['3.5'] = 'Asia/Tehran';
		$zones['4.0'] = 'Asia/Dubai';
		$zones['4.5'] = 'Asia/Kabul';
		$zones['5.0'] = 'Asia/Karachi';
		$zones['5.5'] = 'Asia/Kolkata';
		$zones['6.0'] = 'Asia/Dhaka';
		$zones['6.5'] = 'Asia/Rangoon';
		$zones['7.0'] = 'Asia/Bangkok';
		$zones['7.5'] = 'Asia/Singapore';
		$zones['8.0'] = 'Australia/Perth';
		$zones['8.5'] = 'Australia/Perth';
		$zones['9.0'] = 'Asia/Tokyo';
		$zones['9.5'] = 'Australia/Adelaide';
		$zones['10.0'] = 'Australia/Sydney';
		$zones['10.5'] = 'Australia/Lord_Howe';
		$zones['11.0'] = 'Pacific/Guadalcanal';
		$zones['11.5'] = 'Pacific/Norfolk';
		$zones['12.0'] = 'Pacific/Auckland';
		$zones['12.5'] = 'Pacific/Auckland';
		$zones['13.0'] = 'Pacific/Apia';
		return $zones;
	}

	/**
	 * @method get_clean_timezone
	 * @todo Gets a clean timezone attempting to compensate for some Moodle 'special' timezones
	 *       where the returned zone is compatible with PHP DateTime, DateTimeZone etc functions
	 * @param string/float $tz either a location identifier string or, in some Moodle special cases, a number
	 * @return string a clean timezone that can be used safely
	 */
	function get_clean_timezone($tz = null) {
		global $CFG, $DB;

		$cleanzones = DateTimeZone::listIdentifiers();
		if (empty($tz)) {
			$tz = $this->get_user_timezone();
		}

		//if already a good zone, return
		if (in_array($tz, $cleanzones, true)) {
			return $tz;
		}
		//for when all else fails
		$default = 'Europe/London';
		//try to handle UTC offsets, and numbers including '99' (server local time)
		//note: some old versions of moodle had GMT offsets stored as floats
		if (is_numeric($tz)) {
			if (intval($tz) == 99) {
				//check various config settings to try and resolve to something useful
				if (isset($CFG->forcetimezone) && $CFG->forcetimezone != 99) {
					$tz = $CFG->forcetimezone;
				} else if (isset($CFG->timezone) && $CFG->timezone != 99) {
					$tz = $CFG->timezone;
				}
			}
			if (intval($tz) == 99) {
				//no useful CFG settings, try a system call
				$tz = date_default_timezone_get();
			}
			//do we have something useful yet?
			if (in_array($tz, $cleanzones, true)) {
				return $tz;
			}
			//check the bad timezone replacement list
			if (is_float($tz)) {
				$tz = number_format($tz, 1);
			}
			$badzones = $this->get_bad_timezone_list();
			//does this exist in our replacement list?
			if (in_array($tz, array_keys($badzones))) {
				return $badzones[$tz];
			}
		}
		//everything has failed, set to London
		return $default;
	}

	/**
	 * @method scheduledreport_send_scheduled_report Sent Scheduled report as attachment to user email
	 * @param object $schedule Object containing data from schedule table
	 * @return boolean True/False Email status
	 */
	function scheduledreport_send_scheduled_report($schedule) {
		global $CFG, $DB, $USER, $REPORT_EXPPORT_FORMATS;

		switch ($schedule->exportformat) {
		case 'xls':
			$attachmentfilename = $schedule->name . '.xls';
			break;
		case 'csv':
			$attachmentfilename = $schedule->name . '.csv';
			break;
		case 'ods':
			$attachmentfilename = $schedule->name . '.ods';
			break;
		case 'pdf':
			$attachmentfilename = $schedule->name . '.pdf';
			break;
		}
		$sendinguserids = explode(',', $schedule->sendinguserid);
		foreach ($sendinguserids as $sendinguserid) {
			if (!$user = $DB->get_record('user', array('id' => $sendinguserid))) {
				error_log(get_string('error:invaliduserid', 'block_learnerscript'));
				return false;
			}

			$attachment = $this->scheduledreport_create_attachment($schedule, $user);

			if ($schedule->exporttofilesystem != REPORT_EXPORT) {
				$reporturl = $this->get_report_url($schedule->reportid);
				// $strmgr = get_string_manager();
				$messagedetails = new stdClass();
				$messagedetails->reportname = $schedule->name;
				$messagedetails->exporttype = get_string($schedule->exportformat . 'format', 'block_learnerscript');
				$messagedetails->reporturl = '<a href="' . $reporturl . '" > View Report </a>';
				$messagedetails->scheduledreportsindex = $CFG->wwwroot . '/blocks/learnerscript/components/scheduler/schedule.php?id=' . $schedule->reportid;

				$messagedetails->schedule = $this->get_formatted($schedule->frequency, $schedule->schedule, $user);
				$messagedetails->admin = fullname(\core_user::get_user(2));
				$subject = $schedule->name . ' ' . get_string('report', 'block_learnerscript');

				$messagedetails->nodata = '';
				if (empty($attachment)) {
					$messagedetails->nodata = '<div style="background-color:#fcf8e3;color:#8a6d3b;border-color:#faebcc;width: 30%;text-align: center;padding: 5px;">No Data Available.</div>';
				}

				$message = get_string('scheduledreportmessage', 'block_learnerscript', $messagedetails);

				$fromaddress = !empty($CFG->noreplyaddress) ? $CFG->noreplyaddress : 'noreply@' . $_SERVER['HTTP_HOST'];

				$emailed = false;
				$messagetext = html_to_text($message);
				$emailed = email_to_user($user, $fromaddress, $subject, $messagetext, $message, $attachment, $attachmentfilename);
			}

			if ($schedule->exporttofilesystem == REPORT_EMAIL) {
				if ($attachment && !unlink($CFG->dataroot . '/' . $attachment)) {
					mtrace(get_string('error:failedtoremovetempfile', 'block_learnerscript'));
				}
			}
		}
		if ($schedule->frequency == ONDEMAND) {
			$schedule->timemodified = time();
			$DB->update_record('block_ls_schedule', $schedule);
		}
		return true;
	}

	/**
	 * @method scheduledreport_create_attachment
	 * @todo Creates an export of a report in specified format (xls, csv or ods)
	 *       for adding to email as attachment
	 * @param object $schedule schedule record
	 * @return string Filename of the created attachment
	 */
	function scheduledreport_create_attachment($schedule, $user) {
		global $CFG, $DB;

		$reportid = $schedule->reportid;
		$format = $schedule->exportformat;
		$exporttofilesystem = $schedule->exporttofilesystem;
		$scheduleid = $schedule->id;
                $contextlevel = $schedule->contextlevel;         
		$tempfilename = md5(time());

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			print_error('reportdoesnotexists', 'block_learnerscript');
		}
		$role = $DB->get_field('role', 'shortname', array('id' => $schedule->roleid));
		$reportdata = $this->reportdata($reportid, $user, $role, $contextlevel);

		$reportfilepathname = $this->scheduledreport_get_export_filename($report, $user, $schedule);
		$finalreportarray = array($reportdata->finalreport);
		if ($schedule->exporttofilesystem == REPORT_EMAIL) {
			if ($finalreportarray[0] == false) {
				return false;
			}
			if (empty($reportdata->finalreport->table->data)) {
				return false;
			}
		}
		switch ($format) {
		case 'ods':
			$filename = $this->export_ods($reportdata, $CFG->dataroot . '/' . $reportfilepathname);
			$reportfilepathname = $reportfilepathname . '.ods';
			break;
		case 'xls':
			$filename = $this->export_xls($reportdata, $CFG->dataroot . '/' . $reportfilepathname);
			$reportfilepathname = $reportfilepathname . '.xls';
			break;
		case 'csv':
			$filename = $this->export_csv($reportdata, $CFG->dataroot . '/' . $reportfilepathname);
			$reportfilepathname = $reportfilepathname . '.csv';
			break;
		case 'pdf':
			$filename = $this->export_pdf($reportdata, $CFG->dataroot . '/' . $reportfilepathname);
			$reportfilepathname = $reportfilepathname . '.pdf';
			break;
		}

		$dir = get_config('block_learnerscript', 'exportfilesystempath') . DIRECTORY_SEPARATOR . $user->id;
		$reportfilename = $reportfilepathname;
		return $reportfilename;
	}

	/**
	 * @method scheduledreport_get_export_filename
	 * @todo Checks if username directory under given path exists
	 *       If it does not it creates it and returns fullpath with filename
	 *       userdir + report fullname + time created + schedule id
	 * @param object $report
	 * @param int $userid
	 * @return string reportfullpathname
	 */
	function scheduledreport_get_export_filename($report, $user) {
		global $DB, $CFG;
		$reportfilename = format_string($report->name);
		$reportfilename = clean_param($reportfilename, PARAM_FILE) . time();
		$username = fullname($user);
		$dir = get_config('block_learnerscript', 'exportfilesystempath') . DIRECTORY_SEPARATOR . $user->id;
		if (!file_exists($CFG->dataroot . DIRECTORY_SEPARATOR . $dir)) {
			@mkdir($CFG->dataroot . DIRECTORY_SEPARATOR . $dir, 0777, true);
		}
		$reportfilepathname = $dir . DIRECTORY_SEPARATOR . $reportfilename;

		return $reportfilepathname;
	}

	/*
	 * @method reportdata Generates report
	 * @param int $reportid Report ID
	 * @return object Report data
	*/

	function reportdata($reportid, $user, $role, $contextlevel) {
		global $CFG, $DB;
		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			print_error('reportdoesnotexists', 'block_learnerscript');
		}

		require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');

		if ($report->courseid == SITEID) {
			$context = context_system::instance();
		} else {
			$context = context_course::instance($report->courseid);
		}

		$report->userid = $user->id;
		$reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
		$properties = new stdClass();
		$properties->userid = $user->id;
	 	$rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id), 
	                        r.shortname, rcl.contextlevel 
	                        FROM {role} AS r 
	                        JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
	                        WHERE 1 = 1  
	                        ORDER BY rcl.contextlevel ASC");
		foreach ($rolecontexts as $rc) {
		   if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
		    continue;
		   }
		   $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
		}
		$properties->role = $role;
		$properties->contextlevel = $contextlevel; 
		$properties->contextrole = $role .'_'. $contextlevel;
		$properties->moodleroles = $rcontext;
		$reportclass = new $reportclassname($report, $properties);
		$reportclass->courseid = $report->courseid;
		$reportclass->start = 0;
		$reportclass->length = -1;
		$reportclass->search = '';
		$reportclass->ls_startdate = 0;
		$reportclass->ls_enddate = time();
		$basicparamdata = new stdclass;
		$reportclass->params = (array)$basicparamdata;
		if (empty($role)) {
			$role = '';
			$rolelist = (new ls)->get_currentuser_roles($user->id);
	        $components = (new ls)->cr_unserialize($reportclass->config->components);
	        $permissions = (isset($components['permissions'])) ? $components['permissions'] : array();
			if (empty($permissions['elements'])){
				$role = '';
			} else {
				$rolepermissions = array();
	            foreach ($permissions['elements'] as $p) {
	            	if ($p['pluginname'] == 'roleincourse') {
	            		 $rolepermissions[] = $p['formdata']->roleid;
	            	}
	            }
	            sort($rolepermissions);
	            $roleslistids = array_keys($rolelist);
	            foreach ($rolepermissions as $rolepermission) {
					if (in_array($rolepermission, $roleslistids)) {
						$role = $rolelist[$rolepermission];
						break;
					}
	            }
			}
	    }
	   
		$reportclass->scheduling = true;
		if (!$reportclass->check_permissions($user->id, $context) && ($reportclass->role != 'manager' && $reportclass->contextlevel != CONTEXT_SYSTEM)) {
			return array(array(), false);
		}
		$reportclass->reporttype = 'table';
		$reportclass->create_report();
		return $reportclass;
	}

	/**
	 * @method get_report_url
	 * @todo URL to view report
	 * @param integer $reportid Report ID
	 * @return string URL of the report provided or false
	 */
	function get_report_url($reportid) {
		global $CFG;
		return $CFG->wwwroot . '/blocks/learnerscript/viewreport.php?id=' . $reportid;
	}

	/*****************************************************************
	 * Export functions for Excel,ODS,CSV and PDF                    *
	 * @param obejct $report Report data object                      *
	 * @return Report export file                                    *
	*/

	//XLS
	function export_xls($reportclass, $filename) {
		global $DB, $CFG;
		require_once "$CFG->libdir/phpexcel/PHPExcel.php";
		$report = $reportclass->finalreport;
		$table = $report->table;

		$filename = $filename . '.xls';

		/// Creating a workbook
		$workbook = new PHPExcel();
		$workbook->getActiveSheet()->setTitle(get_string('listofusers', 'block_learnerscript'));
		$rowNumber = 1;
		$col = 'A';
		foreach ($table->head as $key => $heading) {
			$workbook->getActiveSheet()->setCellValue($col . $rowNumber, str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading)))));
			$col++;
		}
		// Loop through the result set
		$rowNumber = 2;
		if (!empty($table->data)) {
			foreach ($table->data as $rkey => $row) {
				$col = 'A';
				foreach ($row as $key => $item) {
					$workbook->getActiveSheet()->setCellValue($col . $rowNumber, str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item)))));
					$col++;
				}
				$rowNumber++;
			}
		}

		// Freeze pane so that the heading line won't scroll
		$workbook->getActiveSheet()->freezePane('A2');

		// Save as an Excel BIFF (xls) file
		$objWriter = PHPExcel_IOFactory::createWriter($workbook, 'Excel2007');

		$objWriter->save($filename);
	}

	// ODS
	function export_ods($reportclass, $filename = null) {
		global $DB, $CFG;
		require_once $CFG->dirroot . '/lib/odslib.class.php';
		$report = $reportclass->finalreport;
		$table = $report->table;
		$matrix = array();
		//!$fname? $filename = 'report_'.(time()).'.ods':
		$filename = $filename . '.ods';

		if (!empty($table->head)) {
			$countcols = count($table->head);
			$keys = array_keys($table->head);
			$lastkey = end($keys);
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
		$workbook = new MoodleODSWorkbook($filename);

		$myxls = array();

		$myxls[0] = $workbook->add_worksheet('');
		foreach ($matrix as $ri => $col) {
			foreach ($col as $ci => $cv) {
				$myxls[0]->write($ri, $ci, $cv);
			}
		}
		$writer = new MoodleODSWriter($myxls);
		$contents = $writer->get_file_content();
		$handle = fopen($filename, 'w');
		fwrite($handle, $contents);
		fclose($handle);
	}

	//PDF
	function export_pdf($reportclass, $fname = '') {
		global $DB, $CFG;
		require_once $CFG->libdir . '/pdflib.php';
		$report = $reportclass->finalreport;
		$table = $report->table;
		$matrix = array();
		$fname == '' ? $filename = 'report' : $filename = $fname . '.pdf';

		if (!empty($table->head)) {
			$countcols = count($table->head);
			$keys = array_keys($table->head);
			$lastkey = end($keys);
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
		$table = "";

		$table .= "<table border=\"1\" cellpadding=\"5\"><thead><tr>";
		$s = count($matrix);
		reset($matrix);
		$first_key = key($matrix);
		for ($i = $first_key; $i < ($first_key + 1); $i++) {
			foreach ($matrix[$i] as $col) {
				$table .= "<td><b>$col</b></td>";
			}
		}
		$table .= "</tr></thead><tbody>";
		for ($i = ($first_key + 1); $i < count($matrix); $i++) {
			$table .= "<tr>";
			foreach ($matrix[$i] as $col) {
				$table .= "<td>$col</td>";
			}
			$table .= "</tr>";
		}
		$table .= "</tbody></table>";
		$table .= "";
		$doc = new pdf;
		$doc->setPrintHeader(false);
		$doc->setPrintFooter(false);
		$doc->AddPage();

		$doc->writeHTML($table);

		if ($fname == '') {
			$doc->Output();
			exit;
		} else {
			$doc->Output($filename, 'F');
		}
	}

	//CSV
	function export_csv($reportclass, $filename = '') {
		global $DB, $CFG;
		require_once $CFG->libdir . '/csvlib.class.php';
		$report = $reportclass->finalreport;
		$table = $report->table;
		$matrix = array();
		$filename = '' ? $filename = 'report.csv' : $filename . '.csv';

		if (!empty($table->head)) {
			$countcols = count($table->head);
			$keys = array_keys($table->head);
			$lastkey = end($keys);
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

		$csvexport = new csv_export_writer();
		$csvexport->set_filename($filename);

		foreach ($matrix as $ri => $col) {
			$csvexport->add_data($col);
		}
		if ($filename) {
			$fp = fopen($filename, "w");
			fwrite($fp, $csvexport->print_csv_data(true));
			fclose($fp);
		} else {
			$csvexport->download_file();
			exit;
		}
	}
	/**
	 * Getting users by using role wise and searching parameter string
	 * @param  array $roles  List of roleids
	 * @param  string $search
	 * @return Object List of users with id and fullname
	 */
	public function rolewiseusers($roleid, $search = '', $page = 0, $reportid = 0, $contextlevel = 10) {
		global $DB, $PAGE;
		if (empty($roleid)) {
			throw new moodle_exception('Missing Role values.');
		}

		$limit = 10;
		$start = ($page-1) * $limit;
		if ($search) {
			$search_sql = " AND CONCAT(u.firstname, ' ', u.lastname) LIKE '%$search%'";
		} else {
			$search_sql = " ";
		}

		$role_sql = " AND ra.roleid IN ($roleid) ";

		$rolewiseuserssql = "SELECT DISTINCT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                               FROM {user} u
                               JOIN {role_assignments} ra ON u.id = ra.userid 
                               JOIN {context} as ctx on ctx.id = ra.contextid AND ctx.contextlevel= $contextlevel
                              WHERE  ra.roleid =$roleid $search_sql AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";

		$rolewiseusers = $DB->get_records_sql($rolewiseuserssql, array());
		$reportclass = (new ls)->create_reportclass($reportid);
		$reportclass->role = $DB->get_field('role', 'shortname', array('id' => $roleid));
		$reportclass->courseid = $reportclass->config->courseid;

		if ($reportclass->config->courseid == SITEID) {
			$context = context_system::instance();
		} else {
			$context = context_course::instance($reportclass->config->courseid);
		}
		$data = array();

		foreach ($rolewiseusers as $rolewiseuser) {
			if ($reportclass->check_permissions($rolewiseuser->id, $context) && !is_siteadmin($rolewiseuser->id)) {
				$data[] = ['id' => $rolewiseuser->id, 'text' => $rolewiseuser->fullname];
			}
		}
		return $data;
	}
	/**
	 * Handling users for bulk selecting to schedule a report
	 * Handling both condition to add or remove users to schedule report.
	 * @param  integer  $reportid           ReportID
	 * @param  integer  $scheduleid      ScheduleID
	 * @param  string  $type               Type usually 'add' or 'remove'
	 * @param  array  $roles         Fetching users by using roles
	 * @param  string $search
	 * @param  string $bullkselectedusers List of users with comma seperatred value
	 * @return Object List of users with id and fullname
	 */
	public function schroleusers($reportid, $scheduleid, $type, $roleid, $search = '', $bullkselectedusers = '', $contextlevel = 10) {
		global $DB, $USER;

		if (!$reportid) {
			throw new moodle_exception('Missing Report ID.');
		}

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			throw new moodle_exception('Report Not Available.');
		}

		if (!$type) {
			throw new moodle_exception('Missing Type.');
		}

		if (empty($roleid)) {
			throw new moodle_exception('Missing Role.');
		}

		if ($search) {
			$search_sql = " AND CONCAT(u.firstname, ' ', u.lastname) LIKE '%$search%'";
		} else {
			$search_sql = " ";
		}

		if ($bullkselectedusers) {
			$bullkselectedusers = implode(',', $bullkselectedusers);
			$escselsql = " AND u.id NOT IN ($bullkselectedusers) ";
		} else {
			$escselsql = " ";
		}

		if ($scheduleid > 0) {
			$concattsql = " AND bcs.id = $scheduleid ";
		} else {
			$concattsql = " ";
		}

		switch ($type) {
		case 'add':
			if (in_array(-1, (array)$roleid)) {
				$role_sql = " ";
			} else {
				$role_sql = " AND ra.roleid = :roleid ";
			}

			$sql = " SELECT u.id,
					 CONCAT(u.firstname, ' ' , u.lastname) as fullname
                     FROM {user}  as u
                     JOIN {role_assignments} as ra
                     JOIN {context} as ctx ON ctx.id = ra.contextid AND ctx.contextlevel =:contextlevel
                     WHERE u.id = ra.userid  AND ra.roleid = :roleid $search_sql $escselsql AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";

			$users = $DB->get_records_sql($sql, ['contextlevel' => $contextlevel, 'roleid' => $roleid]);
			break;

		case 'remove':
			$userslistsql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname
					FROM {user} as u
					JOIN {block_ls_schedule} as bcs ON u.id IN (bcs.sendinguserid)
					WHERE bcs.reportid = $reportid $search_sql  AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0 ";

			$users = $DB->get_records_sql($userslistsql);

			break;
		}
		$data = array();
		foreach ($users as $userdetail) {
			$data[] = ['id' => $userdetail->id, 'fullname' => $userdetail->fullname];
		}
		return $data;
	}
	/**
	 * List Of scheduled users list
	 * @param  integer $reportid   ReportID
	 * @param  integer $scheduleid   ScheduledID
	 * @param  String $schuserslist List of userids with comma seperated
	 * @param  Object $stable contains search value, start, length and table
	 * @return Object List of users with id and fullname
	 */
	public function viewschusers($reportid, $scheduleid, $schuserslist, $stable) {
		global $DB;

		if (!$reportid) {
			throw new moodle_exception('Missing Report ID.');
		}

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			throw new moodle_exception('Report Not Available.');
		}
		if ($stable->table) {
			$schuserscountsql = "SELECT COUNT(u.id) as count
				   FROM {user} as u
				WHERE u.id IN (".$schuserslist.") AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";
		} else {
			$schuserscountsql = "SELECT COUNT(u.id) as count
				   FROM {user} as u
				WHERE u.id IN (".$schuserslist.") AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";
			$schuserssql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname, u.email
				   FROM {user} as u
				WHERE u.id IN (".$schuserslist.") AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";
		}

		if (!empty($stable->table)) {
			return $DB->count_records_sql($schuserscountsql);
		} else {
			$fields = array("CONCAT(u.firstname, ' ', u.lastname) ", "u.email");
			$fields = implode(" LIKE '%" . $stable->search . "%' OR ", $fields);
			$fields .= " LIKE '%" . $stable->search . "%' ";
			if ($stable->search) {
				$schuserscountsql .= " AND ( $fields ) ";
				$schuserssql .= " AND ( $fields ) ";
			}
			$viewschuserscount = $DB->count_records_sql($schuserscountsql);
			$schuserssql .= ' ORDER BY u.id ASC';
			$schedulingdata = $DB->get_records_sql($schuserssql, array(), $stable->start, $stable->length);
			return compact('schedulingdata', 'viewschuserscount');
		}
	}
	/**
	 * Get List of scheduled reports and total count by using report ID
	 * @param  integer  $reportid ReportID
	 * @param  boolean $table table head (true)/ body (false)
	 * @param  integer $start
	 * @param  integer $length
	 * @param  string  $search
	 * @return array  [list of scheduled reports, total scheduled count of each report]
	 */
	public function schedulereports($reportid, $table = true, $start = 0, $length = 5, $search = '') {
		global $DB;

		if (!$reportid) {
			throw new moodle_exception('Missing Report ID.');
		}

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			throw new moodle_exception('Report Not Available.');
		}

		$fields = array('sr.role', 'sr.exportformat');
		$fields = implode(" LIKE '%" . $search . "%' OR ", $fields);
		$fields .= " LIKE '%" . $search . "%' ";

		$fields1 = array('r.shortname', 'bcs.exportformat');
		$fields1 = implode(" LIKE '%" . $search . "%' OR ", $fields1);
		$fields1 .= " LIKE '%" . $search . "%' ";

		if (!$table) {
			$schreportssql = "SELECT * FROM (SELECT bcs.id, bcs.exportformat, bcs.frequency, bcs.schedule,
									bcr.name, CASE WHEN bcs.roleid> 0 THEN r.shortname ELSE 'admin' END as role
								FROM {block_ls_schedule} bcs
					          	JOIN {block_learnerscript} bcr ON bcr.id = bcs.reportid
						   LEFT JOIN {role} r ON r.id = bcs.roleid
					    	   WHERE bcs.reportid = $reportid AND bcs.frequency > 0
					    	   GROUP BY bcs.id, r.shortname, bcr.name, bcs.exportformat, bcs.frequency, bcs.schedule, bcs.roleid) sr WHERE 1=1";
		}
		$totalschreportssql = "SELECT * FROM (SELECT COUNT(bcs.id) as totalrecords
								 FROM {block_ls_schedule} bcs
					    		 JOIN {block_learnerscript} bcr ON bcr.id = bcs.reportid
							LEFT JOIN {role} r ON r.id = bcs.roleid
								WHERE bcs.reportid = $reportid AND bcs.frequency > 0 
								AND ( $fields1 )) sr WHERE 1=1";		

		if ($search) {
			$schreportssql .= " AND ( $fields ) ";
			// $totalschreportssql .= " AND ( $fields ) ";
		}

		$schreportsdata = $DB->get_record_sql($totalschreportssql);
		$totalschreports = $schreportsdata->totalrecords;

		if (!$table) {
			$schreportssql .= ' ORDER BY sr.id DESC';
			$schreports = $DB->get_records_sql($schreportssql, array(), $start, $length);
		} else {
			$schreports = new \stdClass;
		}
		return compact('schreports', 'totalschreports');
	}

	public function reportroles($selectedroleid = '', $reportid = 0) {
		global $DB, $CFG, $PAGE;
		$selectedroleid = json_decode($selectedroleid, true);

		$reportinstance = (new ls)->cr_get_reportinstance($reportid);

        $components = (new ls)->cr_unserialize($reportinstance->components);
        $permissions = (isset($components['permissions'])) ? $components['permissions'] : array();
        $roles[-1] = 'admin';
        if (!empty($permissions['elements'])) {
            foreach ($permissions['elements'] as $p) {
            	if($p['pluginname'] == 'roleincourse') {
            		$contextname = context_helper::get_level_name($p['formdata']->contextlevel);
            		$rolename = $DB->get_field('role', 'shortname', array('id' => $p['formdata']->roleid));
            		$roles[$p['formdata']->roleid .'_'. $p['formdata']->contextlevel ] =  $rolename . ' at '. $contextname .' level';
            	}
            }
        } else {
			// $roleslistsql = "SELECT id, shortname FROM {role} WHERE shortname NOT IN ('guest', 'user', 'frontpage')";
			// $roles = $DB->get_records_sql_menu($roleslistsql);
        	$roles = array(-1 => 'admin');
        }

		$selected = '';
		ksort($roles);
		$roles_list[] = array('key' => null, 'value' => '--SELECT ROLE--');
		// $roles_list[] = array('key' => -1, 'value' => 'All', 'selected' => $selected);
		foreach ($roles as $key => $value) {
//			if(is_array($selectedroleid) && in_array($key,$selectedroleid)){
			if ($key == $selectedroleid) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$rolecontext = explode("-", $value);
	        switch ($rolecontext[0]) {
	            case 'admin':         $original = $value; break;
	            case 'manager':         $original = get_string('manager', 'role'); break;
	            case 'coursecreator':   $original = get_string('coursecreators'); break;
	            case 'editingteacher':  $original = get_string('defaultcourseteacher'); break;
	            case 'teacher':         $original = get_string('noneditingteacher'); break;
	            case 'student':         $original = get_string('defaultcoursestudent'); break;
	            case 'guest':           $original = get_string('guest'); break;
	            case 'user':            $original = get_string('authenticateduser'); break;
	            case 'frontpage':       $original = get_string('frontpageuser', 'role'); break;
	            // We should not get here, the role UI should require the name for custom roles!
	            default:                $original = $rolecontext[0]; break;
	        }
	        if(isset($rolecontext[1])){
	        	$rolecontextname = $original .' - '. $rolecontext[1];
	        }else{
	        	$rolecontextname = $original;
	        }
			$roles_list[] = array('key' => $key, 'value' => $rolecontextname, 'selected' => $selected);
		}
		if (is_array($selectedroleid) && in_array(-1, $selectedroleid)) {
			$selected = 'selected';
		} else {
			$selected = '';
		}

		return $roles_list;
	}

	public function userslist($reportid, $scheduledreportid, $ajaxusers = array()) {
		global $DB;
		$userslist = $DB->get_field('block_ls_schedule', 'sendinguserid', array( 'id' => $scheduledreportid, 'reportid' => $reportid));
		if (!$reportid) {
			throw new moodle_exception('Missing Report ID.');
		}

		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			throw new moodle_exception('Report Not Available.');
		}

		if ($scheduledreportid > 0) {
			$schuserscountsql = "SELECT COUNT(u.id) as count
                           			   FROM {user} as u
                             			     JOIN {block_ls_schedule} as bcs ON u.id IN ($userslist)
                             			WHERE u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0 AND bcs.reportid = $reportid AND bcs.id = $scheduledreportid";
			$schuserscount = $DB->count_records_sql($schuserscountsql);

			$schuserssql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname
                           			   FROM {user} as u
                             			     JOIN {block_ls_schedule} as bcs ON u.id IN ($userslist)
                             			WHERE u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0 AND bcs.reportid = $reportid AND bcs.id = $scheduledreportid";
			$schusers = $DB->get_records_sql_menu($schuserssql, array(), 0, 10);
			if ($schuserscount > 10) {
				$schusers = $schusers + array(-1 => "View More");
			}
			$schusersidssql = "SELECT u.id
                           		 FROM {user} as u
                             	 JOIN {block_ls_schedule} as bcs ON u.id IN ($userslist)
                             	WHERE u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0
                             	AND bcs.reportid = $reportid AND bcs.id = $scheduledreportid ";
			$schusersids = $DB->get_fieldset_sql($schusersidssql);
			$schusersids = implode(',', $schusersids);

		} else {
			$schusers = array();
			$schusersids = '';
			if(!empty($ajaxusers)) {
				$schuserscount = $DB->count_records_sql("SELECT COUNT(u.id)
                           			   FROM {user} as u WHERE u.id IN ($ajaxusers) ");
				$schusersids = $ajaxusers;
				$schusers = $DB->get_records_sql_menu("SELECT u.id, CONCAT(u.firstname, ' ',
					u.lastname) as fullname
                           			   FROM {user} as u WHERE u.id IN ($ajaxusers) ", array(), 0, 10);
				if ($schuserscount > 10) {
					$schusers = $schusers + array(-1 => "View More");
				}
			}
		}

		return array($schusers, $schusersids);
	}

	public function selectesuserslist($schuserslist) {
		global $DB;
		if ($schuserslist) {
			$selecteduserssql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) as fullname
					FROM {user} as u
					WHERE u.id IN (".$schuserslist.") AND u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0";
			$selectedusers = $DB->get_records_sql_menu($selecteduserssql);
		} else {
			$selectedusers = false;
		}
		foreach ($selectedusers as $key => $value) {
			$scheduledusers[] = array('key'=>$key,'value'=>$value);
		}

		return $scheduledusers;
	}
   public function getschedulelist($frequency) {
       if ($frequency == 1) {
           $i = 0;
           for ($i = 0; $i < 24; $i++) {
               if ($i < 10) {
                   $times[] = '0' . $i;
               } else {
                   $times[] = $i;
               }
           }
           $schedule = $times;
       } elseif ($frequency == 2) {
           $weeks = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
           $schedule = $weeks;
       } elseif ($frequency == 3) {
           $i = 0;
           for ($i = 1; $i <= 31; $i++) {
               $months[] = $i;
           }
           $schedule = $months;
       }
       return $schedule;
   }
}
