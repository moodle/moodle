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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../tests/local_codechecker_testcase.php');

// phpcs:disable moodle.NamingConventions

/**
 * Test the PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayBracketSpacingSniff sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayBracketSpacingSniff
 */
class squiz_arrays_arraybracketspacing_test extends local_codechecker_testcase {

    /**
     * Test the Squid.Arrays.ArrayBracketSpacing sniff
     */
    public function test_squiz_arrays_arraybracketspacing() {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('Squiz.Arrays.ArrayBracketSpacing');
        $this->set_fixture(__DIR__ . '/fixtures/squiz_arrays_arraybracketspacing.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors([
            4 => "expected \"\$arr[\" but found \"\$arr [\"",
            5 => ["expected \"['wrong'\" but found \"[ 'wrong'\"", "expected \"'wrong']\" but found \"'wrong' ]\""],
            17 => 3,
            22 => 2,
            25 => 2,
            28 => 2,
            31 => 2,
            34 => 2,
        ]);
        $this->set_warnings([]);

        // Let's do all the hard work!
        $this->verify_cs_results();
    }
}
