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
 * ldap tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  Damyon Wiese, Iñaki Arenaza 2014
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/ldaplib.php');

class core_ldaplib_testcase extends advanced_testcase {

    public function test_ldap_addslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple case',
                'expected' => 'Simple\\20case',
            ),
            array (
                'test' => 'Medium ‒ case',
                'expected' => 'Medium\\20‒\\20case',
            ),
            array (
                'test' => '#Harder+case#',
                'expected' => '\\23Harder\\2bcase\\23',
            ),
            array (
                'test' => ' Harder (and); harder case ',
                'expected' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
            ),
            array (
                'test' => 'Really \\0 (hard) case!\\',
                'expected' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
            ),
            array (
                'test' => 'James "Jim" = Smith, III',
                'expected' => 'James\\20\\22Jim\22\\20\\3d\\20Smith\\2c\\20III',
            ),
            array (
                'test' => '  <jsmith@test.local> ',
                'expected' => '\\20\\20\\3cjsmith@test.local\\3e\\20',
            ),
        );


        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_addslashes($test['test']));
        }
    }

    public function test_ldap_stripslashes() {
        // See http://tools.ietf.org/html/rfc4514#section-5.2 if you want
        // to add additional tests.

        // IMPORTANT NOTICE: While ldap_addslashes() only produces one
        // of the two defined ways of escaping/quoting (the ESC HEX
        // HEX way defined in the grammar in Section 3 of RFC-4514)
        // ldap_stripslashes() has to deal with both of them. So in
        // addition to testing the same strings we test in
        // test_ldap_stripslashes(), we need to also test strings
        // using the second method.

        $tests = array(
            array (
                'test' => 'Simplest',
                'expected' => 'Simplest',
            ),
            array (
                'test' => 'Simple\\20case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ case',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Simple\\ \\63\\61\\73\\65',
                'expected' => 'Simple case',
            ),
            array (
                'test' => 'Medium\\ ‒\\ case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20‒\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => 'Medium\\20\\E2\\80\\92\\20case',
                'expected' => 'Medium ‒ case',
            ),
            array (
                'test' => '\\23Harder\\2bcase\\23',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\#Harder\\+case\\#',
                'expected' => '#Harder+case#',
            ),
            array (
                'test' => '\\20Harder\\20(and)\\3b\\20harder\\20case\\20',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => '\\ Harder\\ (and)\\;\\ harder\\ case\\ ',
                'expected' => ' Harder (and); harder case ',
            ),
            array (
                'test' => 'Really\\20\\5c0\\20(hard)\\20case!\\5c',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'Really\\ \\\\0\\ (hard)\\ case!\\\\',
                'expected' => 'Really \\0 (hard) case!\\',
            ),
            array (
                'test' => 'James\\20\\22Jim\\22\\20\\3d\\20Smith\\2c\\20III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => 'James\\ \\"Jim\\" \\= Smith\\, III',
                'expected' => 'James "Jim" = Smith, III',
            ),
            array (
                'test' => '\\20\\20\\3cjsmith@test.local\\3e\\20',
                'expected' => '  <jsmith@test.local> ',
            ),
            array (
                'test' => '\\ \\<jsmith@test.local\\>\\ ',
                'expected' => ' <jsmith@test.local> ',
            ),
            array (
                'test' => 'Lu\\C4\\8Di\\C4\\87',
                'expected' => 'Lučić',
            ),
        );

        foreach ($tests as $test) {
            $this->assertSame($test['expected'], ldap_stripslashes($test['test']));
        }
    }
}
