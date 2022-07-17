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
 * Test the ValidFunctionName sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleCodeSniffer\moodle\Sniffs\NamingConventions\ValidFunctionNameSniff
 */
class namingconventions_validfunctionname_test extends local_codechecker_testcase {

    /**
     * Data provider for self::test_namingconventions_validfunctionname
     */
    public function provider_namingconventions_validfunctionname() {
        return [
            'Correct' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_correct.php',
                'errors' => [],
                'warnings' => [],
            ],
            'Lower' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_lower.php',
                'errors' => [
                    5 => 'Public method name "class_with_correct_function_names::notUpperPlease" must be in lower-case',
                    11 => 'moodle.NamingConventions.ValidFunctionName.LowercaseMethod',
                    15 => '@Message: method name "interface_with_correct_function_names::withoutScope"',
                    20 => 'moodle.NamingConventions.ValidFunctionName.LowercaseFunction',
                ],
                'warnings' => [],
            ],
            'Global' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_global.php',
                'errors' => [
                    4 => 'moodle.NamingConventions.ValidFunctionName.MagicLikeFunction',
                    8 => '"jsonSerialize" must be lower-case letters only',
                ],
                'warnings' => [],
            ],
            'Scoped' => [
                'fixture' => 'fixtures/namingconventions/validfunctionname_scoped.php',
                'errors' => [
                    '5' => '__magiclike" is invalid; only PHP magic methods should be prefixed with a double underscore',
                ],
                'warnings' => [],
            ],
        ];
    }

    /**
     * Test the moodle.NamingConventions.ValidFunctionName sniff
     *
     * @param string $fixture relative path to fixture to use.
     * @param array $errors array of errors expected.
     * @param array $warnings array of warnings expected.
     * @dataProvider provider_namingconventions_validfunctionname
     */
    public function test_namingconventions_validfunctionname(string $fixture, array $errors, array $warnings) {

        // Define the standard, sniff and fixture to use.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.NamingConventions.ValidFunctionName');
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
