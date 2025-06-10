<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'tool_xmldb', language 'en_us', version '4.1'.
 *
 * @package     tool_xmldb
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['confirmcheckbigints'] = 'This functionality will search for <a href="http://tracker.moodle.org/browse/MDL-11038">potential wrong integer fields</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the integers in your DB properly defined.

Once generated you can copy such statements and execute them safely with your favorite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of wrong integers.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckdefaults'] = 'This functionality will search for inconsistent default values in your Moodle server, generating (but not executing!) the needed SQL statements to have all the default values properly defined.

Once generated you can copy such statements and execute them safely with your favorite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of inconsistent default values.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckindexes'] = 'This functionality will search for potential missing indexes in your Moodle server, generating (but not executing!) automatically the needed SQL statements to keep everything updated.

Once generated you can copy such statements and execute them safely with your favorite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of missing indexes.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['confirmcheckoraclesemantics'] = 'This functionality will search for <a href="http://tracker.moodle.org/browse/MDL-29322">Oracle varchar2 columns using BYTE semantics</a> in your Moodle server, generating (but not executing!) automatically the needed SQL statements to have all the columns converted to use CHAR semantics instead (better for cross-db compatibility and increased contents max. length).

Once generated you can copy such statements and execute them safely with your favorite SQL interface (don\'t forget to backup your data before doing that).

It\'s highly recommended to be running the latest (+ version) available of your Moodle release before executing the search of BYTE semantics.

This functionality doesn\'t perform any action against the DB (just reads from it), so can be safely executed at any moment.';
$string['yesmissingindexesfound'] = 'Some missing indexes have been found in your DB. Here are their details and the needed SQL statements to be executed with your favorite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more missing indexes are found.';
$string['yeswrongdefaultsfound'] = 'Some inconsistent defaults have been found in your DB. Here are their details and the needed SQL statements to be executed with your favorite SQL interface to fix them all (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more inconsistent defaults are found.';
$string['yeswrongintsfound'] = 'Some wrong integers have been found in your DB. Here are their details and the needed SQL statements to be executed with your favorite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more wrong integers are found.';
$string['yeswrongoraclesemanticsfound'] = 'Some Oracle columns using BYTE semantics have been found in your DB. Here are their details and the needed SQL statements to be executed with your favorite SQL interface to create all them (don\'t forget to backup your data before doing that).<br /><br />After doing that, it\'s highly recommended to execute this utility again to check that no more wrong semantics are found.';
