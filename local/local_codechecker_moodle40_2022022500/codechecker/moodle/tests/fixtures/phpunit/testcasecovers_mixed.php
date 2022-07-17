<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Covering nothing at class and something at method is a contradiction.

/**
 * @coversNothing
 */
class contradictionmixed_test extends base_test {
    /**
     * @covers ::something
     */
    public function test_something() {
        // Nothing to test.
    }
}
