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

use coding_exception;
use core_tag_tag;
use lang_string;
use MoodleQuickForm;
use stdClass;
use core_reportbuilder\local\helpers\database;

/**
 * Class containing logic for the tags filter
 *
 * The field SQL should be the field containing the ID of the {tag} table
 *
 * @package     core_reportbuilder
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags extends base {

    /** @var int Any value */
    public const ANY_VALUE = 0;

    /** @var int Tags are present */
    public const NOT_EMPTY = 1;

    /** @var int Filter for selected tags */
    public const EQUAL_TO = 2;

    /** @var int Tags are not present */
    public const EMPTY = 3;

    /** @var int Filter for excluded tags */
    public const NOT_EQUAL_TO = 4;

    /**
     * Returns an array of comparison operators
     *
     * @return array
     */
    private function get_operators(): array {
        $operators = [
            self::ANY_VALUE => new lang_string('filterisanyvalue', 'core_reportbuilder'),
            self::NOT_EMPTY => new lang_string('filterisnotempty', 'core_reportbuilder'),
            self::EMPTY => new lang_string('filterisempty', 'core_reportbuilder'),
            self::EQUAL_TO => new lang_string('filterisequalto', 'core_reportbuilder'),
            self::NOT_EQUAL_TO => new lang_string('filterisnotequalto', 'core_reportbuilder'),
        ];

        return $this->filter->restrict_limited_operators($operators);
    }

    /**
     * Setup form
     *
     * @param MoodleQuickForm $mform
     */
    public function setup_form(MoodleQuickForm $mform): void {
        global $DB;

        $operatorlabel = get_string('filterfieldoperator', 'core_reportbuilder', $this->get_header());
        $mform->addElement('select', "{$this->name}_operator", $operatorlabel, $this->get_operators())
            ->setHiddenLabel(true);

        $sql = 'SELECT DISTINCT t.id, t.name, t.rawname
                  FROM {tag} t
              ORDER BY t.name';

        // Transform tag records into appropriate display name, for selection in the autocomplete element.
        $tags = array_map(static function(stdClass $record): string {
            return core_tag_tag::make_display_name($record);
        }, $DB->get_records_sql($sql));

        $valuelabel = get_string('filterfieldvalue', 'core_reportbuilder', $this->get_header());
        $mform->addElement('autocomplete', "{$this->name}_value", $valuelabel, $tags, ['multiple' => true])
            ->setHiddenLabel(true);
        $mform->hideIf("{$this->name}_value", "{$this->name}_operator", 'in', [self::ANY_VALUE, self::EMPTY, self::NOT_EMPTY]);
    }

    /**
     * Return filter SQL
     *
     * @param array $values
     * @return array
     */
    public function get_sql_filter(array $values): array {
        global $DB;

        $fieldsql = $this->filter->get_field_sql();
        $params = $this->filter->get_field_params();

        $operator = (int) ($values["{$this->name}_operator"] ?? self::ANY_VALUE);
        $tags = (array) ($values["{$this->name}_value"] ?? []);

        if ($operator === self::NOT_EMPTY) {
            $select = "{$fieldsql} IS NOT NULL";
        } else if ($operator === self::EMPTY) {
            $select = "{$fieldsql} IS NULL";
        } else if ($operator === self::EQUAL_TO && !empty($tags)) {
            [$tagselect, $tagselectparams] = $DB->get_in_or_equal($tags, SQL_PARAMS_NAMED,
                database::generate_param_name() . '_');

            $select = "{$fieldsql} {$tagselect}";
            $params = array_merge($params, $tagselectparams);
        } else if ($operator === self::NOT_EQUAL_TO && !empty($tags)) {
            [$tagselect, $tagselectparams] = $DB->get_in_or_equal($tags, SQL_PARAMS_NAMED,
                database::generate_param_name() . '_', false);

            // We should also return those elements that aren't tagged at all.
            $select = "COALESCE({$fieldsql}, 0) {$tagselect}";
            $params = array_merge($params, $tagselectparams);
        } else {
            // Invalid/inactive (any value) filter..
            return ['', []];
        }

        return [$select, $params];
    }

    /**
     * Return sample filter values
     *
     * @return array
     */
    public function get_sample_values(): array {
        return [
            "{$this->name}_operator" => self::EQUAL_TO,
            "{$this->name}_value" => [1],
        ];
    }
}
