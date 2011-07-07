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
 * Unit tests for lib/outputcomponents.php.
 *
 * @package   core
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/outputcomponents.php');

/**
 * Unit tests for the user_picture class
 */
class user_picture_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputcomponents.php');

    public function test_user_picture_fields_aliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));
        $this->assertTrue(in_array('id', $fields));

        $aliased = array();
        foreach ($fields as $field) {
            if ($field === 'id') {
                $aliased['id'] = 'aliasedid';
            } else {
                $aliased[$field] = 'prefix'.$field;
            }
        }

        $returned = user_picture::fields('', array('custom1', 'id'), 'aliasedid', 'prefix');
        $returned = array_map('trim', explode(',', $returned));
        $this->assertEqual(count($returned), count($fields) + 1); // only one extra field added

        foreach ($fields as $field) {
            if ($field === 'id') {
                $expected = "id AS aliasedid";
            } else {
                $expected = "$field AS prefix$field";
            }
            $this->assertTrue(in_array($expected, $returned), "Expected pattern '$expected' not returned");
        }
        $this->assertTrue(in_array("custom1 AS prefixcustom1", $returned), "Expected pattern 'custom1 AS prefixcustom1' not returned");
    }

    public function test_user_picture_fields_unaliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEqual($returned->id, 42);
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $this->assertEqual($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEqual($returned->custom1, 'Value of custom1');
    }

    public function test_user_picture_fields_unaliasing_null() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';
        $fakerecord->prefiximagealt = null;

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEqual($returned->id, 42);
        $this->assertEqual($returned->imagealt, null);
        foreach ($fields as $field) {
            if ($field !== 'id' and $field !== 'imagealt') {
                $this->assertEqual($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEqual($returned->custom1, 'Value of custom1');
    }
}


/**
 * Unit tests for the custom_menu class
 */
class custom_menu_test extends UnitTestCase {

    public function test_empty_menu() {
        $emptymenu = new custom_menu();
        $this->assertTrue($emptymenu instanceof custom_menu);
        $this->assertFalse($emptymenu->has_children());
    }

    public function test_basic_syntax() {
        $definition = <<<EOF
Moodle community|http://moodle.org
-Moodle free support|http://moodle.org/support
-Moodle development|http://moodle.org/development
--Moodle Tracker|http://tracker.moodle.org
--Moodle Docs|http://docs.moodle.org
-Moodle News|http://moodle.org/news
Moodle company
-Hosting|http://moodle.com/hosting|Commercial hosting
-Support|http://moodle.com/support|Commercial support
EOF;

        $menu = new custom_menu($definition);
        $this->assertTrue($menu instanceof custom_menu);
        $this->assertTrue($menu->has_children());
        $firstlevel = $menu->get_children();
        $this->assertIsA($firstlevel, 'array');
        $this->assertEqual(2, count($firstlevel));

        $item = array_shift($firstlevel);
        $this->assertTrue($item instanceof custom_menu_item);
        $this->assertTrue($item->has_children());
        $this->assertEqual(3, count($item->get_children()));
        $this->assertEqual('Moodle community', $item->get_text());
        $itemurl = $item->get_url();
        $this->assertTrue($itemurl instanceof moodle_url);
        $this->assertEqual('http://moodle.org', $itemurl->out());
        $this->assertEqual($item->get_text(), $item->get_title()); // implicit title

        $item = array_shift($firstlevel);
        $this->assertTrue($item->has_children());
        $this->assertEqual(2, count($item->get_children()));
        $this->assertEqual('Moodle company', $item->get_text());
        $this->assertTrue(is_null($item->get_url()));

        $subitem = array_shift($item->get_children());
        $this->assertFalse($subitem->has_children());
        $this->assertEqual('Hosting', $subitem->get_text());
        $this->assertEqual('Commercial hosting', $subitem->get_title());
    }

    public function test_multilang_support() {
        $definition = <<<EOF
Start|http://school.info
Info
-English|http://school.info/en|Information in English|en
-Deutsch|http://school.info/de|Informationen in deutscher Sprache|de,de_du,de_kids
EOF;

        // the menu without multilang support
        $menu = new custom_menu($definition);
        $this->assertTrue($menu->has_children());
        $this->assertEqual(2, count($menu->get_children()));

        $infomenu = array_pop($menu->get_children());
        $this->assertTrue($infomenu->has_children());
        $this->assertEqual(2, count($infomenu->get_children()));

        $langspecinfo = array_shift($infomenu->get_children());
        $this->assertEqual('Information in English', $langspecinfo->get_title());

        // same menu for English language selected
        $menu = new custom_menu($definition, 'en');
        $this->assertTrue($menu->has_children());
        $this->assertEqual(2, count($menu->get_children()));

        $infomenu = array_pop($menu->get_children());
        $this->assertTrue($infomenu->has_children());
        $this->assertEqual(1, count($infomenu->get_children()));

        $langspecinfo = array_shift($infomenu->get_children());
        $this->assertEqual('Information in English', $langspecinfo->get_title());

        // same menu for German (de_du) language selected
        $menu = new custom_menu($definition, 'de_du');
        $this->assertTrue($menu->has_children());
        $this->assertEqual(2, count($menu->get_children()));

        $infomenu = array_pop($menu->get_children());
        $this->assertTrue($infomenu->has_children());
        $this->assertEqual(1, count($infomenu->get_children()));

        $langspecinfo = array_shift($infomenu->get_children());
        $this->assertEqual('Informationen in deutscher Sprache', $langspecinfo->get_title());

        // same menu for Czech language selected
        $menu = new custom_menu($definition, 'cs');
        $this->assertTrue($menu->has_children());
        $this->assertEqual(2, count($menu->get_children()));

        $infomenu = array_pop($menu->get_children());
        $this->assertFalse($infomenu->has_children());
    }
}
