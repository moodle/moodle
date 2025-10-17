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

namespace core\check\environment;

use core\check\check;
use core\check\result;

/**
 * Verifies config.php is not writable anymore after installation
 *
 * @package    core
 * @category   check
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @copyright  2008 petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class configrw extends check {
    #[\Override]
    public function get_name(): string {
        return get_string('check_configrw_name', 'report_security');
    }

    #[\Override]
    public function get_result(): result {
        global $CFG;
        $details = get_string('check_configrw_details', 'report_security');

        $configfilepaths = [
            "{$CFG->root}/config.php",
            "{$CFG->dirroot}/config.php",
        ];

        foreach ($configfilepaths as $configfile) {
            if (is_writable($configfile)) {
                $status = result::WARNING;
                $summary = get_string('check_configrw_warning', 'report_security');
                return new result($status, $summary, $details);
            }
        }

        $status = result::OK;
        $summary = get_string('check_configrw_ok', 'report_security');
        return new result($status, $summary, $details);
    }
}
