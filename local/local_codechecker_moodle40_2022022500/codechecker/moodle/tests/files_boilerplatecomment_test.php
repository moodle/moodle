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
 * Test the BoilerplateCommentSniff sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2022 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleCodeSniffer\moodle\Sniffs\Files\BoilerplateCommentSniff
 */
class files_boilerplatecomment_test extends local_codechecker_testcase {

    public function test_moodle_files_boilerplatecomment_ok() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/ok.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();

        // Also try with the <?php line having some // phpcs:xxxx annotations.
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/ok2.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_boilerplatecomment_nophp() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/nophp.php');

        $this->set_errors([
            1 => 'moodle.Files.BoilerplateComment.NoPHP',
        ]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_boilerplatecomment_blank() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/blank.php');

        $this->set_errors([
            2 => 'followed by exactly one newline',
        ]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_boilerplatecomment_short() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/short.php');

        $this->set_errors([
            14 => 'FileTooShort',
        ]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_boilerplatecomment_wrongline() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/wrongline.php');

        $this->set_errors([
            6 => 'version 3',
            11 => 'FITNESS',
        ]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_boilerplatecomment_gnu_http() {

        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/gnu_http.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    /**
     * Assert that www.gnu.org can be referred to via https URL in the boilerplate.
     */
    public function test_moodle_files_boilerplatecomment_gnu_https() {

        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.BoilerplateComment');
        $this->set_fixture(__DIR__ . '/fixtures/files/boilerplatecomment/gnu_https.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }
}
