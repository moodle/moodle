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

namespace core_grades\output;

use core\output\renderer_base;
use core\output\templatable;
use core\output\renderable;
use grade_grade;

/**
 * The base class for the action bar in the gradebook pages.
 *
 * @package    core_grades
 * @copyright  2024 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class penalty_indicator implements renderable, templatable {
    /**
     * The class constructor.
     *
     * @param int $decimals the decimal places
     * @param grade_grade $grade user grade
     * @param bool $showfinalgrade whether to show the final grade (or show icon only)
     * @param bool $showgrademax whether to show the max grade
     * @param array|null $penaltyicon icon to show if penalty is applied
     */
    public function __construct(
        /** @var int $decimals the decimal places */
        protected int $decimals,

        /** @var grade_grade $grade user grade */
        protected grade_grade $grade,

        /** @var bool $showfinalgrade whether to show the final grade (or show icon only) */
        protected bool $showfinalgrade = false,

        /** @var bool $showgrademax whether to show the max grade */
        protected bool $showgrademax = false,

        /** @var array|null $penaltyicon icon to show if penalty is applied */
        protected ?array $penaltyicon = null
    ) {
    }

    /**
     * Returns the template for the actions bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'core_grades/penalty_indicator';
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $penalty = format_float($this->grade->deductedmark, $this->decimals);
        $finalgrade = $this->showfinalgrade ? format_float($this->grade->finalgrade , $this->decimals) : null;
        $grademax = $this->showgrademax ? format_float($this->grade->get_grade_max(), $this->decimals) : null;
        $icon = $this->penaltyicon ?: ['name' => 'i/risk_xss', 'component' => 'core'];
        $info = get_string('gradepenalty_indicator_info', 'core_grades', $penalty);

        $context = [
            'penalty' => $penalty,
            'finalgrade' => $finalgrade,
            'grademax' => $grademax,
            'icon' => $icon,
            'info' => $info,
        ];

        return $context;
    }
}
