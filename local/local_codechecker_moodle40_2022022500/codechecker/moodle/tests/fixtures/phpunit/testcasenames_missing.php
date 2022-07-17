<?php
defined("MOODLE_INTERNAL") || die(); // Make this always the 1st line in all CS fixtures.

/**
 * Class not extending anything
 */
class testcasenames_notextending {
    // This class does not extend anything.
}

/**
 * Class missing any test_ method
 */
class testcasenames_notestmethod extends local_codechecker_testcase {
    public function notest_something() {
        // This method is not a unit test.
    }
    public function notest_either() {
        // This method is not a unit test.
    }
}
