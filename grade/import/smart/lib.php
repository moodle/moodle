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

defined('MOODLE_INTERNAL') || die();

require_once('classes.php');

// Reads the first line in a file and tries to figure out what kind of grade
// file it is. A new object of the appropriate grade file type is returned.
function smart_autodiscover_filetype($file) {
    $lines = smart_split_file($file);
    $line = $lines[0];

    if (SmartFileKeypadidCSV::validate_line($line)) {
        return new SmartFileKeypadidCSV($file);
    }

    if (SmartFileKeypadidTabbed::validate_line($line)) {
        return new SmartFileKeypadidTabbed($file);
    }

    if (SmartFileFixed::validate_line($line)) {
        return new SmartFileFixed($file);
    }

    if (SmartFileInsane::validate_line($line)) {
        return new SmartFileInsane($file);
    }

    if (SmartFileCommaLongLsuid::validate_line($line)) {
        return new SmartFileCommaLongLsuid($file);
    }

    if (SmartFileCommaLongPawsid::validate_line($line)) {
        return new SmartFileCommaLongPawsid($file);
    }

    if (SmartFileTabLongLsuid::validate_line($line)) {
        return new SmartFileTabLongLsuid($file);
    }

    if (SmartFileTabLongPawsid::validate_line($line)) {
        return new SmartFileTabLongPawsid($file);
    }

    if (SmartFileTabShortLsuid::validate_line($line)) {
        return new SmartFileTabShortLsuid($file);
    }

    if (SmartFileCSVLsuid::validate_line($line)) {
        return new SmartFileCSVLsuid($file);
    }

    if (SmartFileMEC::validate_line($line)) {
        return new SmartFileMEC($file);
    }

    if (SmartFileTabShortPawsid::validate_line($line)) {
        return new SmartFileTabShortPawsid($file);
    }

    if (SmartFileAnonymous::validate_line($line)) {
        return new SmartFileAnonymous($file);
    }

    if (count($lines) >= 1 && SmartFileEmail::validate_line($lines[0])) {
        return new SmartFileEmail($file);
    }

    if (count($lines) >= 2 && SmartFileTurning::validate_line($lines[1])) {
        return new SmartFileTurning($file);
    }

    if (SmartFileCSVPawsid::validate_line($line)) {
        return new SmartFileCSVPawsid($file);
    }

    if (count($lines) >= 3 && SmartFileMaple::validate_line($lines[2])) {
        return new SmartFileMaple($file);
    }

    return false;
}

// Splits a file into an array of lines and normalize newlines.
function smart_split_file($file) {
    // Replace \r\n with \n, replace any leftover \r with \n, explode on \n.
    $lines = explode("\n", preg_replace("/\r/", "\n", preg_replace("/\r\n/", "\n", $file)));

    if (end($lines) == '') {
        return array_slice($lines, 0, count($lines) - 1, true);
    } else {
        return $lines;
    }
}

// Checks whether or not a string is a valid LSUID. It must be a nine digit
// digit number that starts with 89 to pass.
function smart_is_lsuid2($s) {
    return preg_match('/^89\d{7}$/', $s);
}

// Checks whether or not a string is a valid LSU Email address.
// It must contain a valid pawsid and end in @(valid domain name - community and agcenter prohibit limiting this)
// A valid pawsid must be 1-16 and contain only alphanumeric characters including hyphens.
function smart_is_email($s) {
    return preg_match('/^[a-zA-Z0-9\-]{1,16}@[a-zA-Z0-9\-]{1,32}\.[a-zA-Z0-9\-]{2,3}/', $s);
}

// Checks whether or not a string is a valid MEC LSUID. It must be a twelve digit
// digit number that starts with three digits and has 89.* afterward.
function smart_is_mec_lsuid($s) {
    return preg_match('/^...89\d{7}$/', $s);
}

// Checks whether or not a string is a valid grade. It must be of the form
// NNN.NN, NN.NN, or N.NN to pass.
function smart_is_grade($s) {
    return preg_match('/^\d{1,3}|[(.\d{1})]|[(.\d{2})]?$/', trim($s));
}

// Checks whether or not a string is a valid anonymous number. It must be of
// the form XXXX to pass.
function smart_is_anon_num($s) {
    return preg_match('/^\d{4}$/', $s);
}

// Checks whether or not a string is a valid pawsid. It must be 1-16 and contain
// only alphanumeric characters including hyphens.
function smart_is_pawsid($s) {
    return preg_match('/^[a-zA-Z0-9\-]{1,16}$/', $s);
}

function smart_is_keypadid($s) {
    return preg_match('/^[A-Z0-9]{6}$/', $s);
}