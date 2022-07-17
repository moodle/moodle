<?php
namespace local_codechecker\fixtures\phpunit; // Not correct level2, but we aren't checking that. Just correct location.
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Correct class, using correct location matching sub-namespaces.
 */
class testcasenames_correct_level2ns extends local_codechecker_testcase {
    public function test_something() {
    }
}
