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
 * Unit tests for localization support in lib/moodlelib.php
 *
 * @package     core
 * @category    test
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/moodlelib.php');

/**
 * Tests for the API of the string_manager
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class string_manager_test extends advanced_testcase {

    public function test_string_manager_instance() {
        global $CFG;
        $this->resetAfterTest();

        $otherroot = dirname(__FILE__).'/fixtures/langtest';
        $stringman = testable_core_string_manager::instance($otherroot);
        $this->assertTrue(in_array('string_manager', class_implements($stringman)));
    }

    public function test_get_language_dependencies() {
        global $CFG;
        $this->resetAfterTest();

        $otherroot = dirname(__FILE__).'/fixtures/langtest';
        $stringman = testable_core_string_manager::instance($otherroot);

        // There is no parent language for 'en'.
        $this->assertSame(array(), $stringman->get_language_dependencies('en'));
        // Language with no parent language declared.
        $this->assertSame(array('aa'), $stringman->get_language_dependencies('aa'));
        // Language with parent language explicitly set to English (en < de).
        $this->assertSame(array('de'), $stringman->get_language_dependencies('de'));
        // Language dependency hierarchy (de < de_du < de_kids).
        $this->assertSame(array('de', 'de_du', 'de_kids'), $stringman->get_language_dependencies('de_kids'));
        // Language with the parent language misconfigured to itself (sd < sd).
        $this->assertSame(array('sd'), $stringman->get_language_dependencies('sd'));
        // Language with circular dependency (cda < cdb < cdc < cda).
        $this->assertSame(array('cda', 'cdb', 'cdc'), $stringman->get_language_dependencies('cdc'));
        // Orphaned language (N/A < bb).
        $this->assertSame(array('bb'), $stringman->get_language_dependencies('bb'));
        // Descendant of an orphaned language (N/A < bb < bc).
        $this->assertSame(array('bb', 'bc'), $stringman->get_language_dependencies('bc'));
    }
}


/**
 * Helper class providing testable string_manager
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_core_string_manager extends core_string_manager {

    /**
     * Factory method
     *
     * @param string $otherroot full path to the location of installed upstream language packs
     * @param string $localroot full path to the location of locally customized language packs, defaults to $otherroot
     * @param bool $usecache use application permanent cache
     * @param array $translist explicit list of visible translations
     * @param string $menucache the location of a file that caches the list of available translations
     * @return testable_core_string_manager
     */
    public static function instance($otherroot, $localroot = null, $usecache = false, array $translist = array(), $menucache = null) {
        global $CFG;

        if (is_null($localroot)) {
            $localroot = $otherroot;
        }

        if (is_null($menucache)) {
            $menucache = $CFG->cachedir.'/languages';
        }

        return new testable_core_string_manager($otherroot, $localroot, $usecache, $translist, $menucache);
    }
}
