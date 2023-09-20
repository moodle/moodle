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

namespace qbank_columnsortorder;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

use context_system;
use core_question\local\bank\column_base;
use core_question\local\bank\column_manager_base;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use qbank_columnsortorder\local\bank\column_action_move;
use qbank_columnsortorder\local\bank\column_action_remove;
use qbank_columnsortorder\local\bank\column_action_resize;
use qbank_columnsortorder\local\bank\preview_view;
use moodle_url;

/**
 * Class column_manager responsible for loading and saving order to the config setting.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_manager extends column_manager_base {
    /**
     * @var array Column order as set in config_plugins 'class' => 'position', ie: question_type_column => 3.
     */
    public $columnorder;

    /**
     * @var array hidden columns.
     */
    public $hiddencolumns;

    /**
     * @var array columns with size.
     */
    public $colsize;

    /**
     * @var array Disabled columns in config_plugins table.
     */
    public $disabledcolumns;

    /**
     * Constructor for column_manager class.
     *
     * @param bool $globalsettings Only use the global default settings, ignoring user preferences?
     */
    public function __construct(bool $globalsettings = false) {
        $this->columnorder = $this->setup_property('enabledcol', $globalsettings);
        if (empty($this->columnorder)) {
            $this->columnorder = [
                'core_question\local\bank\checkbox_column' . column_base::ID_SEPARATOR . 'checkbox_column',
                'qbank_viewquestiontype\question_type_column' . column_base::ID_SEPARATOR . 'question_type_column',
                'qbank_viewquestionname\question_name_idnumber_tags_column' . column_base::ID_SEPARATOR .
                'question_name_idnumber_tags_column',
                    'core_question\local\bank\edit_menu_column' . column_base::ID_SEPARATOR . 'edit_menu_column',
                'qbank_editquestion\question_status_column' . column_base::ID_SEPARATOR . 'question_status_column',
                'qbank_history\version_number_column' . column_base::ID_SEPARATOR . 'version_number_column',
                'qbank_viewcreator\creator_name_column' . column_base::ID_SEPARATOR . 'creator_name_column',
                'qbank_comment\comment_count_column' . column_base::ID_SEPARATOR . 'comment_count_column',
            ];
        }
        $this->hiddencolumns = $this->setup_property('hiddencols', $globalsettings);
        $this->colsize = $this->setup_property('colsize', $globalsettings, 'json');
        $this->disabledcolumns = $this->setup_property('disabledcol', true); // No user preference for disabledcol.

        if ($this->columnorder) {
            $this->columnorder = array_flip($this->columnorder);
        }
        if ($this->disabledcolumns) {
            $this->disabledcolumns = array_flip($this->disabledcolumns);
        }
    }

    /**
     * Return the value for the given property, based the saved user preference or config setting.
     *
     * If no value is currently stored, returns an empty array.
     *
     * @param string $setting The identifier used for the saved config and user preference settings.
     * @param bool $global Only get the global default, ignoring the user preference?
     * @param string $encoding The encoding used to store the property - csv or json
     * @return array
     */
    private function setup_property(string $setting, bool $global = false, $encoding = 'csv'): array {
        $value = get_config('qbank_columnsortorder', $setting);
        if (!$global) {
            $value = get_user_preferences("qbank_columnsortorder_{$setting}", $value);
        }
        if (empty($value)) {
            return [];
        }
        return $encoding == 'csv' ? explode(',', $value) : json_decode($value);
    }

    /**
     * Sets column order in the qbank_columnsortorder plugin config.
     *
     * @param ?array $columns Column order to set. Null value clears the setting.
     * @param bool $global save this as a global default, rather than a user preference?
     */
    public static function set_column_order(?array $columns, bool $global = false) : void {
        if (!is_null($columns)) {
            $columns = implode(',', $columns);
        }
        self::save_preference('enabledcol', $columns, $global);
    }

    /**
     * Hidden Columns.
     *
     * @param ?array $columns List of hidden columns. Null value clears the setting.
     * @param bool $global save this as a global default, rather than a user preference?
     */
    public static function set_hidden_columns(?array $columns, bool $global = false) : void {
        if (!is_null($columns)) {
            $columns = implode(',', $columns);
        }
        self::save_preference('hiddencols', $columns, $global);
    }

    /**
     * Column size.
     *
     * @param ?string $sizes columns with width. Null value clears the setting.
     * @param bool $global save this as a global default, rather than a user preference?
     */
    public static function set_column_size(?string $sizes, bool $global = false) : void {
        self::save_preference('colsize', $sizes, $global);
    }

    /**
     * Save Preferences.
     *
     * @param string $name name of a configuration
     * @param ?string $value value of a configuration. Null value clears the setting.
     * @param bool $global save this as a global default, rather than a user preference?
     */
    private static function save_preference(string $name, ?string $value, bool $global = false): void {
        if ($global) {
            require_capability('moodle/site:config', context_system::instance());
            set_config($name, $value, 'qbank_columnsortorder');
        } else {
            set_user_preference("qbank_columnsortorder_{$name}", $value);
        }
    }

    /**
     * Get qbank.
     *
     * @return view
     */
    public function get_questionbank(): view {
        $course = (object) ['id' => 0];
        $context = context_system::instance();
        $contexts = new question_edit_contexts($context);
        $category = question_make_default_categories($contexts->all());
        $params = ['cat' => $category->id . ',' . $context->id];
        // Dummy call to get the objects without error.
        $questionbank = new preview_view(
            $contexts,
            new moodle_url('/question/bank/columnsortorder/sortcolumns.php'),
            $course,
            null,
            $params
        );
        return $questionbank;
    }

    /**
     * Get enabled columns.
     *
     * @return array
     */
    public function get_columns(): array {
        $columns = [];
        foreach ($this->get_questionbank()->get_visiblecolumns() as $key => $column) {
            if ($column->get_name() === 'checkbox') {
                continue;
            }
            $columns[] = (object) [
                'class' => get_class($column),
                'name' => $column->get_title(),
                'colname' => $column->get_column_name(),
                'id' => $column->get_column_id(),
            ];
        }
        return $columns;
    }

    /**
     * Get disabled columns.
     *
     * @return array
     */
    public function get_disabled_columns(): array {
        $disabled = [];
        if ($this->disabledcolumns) {
            foreach (array_keys($this->disabledcolumns) as $column) {
                [$classname, $columnname] = explode(column_base::ID_SEPARATOR, $column, 2);
                $columnobject = $classname::from_column_name($this->get_questionbank(), $columnname);
                $disabled[$column] = (object) [
                    'disabledname' => $columnobject->get_title(),
                ];
            }
        }
        return $disabled;
    }

    /**
     * Updates enabled and disabled config for 'qbank_columnsortorder' plugin.
     *
     * @param array $enabledcolumns Enabled columns to set.
     * @param array $disabledcolumns Disabled columns to set.
     */
    protected function update_config($enabledcolumns, $disabledcolumns): void {
        if (!empty($enabledcolumns)) {
            $configenabled = implode(',', array_flip($enabledcolumns));
            set_config('enabledcol', $configenabled, 'qbank_columnsortorder');
        }
        if (!empty($disabledcolumns)) {
            $configdisabled = implode(',', array_flip($disabledcolumns));
            set_config('disabledcol', $configdisabled, 'qbank_columnsortorder');
        } else {
            set_config('disabledcol', null, 'qbank_columnsortorder');
        }
    }

    /**
     * Enables columns.
     *
     * @param string $plugin Plugin type and name ie: qbank_viewcreator.
     */
    public function enable_columns(string $plugin): void {
        $enabledcolumns = [];
        $disabledcolumns = [];
        if ($this->columnorder) {
            $enabledcolumns = $this->columnorder;
        }
        if ($this->disabledcolumns) {
            $disabledcolumns = $this->disabledcolumns;
            foreach ($disabledcolumns as $class => $column) {
                if (strpos($class, $plugin) !== false) {
                    $enabledcolumns[$class] = $class;
                    if (isset($disabledcolumns[$class])) {
                        unset($disabledcolumns[$class]);
                    }
                }
            }
        }
        $this->update_config($enabledcolumns, $disabledcolumns);
    }

    /**
     * Disables columns.
     *
     * @param string $plugin Plugin type and name ie: qbank_viewcreator.
     */
    public function disable_columns(string $plugin): void {
        $disabledcolumns = [];
        $enabledcolumns = [];
        $allcolumns = $this->get_columns();
        if ($this->disabledcolumns) {
            $disabledcolumns = $this->disabledcolumns;
        }
        if ($this->columnorder) {
            $enabledcolumns = $this->columnorder;
        }

        foreach ($allcolumns as $column) {
            if (str_contains($column->class, $plugin)) {
                $disabledcolumns[$column->id] = $column->id;
                if (isset($enabledcolumns[$column->id])) {
                    unset($enabledcolumns[$column->id]);
                }
            }
        }
        $this->update_config($enabledcolumns, $disabledcolumns);
    }

    /**
     * Orders columns in the question bank view according to config_plugins table 'qbank_columnsortorder' config.
     *
     * @param array $ordertosort Unordered array of columns, [columnname => class]
     * @return array $properorder|$ordertosort Returns array ordered if 'qbank_columnsortorder' config exists.
     */
    public function get_sorted_columns($ordertosort): array {
        // Check if db has order set.
        if (!empty($this->columnorder)) {
            // Merge new order with old one.
            $columnsortorder = $this->columnorder;
            asort($columnsortorder);
            $columnorder = [];
            foreach ($columnsortorder as $columnid => $colposition) {
                if (array_key_exists($columnid, $ordertosort)) {
                    $columnorder[$columnid] = $colposition;
                }
            }
            $properorder = array_merge($columnorder, $ordertosort);
            // Always have the checkbox at first column position.
            $checkboxid = 'core_question\local\bank\checkbox_column' . column_base::ID_SEPARATOR . 'checkbox_column';
            if (isset($properorder[$checkboxid])) {
                $checkboxfirstelement = $properorder[$checkboxid];
                unset($properorder[$checkboxid]);
                $properorder = array_merge([
                        $checkboxid => $checkboxfirstelement
                ], $properorder);
            }
            return $properorder;
        }
        return $ordertosort;
    }

    /**
     * Given an array of columns, set the isvisible attribute according to $this->hiddencolumns and $this->disabledcolumns.
     *
     * @param column_base[] $columns
     * @return array
     */
    public function set_columns_visibility(array $columns): array {
        foreach ($columns as $column) {
            if (!is_object($column)) {
                continue;
            }
            $columnid = $column->get_column_id();

            $column->isvisible = !in_array($columnid, $this->hiddencolumns) && !array_key_exists($columnid, $this->disabledcolumns);
        }
        return $columns;
    }

    /**
     * Return $this->colsize mapped as an array of column name => width, excluding empty sizes.
     *
     * @return array
     */
    public function get_colsize_map(): array {
        $sizes = array_reduce($this->colsize, function($result, $colsize) {
            $result[$colsize->column] = $colsize->width;
            return $result;
        }, []);
        return array_filter($sizes);
    }

    /**
     * Return an array of hidden columns as an array of class => column name
     *
     * @return array
     */
    public function get_hidden_columns(): array {
        return array_reduce($this->hiddencolumns, function($result, $hiddencolumn) {
            [$columnclass, $columnname] = explode(column_base::ID_SEPARATOR, $hiddencolumn, 2);
            $result[$hiddencolumn] = $columnclass::from_column_name($this->get_questionbank(), $columnname)->get_title();
            return $result;
        }, []);
    }

    public function get_column_width(column_base $column): string {
        $colsizemap = $this->get_colsize_map();
        $columnid = $column->get_column_id();
        if (array_key_exists($columnid, $colsizemap)) {
            return $colsizemap[$columnid] . 'px';
        }
        return parent::get_column_width($column);
    }

    public function get_column_actions(view $qbank): array {
        return [
            new column_action_move($qbank),
            new column_action_remove($qbank),
            new column_action_resize($qbank),
        ];
    }
}
