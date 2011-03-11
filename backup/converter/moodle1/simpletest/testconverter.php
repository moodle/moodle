<?php

require_once($CFG->dirroot.'/backup/util/includes/convert_includes.php');

class moodle1_converter_test extends UnitTestCase {

    public static $includecoverage = array();

    /**
     * @var string
     */
    protected $tempdir;

    public function setUp() {
        global $CFG;

        $this->tempdir = convert_helper::generate_id('simpletest');
        check_dir_exists("$CFG->dataroot/temp/backup/$this->tempdir");
        copy(
            $CFG->dirroot.'/backup/converter/moodle1/simpletest/files/moodle.xml',
            "$CFG->dataroot/temp/backup/$this->tempdir/moodle.xml"
        );
    }

    public function tearDown() {
        global $CFG;
        fulldelete("$CFG->dataroot/temp/backup/$this->tempdir");
    }

    public function test_can_convert() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $this->assertIsA($converter, 'moodle1_converter');
        $this->assertTrue($converter->can_convert());
    }

    public function test_convert() {
        $converter = convert_factory::converter('moodle1', $this->tempdir);
        $this->assertIsA($converter, 'moodle1_converter');
        $converter->convert();
    }
}