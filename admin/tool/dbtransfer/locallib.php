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
 * Export db content to file.
 *
 * @package    tool
 * @subpackage dbtransfer
 * @copyright  2008 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
/*

TODO:
  - exporting to server file >2GB fails in 32bit operating systems - needs warning
  - we may run out of disk space exporting to server file - we must verify the file is not truncated; read from the end of file?
  - when sending file >4GB - FAT32 limit, Apache limit, browser limit - needs warning
  - there must be some form of progress bar during export, transfer - new tracking class could be passed around
  - command line operation - could work around some 2G/4G limits in PHP; useful for cron full backups
  - by default allow exporting into empty database only (no tables with the same prefix yet)
  - all dangerous operation (like deleting of all data) should be confirmed by key found in special file in dataroot
    (user would need file access to dataroot which might prevent various "accidents")
  - implement "Export/import running" notification in lib/setup.php (similar to new upgrade flag in config table)
  - gzip compression when storing xml file - the xml is very verbose and full of repeated tags (zip is not suitable here at all)
    this could help us keep the files below 2G (expected ratio is > 10:1)

*/

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/dtllib.php');


function dbtransfer_export_xml_database($description, $mdb) {
    @set_time_limit(0);

    session_get_instance()->write_close(); // release session

    header('Content-Type: application/xhtml+xml; charset=utf-8');
    header('Content-Disposition: attachment; filename=database.xml');
    header('Expires: 0');
    header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
    header('Pragma: public');

    while(@ob_flush());

    $var = new file_xml_database_exporter('php://output', $mdb);
    $var->export_database($description);

    // no more output
    die;
}


function dbtransfer_transfer_database($sourcedb, $targetdb, $feedback = null) {
    @set_time_limit(0);

    session_get_instance()->write_close(); // release session

    $var = new database_mover($sourcedb, $targetdb, true, $feedback);
    $var->export_database(null);
}
