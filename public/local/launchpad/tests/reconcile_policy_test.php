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

namespace local_launchpad;

/**
 * Unit tests for existing-object reconcile policy (no DB).
 *
 * Full cascade behaviour of core_course_category::update(visible=0) requires a
 * real Moodle DB and is documented here, not re-executed as an integration test.
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\reconcile_policy
 */
final class reconcile_policy_test extends \basic_testcase {

    public function test_existing_visibility_must_not_be_force_hidden(): void {
        $this->assertFalse(reconcile_policy::may_force_hide_existing());
    }

    public function test_existing_objects_must_not_be_renamed_or_moved(): void {
        $this->assertFalse(reconcile_policy::may_rename_existing());
        $this->assertFalse(reconcile_policy::may_move_existing());
    }

    public function test_documents_category_hide_cascade_risk(): void {
        // Moodle core: hiding a category also hides child categories and courses.
        // setup.php must not call category visibility updates on existing cats.
        $this->assertTrue(reconcile_policy::category_hide_cascades_to_courses());
    }
}
