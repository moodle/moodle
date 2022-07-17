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

namespace local_codechecker;

use MoodleCodeSniffer\moodle\Util\MoodleUtil;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../tests/local_codechecker_testcase.php');
require_once(__DIR__ . '/../Util/MoodleUtil.php');

// phpcs:disable moodle.NamingConventions

/**
 * Test the TestCaseCoversSniff sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleCodeSniffer\moodle\Sniffs\PHPUnit\TestCaseCoversSniff
 */
class phpunit_testcasecovers_test extends local_codechecker_testcase {

    /**
     * Data provider for self::test_phpunit_testcasecovers
     */
    public function provider_phpunit_testcasecovers() {
        return [
            'Correct' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_correct.php',
                'errors' => [],
                'warnings' => [],
            ],
            'Contradiction' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_contradiction.php',
                'errors' => [
                    7 => 'contradiction_test has both',
                    8 => 'TestCaseCovers.ContradictoryClass',
                    12 => 'test_something() has both',
                    13 => 'TestCaseCovers.ContradictoryMethod',
                ],
                'warnings' => [
                    8 => 1,
                    12 => 1,
                    13 => 1,
                ],
            ],
            'Missing' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_missing.php',
                'errors' => [],
                'warnings' => [
                    8 => 'test_something() is missing any coverage information',
                ],
            ],
            'Mixed' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_mixed.php',
                'errors' => [],
                'warnings' => [
                    7 => 'contradictionmixed_test has @coversNothing, but there are methods covering stuff',
                    11 => 'TestCaseCovers.ContradictoryMixed',
                ],
            ],
            'Redundant' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_redundant.php',
                'errors' => [],
                'warnings' => [
                    11 => 'has @coversNothing, but class also has it, redundant',
                ],
            ],
            'Skipped' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_skipped.php',
                'errors' => [],
                'warnings' => [],
            ],
            'Covers' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_covers.php',
                'errors' => [
                    9 => 'it must be FQCN (\\ prefixed) or point to method (:: prefixed)',
                    10 => 'it must contain some value',
                    17 => 'TestCaseCovers.NoFQCNOrMethod',
                    18 => 'TestCaseCovers.Empty',
                ],
                'warnings' => [],
            ],
            'CoversDefaultClass' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_coversdefaultclass.php',
                'errors' => [
                    8 => 'Wrong @coversDefaultClass annotation, it must be FQCN (\\ prefixed)',
                    9 => 'TestCaseCovers.WrongMethod',
                    10 => '@coversDefaultClass annotation, it must contain some value',
                    14 => 'test_something() has @coversDefaultClass tag',
                    15 => 'TestCaseCovers.DefaultClassNotAllowed',
                    16 => 'TestCaseCovers.DefaultClassNotAllowed',
                ],
                'warnings' => [],
            ],
            'CoversNothing' => [
                'fixture' => 'fixtures/phpunit/testcasecovers_coversnothing.php',
                'errors' => [
                    7 => '@coversNothing annotation, it must be empty',
                    11 => 'TestCaseCovers.NotEmpty',
                ],
                'warnings' => [
                    11 => 'has @coversNothing, but class also has it, redundant',
                ],
            ],
        ];
    }

    /**
     * Test the moodle.PHPUnit.TestCaseCovers sniff
     *
     * @param string $fixture relative path to fixture to use.
     * @param array $errors array of errors expected.
     * @param array $warnings array of warnings expected.
     * @dataProvider provider_phpunit_testcasecovers
     */
    public function test_phpunit_testcasecovers(string $fixture, array $errors, array $warnings) {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.PHPUnit.TestCaseCovers');
        $this->set_fixture(__DIR__ . '/' . $fixture);

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors($errors);
        $this->set_warnings($warnings);

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
