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

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Behat steps for qbank_statistics
 *
 * @package   qbank_statistics
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qbank_statistics extends behat_base {

        /**
         * Run pending recalculation tasks.
         *
         * This runs the recalcuation ad-hoc tasks. We need a special step for this
         * as the run time for these tasks is set to an hour in the future, so
         * "I run all ad-hoc tasks" will not trigger them.
         *
         * @Given /^I run pending statistics recalculation tasks$/
         */
        function i_run_pending_statistics_recalculation_tasks() {
            \quiz_statistics\tests\statistics_helper::run_pending_recalculation_tasks();
        }
}