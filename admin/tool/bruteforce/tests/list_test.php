<?php
// Unit tests for list management in tool_bruteforce.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/admin/tool/bruteforce/classes/api.php');

class tool_bruteforce_list_testcase extends advanced_testcase {
    public function test_add_and_check_list_entry() {
        global $DB;
        $this->resetAfterTest();

        \tool_bruteforce\api::add_list_entry('white', 'ip', '1.2.3.4', null, 2);
        $this->assertTrue(\tool_bruteforce\api::is_whitelisted('ip', '1.2.3.4'));
        $this->assertFalse(\tool_bruteforce\api::is_blacklisted('ip', '1.2.3.4'));
    }

    public function test_duplicate_prevented() {
        global $DB;
        $this->resetAfterTest();

        \tool_bruteforce\api::add_list_entry('white', 'ip', '5.6.7.8');
        $this->expectException('dml_write_exception');
        $DB->insert_record('tool_bruteforce_list', (object)[
            'listtype' => 'white',
            'type' => 'ip',
            'value' => '5.6.7.8',
            'timecreated' => time(),
        ]);
    }
}
