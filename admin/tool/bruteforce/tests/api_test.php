<?php
namespace tool_bruteforce;

use advanced_testcase;

// Tests for the public API of tool_bruteforce.
class api_test extends advanced_testcase {
    /**
     * Ensure whitelist and block checks work.
     */
    public function test_block_and_whitelist(): void {
        global $DB;
        $this->resetAfterTest(true);

        $DB->insert_record('tool_bruteforce_list', [
            'listtype' => 'white',
            'type' => 'ip',
            'value' => '1.2.3.4',
            'timecreated' => time(),
        ]);

        $this->assertTrue(api::is_whitelisted('ip', '1.2.3.4'));
        $this->assertFalse(api::is_ip_blocked('1.2.3.4'));

        $record = (object) [
            'type' => 'ip',
            'value' => '5.6.7.8',
            'timecreated' => time(),
            'timerelease' => time() + 3600,
        ];
        $DB->insert_record('tool_bruteforce_block', $record);
        $this->assertTrue(api::is_ip_blocked('5.6.7.8'));
    }

    /**
     * Scheduled task should purge expired blocks.
     */
    public function test_purge_task(): void {
        global $DB;
        $this->resetAfterTest(true);

        $DB->insert_record('tool_bruteforce_block', [
            'type' => 'ip',
            'value' => '9.9.9.9',
            'timecreated' => time() - 7200,
            'timerelease' => time() - 3600,
        ]);
        $this->assertEquals(1, $DB->count_records('tool_bruteforce_block'));

        $task = new \tool_bruteforce\task\purge_blocks();
        $task->execute();

        $this->assertEquals(0, $DB->count_records('tool_bruteforce_block'));
    }
}
