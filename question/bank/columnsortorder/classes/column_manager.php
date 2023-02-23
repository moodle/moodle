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
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use moodle_url;

/**
 * Class column_manager responsible for loading and saving order to the config setting.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class column_manager {
    /**
     * @var array|bool Column order as set in config_plugins 'class' => 'position', ie: question_type_column => 3.
     */
    public $columnorder;

    /**
     * @var array|bool Disabled columns in config_plugins table.
     */
    public $disabledcolumns;

    /**
     * Constructor for column_manager class.
     *
     */
    public function __construct() {
        $this->columnorder = get_config('qbank_columnsortorder', 'enabledcol');
        $this->disabledcolumns = get_config('qbank_columnsortorder', 'disabledcol');
        if ($this->columnorder) {
            $this->columnorder = array_flip(explode(',', $this->columnorder));
        }
        if ($this->disabledcolumns) {
            $this->disabledcolumns = array_flip(explode(',', $this->disabledcolumns));
        }
    }

    /**
     * Sets column order in the qbank_columnsortorder plugin config.
     *
     * @param array $columns Column order to set.
     */
    public static function set_column_order(array $columns) : void {
        $columns = implode(',', $columns);
        set_config('enabledcol', $columns, 'qbank_columnsortorder');
    }

    /**
     * Get qbank.
     *
     * @return view
     */
    protected function get_questionbank(): view {
        $course = (object) ['id' => 0];
        $context = context_system::instance();
        $contexts = new question_edit_contexts($context);
        // Dummy call to get the objects without error.
        $questionbank = new view($contexts, new moodle_url('/question/dummyurl.php'), $course, null);
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
            $classelements = explode('\\', $key);
            $columns[] = (object) [
                'class' => get_class($column),
                'name' => $column->get_title(),
                'colname' => end($classelements),
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
            foreach ($this->disabledcolumns as $class => $value) {
                if (strpos($class, 'qbank_customfields\custom_field_column') !== false) {
                    $class = explode('\\', $class);
                    $disabledname = array_pop($class);
                    $class = implode('\\', $class);
                    $disabled[] = (object) [
                        'disabledname' => $disabledname,
                    ];
                } else {
                    $columnobject = new $class($this->get_questionbank());
                    $disabled[] = (object) [
                        'disabledname' => $columnobject->get_title(),
                    ];
                }
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
            if (strpos($column->class, $plugin) !== false) {
                if ($column->class === 'qbank_customfields\custom_field_column') {
                    $disabledcolumns[$column->class . '\\' . $column->colname] = $column->class . '\\' . $column->colname;
                    if (isset($enabledcolumns[$column->class . '\\' . $column->colname])) {
                        unset($enabledcolumns[$column->class. '\\' . $column->colname]);
                    }
                } else {
                    $disabledcolumns[$column->class] = $column->class;
                    if (isset($enabledcolumns[$column->class])) {
                        unset($enabledcolumns[$column->class]);
                    }
                }
            }
        }
        $this->update_config($enabledcolumns, $disabledcolumns);
    }

    /**
     * Orders columns in the question bank view according to config_plugins table 'qbank_columnsortorder' config.
     *
     * @param array $ordertosort Unordered array of columns
     * @return array $properorder|$ordertosort Returns array ordered if 'qbank_columnsortorder' config exists.
     */
    public function get_sorted_columns($ordertosort): array {
        // Check if db has order set.
        if (!empty($this->columnorder)) {
            // Merge new order with old one.
            $columnsortorder = $this->columnorder;
            asort($columnsortorder);
            $columnorder = [];
            foreach ($columnsortorder as $classname => $colposition) {
                $colname = explode('\\', $classname);
                if (strpos($classname, 'qbank_customfields\custom_field_column') !== false) {
                    unset($colname[0]);
                    $classname = implode('\\', $colname);
                    // Checks if custom column still exists.
                    if (array_key_exists($classname, $ordertosort)) {
                        $columnorder[$classname] = $colposition;
                    } else {
                        $configtounset = str_replace('\\', '\\\\', $classname);
                        // Cleans config db.
                        unset_config($configtounset, 'column_sortorder');
                    }
                } else {
                    $columnorder[end($colname)] = $colposition;
                }
            }
            $properorder = array_merge($columnorder, $ordertosort);
            // Always have the checkbox at first column position.
            if (isset($properorder['checkbox_column'])) {
                $checkboxfirstelement = $properorder['checkbox_column'];
                unset($properorder['checkbox_column']);
                $properorder = array_merge(['checkbox_column' => $checkboxfirstelement], $properorder);
            }
            return $properorder;
        }
        return $ordertosort;
    }
}
