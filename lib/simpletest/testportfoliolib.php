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
    public function expected_time($callertime){}
    public function is_push(){}
    public function prepare_package(){}
    public function send_package(){}
    public function get_continue_url(){}
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

Mock::generate('portfolio_caller_test', 'mock_caller');
Mock::generate('portfolio_plugin_test', 'mock_plugin');

class portfoliolib_test extends UnitTestCase {
    public $caller;
    public $plugin;
    public $exporter;

    function setUp() {
        $this->plugin = new mock_plugin();
        $this->caller = new mock_caller();
        $this->exporter = new portfolio_exporter(&$this->plugin, &$this->caller, '', array());
    }

    function tearDown() {

    }

    function test_construct() {

    }
}

?>
