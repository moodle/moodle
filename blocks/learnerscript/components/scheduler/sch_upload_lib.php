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
 * Bulk user registration functions
 *
 * @package    tool
 * @subpackage uploaduser
 * @author: eAbyas info solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../../../config.php';
use block_learnerscript\local\schedule;
use block_learnerscript\local\ls;
defined('MOODLE_INTERNAL') || die();

define('UU_USER_ADDNEW', 0);
define('UU_USER_ADDINC', 1);
define('UU_USER_ADD_UPDATE', 2);
define('UU_USER_UPDATE', 3);

define('UU_UPDATE_NOCHANGES', 0);
define('UU_UPDATE_FILEOVERRIDE', 1);
define('UU_UPDATE_ALLOVERRIDE', 2);
define('UU_UPDATE_MISSING', 3);

define('UU_BULK_NONE', 0);
define('UU_BULK_NEW', 1);
define('UU_BULK_UPDATED', 2);
define('UU_BULK_ALL', 3);

define('UU_PWRESET_NONE', 0);
define('UU_PWRESET_WEAK', 1);
define('UU_PWRESET_ALL', 2);

/**
 * Tracking of processed users.
 *
 * This class prints user information into a html table.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2007 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uu_progress_tracker {
	private $_row;
	public $columns = array('email', 'exportformat', 'exporttofilesystem', 'schedule', 'frequency', 'roleid');

	/**
	 * Print table header.
	 * @return void
	 */
	public function start() {
		$ci = 0;
		echo '<table id="uuresults" class="generaltable boxaligncenter flexible-wrap" summary="' . get_string('uploadusersresult', 'tool_uploaduser') . '">';
		echo '<tr class="heading r0">';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('email') . '</th>';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('exportformat') . '</th>';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('exporttofilesystem') . '</th>';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('schedule') . '</th>';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('frequency') . '</th>';
		echo '<th class="header c' . $ci++ . '" scope="col">' . get_string('roleid') . '</th>';
		echo '</tr>';
		$this->_row = null;
	}

	/**
	 * Flush previous line and start a new one.
	 * @return void
	 */
	public function flush() {
		if (empty($this->_row) or empty($this->_row['line']['normal'])) {
			// Nothing to print - each line has to have at least number
			$this->_row = array();
			foreach ($this->columns as $col) {
				$this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
			}
			return;
		}
		$ci = 0;
		$ri = 1;
		echo '<tr class="r' . $ri . '">';
		foreach ($this->_row as $key => $field) {
			foreach ($field as $type => $content) {
				if ($field[$type] !== '') {
					$field[$type] = '<span class="uu' . $type . '">' . $field[$type] . '</span>';
				} else {
					unset($field[$type]);
				}
			}
			echo '<td class="cell c' . $ci++ . '">';
			if (!empty($field)) {
				echo implode('<br />', $field);
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
		}
		echo '</tr>';
		foreach ($this->columns as $col) {
			$this->_row[$col] = array('normal' => '', 'info' => '', 'warning' => '', 'error' => '');
		}
	}

	/**
	 * Add tracking info
	 * @param string $col name of column
	 * @param string $msg message
	 * @param string $level 'normal', 'warning' or 'error'
	 * @param bool $merge true means add as new line, false means override all previous text of the same type
	 * @return void
	 */
	public function track($col, $msg, $level = 'normal', $merge = true) {
		if (empty($this->_row)) {
			$this->flush(); //init arrays
		}
		if (!in_array($col, $this->columns)) {
			debugging('Incorrect column:' . $col);
			return;
		}
		if ($merge) {
			if ($this->_row[$col][$level] != '') {
				$this->_row[$col][$level] .= '<br />';
			}
			$this->_row[$col][$level] .= $msg;
		} else {
			$this->_row[$col][$level] = $msg;
		}
	}

	/**
	 * Print the table end
	 * @return void
	 */
	public function close() {
		$this->flush();
		echo '</table>';
	}
}

/**
 * Validation callback function - verified the column line of csv file.
 * Converts standard column names to lowercase.
 * @param csv_import_reader $cir
 * @param array $stdfields standard user fields
 * @param array $profilefields custom profile fields
 * @param moodle_url $returnurl return url in case of any error
 * @return array list of fields
 */
function uu_validate_user_upload_columns(csv_import_reader $cir, $stdfields, $profilefields, $returnurl) {
	$columns = $cir->get_columns();

	if (empty($columns)) {
		$cir->close();
		$cir->cleanup();
		print_error('cannotreadtmpfile', 'error', $returnurl);
	}
	if (count($columns) < 1) {
		$cir->close();
		$cir->cleanup();
		print_error('csvfewcolumns', 'error', $returnurl);
	}

	// test columns
	$processed = array();
	foreach ($columns as $key => $unused) {
		$field = $columns[$key];
		//$lcfield = core_text::strtolower($field);
		$lcfield = false;
		if (in_array($field, $stdfields)) {
			// standard fields are only lowercase
			$newfield = $field;

		} /* else if (in_array($field, $profilefields)) {
	            // exact profile field name match - these are case sensitive
	            $newfield = $field;

	        }  else if (in_array($lcfield, $profilefields)) {
	            // hack: somebody wrote uppercase in csv file, but the system knows only lowercase profile field
	            $newfield = $lcfield;

	        }  else if (preg_match('/^(sysrole|cohort|course|group|type|role|enrolperiod|enrolstatus)\d+$/', $lcfield)) {
	            // special fields for enrolments
	            $newfield = $lcfield;

*/else {
			$cir->close();
			$cir->cleanup();
			print_error('invalidfieldname', 'error', $returnurl, $field);
		}
		if (in_array($newfield, $processed)) {
			$cir->close();
			$cir->cleanup();
			print_error('duplicatefieldname', 'error', $returnurl, $newfield);
		}
		$processed[$key] = $newfield;
	}

	return $processed;
}

/**
 * [formatdata_validation description]
 * @param  [type] $reportid       [description]
 * @param  [type] $data           [description]
 * @param  [type] $linenum        [description]
 * @param  [type] &$formatteddata [description]
 * @return [type]                 [description]
 */
function formatdata_validation($reportid, $data, $linenum, &$formatteddata, $reportclass) {
	global $DB, $USER, $REPORT_EXPPORT_FORMATS;
	$scheduling = new schedule();
	$warnings = array(); // Warnings List
	$errors = array(); // Errors List
	$mfields = array(); // mandatory Fields
	$formatteddata = new stdClass(); //Formatted Data for inserting into DB
	$exportformats = (new ls)->cr_get_export_options($reportid); // Export Formats
	$exporttofilesystems = array('Send report to mail' => 1,
								 'Save to file system' => 2,
								 'Save to file system and send email' => 3); //Export Filesystems
	$frequencies = array('daily' => 1, 'weekly' => 2, 'monthly' => 3); //Frequency

	$reportclass->courseid = $reportclass->config->courseid;
	if ($reportclass->config->courseid == SITEID) {
		$context = context_system::instance();
	} else {
		$context = context_course::instance($reportclass->config->courseid);
	}

	if (empty($data->email)) {
		$mfields[] = 'email';
		$errors[] = '<div class="alert alert-error" role="alert">Please enter email in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else if (!validate_email($data->email)) {
		$errors[] = '<div class="alert alert-error" role="alert">Invalid email in line no "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$userid = $DB->get_field_sql("SELECT id FROM {user} WHERE email = '$data->email'");
	}

	if (empty($userid)) {
		$errors[] = '<div class="alert alert-error" role="alert">User Not Available which is entered email in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$formatteddata->sendinguserid = $userid;
	}

	if (empty($data->exportformat)) {
		$mfields[] = get_string('exportformat','block_learnerscript');
		$errors[] = '<div class="alert alert-error" role="alert">Please enter Export Format in line no. "' . $linenum . '" of uploaded sheet.</div>';
	}

	if (!in_array($data->exportformat, array_keys($REPORT_EXPPORT_FORMATS))) {
		$errors[] = '<div class="alert alert-error" role="alert">Please enter correct Export Format in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$formatteddata->exportformat = $data->exportformat;
	}
	if (empty($data->exporttofilesystem)) {
		$mfields[] = get_string('exporttofilesystem','block_learnerscript');
		$errors[] = '<div class="alert alert-error" role="alert">Please enter Export filesystem in line no. "' . $linenum . '" of uploaded sheet.</div>';
	}
	if (!isset($exporttofilesystems[$data->exporttofilesystem])) {
		$errors[] = '<div class="alert alert-error" role="alert">Please enter correct Export to filesystem in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$formatteddata->exporttofilesystem = $exporttofilesystems[$data->exporttofilesystem];
	}
	if (empty($data->frequency)) {
		$mfields[] = get_string('frequency','block_learnerscript');
		$errors[] = '<div class="alert alert-error" role="alert">Please enter Frequency in line no. "' . $linenum . '" of uploaded sheet.</div>';
	}
	if (!$frequency = isset($frequencies[$data->frequency])) {
		$errors[] = '<div class="alert alert-error" role="alert">Please enter correct Frequency in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$formatteddata->frequency = $frequencies[$data->frequency];
	}
	if (empty($data->schedule)) {
		$mfields[] = get_string('schedule','block_learnerscript');
		$errors[] = '<div class="alert alert-error" role="alert">Please enter Schedule in line no. "' . $linenum . '" of uploaded sheet.</div>';
	}
	$schedules = $scheduling->getschedulelist($formatteddata->frequency);

	$schedule = $data->schedule;
	if (!in_array($schedule, $schedules)) {
		$errors[] = '<div class="alert alert-error" role="alert">Please enter correct Schedule in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		if($formatteddata->frequency == 2) {
			$formatteddata->schedule = array_search($data->schedule, $schedules);
		} else {
			$formatteddata->schedule = $data->schedule;
		}
	}
	if (empty($data->roleid)) {
		$mfields[] = get_string('role','block_learnerscript');
		$errors[] = '<div class="alert alert-error" role="alert">Please enter role in line no. "' . $linenum . '" of uploaded sheet.</div>';
	}
	if (!$DB->record_exists('role', array('id' => $data->roleid))) {
		$errors[] = '<div class="alert alert-error" role="alert">Please enter Correct role in line no. "' . $linenum . '" of uploaded sheet.</div>';
	} else {
		$formatteddata->roleid = $data->roleid;
	}
	if ($data->roleid > 0) {
		if (!$DB->record_exists('role_assignments', array('userid' => $userid, 'roleid' => $data->roleid))) {
			$mfields[] = get_string('nouserrole','block_learnerscript');
			$errors[] = '<div class="alert alert-error" role="alert">Combination of User and Role not available which you entered in line no. "' . $linenum . '" of uploaded sheet.</div>';
		}
	}
	if ($userid > 0 && $data->roleid > 0) {
		$reportclass->userid = $userid;
		$reportclass->role = $DB->get_field('role', 'shortname', array('id' => $data->roleid));
		if (!is_siteadmin($userid) && !$reportclass->check_permissions($userid, $context)) {
			$mfields[] = get_string('noreportpermission','block_learnerscript');
			$errors[] = '<div class="alert alert-error" role="alert">This Report Not available for which you entered in line no. "' . $linenum . '" of uploaded sheet.</div>';
		}
	}
	$formatteddata->contextlevel = isset($data->contextlevel) ? $data->contextlevel : 10;
	return compact('mfields', 'errors');
}
