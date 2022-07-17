<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// A class with 3 methods, using all the covers options correctly.

/**
 * @coversDefaultClass \some\namespace\one
 * @covers ::all()
 */
class correct_test extends base_test {
    /**
     * @covers ::one()
     */
    public function test_one() {
        // Nothing to test.
    }

    /**
     * @covers ::two()
     * @covers \some\namespace\two::two()
     */
    public function test_two() {
        // Nothing to test.
    }

    /**
     * @coversNothing
     */
    public function test_three() {
        // Nothing to test.
    }
}
