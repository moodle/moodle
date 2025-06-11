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
 * Class defining resuable tests methods for external functions
 *
 * @package   qbank_columnsortorder
 * @copyright 2023 Catalyst IT Europe Ltd.
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qbank_columnsortorder\tests;

/**
 * Class defining resuable tests methods for external functions
 */
abstract class external_function_testcase extends \advanced_testcase {
    /**
     * @var string Fully-qualified external function class to test.
     */
    protected $testclass;

    /**
     * @var string The name of the setting used to store the data.
     */
    protected $setting;

    /**
     * @var bool Whether the data is stored as a comma-separated list.
     */
    protected $csv = true;

    /**
     * A function that returns the data to be passed to the external function.
     *
     * The data returned will depend on the testclass.
     *
     * @return mixed
     */
    abstract protected function generate_test_data(): mixed;

    /**
     * Test that execute() method sets the correct config setting.
     */
    public function test_execute(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $testdata = $this->generate_test_data();
        $this->testclass::execute($testdata, true);

        $currentconfig = get_config('qbank_columnsortorder', $this->setting);
        if ($this->csv) {
            $currentconfig = explode(',', $currentconfig);
        }

        $this->assertEqualsCanonicalizing($testdata, $currentconfig);
    }

    /**
     * Test that execute() method sets user preference when a component is passed.
     */
    public function test_execute_user(): void {
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $testdata = $this->generate_test_data();
        $this->testclass::execute($testdata);

        $userpreference = get_user_preferences('qbank_columnsortorder_' . $this->setting);
        if ($this->csv) {
            $userpreference = explode(',', $userpreference);
        }

        $this->assertEqualsCanonicalizing($testdata, $userpreference);
    }
}
