<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once('grade_object.php');

/**
 * Class representing a grade history. It is responsible for handling its DB representation,
 * modifying and returning its metadata.
 */
class grade_history extends grade_object {
    /**
     * DB Table (used by grade_object).
     * @var string $table
     */
    var $table = 'grade_history';

    /**
     * Array of class variables that are not part of the DB table fields
     * @var array $nonfields
     */
    var $nonfields = array('table', 'nonfields');

    /**
     * The grade_item whose raw grade is being changed.
     * @var int $itemid
     */
    var $itemid;

    /**
     * The user whose raw grade is being changed.
     * @var int $userid
     */
    var $userid;

    /**
     * The value of the grade before the change.
     * @var float $oldgrade
     */
    var $oldgrade;

    /**
     * The value of the grade after the change.
     * @var float $newgrade
     */
    var $newgrade;

    /**
     * An optional annotation to explain the change.
     * @var string $note
     */
    var $note;

    /**
     * Which user account did the modification.
     * @var string $usermodified
     */
    var $usermodified;

    /**
     * How the grade was modified ('manual', 'module', 'import' etc...).
     * @var string $howmodified
     */
    var $howmodified = 'manual';

    /**
     * Finds and returns a grade_history instance based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return object grade_history instance or false if none found.
     */
    function fetch($params) {
        return grade_object::fetch_helper('grade_history', 'grade_history', $params);
    }

    /**
     * Finds and returns all grade_history instances based on params.
     * @static
     *
     * @param array $params associative arrays varname=>value
     * @return array array of grade_history insatnces or false if none found.
     */
    function fetch_all($params) {
        return grade_object::fetch_all_helper('grade_history', 'grade_history', $params);
    }

    /**
     * Given a info about changed raw grade and some other parameters, records the
     * change of grade value for this object, and associated data.
     * @static
     * @param object $grade_raw
     * @param float $oldgrade
     * @param string $note
     * @param string $howmodified
     * @return boolean Success or Failure
     */
    function insert_change($userid, $itemid, $newgrade, $oldgrade, $howmodified='manual', $note=NULL) {
        global $USER;
        $history = new grade_history();
        $history->itemid       = $itemid;
        $history->userid       = $userid;
        $history->oldgrade     = $oldgrade;
        $history->newgrade     = $newgrade;
        $history->note         = $note;
        $history->howmodified  = $howmodified;

        return $history->insert();
    }
}
?>
