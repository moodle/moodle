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
 * Language Details: First Version
 * Language Strings for etextbook
 *
 * @package    block_etextbook
 * @copyright  2016 Lousiana State University
 * @author     David Elliott <delliott@lsu.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version 1.0
 */

$string['pluginname'] = 'LSU E-Textbooks block';
$string['etextbook'] = 'E-Textbooks';
$string['etextbook:addinstance'] = 'Add a new E-Textbook block';
$string['etextbook:myaddinstance'] = 'Add a new E-Textbook block to the My Moodle Page';
$string['linktolsulibraries'] = '<hr><p><br />Free access through LSU Libraries!</p>';

$string['headerconfig'] = 'LSU E-Textbooks Options';
$string['descconfig'] = 'Options';
$string['labellibrarylink'] = 'link for xml data - http://lib.lsu.edu/ebooks/xml';
$string['desclibrarylink'] = 'URL where the XML data for etextbooks can be found';
$string['retrieve_etextbooks'] = 'RETRIEVE ETEXTBOOKS FROM LIBRARY';

$string['email_report_to'] = 'Person to Email the etextbook report to'; // For now leaving it as just one email address. As it will require less updating if someone leaves, things change, etc.
$string['no_email_address'] = 'Could not email the library admin.';
$string['not_found'] = "Total courses not matched with a book but perhaps shouldve been - ";
$string['subject'] = 'LSU Library Moodle Etextbook Block Report';
$string['mismatch_error'] = 'Book found but not matched for ';
$string['library_admin_email_username'] = "Library Etextbook Administrator";
$string['email_found_books'] = "These courses had books that were found in the xml document - Total Number -  ";
$string['number_books'] = " Number of books returned ";