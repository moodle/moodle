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
 * DTL == Database Transfer Library
 *
 * This library includes all the required functions used to handle
 * transfer of data from one database to another.
 *
 * @package    core
 * @subpackage dtl
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Require {@link ddllib.php}
require_once($CFG->libdir.'/ddllib.php');
// Require {@link database_exporter.php}
require_once($CFG->libdir.'/dtl/database_exporter.php');
// Require {@link xml_database_exporter.php}
require_once($CFG->libdir.'/dtl/xml_database_exporter.php');
// Require {@link file_xml_database_exporter.php}
require_once($CFG->libdir.'/dtl/file_xml_database_exporter.php');
// Require {@link string_xml_database_exporter.php}
require_once($CFG->libdir.'/dtl/string_xml_database_exporter.php');
// Require {@link database_mover.php}
require_once($CFG->libdir.'/dtl/database_mover.php');
// Require {@link database_importer.php}
require_once($CFG->libdir.'/dtl/database_importer.php');
// Require {@link xml_database_importer.php}
require_once($CFG->libdir.'/dtl/xml_database_importer.php');
// Require {@link file_xml_database_importer.php}
require_once($CFG->libdir.'/dtl/file_xml_database_importer.php');
// Require {@link string_xml_database_importer.php}
require_once($CFG->libdir.'/dtl/string_xml_database_importer.php');

/**
 * Exception class for db transfer
 * @see moodle_exception
 */
class dbtransfer_exception extends moodle_exception {
    /**
     * @global object
     * @param string $errorcode
     * @param string $a
     * @param string $link
     * @param string $debuginfo
     */
    function __construct($errorcode, $a=null, $link='', $debuginfo=null) {
        global $CFG;
        if (empty($link)) {
            $link = "$CFG->wwwroot/$CFG->admin/";
        }
        parent::__construct($errorcode, 'core_dbtransfer', $link, $a, $debuginfo);
    }
}

