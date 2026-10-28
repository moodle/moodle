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
 * Pure enrol planning for LaunchPad courses (no DB writes).
 *
 * Ensures manual enrolment is present/enabled; reports other enabled methods
 * without disabling them.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class enrol_plan {

    /** Matches ENROL_INSTANCE_ENABLED in enrollib.php. */
    public const STATUS_ENABLED = 0;

    /**
     * @param list<array{enrol: string, status: int}> $instances
     * @return array{
     *   create_manual: bool,
     *   enable_manual: bool,
     *   other_enabled: list<string>,
     *   lines: list<string>
     * }
     */
    public static function analyse(array $instances): array {
        $manual = null;
        $otherenabled = [];
        foreach ($instances as $instance) {
            $enrol = (string) $instance['enrol'];
            $status = (int) $instance['status'];
            if ($enrol === 'manual') {
                $manual = ['enrol' => $enrol, 'status' => $status];
                continue;
            }
            if ($status === self::STATUS_ENABLED) {
                $otherenabled[] = $enrol;
            }
        }

        $createmanual = ($manual === null);
        $enablemanual = ($manual !== null && $manual['status'] !== self::STATUS_ENABLED);

        $lines = [];
        if ($createmanual) {
            $lines[] = 'will create manual instance';
        } else if ($enablemanual) {
            $lines[] = 'will enable manual instance';
        } else {
            $lines[] = 'manual already enabled';
        }
        foreach ($otherenabled as $name) {
            $lines[] = 'other enabled method left unchanged: ' . $name;
        }

        return [
            'create_manual' => $createmanual,
            'enable_manual' => $enablemanual,
            'other_enabled' => $otherenabled,
            'lines' => $lines,
        ];
    }
}
