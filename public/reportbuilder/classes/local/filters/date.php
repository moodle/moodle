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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use core\{clock, di};
use core\lang_string;
use core_reportbuilder\local\helpers\database;
use MoodleQuickForm;

/**
 * Date report filter
 *
 * This filter accepts a unix timestamp to perform date filtering on
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date extends base {

    /** @var int Any value */
    public const DATE_ANY = 0;

    /** @var int Non-empty (positive) value */
    public const DATE_NOT_EMPTY = 1;

    /** @var int Empty (zero) value */
    public const DATE_EMPTY = 2;

    /** @var int Date within defined range */
    public const DATE_RANGE = 3;

    /** @var int Date in the last [X relative date unit(s)] */
    public const DATE_LAST = 4;

    /** @var int Date in the previous [X relative date unit(s)] Kept for backwards compatibility */
    public const DATE_PREVIOUS = self::DATE_LAST;

    /** @var int Date in current [relative date unit] */
    public const DATE_CURRENT = 5;

    /** @var int Date in the next [X relative date unit(s)] */
    public const DATE_NEXT = 6;

    /** @var int Date in the past */
    public const DATE_PAST = 7;

    /** @var int Date in the future */
    public const DATE_FUTURE = 8;

    /** @var int Date before [X relative date unit(s)] */
    public const DATE_BEFORE = 9;

    /** @var int Date after [X relative date unit(s)] */
    public const DATE_AFTER = 10;

    /** @var int Relative date unit for a minute */
    public const DATE_UNIT_MINUTE = 5;

    /** @var int Relative date unit for an hour */
    public const DATE_UNIT_HOUR = 0;

    /** @var int Relative date unit for a day */
    public const DATE_UNIT_DAY = 1;

    /** @var int Relative date unit for a week */
    public const DATE_UNIT_WEEK = 2;

    /** @var int Relative date unit for a month */
    public const DATE_UNIT_MONTH = 3;

    /** @var int Relative date unit for a month */
    public const DATE_UNIT_YEAR = 4;

    /**
     * Return an array of operators available for this filter
     *
     * @return lang_string[]
     */
    private function get_operators(): array {
        $operators = [
            self::DATE_ANY => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::DATE_NOT_EMPTY => new lang_string('filterisnotempty', 'core_reportbuilder'),
            self::DATE_EMPTY => new lang_string('filterisempty', 'core_reportbuilder'),
            self::DATE_RANGE => new lang_string('filterrange', 'core_reportbuilder'),
            self::DATE_BEFORE => new lang_string('filterdatebefore', 'core_reportbuilder'),
            self::DATE_AFTER => new lang_string('filterdateafter', 'core_reportbuilder'),
            self::DATE_LAST => new lang_string('filterdatelast', 'core_reportbuilder'),
            self::DATE_CURRENT => new lang_string('filterdatecurrent', 'core_reportbuilder'),
            self::DATE_NEXT => new lang_string('filterdatenext', 'core_reportbuilder'),
            self::DATE_PAST => new lang_string('filterdatepast', 'core_reportbuilder'),
            self::DATE_FUTURE => new lang_string('filterdatefuture', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * Note that we cannot support float inputs in this filter currently, because decimals are not supported when calculating
     * relative timeframes according to {@link https://www.php.net/manual/en/datetime.formats.php}
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        // Operator selector.
        $operatorlabel = get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header());
        $typesnounit = [self::DATE_ANY, self::DATE_NOT_EMPTY, self::DATE_EMPTY, self::DATE_RANGE,
            self::DATE_PAST, self::DATE_FUTURE];

        $elements[] = $mform->createElement('select', "{$this->name}_operator", $operatorlabel, $this->get_operators());
        $mform->setType("{$this->name}_operator", PARAM_INT);
        $mform->setDefault("{$this->name}_operator", self::DATE_ANY);

        // Value selector for last and next operators.
        $valuelabel = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());

        $elements[] = $mform->createElement('text', "{$this->name}_value", $valuelabel, ['size' => 3]);
        $mform->setType("{$this->name}_value", PARAM_INT);
        $mform->setDefault("{$this->name}_value", 1);
        $mform->hideIf("{$this->name}_value", "{$this->name}_operator", 'in', array_merge($typesnounit, [self::DATE_CURRENT]));

        // Unit selector for last and next operators.
        $unitlabel = get_string('filterfieldunit', 'core_reportbuilder', $this->get_header());
        $units = [
            self::DATE_UNIT_MINUTE => get_string('filterdateminutes', 'core_reportbuilder'),
            self::DATE_UNIT_HOUR => get_string('filterdatehours', 'core_reportbuilder'),
            self::DATE_UNIT_DAY => get_string('filterdatedays', 'core_reportbuilder'),
            self::DATE_UNIT_WEEK => get_string('filterdateweeks', 'core_reportbuilder'),
            self::DATE_UNIT_MONTH => get_string('filterdatemonths', 'core_reportbuilder'),
            self::DATE_UNIT_YEAR => get_string('filterdateyears', 'core_reportbuilder'),
        ];

        $elements[] = $mform->createElement('select', "{$this->name}_unit", $unitlabel, $units);
        $mform->setType("{$this->name}_unit", PARAM_INT);
        $mform->setDefault("{$this->name}_unit", self::DATE_UNIT_DAY);
        $mform->hideIf("{$this->name}_unit", "{$this->name}_operator", 'in', $typesnounit);

        // Add operator/value/unit group.
        $mform->addGroup($elements, "{$this->name}_group", $this->get_header(), '', false)
            ->setHiddenLabel(true);

        // Date selectors for range operator.
        $mform->addElement('date_time_selector', "{$this->name}_from",
            get_string('filterfieldfrom', 'core_reportbuilder', $this->get_header()), ['optional' => true]);
        $mform->setType("{$this->name}_from", PARAM_INT);
        $mform->setDefault("{$this->name}_from", 0);
        $mform->hideIf("{$this->name}_from", "{$this->name}_operator", 'neq', self::DATE_RANGE);

        $mform->addElement('date_time_selector', "{$this->name}_to",
            get_string('filterfieldto', 'core_reportbuilder', $this->get_header()), ['optional' => true]);
        $mform->setType("{$this->name}_to", PARAM_INT);
        $mform->setDefault("{$this->name}_to", 0);
        $mform->hideIf("{$this->name}_to", "{$this->name}_operator", 'neq', self::DATE_RANGE);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $operator = (int) ($values["{$this->name}_operator"] ?? self::DATE_ANY);
        $dateunitvalue = (int) ($values["{$this->name}_value"] ?? 1);
        $dateunit = (int) ($values["{$this->name}_unit"] ?? self::DATE_UNIT_DAY);

        switch ($operator) {
            case self::DATE_NOT_EMPTY:
                $sql = "COALESCE({$fieldsql}, 0) <> 0";
                break;
            case self::DATE_EMPTY:
                $sql = "COALESCE({$fieldsql}, 0) = 0";
                break;
            case self::DATE_RANGE:
                $sql = '';

                $datefrom = (int)($values["{$this->name}_from"] ?? 0);
                $dateto = (int)($values["{$this->name}_to"] ?? 0);

                [$paramdatefrom, $paramdateto] = database::generate_param_names(2);

                if ($datefrom > 0 && $dateto > 0) {
                    $sql = "{$fieldsql} BETWEEN :{$paramdatefrom} AND :{$paramdateto}";
                    $params[$paramdatefrom] = $datefrom;
                    $params[$paramdateto] = $dateto;
                } else if ($datefrom > 0) {
                    $sql = "{$fieldsql} >= :{$paramdatefrom}";
                    $params[$paramdatefrom] = $datefrom;
                } else if ($dateto > 0) {
                    $sql = "{$fieldsql} < :{$paramdateto}";
                    $params[$paramdateto] = $dateto;
                }

                break;
            case self::DATE_BEFORE:
                $param = database::generate_param_name();

                // We can use the start date of the "Last" operator as the end date here.
                $sql = "{$fieldsql} < :{$param}";
                $params[$param] = self::get_relative_timeframe(self::DATE_LAST, $dateunitvalue, $dateunit)[0];
                break;
            case self::DATE_AFTER:
                $param = database::generate_param_name();

                // We can use the end date of the "Next" operator as the start date here.
                $sql = "{$fieldsql} > :{$param}";
                $params[$param] = self::get_relative_timeframe(self::DATE_NEXT, $dateunitvalue, $dateunit)[1];
                break;
            // Relative helper method can handle these three cases.
            case self::DATE_LAST:
            case self::DATE_CURRENT:
            case self::DATE_NEXT:

                // Last and next operators require a unit value greater than zero.
                if ($operator !== self::DATE_CURRENT && $dateunitvalue === 0) {
                    return ['', []];
                }

                // Generate parameters and SQL clause for the relative date comparison.
                [$paramdatefrom, $paramdateto] = database::generate_param_names(2);
                $sql = "{$fieldsql} BETWEEN :{$paramdatefrom} AND :{$paramdateto}";

                [
                    $params[$paramdatefrom],
                    $params[$paramdateto],
                ] = self::get_relative_timeframe($operator, $dateunitvalue, $dateunit);

                break;
            case self::DATE_PAST:
                $param = database::generate_param_name();
                $sql = "{$fieldsql} < :{$param}";
                $params[$param] = di::get(clock::class)->time();
                break;
            case self::DATE_FUTURE:
                $param = database::generate_param_name();
                $sql = "{$fieldsql} > :{$param}";
                $params[$param] = di::get(clock::class)->time();
                break;
            default:
                // Invalid or inactive filter.
                return ['', []];
        }

        return [$sql, $params];
    }

    /**
     * Return start and end time of given relative date period
     *
     * @param int $operator One of the ::DATE_LAST/CURRENT/NEXT constants
     * @param int $dateunitvalue Unit multiplier of the date unit
     * @param int $dateunit One of the ::DATE_UNIT_* constants
     * @return int[] Timestamps representing the start/end of timeframe
     */
    private static function get_relative_timeframe(int $operator, int $dateunitvalue, int $dateunit): array {
        // Initialise start/end time to now.
        $datestart = $dateend = di::get(clock::class)->now();

        switch ($dateunit) {
            case self::DATE_UNIT_MINUTE:
                if ($operator === self::DATE_CURRENT) {
                    $hour = (int) $datestart->format('G');
                    $minute = (int) $datestart->format('i');
                    $datestart = $datestart->setTime($hour, $minute);
                    $dateend = $dateend->setTime($hour, $minute, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} minute");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} minute");
                }
                break;
            case self::DATE_UNIT_HOUR:
                if ($operator === self::DATE_CURRENT) {
                    $hour = (int) $datestart->format('G');
                    $datestart = $datestart->setTime($hour, 0);
                    $dateend = $dateend->setTime($hour, 59, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} hour");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} hour");
                }
                break;
            case self::DATE_UNIT_DAY:
                if ($operator === self::DATE_CURRENT) {
                    $datestart = $datestart->setTime(0, 0);
                    $dateend = $dateend->setTime(23, 59, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} day");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} day");
                }

                break;
            case self::DATE_UNIT_WEEK:
                if ($operator === self::DATE_CURRENT) {
                    // The first day of the week is determined by site calendar configuration/preferences.
                    $startweekday = \core_calendar\type_factory::get_calendar_instance()->get_starting_weekday();
                    $weekdays = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];

                    // If calculated start of week is after today (today is Tues/start of week is Weds), move back a week.
                    $datestartnow = $datestart->getTimestamp();
                    $datestart = $datestart->modify($weekdays[$startweekday] . ' this week')->setTime(0, 0);
                    if ($datestart->getTimestamp() > $datestartnow) {
                        $datestart = $datestart->modify('-1 week');
                    }

                    $dateend = $datestart->modify('+6 day')->setTime(23, 59, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} week");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} week");
                }

                break;
            case self::DATE_UNIT_MONTH:
                if ($operator === self::DATE_CURRENT) {
                    $datestart = $datestart->modify('first day of this month')->setTime(0, 0);
                    $dateend = $dateend->modify('last day of this month')->setTime(23, 59, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} month");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} month");
                }

                break;
            case self::DATE_UNIT_YEAR:
                if ($operator === self::DATE_CURRENT) {
                    $datestart = $datestart->modify('first day of january this year')->setTime(0, 0);
                    $dateend = $dateend->modify('last day of december this year')->setTime(23, 59, 59);
                } else if ($operator === self::DATE_LAST) {
                    $datestart = $datestart->modify("-{$dateunitvalue} year");
                } else if ($operator === self::DATE_NEXT) {
                    $dateend = $dateend->modify("+{$dateunitvalue} year");
                }

                break;
        }

        return [
            $datestart->getTimestamp(),
            $dateend->getTimestamp(),
        ];
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_operator" => self::DATE_CURRENT,
            "{$this->name}_unit" => self::DATE_UNIT_WEEK,
        ];
    }
}
