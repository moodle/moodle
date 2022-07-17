<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Skipped checks go here.

// A class not named _test is not inspected.
class something_notest extends base_test {
    public function test_something() {
        // Nothing to test.
    }
}

// A class not extending is not inspected.
class something2_test {
    public function test_something() {
        // Nothing to test.
    }
}

// A method not named test_xxx is not inspected.
class something3_test extends base_test {
    public function notest_something() {
        // Nothing to test.
    }
}
