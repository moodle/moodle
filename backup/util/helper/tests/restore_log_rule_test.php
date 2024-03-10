<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core_backup;

use restore_log_rule;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

/**
 * Test the backup and restore of logs using rules.
 *
 * @package    core_backup
 * @category   test
 * @copyright  2015 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_log_rule_test extends \basic_testcase {

    function test_process_keeps_log_unmodified() {

        // Prepare a tiny log entry.
        $originallog = new \stdClass();
        $originallog->url = 'original';
        $originallog->info = 'original';
        $log = clone($originallog);

        // Process it with a tiny log rule, only modifying url and info.
        $lr = new restore_log_rule('test', 'test', 'changed', 'changed');
        $result = $lr->process($log);

        // The log has been processed.
        $this->assertEquals('changed', $result->url);
        $this->assertEquals('changed', $result->info);

        // But the original log has been kept unmodified by the process() call.
        $this->assertEquals($originallog, $log);
    }

    public function test_build_regexp() {
        $original = 'Any (string) with [placeholders] like {this} and {this}. [end].';
        $expectation = '~Any \(string\) with (.*) like (.*) and (.*)\. (.*)\.~';

        $lr = new restore_log_rule('this', 'doesnt', 'matter', 'here');
        $class = new \ReflectionClass('restore_log_rule');

        $method = $class->getMethod('extract_tokens');
        $tokens = $method->invoke($lr, $original);

        $method = $class->getMethod('build_regexp');
        $this->assertSame($expectation, $method->invoke($lr, $original, $tokens));
    }
}
