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
 * Unit tests for  ../portfoliolib.php.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->dirroot . '/admin/generator.php');

class portfolio_plugin_test extends portfolio_plugin_push_base {
    public function expected_time($callertime){
        return $callertime;
    }

    public function prepare_package() {
        return true;
    }

    public function send_package() {
        return true;
    }

    public function get_continue_url() {
        return '';
    }

    public static function get_name() {
        return '';
    }
}

class portfolio_caller_test extends portfolio_caller_base {
    private $content;

    public function __construct($content) {
        $this->content = $content;
    }

    public function expected_time() {
        return PORTFOLIO_TIME_LOW;
    }

    public function get_navigation() {
        $extranav = array('name' => 'Test caller class', 'link' => $this->get_return_url());
        return array($extranav, 'test');
    }

    public function get_sha1(){
        return sha1($this->content);
    }

    public function prepare_package() {

    }

    public function get_return_url() {
        return '';
    }

    public function check_permissions() {
        return true;
    }

    public static function display_name() {
        return "Test caller subclass";
    }
}

/**
 * The following two classes are full mocks: none of their methods do anything, including their constructor.
 * They can be instantiated directly with no params (new portfolio_caller_test())
 */
Mock::generate('portfolio_caller_test', 'mock_caller');
Mock::generate('portfolio_plugin_test', 'mock_plugin');

/**
 * Partial mocks work as normal except the methods listed in the 3rd param, which are mocked.
 * They are instantiated by passing $this to the constructor within the test case class.
 */
Mock::generatePartial('portfolio_plugin_test', 'partialmock_plugin', array('send_package'));

class portfoliolib_test extends UnitTestCase {
    public $caller;
    public $plugin;
    public $exporter;
    public $original_db;

    function setUp() {
        global $DB, $CFG;
        $this->original_db = clone($DB);

        $class = get_class($DB);
        $DB = new $class();
        $DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, true, 'tst_');

        $u = new StdClass;
        $u->id = 100000000000;
        $this->plugin = new mock_plugin();
        $this->caller = new mock_caller();
        $this->exporter = new portfolio_exporter(&$this->plugin, &$this->caller, '', array());
        $this->exporter->set('user', $u);
        $partialplugin = &new partialmock_plugin($this);

        // Write a new text file
        $this->exporter->save();
        $this->exporter->write_new_file('Test text', 'test.txt');
    }

    function tearDown() {
        global $DB;
        $DB->delete_records('portfolio_tempdata', array('id' => $this->exporter->get('id')));
        $fs = get_file_storage();
        $fs->delete_area_files(SYSCONTEXTID, 'portfolio_exporter', $this->exporter->get('id'));

        $settings = array('no_data' => 1, 'post_cleanup' => 1, 'database_prefix' => 'tst_', 'quiet' => 1);
        generator_generate_data($settings);

        // Restore original DB
        $DB = $this->original_db;
    }

    function test_construct_dupe_instance() {
        $gotexception = false;
        try {
            $plugin1 = portfolio_plugin_base::create_instance('download', 'download1', array());
            $plugin2 = portfolio_plugin_base::create_instance('download', 'download2', array());
            $test1 = new portfolio_plugin_download($plugin1->get('id'));
        } catch (portfolio_exception $e) {
            $this->assertEqual('multipledisallowed', $e->errorcode);
            $gotexception = true;
        }
        $this->assertTrue($gotexception);
    }
}

// Load tests for various modules
require_once($CFG->dirroot . '/mod/forum/simpletest/test_forum_portfolio_callers.php');
require_once($CFG->dirroot . '/mod/glossary/simpletest/test_glossary_portfolio_callers.php');
require_once($CFG->dirroot . '/mod/assignment/simpletest/test_assignment_portfolio_callers.php');
?>
