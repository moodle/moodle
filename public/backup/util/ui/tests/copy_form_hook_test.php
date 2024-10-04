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

namespace core_backup;

use core\di;
use core\hook\manager;
use advanced_testcase;
use core_backup\output\copy_form;

/**
 * Tests the after_copy_form_definition hook.
 *
 * @package core_backup
 * @copyright 2024 Monash University (https://www.monash.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class copy_form_hook_test extends advanced_testcase {
    /**
     * Test the after_copy_form_definition hook.
     *
     * @covers \core_backup\output\copy_form::definition
     */
    public function test_copy_form_hook(): void {
        // Load the callback classes.
        require_once(__DIR__ . '/fixtures/copy_form_hooks.php');

        // Replace the version of the manager in the DI container with a phpunit one.
        di::set(
            manager::class,
            manager::phpunit_get_instance([
                // Load a list of hooks for `test_plugin1` from the fixture file.
                'test_plugin1' => __DIR__ .
                    '/fixtures/copy_form_hooks.php',
            ]),
        );

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $form  = new copy_form(
            null,
            ['course' => $course, 'returnto' => null, 'returnurl' => null]
        );
        ob_start();
        $form->display();
        $html = ob_get_clean();

        // Check that the wierdtestname element is part of the form.
        $pos = strpos($html, 'wierdtestname');
        $this->assertNotFalse($pos);
    }
}
