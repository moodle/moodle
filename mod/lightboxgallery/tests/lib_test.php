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
 * Unit tests for (some of) mod/lightboxgallery/lib.php.
 *
 * @package    mod_lightboxgallery
 * @author     Adam Olley <adam.olley@netspot.com.au>
 * @copyright  2012 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lightboxgallery/lib.php');
require_once($CFG->dirroot . '/mod/lightboxgallery/locallib.php');

/**
 * Unit tests for (some of) mod/lightboxgallery/lib.php.
 *
 * @copyright  2012 NetSpot Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class mod_lightboxgallery_lib_testcase extends advanced_testcase {
    public function test_lightboxgallery_resize_text() {
        $this->assertEquals('test123', lightboxgallery_resize_text('test123', 10));
        $this->assertEquals('test123456...', lightboxgallery_resize_text('test1234567', 10));
    }

    public function test_lightboxgallery_edit_types() {
        $this->resetAfterTest();

        $disabledplugins = explode(',', get_config('lightboxgallery', 'disabledplugins'));

        $types = ['caption', 'crop', 'delete', 'flip', 'resize', 'rotate', 'tag', 'thumbnail'];

        // Test showall returns all types..
        $actual = array_keys(lightboxgallery_edit_types(true));
        $this->assertEquals($types, $actual);

        // Check crop is currently forcefully disabled.
        $types = ['caption', 'delete', 'flip', 'resize', 'rotate', 'tag', 'thumbnail'];
        $actual = array_keys(lightboxgallery_edit_types());
        $this->assertEquals($types, $actual);

        // Check disabling via config works.
        $types = ['caption', 'delete', 'resize', 'rotate', 'tag', 'thumbnail'];
        set_config('disabledplugins', 'flip', 'lightboxgallery');
        $actual = array_keys(lightboxgallery_edit_types());
        $this->assertEquals($types, $actual);

        $types = ['caption', 'resize', 'rotate', 'tag', 'thumbnail'];
        set_config('disabledplugins', 'delete,flip', 'lightboxgallery');
        $actual = array_keys(lightboxgallery_edit_types());
        $this->assertEquals($types, $actual);
    }
}
