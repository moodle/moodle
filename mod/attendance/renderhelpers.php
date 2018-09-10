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
 * Attendance module renderering helpers
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/renderables.php');

/**
 * class Template method for generating user's session's cells
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sessions_cells_generator {
    /** @var array $cells - list of table cells. */
    protected $cells = array();

    /** @var stdClass $reportdata - data for report. */
    protected $reportdata;

    /** @var stdClass $user - user record. */
    protected $user;

    /**
     * Set up params.
     * @param attendance_report_data $reportdata - reportdata.
     * @param stdClass $user - user record.
     */
    public function  __construct(attendance_report_data $reportdata, $user) {
        $this->reportdata = $reportdata;
        $this->user = $user;
    }

    /**
     * Get cells for the table.
     *
     * @param boolean $remarks - include remarks cell.
     */
    public function get_cells($remarks = false) {
        foreach ($this->reportdata->sessions as $sess) {
            if (array_key_exists($sess->id, $this->reportdata->sessionslog[$this->user->id]) &&
            !empty($this->reportdata->sessionslog[$this->user->id][$sess->id]->statusid)) {
                $statusid = $this->reportdata->sessionslog[$this->user->id][$sess->id]->statusid;
                if (array_key_exists($statusid, $this->reportdata->statuses)) {
                    $points = format_float($this->reportdata->statuses[$statusid]->grade, 1, true, true);
                    $maxpoints = format_float($sess->maxpoints, 1, true, true);
                    $this->construct_existing_status_cell($this->reportdata->statuses[$statusid]->acronym .
                                " ({$points}/{$maxpoints})");
                } else {
                    $this->construct_hidden_status_cell($this->reportdata->allstatuses[$statusid]->acronym);
                }
                if ($remarks) {
                    $this->construct_remarks_cell($this->reportdata->sessionslog[$this->user->id][$sess->id]->remarks);
                }
            } else {
                if ($this->user->enrolmentstart > $sess->sessdate) {
                    $starttext = get_string('enrolmentstart', 'attendance', userdate($this->user->enrolmentstart, '%d.%m.%Y'));
                    $this->construct_enrolments_info_cell($starttext);
                } else if ($this->user->enrolmentend and $this->user->enrolmentend < $sess->sessdate) {
                    $endtext = get_string('enrolmentend', 'attendance', userdate($this->user->enrolmentend, '%d.%m.%Y'));
                    $this->construct_enrolments_info_cell($endtext);
                } else if (!$this->user->enrolmentend and $this->user->enrolmentstatus == ENROL_USER_SUSPENDED) {
                    // No enrolmentend and ENROL_USER_SUSPENDED.
                    $suspendext = get_string('enrolmentsuspended', 'attendance', userdate($this->user->enrolmentend, '%d.%m.%Y'));
                    $this->construct_enrolments_info_cell($suspendext);
                } else {
                    if ($sess->groupid == 0 or array_key_exists($sess->groupid, $this->reportdata->usersgroups[$this->user->id])) {
                        $this->construct_not_taken_cell('?');
                    } else {
                        $this->construct_not_existing_for_user_session_cell('');
                    }
                }
                if ($remarks) {
                    $this->construct_remarks_cell('');
                }
            }
        }
        $this->finalize_cells();

        return $this->cells;
    }

    /**
     * Construct status cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_existing_status_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Construct hidden status cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_hidden_status_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Construct enrolments info cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_enrolments_info_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Construct not taken cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_not_taken_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Construct remarks cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_remarks_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Construct not existing user session cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_not_existing_for_user_session_cell($text) {
        $this->cells[] = $text;
    }

    /**
     * Dummy stub method, called at the end. - override if you need/
     */
    protected function finalize_cells() {
    }
}

/**
 * class Template method for generating user's session's cells in html
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sessions_cells_html_generator extends user_sessions_cells_generator {
    /** @var html_table_cell $cell */
    private $cell;

    /**
     * Construct status cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_existing_status_cell($text) {
        $this->close_open_cell_if_needed();
        $this->cells[] = html_writer::span($text, 'attendancestatus-'.$text);
    }

    /**
     * Construct hidden status cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_hidden_status_cell($text) {
        $this->cells[] = html_writer::tag('s', $text);
    }

    /**
     * Construct enrolments info cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_enrolments_info_cell($text) {
        if (is_null($this->cell)) {
            $this->cell = new html_table_cell($text);
            $this->cell->colspan = 1;
        } else {
            if ($this->cell->text != $text) {
                $this->cells[] = $this->cell;
                $this->cell = new html_table_cell($text);
                $this->cell->colspan = 1;
            } else {
                $this->cell->colspan++;
            }
        }
    }

    /**
     * Close cell if needed.
     */
    private function close_open_cell_if_needed() {
        if ($this->cell) {
            $this->cells[] = $this->cell;
            $this->cell = null;
        }
    }

    /**
     * Construct not taken cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_not_taken_cell($text) {
        $this->close_open_cell_if_needed();
        $this->cells[] = $text;
    }

    /**
     * Construct remarks cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_remarks_cell($text) {
        global $OUTPUT;

        if (!trim($text)) {
            return;
        }

        // Format the remark.
        $icon = $OUTPUT->pix_icon('i/info', '');
        $remark = html_writer::span($text, 'remarkcontent');
        $remark = html_writer::span($icon.$remark, 'remarkholder');

        // Add it into the previous cell.
        $markcell = array_pop($this->cells);
        $markcell .= ' '.$remark;
        $this->cells[] = $markcell;
    }

    /**
     * Construct not existing for user session cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_not_existing_for_user_session_cell($text) {
        $this->close_open_cell_if_needed();
        $this->cells[] = $text;
    }

    /**
     * Finalize cells.
     *
     */
    protected function finalize_cells() {
        if ($this->cell) {
            $this->cells[] = $this->cell;
        }
    }
}

/**
 * class Template method for generating user's session's cells in text
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_sessions_cells_text_generator extends user_sessions_cells_generator {
    /** @var string $enrolmentsinfocelltext. */
    private $enrolmentsinfocelltext;

    /**
     * Construct hidden status cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_hidden_status_cell($text) {
        $this->cells[] = '-'.$text;
    }

    /**
     * Construct enrolments info cell.
     *
     * @param string $text - text for the cell.
     */
    protected function construct_enrolments_info_cell($text) {
        if ($this->enrolmentsinfocelltext != $text) {
            $this->enrolmentsinfocelltext = $text;
            $this->cells[] = $text;
        } else {
            $this->cells[] = '←';
        }
    }
}

/**
 * Used to print simple time - 1am instead of 1:00am.
 *
 * @param int $time - unix timestamp.
 */
function attendance_strftimehm($time) {
    $mins = userdate($time, '%M');
    if ($mins == '00') {
        $format = get_string('strftimeh', 'attendance');
    } else {
        $format = get_string('strftimehm', 'attendance');
    }

    $userdate = userdate($time, $format);

    // Some Lang packs use %p to suffix with AM/PM but not all strftime support this.
    // Check if %p is in use and make sure it's being respected.
    if (stripos($format, '%p')) {
        // Check if $userdate did something with %p by checking userdate against the same format without %p.
        $formatwithoutp = str_ireplace('%p', '', $format);
        if (userdate($time, $formatwithoutp) == $userdate) {
            // The date is the same with and without %p - we have a problem.
            if (userdate($time, '%H') > 11) {
                $userdate .= 'pm';
            } else {
                $userdate .= 'am';
            }
        }
        // Some locales and O/S don't respect correct intended case of %p vs %P
        // This can cause problems with behat which expects AM vs am.
        if (strpos($format, '%p')) { // Should be upper case according to PHP spec.
            $userdate = str_replace('am', 'AM', $userdate);
            $userdate = str_replace('pm', 'PM', $userdate);
        }
    }

    return $userdate;
}

/**
 * Used to print simple time - 1am instead of 1:00am.
 *
 * @param int $datetime - unix timestamp.
 * @param int $duration - number of seconds.
 */
function construct_session_time($datetime, $duration) {
    $starttime = attendance_strftimehm($datetime);
    $endtime = attendance_strftimehm($datetime + $duration);

    return $starttime . ($duration > 0 ? ' - ' . $endtime : '');
}

/**
 * Used to print session time.
 *
 * @param int $datetime - unix timestamp.
 * @param int $duration - number of seconds duration.
 * @return string.
 */
function construct_session_full_date_time($datetime, $duration) {
    $sessinfo = userdate($datetime, get_string('strftimedmyw', 'attendance'));
    $sessinfo .= ' '.construct_session_time($datetime, $duration);

    return $sessinfo;
}

/**
 * Used to construct user summary.
 *
 * @param stdclass $usersummary - data for summary.
 * @param int $view - ATT_VIEW_ALL|ATT_VIEW_
 * @return string.
 */
function construct_user_data_stat($usersummary, $view) {
    $stattable = new html_table();
    $stattable->attributes['class'] = 'attlist';
    $row = new html_table_row();
    $row->attributes['class'] = 'normal';
    $row->cells[] = get_string('sessionscompleted', 'attendance') . ':';
    $row->cells[] = $usersummary->numtakensessions;
    $stattable->data[] = $row;

    $row = new html_table_row();
    $row->attributes['class'] = 'normal';
    $row->cells[] = get_string('pointssessionscompleted', 'attendance') . ':';
    $row->cells[] = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                        format_float($usersummary->takensessionsmaxpoints, 1, true, true);
    $stattable->data[] = $row;

    $row = new html_table_row();
    $row->attributes['class'] = 'normal';
    $row->cells[] = get_string('percentagesessionscompleted', 'attendance') . ':';
    $row->cells[] = format_float($usersummary->takensessionspercentage * 100) . '%';
    $stattable->data[] = $row;

    if ($view == ATT_VIEW_ALL) {
        $row = new html_table_row();
        $row->attributes['class'] = 'highlight';
        $row->cells[] = get_string('sessionstotal', 'attendance') . ':';
        $row->cells[] = $usersummary->numallsessions;
        $stattable->data[] = $row;

        $row = new html_table_row();
        $row->attributes['class'] = 'highlight';
        $row->cells[] = get_string('pointsallsessions', 'attendance') . ':';
        $row->cells[] = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                            format_float($usersummary->allsessionsmaxpoints, 1, true, true);
        $stattable->data[] = $row;

        $row = new html_table_row();
        $row->attributes['class'] = 'highlight';
        $row->cells[] = get_string('percentageallsessions', 'attendance') . ':';
        $row->cells[] = format_float($usersummary->allsessionspercentage * 100) . '%';
        $stattable->data[] = $row;

        $row = new html_table_row();
        $row->attributes['class'] = 'normal';
        $row->cells[] = get_string('maxpossiblepoints', 'attendance') . ':';
        $row->cells[] = format_float($usersummary->maxpossiblepoints, 1, true, true) . ' / ' .
                            format_float($usersummary->allsessionsmaxpoints, 1, true, true);
        $stattable->data[] = $row;

        $row = new html_table_row();
        $row->attributes['class'] = 'normal';
        $row->cells[] = get_string('maxpossiblepercentage', 'attendance') . ':';
        $row->cells[] = format_float($usersummary->maxpossiblepercentage * 100) . '%';
        $stattable->data[] = $row;
    }

    return html_writer::table($stattable);
}

/**
 * Returns html user summary
 *
 * @param stdclass $attendance - attendance record.
 * @param stdclass $user - user record
 * @return string.
 *
 */
function construct_full_user_stat_html_table($attendance, $user) {
    $summary = new mod_attendance_summary($attendance->id, $user->id);
    return construct_user_data_stat($summary->get_all_sessions_summary_for($user->id), ATT_VIEW_ALL);
}
