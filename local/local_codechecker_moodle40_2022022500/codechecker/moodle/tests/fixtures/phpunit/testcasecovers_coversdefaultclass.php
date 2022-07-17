i<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

// Wrong @coversDefaultClass.

/**
 * @coversDefaultClass \ok
 * @coversDefaultClass wrong
 * @coversDefaultClass \wrong::wrong
 * @coversDefaultClass
 */
class coversdefaultclass_test extends base_test {
    /**
     * @coversDefaultClass ::wrong
     * @coversDefaultClass wrong
     * @coversDefaultClass
     * @covers ::something
     */
    public function test_something() {
        // Nothing to test.
    }
}
