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

namespace factor_cohort;

/**
 * Tests for cohort factor.
 *
 * @covers      \factor_cohort\factor
 * @package     factor_cohort
 * @copyright   2023 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor_test extends \advanced_testcase {

    /**
     * Tests getting the summary condition
     *
     * @covers ::get_summary_condition
     * @covers ::get_cohorts
     */
    public function test_get_summary_condition() {
        $this->resetAfterTest();

        set_config('enabled', 1, 'factor_cohort');
        $cohortfactor = \tool_mfa\plugininfo\factor::get_factor('cohort');

        $cohort = $this->getDataGenerator()->create_cohort();
        $userassignover = $this->getDataGenerator()->create_user();
        cohort_add_member($cohort->id, $userassignover->id);

        // Add the created cohortid into factor_cohort plugin.
        set_config('cohorts', $cohort->id, 'factor_cohort');

        $selectedcohorts = get_config('factor_cohort', 'cohorts');
        $selectedcohorts = $cohortfactor->get_cohorts(explode(',', $selectedcohorts));
        $this->assertArrayHasKey($cohort->id, $selectedcohorts);
        $this->assertStringContainsString(
            implode(', ', $selectedcohorts),
            $cohortfactor->get_summary_condition()
        );
    }
}
