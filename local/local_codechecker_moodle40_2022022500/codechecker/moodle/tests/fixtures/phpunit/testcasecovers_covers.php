<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Wrong @covers.

/**
 * @covers ::ok
 * @covers \ok
 * @covers wrong
 * @covers
 */
class covers_test extends base_test {
    /**
     * @covers ::ok
     * @covers \ok
     * @covers \ok::ok
     * @covers wrong
     * @covers
     */
    public function test_something() {
        // Nothing to test.
    }
}
