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

    function setUp() {
        $this->plugin = new mock_plugin();
        $this->caller = new mock_caller();
        $this->exporter = new portfolio_exporter(&$this->plugin, &$this->caller, '', array());
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
    }

    function test_construct_dupe_instance() {
        $gotexception = false;
        try {
            portfolio_plugin_base::create_instance('download', 'download1', array());
            portfolio_plugin_base::create_instance('download', 'download2', array());
        } catch (portfolio_exception $e) {
            $this->assertEqual('invalidinstance', $e->errorcode);
            $gotexception = true;
        }
        $this->assertTrue($gotexception);
    }
}

?>
