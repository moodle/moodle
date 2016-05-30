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
 * Unit tests for /lib/classes/filetypes.php.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Unit tests for /lib/classes/filetypes.php.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_filetypes_testcase extends advanced_testcase {

    public function test_add_type() {
        $this->resetAfterTest();

        // Check the filetypes to be added do not exist yet (basically this
        // ensures we're testing the cache clear).
        $types = get_mimetypes_array();
        $this->assertArrayNotHasKey('frog', $types);
        $this->assertArrayNotHasKey('zombie', $types);

        // Add two filetypes (minimal, then all options).
        core_filetypes::add_type('frog', 'application/x-frog', 'document');
        core_filetypes::add_type('zombie', 'application/x-zombie', 'document',
            array('document', 'image'), 'image', 'A zombie', true);

        // Check they now exist, and check data.
        $types = get_mimetypes_array();
        $this->assertEquals('application/x-frog', $types['frog']['type']);
        $this->assertEquals('document', $types['frog']['icon']);
        $this->assertEquals(array('document', 'image'), $types['zombie']['groups']);
        $this->assertEquals('image', $types['zombie']['string']);
        $this->assertEquals(true, $types['zombie']['defaulticon']);
        $this->assertEquals('A zombie', $types['zombie']['customdescription']);

        // Test adding again causes exception.
        try {
            core_filetypes::add_type('frog', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('already exists', $e->getMessage());
            $this->assertContains('frog', $e->getMessage());
        }

        // Test bogus extension causes exception.
        try {
            core_filetypes::add_type('.frog', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid extension', $e->getMessage());
            $this->assertContains('..frog', $e->getMessage());
        }
        try {
            core_filetypes::add_type('', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid extension', $e->getMessage());
        }

        // Test there is an exception if you add something with defaulticon when
        // there is already a type that has it.
        try {
            core_filetypes::add_type('gecko', 'text/plain', 'document',
                    array(), '', '', true);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('default icon set', $e->getMessage());
            $this->assertContains('text/plain', $e->getMessage());
        }
    }

    public function test_update_type() {
        $this->resetAfterTest();

        // Check previous value for the MIME type of Word documents.
        $types = get_mimetypes_array();
        $this->assertEquals('application/msword', $types['doc']['type']);

        // Change it.
        core_filetypes::update_type('doc', 'doc', 'application/x-frog', 'document');

        // Check the MIME type is now set and also the other (not specified)
        // options, like groups, were removed.
        $types = get_mimetypes_array();
        $this->assertEquals('application/x-frog', $types['doc']['type']);
        $this->assertArrayNotHasKey('groups', $types['doc']);

        // This time change the extension.
        core_filetypes::update_type('doc', 'docccc', 'application/x-frog', 'document');
        $types = get_mimetypes_array();
        $this->assertEquals('application/x-frog', $types['docccc']['type']);
        $this->assertArrayNotHasKey('doc', $types);

        // Test unknown extension.
        try {
            core_filetypes::update_type('doc', 'doc', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('not found', $e->getMessage());
            $this->assertContains('doc', $e->getMessage());
        }

        // Test bogus extension causes exception.
        try {
            core_filetypes::update_type('docccc', '.frog', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid extension', $e->getMessage());
            $this->assertContains('.frog', $e->getMessage());
        }
        try {
            core_filetypes::update_type('docccc', '', 'application/x-frog', 'document');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid extension', $e->getMessage());
        }

        // Test defaulticon changes.
        try {
            core_filetypes::update_type('docccc', 'docccc', 'text/plain', 'document',
                    array(), '', '', true);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('default icon set', $e->getMessage());
            $this->assertContains('text/plain', $e->getMessage());
        }
    }

    public function test_delete_type() {
        $this->resetAfterTest();

        // Filetype exists.
        $types = get_mimetypes_array();
        $this->assertArrayHasKey('doc', $types);

        // Remove it.
        core_filetypes::delete_type('doc');
        $types = get_mimetypes_array();
        $this->assertArrayNotHasKey('doc', $types);

        // Test removing one that doesn't exist causes exception.
        try {
            core_filetypes::delete_type('doc');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('not found', $e->getMessage());
            $this->assertContains('doc', $e->getMessage());
        }

        // Try a custom type (slightly different).
        core_filetypes::add_type('frog', 'application/x-frog', 'document');
        $types = get_mimetypes_array();
        $this->assertArrayHasKey('frog', $types);
        core_filetypes::delete_type('frog');
        $types = get_mimetypes_array();
        $this->assertArrayNotHasKey('frog', $types);
    }

    public function test_revert_type_to_default() {
        $this->resetAfterTest();

        // Delete and then revert.
        core_filetypes::delete_type('doc');
        $this->assertArrayNotHasKey('doc', get_mimetypes_array());
        core_filetypes::revert_type_to_default('doc');
        $this->assertArrayHasKey('doc', get_mimetypes_array());

        // Update and then revert.
        core_filetypes::update_type('asm', 'asm', 'text/plain', 'sourcecode', array(), '', 'An asm file');
        $types = get_mimetypes_array();
        $this->assertEquals('An asm file', $types['asm']['customdescription']);
        core_filetypes::revert_type_to_default('asm');
        $types = get_mimetypes_array();
        $this->assertArrayNotHasKey('customdescription', $types['asm']);

        // Test reverting a non-default type causes exception.
        try {
            core_filetypes::revert_type_to_default('frog');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('not a default type', $e->getMessage());
            $this->assertContains('frog', $e->getMessage());
        }
    }

    /**
     * Check that the logic cleans up the variable by deleting parts that are
     * no longer needed.
     */
    public function test_cleanup() {
        global $CFG;
        $this->resetAfterTest();

        // The custom filetypes setting is empty to start with.
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);

        // Add a custom filetype, then delete it.
        core_filetypes::add_type('frog', 'application/x-frog', 'document');
        $this->assertObjectHasAttribute('customfiletypes', $CFG);
        core_filetypes::delete_type('frog');
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);

        // Change a standard filetype, then change it back.
        core_filetypes::update_type('asm', 'asm', 'text/plain', 'document');
        $this->assertObjectHasAttribute('customfiletypes', $CFG);
        core_filetypes::update_type('asm', 'asm', 'text/plain', 'sourcecode');
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);

        // Delete a standard filetype, then add it back (the same).
        core_filetypes::delete_type('asm');
        $this->assertObjectHasAttribute('customfiletypes', $CFG);
        core_filetypes::add_type('asm', 'text/plain', 'sourcecode');
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);

        // Revert a changed type.
        core_filetypes::update_type('asm', 'asm', 'text/plain', 'document');
        $this->assertObjectHasAttribute('customfiletypes', $CFG);
        core_filetypes::revert_type_to_default('asm');
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);

        // Revert a deleted type.
        core_filetypes::delete_type('asm');
        $this->assertObjectHasAttribute('customfiletypes', $CFG);
        core_filetypes::revert_type_to_default('asm');
        $this->assertObjectNotHasAttribute('customfiletypes', $CFG);
    }
}
