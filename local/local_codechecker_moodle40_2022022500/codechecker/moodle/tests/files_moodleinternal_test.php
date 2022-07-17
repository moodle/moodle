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
 * Test the MoodleInternalSniff sniff.
 *
 * @package    local_codechecker
 * @category   test
 * @copyright  2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \MoodleCodeSniffer\moodle\Sniffs\Files\MoodleInternalSniff
 */
class files_moodleinternal_test extends local_codechecker_testcase {

    public function test_moodle_files_moodleinternal_problem() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/problem.php');

        // Define expected results (errors and warnings). Format, array of:
        // - line => number of problems,  or
        // - line => array of contents for message / source problem matching.
        // - line => string of contents for message / source problem matching (only 1).
        $this->set_errors([
            19 => 'Expected MOODLE_INTERNAL check or config.php inclusion'
        ]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_warning() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/warning.php');

        $this->set_errors([]);
        $this->set_warnings([
            32 => 'Expected MOODLE_INTERNAL check or config.php inclusion. Multiple artifacts'
        ]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_nowarning() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/nowarning.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_declare_ok() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/declare_ok.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_namespace_ok() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/namespace_ok.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_no_moodle_cookie_ok() {
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/no_moodle_cookie_ok.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_behat_skipped() {
        // Files in /tests/behat/ dirs are ignored.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/tests/behat/behat_mod_workshop.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();

        // Files in /lib/behat/ dirs are ignored.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/lib/behat/behat_mod_workshop.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_lang_skipped() {
        // Files in lang dirs are ignored.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/lang/en/repository_dropbox.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_namespace_with_use_ok() {
        // An edge case which allows use statements before defined().
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/namespace_with_use_ok.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_old_style_if_die() {
        // Old style if statement MOODLE_INTERNAL check.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/old_style_if_die_ok.php');

        $this->set_errors([]);
        $this->set_warnings([
            24 => 'Old MOODLE_INTERNAL check detected. Replace it by',
        ]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_no_relevant_ok() {
        // Files that only contain non-relevant (and no side-effects) code.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/no_relevant_ok.php');

        $this->set_errors([]);
        $this->set_warnings([]);

        $this->verify_cs_results();
    }

    public function test_moodle_files_moodleinternal_unexpected() {
        // Unexpected (not needed) check.
        $this->set_standard('moodle');
        $this->set_sniff('moodle.Files.MoodleInternal');
        $this->set_fixture(__DIR__ . '/fixtures/files/moodleinternal/unexpected.php');

        $this->set_errors([]);
        $this->set_warnings([
            17 => 'MoodleInternalNotNeeded'
        ]);

        $this->verify_cs_results();
    }
}
