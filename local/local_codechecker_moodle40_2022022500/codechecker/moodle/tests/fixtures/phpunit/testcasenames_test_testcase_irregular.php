<?php
namespace local_codechecker;
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Correct class but with incorrect name (not ended in _test or _testcase)
 */
class testcasenames_test_testcase_irregular extends local_codechecker_testcase {
    public function test_something() {
    }
}
