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
 * File handling related exceptions.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Basic file related exception class
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_exception extends moodle_exception {
    function __construct($errorcode, $a=NULL, $debuginfo = NULL) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * Can not create file exception
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stored_file_creation_exception extends file_exception {
    function __construct($contextid, $component, $filearea, $itemid, $filepath, $filename, $debuginfo = NULL) {
        $a = new stdClass();
        $a->contextid = $contextid;
        $a->component = $component;
        $a->filearea  = $filearea;
        $a->itemid    = $itemid;
        $a->filepath  = $filepath;
        $a->filename  = $filename;
        parent::__construct('storedfilenotcreated', $a, $debuginfo);
    }
}

/**
 * No file access exception.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_access_exception extends file_exception {
    function __construct($debuginfo = NULL) {
        parent::__construct('nopermissions', NULL, $debuginfo);
    }
}

/**
 * Hash file content problem exception.
 *
 * @package    core
 * @subpackage filestorage
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_pool_content_exception extends file_exception {
    function __construct($contenthash, $debuginfo = NULL) {
        parent::__construct('hashpoolproblem', $contenthash, $debuginfo);
    }
}
