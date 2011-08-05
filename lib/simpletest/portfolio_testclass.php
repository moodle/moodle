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
 * Unit tests for  ../portfoliolib.php.
 *
 * @author nicolasconnault@gmail.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("$CFG->libdir/portfoliolib.php");
require_once("$CFG->libdir/portfolio/exporter.php");
require_once("$CFG->libdir/portfolio/plugin.php");

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

    public function get_interactive_continue_url() {
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

    public function load_data() {

    }

    public static function expected_callbackargs() {
        return array();
    }
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_RICH, PORTFOLIO_FORMAT_FILE);
    }
    public function set_context($PAGE) {
        $PAGE->set_context(get_system_context());
    }
}

class portfolio_exporter_test extends portfolio_exporter {
    private $files;

    public function write_new_file($content, $name) {
        if (empty($this->files)) {
            $this->files = array();
        }
        $this->files[] = new portfolio_fake_file($content, $name);
    }

    public function copy_existing_file($oldfile) {
        throw new portfolio_exception('notimplemented', 'portfolio', 'files api test');
    }

    public function get_tempfiles() {
        return $this->files;
    }

    public function zip_tempfiles() {
        return new portfolio_fake_file('fake content zipfile', 'portfolio-export.zip');
    }
}

/**
* this should be REMOVED when we have proper faking of the files api
*/
class portfolio_fake_file {

    private $name;
    private $content;
    private $hash;
    private $size;

    public function __construct($content, $name) {
        $this->content = $content;
        $this->name = $name;
        $this->hash = sha1($content);
        $this->size = strlen($content);
    }

    public function get_contenthash() {
        return $this->hash;
    }

    public function get_filename() {
        return $this->name;
    }

    public function get_filesize() {
        return $this->size;
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
Mock::generatePartial('portfolio_exporter_test', 'partialmock_exporter', array('process_stage_confirm',
                                                                               'process_stage_cleanup',
                                                                               'log_transfer',
                                                                               'save',
                                                                               'rewaken_object'));


// Generate a mock class for each plugin subclass present
$portfolio_plugins = get_list_of_plugins('portfolio');
foreach ($portfolio_plugins as $plugin) {
    require_once($CFG->dirroot . "/portfolio/$plugin/lib.php");
    Mock::generatePartial("portfolio_plugin_$plugin", "partialmock_plugin_$plugin", array('send_package'));
}

class portfoliolib_test extends UnitTestCaseUsingDatabase {
    private $olduser;

    protected $testtables = array(
                'lib' => array(
                    'portfolio_instance', 'portfolio_instance_user', 'portfolio_instance_config', 
                        'user', 'course', 'course_categories'));

    function setup() {
        global $USER;
        parent::setup();
        
        $this->switch_to_test_db(); // Switch to test DB for all the execution

        foreach ($this->testtables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
        }
        
        // It is necessary to store $USER object because some subclasses use generator
        // stuff which breaks $USER
        $this->olduser = $USER;
    }

    function tearDown() {
        global $USER;
        $USER = $this->olduser;
        parent::tearDown();
    }

    function test_construct_dupe_instance() {
        $gotexception = false;
        try {
            $plugin1 = portfolio_plugin_base::create_instance('download', 'download1', array());
            $plugin2 = portfolio_plugin_base::create_instance('download', 'download2', array());
            $test1 = new portfolio_plugin_download($plugin1->get('id'));
        } catch (portfolio_exception $e) {
            $this->assertEqual('multipleinstancesdisallowed', $e->errorcode);
            $gotexception = true;
        }
        $this->assertTrue($gotexception);
    }

    /**
    * does everything we need to set up a new caller
    * so each subclass doesn't have to implement this
    *
    * @param string $class name of caller class to generate (this class def must be already loaded)
    * @param array $callbackargs the arguments to pass the constructor of the caller
    * @param int $userid a userid the subclass has generated
    *
    * @return portfolio_caller_base subclass
    */
    protected function setup_caller($class, $callbackargs, $user=null) {
        global $DB;
        $caller = new $class($callbackargs);
        $caller->set('exporter', new partialmock_exporter($this));
        if (is_numeric($user)) {
            $user = $DB->get_record('user', array('id' => $user));
        }
        if (is_object($user)) {
            $caller->set('user', $user);
        }
        $caller->load_data();
        // set any format
        $caller->get('exporter')->set('format', array_shift($caller->supported_formats()));
        return $caller;
    }

    public function test_caller_with_plugins() {
        if (!empty($this->caller)) {
            $plugins = get_list_of_plugins('portfolio');

            foreach ($plugins as $plugin) {
                // Instantiate a fake plugin instance
                $plugin_class = "partialmock_plugin_$plugin";
                $plugin = new $plugin_class($this);

                // figure out our format intersection and test all of them.
                $formats = portfolio_supported_formats_intersect($this->caller->supported_formats(), $plugin->supported_formats());
                if (count($formats) == 0) {
                    // bail. no common formats.
                    continue;
                }

                foreach ($formats as $format) {
                    // Create a new fake exporter
                    $exporter =& $this->caller->get('exporter'); // new partialmock_exporter(&$this);
                    $exporter->set('caller', $this->caller);
                    $exporter->set('instance', $plugin);
                    $exporter->set('format', $format);
                    $this->caller->set_export_config(array('format' => $format));
                    $plugin->set_export_config(array('format' => $format));
                    $exporter->set('user', $this->caller->get('user'));

                    $exception = false;
                    try {
                        $exporter->process_stage_package();
                    } catch (Exception $e) {
                        $exception = $e->getMessage();
                    }

                    $this->assertFalse($exception, "Unwanted exception: $exception");
                }
            }
        }
    }
}

