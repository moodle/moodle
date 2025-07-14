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

use core\output\stored_progress_bar;

/**
 * Unit tests for \core\output\stored_progress_bar
 *
 * @package   core
 * @copyright 2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\output\stored_progress_bar
 */
final class stored_progress_bar_test extends \advanced_testcase {
    /**
     * Test the progress bar initialisation.
     *
     * Creating a new stored progress bar object should set the idnumber,
     * and not generate any output.
     *
     * @return void
     */
    public function test_init(): void {
        $idnumber = random_string();
        $progress = new stored_progress_bar($idnumber);
        $this->assertEquals($idnumber, $progress->get_id());
    }

    /**
     * Calling get_by_idnumber() fetches the correct record.
     *
     * @return void
     * @throws \dml_exception
     */
    public function test_get_by_idnumber(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $progress1 = $generator->create_stored_progress(message: 'progress1');
        $progress2 = $generator->create_stored_progress(message: 'progress2');
        $progress3 = $generator->create_stored_progress(message: 'progress3');

        $progressbar = stored_progress_bar::get_by_idnumber($progress2->idnumber);
        $this->assertEquals('progress2', $progressbar->get_message());
        $progressbar = stored_progress_bar::get_by_idnumber($progress1->idnumber);
        $this->assertEquals('progress1', $progressbar->get_message());
        $progressbar = stored_progress_bar::get_by_idnumber($progress3->idnumber);
        $this->assertEquals('progress3', $progressbar->get_message());
    }

    /**
     * Calling get_by_id() fetches the correct record.
     *
     * @return void
     */
    public function test_get_by_id(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $progress1 = $generator->create_stored_progress();
        $progress2 = $generator->create_stored_progress();
        $progress3 = $generator->create_stored_progress();

        $progressbar = stored_progress_bar::get_by_id($progress2->id);
        $this->assertEquals($progress2->idnumber, $progressbar->get_id());
        $progressbar = stored_progress_bar::get_by_id($progress1->id);
        $this->assertEquals($progress1->idnumber, $progressbar->get_id());
        $progressbar = stored_progress_bar::get_by_id($progress3->id);
        $this->assertEquals($progress3->idnumber, $progressbar->get_id());
    }

    /**
     * Calling error() method updates the record with the new message and haserrored = true.
     *
     * @return void
     */
    public function test_error(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $progress = $generator->create_stored_progress();

        $originalprogressbar = stored_progress_bar::get_by_id($progress->id);
        $originalprogressbar->auto_update(false);
        $this->assertEmpty($originalprogressbar->get_message());
        $this->assertFalse($originalprogressbar->get_haserrored());

        $message = 'There was an error';
        $originalprogressbar->error($message);

        $updatedprogressbar = stored_progress_bar::get_by_id($progress->id);
        $this->assertEquals($message, $updatedprogressbar->get_message());
        $this->assertTrue($updatedprogressbar->get_haserrored());
    }

    /**
     * Calling start() replaces the existing record with a new one for the same idnumber.
     */
    public function test_start(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $originalprogress = $generator->create_stored_progress();

        $progressbar = stored_progress_bar::get_by_id($originalprogress->id);
        $this->assertNotNull($progressbar);
        $this->assertEquals($originalprogress->idnumber, $progressbar->get_id());

        $newid = $progressbar->start();

        $oldprogressbar = stored_progress_bar::get_by_id($originalprogress->id);
        $this->assertNull($oldprogressbar);

        $newprogressbar = stored_progress_bar::get_by_id($newid);
        $this->assertNotNull($newprogressbar);
        $this->assertEquals($originalprogress->idnumber, $newprogressbar->get_id());
    }

    /**
     * Calling convert_to_idnumber() returns a valid idnumber.
     *
     * Leading backslashes are stripped from the class name, and any disallowed characters
     * (any except lower-case letters, numbers and underscores) are replaced with underscores.
     * The result is then concatenated with an underscore and the id argument.
     *
     * @return void
     */
    public function test_convert_to_idnumber(): void {
        $classname = '\\foo\\bar\\class-1_Name';
        $id = rand(1, 10);

        $idnumber = stored_progress_bar::convert_to_idnumber($classname, $id);
        $this->assertEquals('foo_bar_class_1__ame_' . $id, $idnumber);
    }

    /**
     * Calling get_timeout() returns the global progresspollinterval setting, or 5 by default.
     *
     * @return void
     */
    public function test_get_timeout(): void {
        global $CFG;
        $this->resetAfterTest();

        $this->assertEquals(5, stored_progress_bar::get_timeout());
        $progresspollinterval = rand(10, 20);
        $CFG->progresspollinterval = $progresspollinterval;
        $this->assertEquals($progresspollinterval, stored_progress_bar::get_timeout());

    }

    /**
     * Calling export_for_template() returns the current values for rendering the progress bar.
     */
    public function test_export_for_template(): void {
        global $PAGE;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $timenow = time();
        $progress = $generator->create_stored_progress(
            'foo_bar_123',
            $timenow - 10,
            $timenow - 1,
            50.00,
            'error',
            true
        );

        $progressbar = stored_progress_bar::get_by_id($progress->id);

        $templatecontext = $progressbar->export_for_template($PAGE->get_renderer('core'));

        $this->assertEquals([
            'id' => $progress->id,
            'idnumber' => $progress->idnumber,
            'width' => 0,
            'class' => 'stored-progress-bar',
            'value' => $progress->percentcompleted,
            'message' => $progress->message,
            'error' => $progress->haserrored,
        ], $templatecontext);
    }
}
