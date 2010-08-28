<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Tests for the parts of ../filterlib.php that handle creating filter objects,
 * and using them to filter strings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//TODO: messing with CFG->dirroot is a really bad idea! I am not going to fix this, sorry. (skodak)
//      if anybody wants to fix this then filter manager has to be modified so that it uses different dir, sorry

require_once($CFG->libdir . '/filterlib.php');

class testable_filter_manager extends filter_manager {

    public function __construct() {
        parent::__construct();
    }
    public function make_filter_object($filtername, $context, $courseid, $localconfig) {
        return parent::make_filter_object($filtername, $context, $courseid, $localconfig);
    }
    public function apply_filter_chain($text, $filterchain) {
        return parent::apply_filter_chain($text, $filterchain);
    }
}

/**
 * Test functions that affect filter_active table with contextid = $syscontextid.
 */
class filter_manager_test extends UnitTestCase {
    public static $includecoverage = array('lib/filterlib.php');
    protected $filtermanager;
    protected $olddirroot;

    public function setUp() {
        global $CFG;
        $this->filtermanager = new testable_filter_manager();
        $this->olddirroot = $CFG->dirroot;
        $CFG->dirroot = $CFG->dataroot . '/temp';
    }

    public function tearDown() {
        global $CFG;
        $CFG->dirroot = $this->olddirroot;
    }

    /** Basically does file_put_contents, but ensures the directory exists first. */
    protected function write_file($path, $content) {
        global $CFG;
        make_upload_directory(str_replace($CFG->dataroot . '/', '', dirname($path)));
        file_put_contents($path, $content);
    }

    public function test_make_filter_object_newstyle() {
        global $CFG;
        $this->write_file($CFG->dirroot . '/filter/makenewstyletest/filter.php', <<<ENDCODE
<?php
class makenewstyletest_filter extends moodle_text_filter {
    public function filter(\$text) {
        return \$text;
    }
}
ENDCODE
        );
        $filter = $this->filtermanager->make_filter_object('filter/makenewstyletest', null, 1, array());
        $this->assertIsA($filter, 'moodle_text_filter');
        $this->assertNotA($filter, 'legacy_filter');
    }

    public function test_make_filter_object_legacy() {
        global $CFG;
        $this->write_file($CFG->dirroot . '/filter/makelegacytest/filter.php', <<<ENDCODE
<?php
function makelegacytest_filter(\$courseid, \$text) {
    return \$text;
}
ENDCODE
        );
        $filter = $this->filtermanager->make_filter_object('filter/makelegacytest', null, 1, array());
        $this->assertIsA($filter, 'legacy_filter');
    }

    public function test_make_filter_object_missing() {
        $this->assertNull($this->filtermanager->make_filter_object('filter/nonexistant', null, 1, array()));
    }

    public function test_apply_filter_chain() {
        $filterchain = array(new doubleup_test_filter(null, 1, array()), new killfrogs_test_filter(null, 1, array()));
        $this->assertEqual('pawn pawn', $this->filtermanager->apply_filter_chain('frogspawn', $filterchain));
    }
}

class doubleup_test_filter extends moodle_text_filter {
    public function filter($text) {
        return $text . ' ' . $text;
    }
}

class killfrogs_test_filter extends moodle_text_filter {
    public function filter($text) {
        return str_replace('frogs', '', $text);
    }
}


