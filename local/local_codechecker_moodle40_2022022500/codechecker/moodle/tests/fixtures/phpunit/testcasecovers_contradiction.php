<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Covering nothing and something is a contradiction.

/**
 * @covers ::something
 * @coversNothing
 */
class contradiction_test extends base_test {
    /**
     * @coversNothing
     * @covers ::something
     */
    public function test_something() {
        // Nothing to test.
    }
}
