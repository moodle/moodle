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
class testable_string_manager extends legacy_string_manager {
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

class string_manager_test extends UnitTestCase {

    public static $includecoverage = array('lib/moodlelib.php');
    protected $originallang;
    protected $workspace = 'temp/get_string_fixtures'; // Path inside $CFG->dataroot where we work.
    protected $basedir;
    protected $stringmanager;
    protected $fileswritten = array();

    public function setUp() {
        global $CFG, $SESSION;
        if (isset($SESSION->lang)) {
            $this->originallang = $SESSION->lang;
        } else {
            $this->originallang = null;
        }
        $this->basedir = $CFG->dataroot . '/' . $this->workspace . '/';
        $this->stringmanager = new testable_string_manager($this->basedir . 'moodle',
                $this->basedir . 'moodledata', false);
        make_upload_directory($this->workspace . '/' . 'moodle');
        make_upload_directory($this->workspace . '/' . 'moodledata');
    }

    public function tearDown() {
        global $SESSION;
        if (is_null($this->originallang)) {
            unset($SESSION->lang);
        } else {
            $SESSION->lang = $this->originallang;
        }
        foreach ($this->fileswritten as $path) {
            unlink($path);
        }
        $this->fileswritten = array();
    }

    /**
     * Write a teest language file under $this->basedir
     * @param string $path e.g. 'moodle/lang/en_htf8/test.php' or 'moodledata/lang/fr_utf8/test/php'.
     * @param array $strings the strings to store in the file.
     */
    protected function write_lang_file($path, $strings) {
        $contents = "<?php\n";
        foreach ($strings as $key => $string) {
            $string = str_replace("'", "\'", $string);
            if (substr($string, -1) == '\\') {
                $string .= '\\';
            }
            $contents .= "\$string['$key'] = '" . $string . "';\n";
        }
        $contents .= "?>\n";
        make_upload_directory($this->workspace . '/' . dirname($path));
        $path = $this->basedir . $path;
        file_put_contents($path, $contents);
        $this->fileswritten[] = $path;
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
            $this->basedir . 'moodle/mod/local/lang/' => 'local/',
        ));
    }

    public function test_locations_to_search_report() {
        global $CFG;
        $this->assertEqual($this->stringmanager->locations_to_search('report_super'), array(
            $this->basedir . 'moodle/lang/' => 'report_super/',
            $this->basedir . 'moodledata/lang/' => 'report_super/',
            $this->basedir . 'moodle/' . $CFG->admin . '/report/super/lang/' => 'super/',
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
        // Setup fixture.
        $this->write_lang_file('moodledata/lang/fr_ca_utf8/langconfig.php', array(
            'parentlanguage' => 'fr_utf8',
        ));
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_parent_language('fr_ca_utf8'), 'fr_utf8');
    }

    public function test_get_parent_language_local_override() {
        // Setup fixture.
        $this->write_lang_file('moodledata/lang/es_ar_utf8/langconfig.php', array(
            'parentlanguage' => 'es_utf8',
        ));
        $this->write_lang_file('moodle/lang/es_ar_utf8_local/langconfig.php', array(
            'parentlanguage' => 'es_mx_utf8',
        ));
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_parent_language('es_ar_utf8'), 'es_mx_utf8');
    }

    public function test_load_lang_file() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'hello' => 'Hello \'world\'!',
            'hellox' => 'Hello $a!',
            'results' => 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.',
        ));
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->load_lang_file($this->basedir . 'moodle/lang/en_utf8/test.php'), array(
                'hello' => "Hello 'world'!",
                'hellox' => 'Hello $a!',
                'results' => 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.',
        ));
    }

    public function test_get_string_from_file_empty() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'emptyen' => '',
        ));
        // Exercise SUT.
        $this->assertIdentical($this->stringmanager->get_string_from_file(
                'emptyen', $this->basedir . 'moodle/lang/en_utf8/test.php', NULL),
                '');
    }

    public function test_get_string_from_file_simple() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'hello' => 'Hello \'world\'!',
        ));
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_string_from_file(
                'hello', $this->basedir . 'moodle/lang/en_utf8/test.php', NULL),
                "Hello 'world'!");
    }

    public function test_get_string_from_file_simple_interp_with_special_chars() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'hellox' => 'Hello $a!',
        ));
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_string_from_file(
                'hellox', $this->basedir . 'moodle/lang/en_utf8/test.php', 'Fred. $100 = 100%'),
                "Hello Fred. $100 = 100%!");
    }

    public function test_get_string_from_file_complex_interp() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'results' => 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.',
        ));
        // Exercise SUT.
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
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/moodle.php', array(
            'test' => 'Test',
        ));
        $this->write_lang_file('moodle/lang/en_utf8/test.php', array(
            'hello' => 'Hello \'world\'!',
            'hellox' => 'Hello $a!',
            'results' => 'Dear $a->firstname $a->lastname,\n\nOn test \"$a->testname\" you scored $a->grade%% which earns you \$100.',
            'emptyen' => '',
        ));
        $this->write_lang_file('moodle/blocks/mrbs/lang/en_utf8/block_mrbs.php', array(
            'yes' => 'Yes',
        ));
        global $SESSION;
        $SESSION->lang = 'en_utf8';
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), "Hello 'world'!");
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Tim'), 'Hello Tim!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Yes');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
        $this->assertEqual($this->stringmanager->get_string('emptyen', 'test'), '');
    }

    public function test_non_default_no_parent() {
        // Setup fixture.
        $this->write_lang_file('moodle/lang/en_utf8/moodle.php', array(
            'test' => 'Test',
        ));
        $this->write_lang_file('moodle/lang/fr_utf8/test.php', array(
            'hello' => 'Bonjour tout le monde!',
            'hellox' => 'Bonjour $a!',
            'emptyfr' => '',
        ));
        $this->write_lang_file('moodle/blocks/mrbs/lang/fr_utf8/block_mrbs.php', array(
            'yes' => 'Oui',
        ));
        global $SESSION;
        $SESSION->lang = 'fr_utf8';
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), 'Bonjour tout le monde!');
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Jean-Paul'), 'Bonjour Jean-Paul!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Oui');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
        $this->assertEqual($this->stringmanager->get_string('emptyfr', 'test'), '');
    }

    public function test_lang_with_parent() {
        // Setup fixture.
        $this->write_lang_file('moodledata/lang/fr_ca_utf8/langconfig.php', array(
            'parentlanguage' => 'fr_utf8',
        ));
        $this->write_lang_file('moodle/lang/en_utf8/moodle.php', array(
            'test' => 'Test',
        ));
        $this->write_lang_file('moodle/lang/fr_utf8/test.php', array(
            'hello' => 'Bonjour tout le monde!',
            'hellox' => 'Bonjour $a!',
            'emptyfr' => '',
        ));
        $this->write_lang_file('moodle/blocks/mrbs/lang/fr_utf8/block_mrbs.php', array(
            'yes' => 'Oui',
        ));
        $this->write_lang_file('moodledata/lang/fr_ca_utf8/test.php', array(
            'hello' => 'Bonjour Québec!',
        ));
        global $SESSION;
        $SESSION->lang = 'fr_ca_utf8';
        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_string('test'), 'Test');
        $this->assertEqual($this->stringmanager->get_string('hello', 'test'), 'Bonjour Québec!');
        $this->assertEqual($this->stringmanager->get_string('hellox', 'test', 'Jean-Paul'), 'Bonjour Jean-Paul!');
        $this->assertEqual($this->stringmanager->get_string('yes', 'block_mrbs'), 'Oui');
        $this->assertEqual($this->stringmanager->get_string('stringnotdefinedanywhere'), '[[stringnotdefinedanywhere]]');
    }

    public function test_get_list_of_countries_en_utf8() {
        global $CFG, $SESSION;
        // Setup fixture.
        $countriesen = array(
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
        );
        $this->write_lang_file('moodle/lang/en_utf8/countries.php', $countriesen);

        $oldlist = $CFG->allcountrycodes;
        $CFG->allcountrycodes = '';
        $SESSION->lang = 'en_utf8';

        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_list_of_countries(),
                $countriesen);

        // Tear down.
        $CFG->allcountrycodes = $oldlist;
    }

    public function test_get_list_of_countries_specific_list() {
        global $CFG, $SESSION;
        // Setup fixture.
        $countriesen = array(
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
        );
        $this->write_lang_file('moodle/lang/en_utf8/countries.php', $countriesen);

        $oldlist = $CFG->allcountrycodes;
        $CFG->allcountrycodes = 'AU';
        $SESSION->lang = 'en_utf8';

        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_list_of_countries(),
                array('AU' => $this->stringmanager->get_string('AU', 'countries')));

        // Tear down.
        $CFG->allcountrycodes = $oldlist;
    }

    public function test_get_list_of_countries_fr_utf8() {
        global $CFG, $SESSION;
        // Setup fixture.
        $countriesen = array(
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
        );
        $this->write_lang_file('moodle/lang/en_utf8/countries.php', $countriesen);

        $countriesfr = array(
            'AU' => 'Australie',
            'FR' => 'France',
            'GB' => 'Royaume-Uni',
        );
        $this->write_lang_file('moodledata/lang/fr_utf8/countries.php', $countriesfr);

        $oldlist = $CFG->allcountrycodes;
        $CFG->allcountrycodes = '';
        $SESSION->lang = 'fr_utf8';

        // Exercise SUT.
        unset($countriesfr['FR']);
        $this->assertEqual($this->stringmanager->get_list_of_countries(),
                $countriesfr);

        // Tear down.
        $CFG->allcountrycodes = $oldlist;
    }

    public function test_get_list_of_countries_specific_list_fr() {
        global $CFG, $SESSION;
        // Setup fixture.
        $countriesen = array(
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
        );
        $this->write_lang_file('moodle/lang/en_utf8/countries.php', $countriesen);

        $countriesfr = array(
            'AU' => 'Australie',
            'FR' => 'France',
            'GB' => 'Royaume-Uni',
        );
        $this->write_lang_file('moodledata/lang/fr_utf8/countries.php', $countriesfr);

        $oldlist = $CFG->allcountrycodes;
        $CFG->allcountrycodes = 'FR';
        $SESSION->lang = 'fr_utf8';

        // Exercise SUT.
        unset($countriesfr['FR']);
        $this->assertEqual($this->stringmanager->get_list_of_countries(),
                array('FR' => 'France'));
        // Tear down.
        $CFG->allcountrycodes = $oldlist;
    }

    public function test_get_list_of_countries_lang_with_parent_local_override() {
        global $CFG, $SESSION;

        // Setup fixture.
        $this->write_lang_file('moodledata/lang/fr_ca_utf8/langconfig.php', array(
            'parentlanguage' => 'fr_utf8',
        ));

        $countriesen = array(
            'AU' => 'Australia',
            'GB' => 'United Kingdom',
        );
        $this->write_lang_file('moodle/lang/en_utf8/countries.php', $countriesen);

        $countriesfr = array(
            'AU' => 'Australie',
            'GB' => 'Royaume-Uni',
        );
        $this->write_lang_file('moodledata/lang/fr_utf8/countries.php', $countriesfr);

        $this->write_lang_file('moodle/lang/fr_ca_utf8_local/countries.php', array(
            'AU' => 'Aussie',
        ));

        $oldlist = $CFG->allcountrycodes;
        $CFG->allcountrycodes = '';
        $SESSION->lang = 'fr_ca_utf8';

        // Exercise SUT.
        $this->assertEqual($this->stringmanager->get_list_of_countries(),
                array('AU' => 'Aussie', 'GB' => 'Royaume-Uni'));

        // Tear down.
        $CFG->allcountrycodes = $oldlist;
    }
}


