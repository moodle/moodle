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
 * @package   local_iomad_settings
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const RESET_SEQUENCE_NEVER = 'never';
const RESET_SEQUENCE_DAILY = 'daily';
const RESET_SEQUENCE_ANNUALLY = 'annually';

function padleft($value, $n) {
    return str_pad($value, $n, "0", STR_PAD_LEFT);
}

function iomad_settings_serialnumber($serialnumberrecord) {
    $matches = array();
    $prefix = $serialnumberrecord->prefix;
    if (preg_match_all('/\{SEQNO:(\d+)\}/i', $prefix, $matches)) {
        foreach ($matches[1] as $match) {
            $seqno = padleft($serialnumberrecord->sequenceno, $match);
            $prefix = preg_replace('/\{SEQNO[^\}]*\}/i', $seqno, $prefix);
        }
    }

    return $prefix;
}

function iomad_settings_establishment_code() {
    global $CFG;
    return padleft(isset($CFG->establishment_code) ? $CFG->establishment_code : 0, 4);
}

function iomad_settings_attempt_serialnumber_insert($serialobj) {
    global $DB;

    $bool = 0;
    try {
        $bool = $DB->insert_record('certificate_serialnumber', $serialobj);
    } catch (Exception $e) {
        // Need to do something.
    }
    return $bool;
}

// Create serial number or retrieve if one already exists for the issues certificate.
function iomad_settings_create_serial_number($certificate, $certrecord, $course, $certdate) {
    global $DB;

    if (!$serialobj = $DB->get_record('certificate_serialnumber', array('issued_certificate' => $certrecord->id), '*')) {
        $prefix = iomad_settings_replacement($certificate->serialnumberformat, $course, '', $certdate);

        $serialobj = new stdClass;
        $serialobj->certificateid = $certrecord->certificateid;
        $serialobj->issued_certificate = $certrecord->id;
        $serialobj->prefix = $prefix;
        $serialobj->timecreated = time();
        if ($certificate->reset_sequence == RESET_SEQUENCE_DAILY) {
            $serialobj->sequence = date('Ymd');
        } else {
            $serialobj->sequence = date('Y');
        }
        $serialobj->sequenceno = iomad_settings_next_serial_number($serialobj->certificateid, $serialobj->sequence);

        // There is a unique index on prefix and sequenceno that will prevent inserts if the prefix/sequenceno combo already exists.
        while (!iomad_settings_attempt_serialnumber_insert($serialobj)) {
            // Check that serial number already exists in database and the insert didn't fail for some other reason.
            if ($DB->get_record('certificate_serialnumber', array('prefix' => $prefix,
                                                                  'sequenceno' => $serialobj->sequenceno), 'id')) {
                // Somebody moved the goal posts, try again.
                $serialobj->sequenceno = iomad_settings_next_serial_number($serialobj->certificateid, $serialobj->sequence);
                $serialobj->timecreated = time();
            } else {
                // This shouldn't happen.
                print_error("Certificate Serial Number couldn't be created");
            }
        }
    }

    return iomad_settings_serialnumber($serialobj);
}

function iomad_settings_next_serial_number($certificateid, $sequence) {
    global $DB;

    // Find the last serialnumber created in the same sequence.
    $lastserial = $DB->get_records_select('certificate_serialnumber', "certificateid = $certificateid
                                                                       AND sequence >= $sequence",
                                                                       array(), "*", "timecreated desc", 0, 1);

    // Get the record out of the array (or set to null if no record returned).
    if (count($lastserial) > 0) {
        $keys = (array_keys($lastserial));
        $lastserial = $lastserial[$keys[0]];
    } else {
        $lastserial = null;
    }

    return isset($lastserial) ? $lastserial->sequenceno + 1 : 1;
}

function format_date_with_iomad_settings_format($format, $date) {
    $tformat = preg_replace('/DD/i', '%d', $format);
    $tformat = preg_replace('/MM/i', '%m', $tformat);
    $tformat = preg_replace('/YYYY/i', '%Y', $tformat);
    $tformat = preg_replace('/YY/i', '%y', $tformat);

    return strftime($tformat, $date ? $date : time());
}

function iomad_settings_replacement($customtext, $course, $serialnumber, $certdate) {
    // Where {SN} = serial number.
    $customtext = str_replace("{SN}", $serialnumber, $customtext);

    // Where {EC} = establishment code.
    $customtext = str_replace("{EC}", iomad_settings_establishment_code(), $customtext);

    // Where {CC} = course code.
    $coursecode = padleft(isset($course->idnumber) && $course->idnumber != "" ? $course->idnumber : $course->id, 4);
    $customtext = str_replace("{CC}", $coursecode, $customtext);

    // Where {CD:???} = completion date.
    $matches = array();
    // Match {CD:YMD} (YMD = date format, can be anything except end brace '}', but
    // format_date_with_iomad_settings_format determines what really is used.
    if (preg_match_all('/\{CD:([^\}]+)\}/i', $customtext, $matches)) {
        foreach ($matches[1] as $match) {
            $formatteddate = format_date_with_iomad_settings_format($match, $certdate);
            $customtext = preg_replace('/\{CD:' . $match . '\}/i', $formatteddate, $customtext);
        }
    }

    return $customtext;
}
