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

namespace qbank_statistics;

/**
 * Helper for statistics
 *
 * @package    qbank_statistics
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * @var float Threshold to determine 'Needs checking?'
     */
    private const NEED_FOR_REVISION_LOWER_THRESHOLD = 30;

    /**
     * @var float Threshold to determine 'Needs checking?'
     */
    private const NEED_FOR_REVISION_UPPER_THRESHOLD = 50;

    /**
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     */
    #[\core\attribute\deprecated(
        'statistics_bulk_loader or get_required_statistics_fields',
        since: '4.3',
        mdl: 'MDL-75576',
        final: true
    )]
    public static function calculate_average_question_facility(int $questionid): ?float {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     */
    #[\core\attribute\deprecated(
        'statistics_bulk_loader or get_required_statistics_fields',
        since: '4.3',
        mdl: 'MDL-75576',
        final: true
    )]
    public static function calculate_average_question_discriminative_efficiency(int $questionid): ?float {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * @deprecated since Moodle 4.3 please use the method from statistics_bulk_loader.
     */
    #[\core\attribute\deprecated(
        'statistics_bulk_loader or get_required_statistics_fields',
        since: '4.3',
        mdl: 'MDL-75576',
        final: true
    )]
    public static function calculate_average_question_discrimination_index(int $questionid): ?float {
        \core\deprecation::emit_deprecation_if_present([self::class, __FUNCTION__]);
    }

    /**
     * Format a number to a localised percentage with specified decimal points.
     *
     * @param float|null $number The number being formatted
     * @param bool $fraction An indicator for whether the number is a fraction or is already multiplied by 100
     * @param int $decimals Sets the number of decimal points
     * @return string
     * @throws \coding_exception
     */
    public static function format_percentage(?float $number, bool $fraction = true, int $decimals = 2): string {
        if (is_null($number)) {
            return get_string('na', 'qbank_statistics');
        }
        $coefficient = $fraction ? 100 : 1;
        return get_string('percents', 'moodle', format_float($number * $coefficient, $decimals));
    }

    /**
     * Format discrimination index (Needs checking?).
     *
     * @param float|null $value stats value
     * @return array
     */
    public static function format_discrimination_index(?float $value): array {
        if (is_null($value)) {
            $content = get_string('emptyvalue', 'qbank_statistics');
            $classes = '';
        } else if ($value < self::NEED_FOR_REVISION_LOWER_THRESHOLD) {
            $content = get_string('verylikely', 'qbank_statistics');
            $classes = 'alert-danger';
        } else if ($value < self::NEED_FOR_REVISION_UPPER_THRESHOLD) {
            $content = get_string('likely', 'qbank_statistics');
            $classes = 'alert-warning';
        } else {
            $content = get_string('unlikely', 'qbank_statistics');
            $classes = 'alert-success';
        }
        return [$content, $classes];
    }
}
