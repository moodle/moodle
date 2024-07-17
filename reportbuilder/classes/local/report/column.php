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

namespace core_reportbuilder\local\report;

use coding_exception;
use lang_string;
use core_reportbuilder\local\helpers\{aggregation, database, join_trait};
use core_reportbuilder\local\aggregation\base;
use core_reportbuilder\local\models\column as column_model;

/**
 * Class to represent a report column
 *
 * @package     core_reportbuilder
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class column {

    use join_trait;

    /** @var int Column type is integer */
    public const TYPE_INTEGER = 1;

    /** @var int Column type is text */
    public const TYPE_TEXT = 2;

    /** @var int Column type is timestamp */
    public const TYPE_TIMESTAMP = 3;

    /** @var int Column type is boolean */
    public const TYPE_BOOLEAN = 4;

    /** @var int Column type is float */
    public const TYPE_FLOAT = 5;

    /** @var int Column type is long text */
    public const TYPE_LONGTEXT = 6;

    /** @var int $index Column index within a report */
    private $index;

    /** @var bool $hascustomcolumntitle Used to store if the column has been given a custom title */
    private $hascustomcolumntitle = false;

    /** @var int $type Column data type (one of the TYPE_* class constants) */
    private $type = self::TYPE_TEXT;

    /** @var array $fields */
    private $fields = [];

    /** @var array $params  */
    private $params = [];

    /** @var string $groupbysql */
    private $groupbysql;

    /** @var array[] $callbacks Array of [callable, additionalarguments] */
    private $callbacks = [];

    /** @var base|null $aggregation Aggregation type to apply to column */
    private $aggregation = null;

    /** @var array $disabledaggregation Aggregation types explicitly disabled  */
    private $disabledaggregation = [];

    /** @var bool $issortable Used to indicate if a column is sortable */
    private $issortable = false;

    /** @var array $sortfields Fields to sort the column by */
    private $sortfields = [];

    /** @var array $attributes */
    private $attributes = [];

    /** @var bool $available Used to know if column is available to the current user or not */
    private $available = true;

    /** @var bool $deprecated */
    private $deprecated = false;

    /** @var string $deprecatedmessage */
    private $deprecatedmessage;

    /** @var column_model $persistent */
    private $persistent;

    /**
     * Column constructor
     *
     * For better readability use chainable methods, for example:
     *
     * $report->add_column(
     *    (new column('name', new lang_string('name'), 'user'))
     *    ->add_join('left join {table} t on t.id = p.tableid')
     *    ->add_field('t.name')
     *    ->add_callback([format::class, 'format_string']));
     *
     * @param string $name Internal name of the column
     * @param lang_string|null $title Title of the column used in reports (null for blank)
     * @param string $entityname Name of the entity this column belongs to. Typically when creating columns within entities
     *      this value should be the result of calling {@see get_entity_name}, however if creating columns inside reports directly
     *      it should be the name of the entity as passed to {@see \core_reportbuilder\local\report\base::annotate_entity}
     */
    public function __construct(
        /** @var string Internal name of the column */
        private string $name,
        /** @var lang_string|null Title of the column used in reports */
        private ?lang_string $title,
        /** @var string Name of the entity this column belongs to */
        private readonly string $entityname,
    ) {

    }

    /**
     * Set column name
     *
     * @param string $name
     * @return self
     */
    public function set_name(string $name): self {
        $this->name = $name;
        return $this;
    }

    /**
     * Return column name
     *
     * @return mixed
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Set column title
     *
     * @param lang_string|null $title
     * @return self
     */
    public function set_title(?lang_string $title): self {
        $this->title = $title;
        $this->hascustomcolumntitle = true;
        return $this;
    }

    /**
     * Return column title
     *
     * @return string
     */
    public function get_title(): string {
        return $this->title ? (string) $this->title : '';
    }

    /**
     * Check whether this column has been given a custom title
     *
     * @return bool
     */
    public function has_custom_title(): bool {
        return $this->hascustomcolumntitle;
    }

    /**
     * Get column entity name
     *
     * @return string
     */
    public function get_entity_name(): string {
        return $this->entityname;
    }


    /**
     * Return unique identifier for this column
     *
     * @return string
     */
    public function get_unique_identifier(): string {
        return $this->get_entity_name() . ':' . $this->get_name();
    }

    /**
     * Set the column index within the current report
     *
     * @param int $index
     * @return self
     */
    public function set_index(int $index): self {
        $this->index = $index;
        return $this;
    }

    /**
     * Set the column type, if not called then the type will be assumed to be {@see TYPE_TEXT}
     *
     * The type of a column is used to cast the first column field passed to any callbacks {@see add_callback} as well as the
     * aggregation options available for the column. It should represent how the column content is returned from callbacks
     *
     *
     * @param int $type
     * @return self
     * @throws coding_exception
     */
    public function set_type(int $type): self {
        $allowedtypes = [
            self::TYPE_INTEGER,
            self::TYPE_TEXT,
            self::TYPE_TIMESTAMP,
            self::TYPE_BOOLEAN,
            self::TYPE_FLOAT,
            self::TYPE_LONGTEXT,
        ];
        if (!in_array($type, $allowedtypes)) {
            throw new coding_exception('Invalid column type', $type);
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Return column type, that being one of the TYPE_* class constants
     *
     * @return int
     */
    public function get_type(): int {
        return $this->type;
    }

    /**
     * Adds a field to be queried from the database that is necessary for this column
     *
     * Multiple fields can be added per column, this method may be called several times. Field aliases must be unique inside
     * any given column, but there will be no conflicts if the same aliases are used in other columns in the same report
     *
     * @param string $sql SQL query, this may be a simple "tablealias.fieldname" or a complex sub-query that returns only one field
     * @param string $alias
     * @param array $params
     * @return self
     * @throws coding_exception
     */
    public function add_field(string $sql, string $alias = '', array $params = []): self {
        database::validate_params($params);

        // SQL ends with a space and a word - this looks like an alias was passed as part of the field.
        if (preg_match('/ \w+$/', $sql) && empty($alias)) {
            throw new coding_exception('Column alias must be passed as a separate argument', $sql);
        }

        // If no alias was specified, auto-detect it based on common patterns ("table.column" or just "column").
        if (empty($alias) && preg_match('/^(\w+\.)?(?<fieldname>\w+)$/', $sql, $matches)) {
            $alias = $matches['fieldname'];
        }

        if (empty($alias)) {
            throw new coding_exception('Complex columns must have an alias', $sql);
        }

        $this->fields[$alias] = $sql;
        $this->params += $params;

        return $this;
    }

    /**
     * Add a list of comma-separated fields
     *
     * @param string $sql
     * @param array $params
     * @return self
     */
    public function add_fields(string $sql, array $params = []): self {
        database::validate_params($params);

        // Split SQL into separate fields (separated by comma).
        $fields = preg_split('/\s*,\s*/', $sql);
        foreach ($fields as $field) {
            // Split each field into expression, <field> <as> <alias> where "as" and "alias" are optional.
            $fieldparts = preg_split('/\s+/', $field);

            if (count($fieldparts) == 2 || (count($fieldparts) == 3 && strtolower($fieldparts[1]) === 'as')) {
                $sql = reset($fieldparts);
                $alias = array_pop($fieldparts);
                $this->add_field($sql, $alias);
            } else {
                $this->add_field($field);
            }
        }

        $this->params += $params;

        return $this;
    }

    /**
     * Given a param name, add a unique prefix to ensure that the same column with params can be added multiple times to a report
     *
     * @param string $name
     * @return string
     */
    private function unique_param_name(string $name): string {
        return "p{$this->index}_{$name}";
    }

    /**
     * Helper method to take all fields added to the column, and return appropriate SQL and alias
     *
     * @return array[]
     */
    private function get_fields_sql_alias(): array {
        $fields = [];

        foreach ($this->fields as $alias => $sql) {

            // Ensure parameter names within SQL are prefixed with column index.
            $params = array_keys($this->params);
            $sql = database::sql_replace_parameter_names($sql, $params, function(string $param): string {
                return $this->unique_param_name($param);
            });

            $fields[$alias] = [
                'sql' => $sql,
                'alias' => substr("c{$this->index}_{$alias}", 0, 30),
            ];
        }

        return $fields;
    }

    /**
     * Return array of SQL expressions for each field of this column
     *
     * @return array
     */
    public function get_fields(): array {
        $fieldsalias = $this->get_fields_sql_alias();

        if (!empty($this->aggregation)) {
            $fieldsaliassql = array_column($fieldsalias, 'sql');
            $field = reset($fieldsalias);

            // If aggregating the column, generate SQL from column fields and use it to generate aggregation SQL.
            $columnfieldsql = $this->aggregation::get_column_field_sql($fieldsaliassql);
            $aggregationfieldsql = $this->aggregation::get_field_sql($columnfieldsql, $this->get_type());

            $fields = ["{$aggregationfieldsql} AS {$field['alias']}"];
        } else {
            $fields = array_map(static function(array $field): string {
                return "{$field['sql']} AS {$field['alias']}";
            }, $fieldsalias);
        }

        return array_values($fields);
    }

    /**
     * Return column parameters, prefixed by the current index to allow the column to be added multiple times to a report
     *
     * @return array
     */
    public function get_params(): array {
        $params = [];

        foreach ($this->params as $name => $value) {
            $paramname = $this->unique_param_name($name);
            $params[$paramname] = $value;
        }

        return $params;
    }

    /**
     * Return an alias for this column (the generated alias of it's first field)
     *
     * @return string
     * @throws coding_exception
     */
    public function get_column_alias(): string {
        if (!$fields = $this->get_fields_sql_alias()) {
            throw new coding_exception('Column ' . $this->get_unique_identifier() . ' contains no fields');
        }

        return reset($fields)['alias'];
    }

    /**
     * Define suitable SQL fragment for grouping by the columns fields. This will be returned from {@see get_groupby_sql} if set
     *
     * @param string $groupbysql
     * @return self
     */
    public function set_groupby_sql(string $groupbysql): self {
        $this->groupbysql = $groupbysql;
        return $this;
    }

    /**
     * Return suitable SQL fragment for grouping by the column fields (during aggregation)
     *
     * @return array
     */
    public function get_groupby_sql(): array {
        global $DB;

        // Return defined value if it's already been set during column definition.
        if (!empty($this->groupbysql)) {
            return [$this->groupbysql];
        }

        $fieldsalias = $this->get_fields_sql_alias();

        // Note that we can reference field aliases in GROUP BY only in MySQL/Postgres.
        $usealias = in_array($DB->get_dbfamily(), ['mysql', 'postgres']);
        $columnname = $usealias ? 'alias' : 'sql';

        return array_column($fieldsalias, $columnname);
    }

    /**
     * Adds column callback (in the case there are multiple, they will be called iteratively - the result of each passed
     * along to the next in the chain)
     *
     * The callback should implement the following signature (where $value is the first column field, $row is all column
     * fields, $additionalarguments are those passed to this method, and $aggregation indicates the current aggregation type
     * being applied to the column):
     *
     * function($value, stdClass $row, $additionalarguments, ?string $aggregation): string
     *
     * The type of the $value parameter passed to the callback is determined by calling {@see set_type}, this type is preserved
     * if the column is part of a report source and is being aggregated. For entities that can be left joined to a report, the
     * first argument of the callback must be nullable (as it should also be if the first column field is itself nullable).
     *
     * @param callable $callable
     * @param mixed $additionalarguments
     * @return self
     */
    public function add_callback(callable $callable, $additionalarguments = null): self {
        $this->callbacks[] = [$callable, $additionalarguments];
        return $this;
    }

    /**
     * Sets column callback. This will overwrite any previously added callbacks {@see add_callback}
     *
     * @param callable $callable
     * @param mixed $additionalarguments
     * @return self
     */
    public function set_callback(callable $callable, $additionalarguments = null): self {
        $this->callbacks = [];
        return $this->add_callback($callable, $additionalarguments);
    }

    /**
     * Set column aggregation type
     *
     * @param string|null $aggregation Type of aggregation, e.g. 'sum', 'count', etc
     * @return self
     * @throws coding_exception For invalid aggregation type, or one that is incompatible with column type
     */
    public function set_aggregation(?string $aggregation): self {
        if (!empty($aggregation)) {
            $aggregation = aggregation::get_full_classpath($aggregation);
            if (!aggregation::valid($aggregation) || !$aggregation::compatible($this->get_type())) {
                throw new coding_exception('Invalid column aggregation', $aggregation);
            }
        }

        $this->aggregation = $aggregation;
        return $this;
    }

    /**
     * Get column aggregation type
     *
     * @return base|null
     */
    public function get_aggregation(): ?string {
        return $this->aggregation;
    }

    /**
     * Set disabled aggregation methods for the column. Typically only those methods suitable for the current column type are
     * available: {@see aggregation::get_column_aggregations}, however in some cases we may want to disable specific methods
     *
     * @param array $disabledaggregation Array of types, e.g. ['min', 'sum']
     * @return self
     */
    public function set_disabled_aggregation(array $disabledaggregation): self {
        $this->disabledaggregation = $disabledaggregation;
        return $this;
    }

    /**
     * Disable all aggregation methods for the column, for instance when current database can't aggregate fields that contain
     * sub-queries
     *
     * @return self
     */
    public function set_disabled_aggregation_all(): self {
        $aggregationnames = array_map(static function(string $aggregation): string {
            return $aggregation::get_class_name();
        }, aggregation::get_aggregations());

        return $this->set_disabled_aggregation($aggregationnames);
    }

    /**
     * Return those aggregations methods explicitly disabled for the column
     *
     * @return array
     */
    public function get_disabled_aggregation(): array {
        return $this->disabledaggregation;
    }

    /**
     * Sets the column as sortable
     *
     * @param bool $issortable
     * @param array $sortfields Define the fields that should be used when the column is sorted, typically a subset of the fields
     *      selected for the column, via {@see add_field}. If omitted then the first selected field is used
     * @return self
     */
    public function set_is_sortable(bool $issortable, array $sortfields = []): self {
        $this->issortable = $issortable;
        $this->sortfields = $sortfields;
        return $this;
    }

    /**
     * Return sortable status of column
     *
     * @return bool
     */
    public function get_is_sortable(): bool {

        // Defer sortable status to aggregation type if column is being aggregated.
        if (!empty($this->aggregation)) {
            return $this->aggregation::sortable($this->issortable);
        }

        return $this->issortable;
    }

    /**
     * Return fields to use for sorting of the column, where available the field aliases will be returned
     *
     * @return array
     */
    public function get_sort_fields(): array {
        $fieldsalias = $this->get_fields_sql_alias();

        return array_map(static function(string $sortfield) use ($fieldsalias): string {

            // Check whether sortfield refers to a defined field alias.
            if (array_key_exists($sortfield, $fieldsalias)) {
                return $fieldsalias[$sortfield]['alias'];
            }

            // Check whether sortfield refers to field SQL.
            foreach ($fieldsalias as $field) {
                if (strcasecmp($sortfield, $field['sql']) === 0) {
                    $sortfield = $field['alias'];
                    break;
                }
            }

            return $sortfield;
        }, $this->sortfields);
    }

    /**
     * Extract all values from given row for this column
     *
     * @param array $row
     * @return array
     */
    private function get_values(array $row): array {
        $values = [];

        // During aggregation we only get a single alias back, subsequent aliases won't exist.
        foreach ($this->get_fields_sql_alias() as $alias => $field) {
            $values[$alias] = $row[$field['alias']] ?? null;
        }

        return $values;
    }

    /**
     * Return the default column value, that being the value of it's first field
     *
     * @param array $values
     * @param int $columntype
     * @return mixed
     */
    public static function get_default_value(array $values, int $columntype) {
        $value = reset($values);
        if ($value === null) {
            return $value;
        }

        // Ensure default value is cast to it's strict type.
        switch ($columntype) {
            case self::TYPE_INTEGER:
            case self::TYPE_TIMESTAMP:
                $value = (int) $value;
                break;
            case self::TYPE_FLOAT:
                $value = (float) $value;
                break;
            case self::TYPE_BOOLEAN:
                $value = (bool) $value;
                break;
        }

        return $value;
    }

    /**
     * Return column value based on complete table row
     *
     * @param array $row
     * @return mixed
     */
    public function format_value(array $row) {
        $values = $this->get_values($row);
        $value = self::get_default_value($values, $this->get_type());

        // If column is being aggregated then defer formatting to them, otherwise loop through all column callbacks.
        if (!empty($this->aggregation)) {
            $value = $this->aggregation::format_value($value, $values, $this->callbacks, $this->get_type());
        } else {
            foreach ($this->callbacks as $callback) {
                [$callable, $arguments] = $callback;
                $value = ($callable)($value, (object) $values, $arguments, null);
            }
        }

        return $value;
    }

    /**
     * Add column attributes (data-, class, etc.) that will be included in HTML when column is displayed
     *
     * @param array $attributes
     * @return self
     */
    public function add_attributes(array $attributes): self {
        $this->attributes = $attributes + $this->attributes;
        return $this;
    }

    /**
     * Returns the column HTML attributes
     *
     * @return array
     */
    public function get_attributes(): array {
        return $this->attributes;
    }

    /**
     * Return available state of the column for the current user. For instance the column may be added to a report with the
     * expectation that only some users are able to see it
     *
     * @return bool
     */
    public function get_is_available(): bool {
        return $this->available;
    }

    /**
     * Conditionally set whether the column is available.
     *
     * @param bool $available
     * @return self
     */
    public function set_is_available(bool $available): self {
        $this->available = $available;
        return $this;
    }

    /**
     * Set deprecated state of the column, in which case it will still be shown when already present in existing reports but
     * won't be available for selection in the report editor
     *
     * @param string $deprecatedmessage
     * @return self
     */
    public function set_is_deprecated(string $deprecatedmessage = ''): self {
        $this->deprecated = true;
        $this->deprecatedmessage = $deprecatedmessage;
        return $this;
    }

    /**
     * Return deprecated state of the column
     *
     * @return bool
     */
    public function get_is_deprecated(): bool {
        return $this->deprecated;
    }

    /**
     * Return deprecated message of the column
     *
     * @return string
     */
    public function get_is_deprecated_message(): string {
        return $this->deprecatedmessage;
    }

    /**
     * Set column persistent
     *
     * @param column_model $persistent
     * @return self
     */
    public function set_persistent(column_model $persistent): self {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * Return column persistent
     *
     * @return mixed
     */
    public function get_persistent(): column_model {
        return $this->persistent;
    }
}
