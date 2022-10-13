<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_codechecker;

/**
 * Tests related with local_codechecker locallib.php
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \basic_testcase {

    /**
     * Data provider for test_local_codechecker_find_other_files()
     */
    public function local_codechecker_find_other_files_provider() {
        $defaultextensions = ['txt', 'html', 'csv'];
        return [
            'one wrong file' => [
                'path' => 'local/codechecker/tests/nonono_test.php',
                'ignores' => [],
                'extensions' => $defaultextensions,
                'matches' => [\moodle_exception::class],
                'notmatches' => [],
            ],
            'one wrong dir' => [
                'path' => 'local/codechecker/nononotests/',
                'ignores' => [],
                'extensions' => $defaultextensions,
                'matches' => [\moodle_exception::class],
                'notmatches' => [],
            ],
            'one file' => [
                'path' => 'local/codechecker/tests/locallib_test.php',
                'ignores' => [],
                'extensions' => $defaultextensions,
                'matches' => [],
                'notmatches' => [],
            ],
            'one php file' => [
                'path' => 'local/codechecker/tests/locallib_test.php',
                'ignores' => [],
                'extensions' => ['php'],
                'matches' => ['local/codechecker/tests/locallib_test.php'],
                'notmatches' => [],
            ],
            'one dir' => [
                'path' => 'local/codechecker/tests',
                'ignores' => [],
                'extensions' => $defaultextensions,
                'matches' => ['one.txt', 'two.txt'],
                'nomatches' => [],
            ],
            'one dir with php files' => [
                'path' => 'local/codechecker/tests',
                'ignores' => [],
                'extensions' => array_merge($defaultextensions, ['php']),
                'matches' => ['locallib_test.php', 'one.txt', 'two.txt'],
                'nomatches' => [],
            ],
            'one dir one ignored file' => [
                'path' => 'local/codechecker/tests',
                'ignores' => ['one.txt'],
                'extensions' => $defaultextensions,
                'matches' => ['two.txt'],
                'nomatches' => ['one.txt'],
            ],
            'one dir many ignored files' => [
                'path' => 'local/codechecker/tests',
                'ignores' => ['one.txt', 'three.txt'],
                'extensions' => $defaultextensions,
                'matches' => ['two.txt'],
                'nomatches' => ['one.txt', 'three.txt'],
            ],
            'one dir one wildchar ignored' => [
                'path' => 'local/codechecker/tests',
                'ignores' => ['fixtures*three'],
                'extensions' => $defaultextensions,
                'matches' => ['one.txt', 'two.txt'],
                'nomatches' => ['three.txt'],
            ],
        ];
    }

    /**
     * Verify local_codechecker_find_other_files() behaviour.
     *
     * @param string $path dirroot relative path to be examined (file or folder).
     * @param string[] $ignores substring-matching, accepting wild-chars array strings to ignore.
     * @param string[] $extensions list of extensions to look for.
     * @param string[] $matches list of substring-matching strings expected to be in the results.
     * @param string[] $nomatches list of substring-matching strings not expected to be in the results.
     *
     * @dataProvider local_codechecker_find_other_files_provider()
     * @covers ::local_codechecker_find_other_files
     */
    public function test_local_codechecker_find_other_files(string $path, array $ignores,
            array $extensions, array $matches, array $nomatches) {

        global $CFG;
        require_once(__DIR__ . '/../locallib.php');

        $results = [];

        // If matches has moodle_exception, then we expect it to happen.
        if ($matches && $matches[0] === \moodle_exception::class) {
            $this->expectException(\moodle_exception::class);
        }

        // Look for results.
        local_codechecker_find_other_files($results, $CFG->dirroot . '/' . $path, $ignores, $extensions);

        // Empty results, means we expect also empty matches.
        if (empty($results)) {
            $this->assertSame($matches, $results);
            return; // We have ended, no more assertions for this case.
        }

        // We have results, let's check them.
        $results = implode(' ', $results);

        // Let's perform simple substring matching, that's enough.
        foreach ($matches as $match) {
            $this->assertStringContainsString($match, $results);
        }
        foreach ($nomatches as $nomatch) {
            $this->assertStringNotContainsString($nomatch, $results);
        }
    }

    /**
     * Verify test_local_codechecker_check_other_file() behaviour.
     *
     * @covers ::local_codechecker_check_other_file
     */
    public function test_local_codechecker_check_other_file() {

        require_once(__DIR__ . '/../locallib.php');

        // Verify that lf files are ok.
        $xml = new \SimpleXMLElement('<xml/>');
        local_codechecker_check_other_file(__DIR__ . '/../version.php', $xml);
        $this->assertStringContainsString('errors="0" warnings="0"', $xml->asXML());

        // Verify crlf files in /tests/fixtures/ locations are ok.
        $xml = new \SimpleXMLElement('<xml/>');
        local_codechecker_check_other_file(__DIR__ . '/fixtures/crlf.csv', $xml);
        $this->assertStringContainsString('errors="0" warnings="0"', $xml->asXML());

        // Verify crlf files in not in /tests/fixtures/ locations are wrong.
        $xml = new \SimpleXMLElement('<xml/>');
        local_codechecker_check_other_file(__DIR__ . '/fixtures2/crlf.csv', $xml);
        $this->assertStringContainsString('errors="1" warnings="0"', $xml->asXML());
        $this->assertStringContainsString('Windows (CRLF) line ending instead of just LF', $xml->asXML());
    }
}
