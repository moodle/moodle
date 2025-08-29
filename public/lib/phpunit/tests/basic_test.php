<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core;

use phpunit_util;

/**
 * Test basic_testcase extra features and PHPUnit Moodle integration.
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\basic_testcase::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\phpunit_util::class)]
final class basic_test extends \basic_testcase {
    /** @var bool */
    protected bool $testassertexecuted = false;

    protected function setUp(): void {
        parent::setUp();
        if ($this->getName() === 'test_setup_assert') {
            $this->assertTrue(true);
            $this->testassertexecuted = true;
            return;
        }
    }

    /**
     * Tests that bootstrapping has occurred correctly
     */
    public function test_bootstrap(): void {
        global $CFG;

        // The httpswwwroot has been deprecated, we keep it as an alias for backwards compatibility with plugins only.
        $this->assertTrue(isset($CFG->httpswwwroot));
        $this->assertEquals($CFG->httpswwwroot, $CFG->wwwroot);
        $this->assertEquals($CFG->prefix, $CFG->phpunit_prefix);
    }

    /**
     * This is just a verification if I understand the PHPUnit assert docs right --skodak
     */
    public function test_assert_behaviour(): void {
        // Arrays.
        $a = ['a', 'b', 'c'];
        $b = ['a', 'c', 'b'];
        $c = ['a', 'b', 'c'];
        $d = ['a', 'b', 'C'];
        $this->assertNotEquals($a, $b);
        $this->assertNotEquals($a, $d);
        $this->assertEquals($a, $c);
        $this->assertEqualsCanonicalizing($a, $b);

        // Objects.
        $a = new \stdClass();
        $a->x = 'x';
        $a->y = 'y';
        $b = new \stdClass(); // Switched order.
        $b->y = 'y';
        $b->x = 'x';
        $c = $a;
        $d = new \stdClass();
        $d->x = 'x';
        $d->y = 'y';
        $d->z = 'z';
        $this->assertEquals($a, $b);
        $this->assertNotSame($a, $b);
        $this->assertEquals($a, $c);
        $this->assertSame($a, $c);
        $this->assertNotEquals($a, $d);

        // String comparison.
        $this->assertEquals(1, '1');
        $this->assertEquals(null, '');

        $this->assertNotEquals(0, '');
        $this->assertNotEquals(null, '0');
        $this->assertNotEquals([], '');

        // Other comparison.
        $this->assertEquals(null, null);
        $this->assertEquals(false, null);
        $this->assertEquals(0, null);

        // Emptiness.
        $this->assertEmpty(0);
        $this->assertEmpty(0.0);
        $this->assertEmpty('');
        $this->assertEmpty('0');
        $this->assertEmpty(false);
        $this->assertEmpty(null);
        $this->assertEmpty([]);

        $this->assertNotEmpty(1);
        $this->assertNotEmpty(0.1);
        $this->assertNotEmpty(-1);
        $this->assertNotEmpty(' ');
        $this->assertNotEmpty('0 ');
        $this->assertNotEmpty(true);
        $this->assertNotEmpty([null]);
        $this->assertNotEmpty(new \stdClass());
    }

    /**
     * Make sure there are no sloppy Windows line endings
     * that would break our tests.
     */
    public function test_lineendings(): void {
        $string = <<<STRING
a
b
STRING;
        $this->assertSame("a\nb", $string, 'Make sure all project files are checked out with unix line endings.');
    }

    /**
     * Make sure asserts in setUp() do not create problems.
     */
    public function test_setup_assert(): void {
        $this->assertTrue($this->testassertexecuted);
        $this->testassertexecuted = false;
    }

    /**
     * Test assert Tag
     */
    public function test_assert_tag(): void {
        // This should succeed.
        self::assertTag(['id' => 'testid'], "<div><span id='testid'></span></div>");
        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        self::assertTag(['id' => 'testid'], "<div><div>");
    }

    /**
     * Tests for assertEqualsIgnoringWhitespace.
     *
     * @param string $expected
     * @param string $actual
     * @param bool $expectationvalid
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('equals_ignoring_whitespace_provider')]
    public function test_assertEqualsIgnoringWhitespace( // phpcs:ignore
        string $expected,
        string $actual,
        bool $expectationvalid,
    ): void {
        if (!$expectationvalid) {
            $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        }
        self::assertEqualsIgnoringWhitespace($expected, $actual);
    }

    /**
     * Data provider for assertEqualsIgnoringWhitespace tests
     *
     * @return array
     */
    public static function equals_ignoring_whitespace_provider(): array {
        return [
            'equal' => ['a b c', 'a b c', true],
            'equal with whitespace' => ["a b c", "a\nb c", true],
            'equal with extra whitespace' => ["a b c", "a\nb  c", true],
            'whitespace missing' => ["ab c", "a\nb  c", false],
            'not equal' => ['a b c', 'a b d', false],
            'various space types' => [
                implode(' ', [
                    '20', // Regular space.
                    "a0", // No-Break Space (NBSP).
                    "80", // Ogham Space Mark.
                    "0", // En Quad.
                    "1", // Em Quad.
                    "2", // En Space.
                    "3", // Em Space.
                    "4", // Three-Per-Em Space.
                    "5", // Four-Per-Em Space.
                    "6", // Six-Per-Em Space.
                    "7", // Figure Space.
                    "8", // Punctuation Space.
                    "9", // Thin Space.
                    "0a", // Hair Space.
                    "2f", // Narrow No-Break Space (NNBSP).
                    "5f", // Medium Mathematical Space.
                    "3000", // Ideographic Space.
                    ".",
                ]),
                implode('', [
                    // All space chars taken from https://www.compart.com/en/unicode/category/Zs.
                    "20\u{0020}", // Regular space.
                    "a0\u{00a0}", // No-Break Space (NBSP).
                    "80\u{1680}", // Ogham Space Mark.
                    "0\u{2000}", // En Quad.
                    "1\u{2001}", // Em Quad.
                    "2\u{2002}", // En Space.
                    "3\u{2003}", // Em Space.
                    "4\u{2004}", // Three-Per-Em Space.
                    "5\u{2005}", // Four-Per-Em Space.
                    "6\u{2006}", // Six-Per-Em Space.
                    "7\u{2007}", // Figure Space.
                    "8\u{2008}", // Punctuation Space.
                    "9\u{2009}", // Thin Space.
                    "0a\u{200a}", // Hair Space.
                    "2f\u{202f}", // Narrow No-Break Space (NNBSP).
                    "5f\u{205f}", // Medium Mathematical Space.
                    "3000\u{3000}", // Ideographic Space.
                    ".",
                ]),
                true,
            ],
        ];
    }

    /**
     * Test that a database modification is detected.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_db_modification(): void {
        global $DB;
        $DB->set_field('user', 'confirmed', 1, ['id' => -1]);

        $this->expectException(\core_phpunit\exception\test_exception::class);
        $this->expectExceptionMessage('Warning: unexpected database modification');
        phpunit_util::reset_all_data(true);
    }

    /**
     * Test that a $CFG modification is detected.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_cfg_modification(): void {
        global $CFG;
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/rolesactive.*xx value.*removal.*admin/ms'); // 3 messages matched.
        phpunit_util::reset_all_data(true);
    }

    /**
     * Test that a $USER modification is detected.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_user_modification(): void {
        global $USER;
        $USER->id = 10;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Warning: unexpected change of $USER');
        phpunit_util::reset_all_data(true);
    }

    /**
     * Test that a $COURSE modification is detected.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_course_modification(): void {
        global $COURSE;
        $COURSE->id = 10;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Warning: unexpected change of $COURSE');
        phpunit_util::reset_all_data(true);
    }

    /**
     * Test that all modifications are detected together.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_all_modifications(): void {
        global $DB, $CFG, $USER, $COURSE;
        $DB->set_field('user', 'confirmed', 1, ['id' => -1]);
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;
        $USER->id = 10;
        $COURSE->id = 10;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/resetting.*rolesactive.*new.*removal.*USER.*COURSE/ms'); // 6 messages matched.
        phpunit_util::reset_all_data(true);
    }

    /**
     * Test that an open transaction are managed ok by the reset code (silently rolled back).
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_transaction_problem(): void {
        global $DB, $COURSE;
        $originalname = $DB->get_field('course', 'fullname', ['id' => $COURSE->id]); // Normally "PHPUnit test site".
        $changedname = 'Ongoing transaction test site';

        // Start a transaction and make some database changes.
        $DB->start_delegated_transaction();
        $DB->set_field('course', 'fullname', $changedname, ['id' => $COURSE->id]);

        // Assert that the transaction is open and the changes were made.
        $this->assertTrue($DB->is_transaction_started());
        $this->assertEquals($changedname, $DB->get_field('course', 'fullname', ['id' => $COURSE->id]));

        phpunit_util::reset_all_data(false); // We don't want to detect/warn on database changes for this test.

        // Assert that the transaction is now closed and the changes were rolled back.
        $this->assertFalse($DB->is_transaction_started());
        $this->assertEquals($originalname, $DB->get_field('course', 'fullname', ['id' => $COURSE->id]));
    }

    /**
     * Test that the navigation node URL is overridden correctly.
     */
    public function test_set_navigation_url(): void {
        \navigation_node::override_active_url(new \core\url('/foo/bar/baz'));
        $this->assertNotNull(
            (new \ReflectionClass(\navigation_node::class))->getStaticPropertyValue('fullmeurl', null),
        );
    }

    /**
     * Test that the after-test teardown correctly resets the navigation node URL.
     */
    #[\PHPUnit\Framework\Attributes\Depends('test_set_navigation_url')]
    public function test_navigation_url_reset(): void {
        $this->assertNull(
            (new \ReflectionClass(\navigation_node::class))->getStaticPropertyValue('fullmeurl', null),
        );
    }
}
