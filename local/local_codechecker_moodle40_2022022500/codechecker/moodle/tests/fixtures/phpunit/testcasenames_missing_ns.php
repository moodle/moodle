<?php
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Correct class but with missing namespace (and irregular test name).
 */
class testcasenames_missing_ns extends \local_codechecker_testcase {
    public function test_something() {
    }
}
