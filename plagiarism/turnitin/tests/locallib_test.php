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

/**
 * Unit tests for (some of) plagiarism/turnitin/locallib.php.
 *
 * @package   plagiarism_turnitin
 * @copyright 2018 Turnitin
 * @author    John McGettrick <jmcgettrick@turnitin.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/plagiarism/turnitin/lib.php');

/**
 * Tests for locallib class
 *
 * @package turnitin
 */
class plagiarism_turnitin_locallib_testcase extends advanced_testcase {

    /**
     * Test that we have the correct repository depending on the config settings.
     */
    public function test_plagiarism_turnitin_override_repository() {
        $this->resetAfterTest();

        // Note that $submitpapersto would only ever be 0, 1 or 2 but this is to illustrate
        // that it won't be overridden by the plagiarism_turnitin_override_repository method.
        $submitpapersto = 6;

        // Test that repository is not overridden for value of 0.
        set_config('plagiarism_turnitin_repositoryoption', PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_STANDARD, 'plagiarism_turnitin');
        $response = plagiarism_turnitin_override_repository($submitpapersto);
        $this->assertEquals($submitpapersto, $response);

        // Test that repository is not overridden for value of 1.
        set_config('plagiarism_turnitin_repositoryoption', PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED, 'plagiarism_turnitin');
        $response = plagiarism_turnitin_override_repository($submitpapersto);
        $this->assertEquals($submitpapersto, $response);

        // Standard Repository is being forced.
        set_config('plagiarism_turnitin_repositoryoption', PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_STANDARD, 'plagiarism_turnitin');
        $response = plagiarism_turnitin_override_repository($submitpapersto);
        $this->assertEquals(PLAGIARISM_TURNITIN_SUBMIT_TO_STANDARD_REPOSITORY, $response);

        // No Repository is being forced.
        set_config('plagiarism_turnitin_repositoryoption', PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_NO, 'plagiarism_turnitin');
        $response = plagiarism_turnitin_override_repository($submitpapersto);
        $this->assertEquals(PLAGIARISM_TURNITIN_SUBMIT_TO_NO_REPOSITORY, $response);

        // Institutional Repository is being forced.
        set_config('plagiarism_turnitin_repositoryoption', PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL, 'plagiarism_turnitin');
        $response = plagiarism_turnitin_override_repository($submitpapersto);
        $this->assertEquals(PLAGIARISM_TURNITIN_SUBMIT_TO_INSTITUTIONAL_REPOSITORY, $response);
    }
}