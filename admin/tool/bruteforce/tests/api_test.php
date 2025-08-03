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

    /**
     * CIDR whitelist and blacklist entries should match IP ranges.
     */
    public function test_cidr_matching(): void {
        $this->resetAfterTest(true);
        
        api::add_list_entry('white', 'ip', '10.0.0.0/24');
        $this->assertTrue(api::is_whitelisted('ip', '10.0.0.42'));
        $this->assertFalse(api::is_whitelisted('ip', '10.0.1.5'));
        
        api::add_list_entry('black', 'ip', '192.168.1.0/24');
        $this->assertTrue(api::is_blacklisted('ip', '192.168.1.99'));
        $this->assertFalse(api::is_blacklisted('ip', '192.168.2.1'));
    }

    /**
     * Manual unblocking should remove block and log audit entry.
     */
    public function test_unblock_logs_audit(): void {
        global $DB;
        $this->resetAfterTest(true);
        
        $DB->insert_record('tool_bruteforce_block', [
            'type' => 'ip',
            'value' => '1.2.3.4',
            'timecreated' => time(),
            'timerelease' => time() + 3600,
        ]);
        
        api::unblock('ip', '1.2.3.4', 99, 'test');
        
        $this->assertFalse(api::is_ip_blocked('1.2.3.4'));
        
        $audit = $DB->get_record('tool_bruteforce_audit', ['targetvalue' => '1.2.3.4']);
        $this->assertEquals(99, $audit->actorid);
        $this->assertEquals('unblock', $audit->action);
    }
}