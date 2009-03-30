<?php // $Id$

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
 * Tests for get_string in ../moodlelib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/moodlelib.php');

/**
 * Test subclass that makes all the protected methods we want to test pubic.
 */
class testable_string_manager extends string_manager {
    public function __construct($dirroot, $dataroot, $admin, $runninginstaller) {
        parent::__construct($dirroot, $dataroot, $admin, $runninginstaller);
    }
    public function locations_to_search($module) {
        return parent::locations_to_search($module);
    }
    public function parse_module_name($module) {
        return parent::parse_module_name($module);
    }
    public function get_parent_language($lang) {
        return parent::get_parent_language($lang);
    }
    public function load_lang_file($langfile) {
        return parent::load_lang_file($langfile);
    }
    public function get_string_from_file($identifier, $langfile, $a) {
        return parent::get_string_from_file($identifier, $langfile, $a);
    }
}

/*
These tests use a shared fixture comprising language files in 
./get_string_fixtures/moodle, which the test class treats as $CFG->dirroot and
./get_string_fixtures/moodledata, which the test class treats as $CFG->dataroot.

The files we have, and their contents, are

.../moodle/lang/en_utf8/moodle.php:
$string['test'] = 'Test';
$string['locallyoverridden'] = 'Not used';

.../moodle/lang/en_utf8/test.php:
$string['hello'] = 'Hello \'world\'!';
$string['hellox'] = 'Hello $a!';
$string['results'] = 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.';

.../moodle/lang/en_utf8_local/moodle.php:
$string['locallyoverridden'] = 'Should see this';

.../moodledata/lang/fr_ca_utf8/langconfig.php
$string['parentlanguage'] = 'fr_utf8';

.../moodledata/lang/es_ar_utf8/langconfig.php
$string['parentlanguage'] = 'es_utf8';

.../moodle/lang/es_ar_utf8_local/langconfig.php
$string['parentlanguage'] = 'es_mx_utf8';

.../moodledata/lang/fr_utf8/test.php
$string['hello'] = 'Bonjour tout le monde!';
$string['hellox'] = 'Bonjour $a!';

.../moodledata/lang/fr_ca_utf8/test.php
$string['hello'] = 'Bonjour Québec!';

.../moodle/blocks/mrbs/lang/en_utf8/block_mrbs.php:
$string['yes'] = 'Yes';

.../moodle/blocks/mrbs/lang/fr_utf8/block_mrbs.php:
$string['yes'] = 'Oui';

*/

class string_manager_test extends UnitTestCase {
    protected $originallang;
    protected $basedir;
    protected $stringmanager;

    public function setUp() {
        global $CFG, $SESSION;
        if (isset($SESSION->lang)) {
            $this->originallang = $SESSION->lang;
        } else {
            $this->originallang = null;
        }
        $this->basedir = $CFG->libdir . '/simpletest/get_string_fixtures/';
        $this->stringmanager = new testable_string_manager($this->basedir . 'moodle',
                $this->basedir . 'moodledata', 'adminpath', false);
    }

    public function tearDown() {
        global $SESSION;
        if (is_null($this->originallang)) {
            unset($SESSION->lang);
        } else {
            $SESSION->lang = $this->originallang;
        }
    }

    public function test_locations_to_search_moodle() {
        $this->assertEqual($this->stringmanager->locations_to_search('moodle'), array(
            $this->basedir . 'moodle/lang/' => '',
            $this->basedir . 'moodledata/lang/' => '',
        ));
    }

    public function test_locations_to_search_langconfig() {
            $this->assertEqual($this->stringmanager->locations_to_search('langconfig'), array(
            $this->basedir . 'moodle/lang/' => '',
            $this->basedir . 'moodledata/lang/' => '',
        ));
    }

    public function test_locations_to_search_module() {
        $this->assertEqual($this->stringmanager->locations_to_search('forum'), array(
            $this->basedir . 'moodle/lang/' => 'forum/',
            $this->basedir . 'moodledata/lang/' => 'forum/',
            $this->basedir . 'moodle/mod/forum/lang/' => 'forum/',
        ));
    }

    public function test_locations_to_search_question_type() {
        $this->assertEqual($this->stringmanager->locations_to_search('qtype_matrix'), array(
            $this->basedir . 'moodle/lang/' => 'qtype_matrix/',
            $this->basedir . 'moodledata/lang/' => 'qtype_matrix/',
            $this->basedir . 'moodle/question/type/matrix/lang/' => 'matrix/',
        ));
    }

    public function test_locations_to_search_local() {
        $this->assertEqual($this->stringmanager->locations_to_search('local'), array(
            $this->basedir . 'moodle/lang/' => 'local/',
            $this->basedir . 'moodledata/lang/' => 'local/',
            $this->basedir . 'moodle/local/lang/' => 'local/',
        ));
    }

    public function test_locations_to_search_report() {
        $this->assertEqual($this->stringmanager->locations_to_search('report_super'), array(
            $this->basedir . 'moodle/lang/' => 'report_super/',
            $this->basedir . 'moodledata/lang/' => 'report_super/',
            $this->basedir . 'moodle/adminpath/report/super/lang/' => 'super/',
            $this->basedir . 'moodle/course/report/super/lang/' => 'super/',
        ));
    }

    public function test_parse_module_name_module() {
        $this->assertEqual($this->stringmanager->parse_module_name('forum'),
                array('', 'forum'));
    }

    public function test_parse_module_name_grade_report() {
        $this->assertEqual($this->stringmanager->parse_module_name('gradereport_magic'),
                array('gradereport_', 'magic'));
    }

    public function test_get_parent_language_normal() {
        // This is a standard case with parent language defined in
        // moodledata/lang/fr_ca_utf8/langconfig.php. From the shared fixture:
        //
        //.../moodledata/lang/fr_ca_utf8/langconfig.php
        //$string['parentlanguage'] = 'fr_utf8';
        $this->assertEqual($this->stringmanager->get_parent_language('fr_ca_utf8'), 'fr_utf8');
    }

    public function test_get_parent_language_local_override() {
        // This is an artificial case where the parent from moodledata/lang/es_ar_utf8 is overridden by
        // a custom file in moodle/lang/es_ar_utf8_local. From the shared fixture:
        //
        //.../moodledata/lang/es_ar_utf8/langconfig.php
        //$string['parentlanguage'] = 'es_utf8';
        //
        //.../moodle/lang/es_ar_utf8_local/langconfig.php
        //$string['parentlanguage'] = 'es_mx_utf8';
        $this->assertEqual($this->stringmanager->get_parent_language('es_ar_utf8'), 'es_mx_utf8');
    }

    public function test_load_lang_file() {
        // From, the shared fixture:
        //
        //.../moodle/lang/en_utf8/test.php:
        //$string['hello'] = 'Hello \'world\'!';
        //$string['hellox'] = 'Hello $a!';
        //$string['results'] = 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.';
        $this->assertEqual($this->stringmanager->load_lang_file($this->basedir . 'moodle/lang/en_utf8/test.php'), array(
                'hello' => "Hello 'world'!",
                'hellox' => 'Hello $a!',
                'results' => 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.',
        ));
    }

    public function test_get_string_from_file_simple() {
        // From the shared fixture:
        //.../moodle/lang/en_utf8/test.php:
        //$string['hello'] = 'Hello \'world\'!';
        // ...
        $this->assertEqual($this->stringmanager->get_string_from_file(
                'hello', $this->basedir . 'moodle/lang/en_utf8/test.php', NULL),
                "Hello 'world'!");
    }

    public function test_get_string_from_file_simple_interp_with_special_chars() {
        // From the shared fixture:
        //.../moodle/lang/en_utf8/test.php:
        // ...
        //$string['hellox'] = 'Hello $a!';
        // ...
        $this->assertEqual($this->stringmanager->get_string_from_file(
                'hellox', $this->basedir . 'moodle/lang/en_utf8/test.php', 'Fred. $100 = 100%'),
                "Hello Fred. $100 = 100%!");
    }

    public function test_get_string_from_file_complex_interp() {
        // From the shared fixture:
        //.../moodle/lang/en_utf8/test.php:
        // ...
        //$string['results'] = 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.';
        $a = new stdClass;
        $a->firstname = 'Tim';
        $a->lastname = 'Hunt';
        $a->testname = 'The song "\'Right\' said Fred"';
        $a->grade = 75;
        $this->assertEqual($this->stringmanager->get_string_from_file(
                'results', $this->basedir . 'moodle/lang/en_utf8/test.php', $a),
                "Dear Tim Hunt,\n\nOn test \"The song \"'Right' said Fred\"\" you scored 75% which earns you $100.");
    }

    public function test_default_lang() {
        global $SESSION;
        $SESSION->lang = 'en_utf8';
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), "Hello 'world'!");
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Tim'), 'Hello Tim!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Yes');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
    }

    public function test_non_default_no_parent() {
        global $SESSION;
        $SESSION->lang = 'fr_utf8';
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), 'Bonjour tout le monde!');
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Jean-Paul'), 'Bonjour Jean-Paul!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Oui');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
    }

    public function test_lang_with_parent() {
        global $SESSION;
        $SESSION->lang = 'fr_ca_utf8';
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), 'Bonjour Québec!');
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Jean-Paul'), 'Bonjour Jean-Paul!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Oui');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
    }
}

?>
