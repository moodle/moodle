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

/**
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * UES base class.
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class ues_base {

    /**
     * Protected static helper function to maintain calling class static overrides.
     */
    protected static function with_class($fun) {
        return $fun(get_called_class());
    }

    /**
     * Protected static override function.
     * @return call_user_func
     */
    protected static function call($fun, $params = array()) {
        return self::with_class(function ($class) use ($fun, $params) {
            return call_user_func(array($class, $fun), $params);
        });
    }

    /**
     * Protected static function to implode or pass $params
     * @var $params
     * @return array of imploded params
     */
    protected static function strip_joins($params) {
        if (is_array($params)) {
            return array($params, isset($params['joins']) ?
                ', ' . implode(', ', $params['joins']) : '');
        } else {
            return array($params->get(), $params->join_sql());
        }
    }

    /**
     * Protected static function to implode or pass $params
     * @var $params
     * @return array of imploded params
     */
    protected static function get_internal($params, $fields = '*', $trans = null) {
        return current(self::get_all_internal($params, '', $fields, 0, 0, $trans));
    }

    protected static function get_all_internal($params = array(), $sort = '', $fields='*', $offset = 0, $limit = 0, $trans = null) {
        global $DB;

        $tablename = self::call('tablename');

        if (is_array($params)) {
            $res = $DB->get_records($tablename, $params, $sort, $fields, $offset, $limit);
        } else {
            $ofields = array_map(
                function($field) {
                    return 'original.' . $field;
                },
                explode(',', $fields)
            );

            $joins = $params->join_sql('original');
            $where = $params->sql(function($key, $field) {
                return $field->is_aliased() ? $key : 'original.' . $key;
            });

            $order = !empty($sort) ? ' ORDER BY '. $sort : '';

            $sql = 'SELECT '.implode(',', $ofields). ' FROM {'.$tablename.'} '
                . $joins . ' WHERE '.$where . $order;

            $res = $DB->get_records_sql($sql, null, $offset, $limit);
        }

        $ret = array();
        foreach ($res as $r) {
            $temp = self::call('upgrade', $r);

            $ret[$r->id] = $trans ? $trans($temp) : $temp;
        }

        return $ret;
    }

    public static function by_sql($sql, $params = null, $offset = 0, $limit = 0, $trans = null) {
        global $DB;

        $results = array();
        foreach ($DB->get_records_sql($sql, $params, $offset, $limit) as $record) {
            $upped = self::call('upgrade', $record);
            $results[$upped->id] = $trans ? $trans($upped) : $upped;
        }

        return $results;
    }

    protected static function delete_all_internal($params = array(), $trans = null) {
        global $DB;

        $tablename = self::call('tablename');

        $todelete = self::count($params);

        if ($trans and $todelete) {
            $trans($tablename);
        }

        if (is_array($params)) {
            return $DB->delete_records($tablename, $params);
        } else {
            // DELETE SQL standard does not support joins, neither do we.
            $sql = 'DELETE FROM {'.$tablename.'}  WHERE ' . $params->sql();

            return $DB->execute($sql);
        }
    }

    public static function count($params = array()) {
        global $DB;

        $tablename = self::call('tablename');

        if (is_array($params)) {
            return $DB->count_records($tablename, $params);
        } else {
            $where = $params->sql(function($key, $field) {
                return $field->is_aliased() ? $key : 'original.' . $key;
            });
            $joins = $params->join_sql('original');
            $sql = 'SELECT COUNT(original.id) FROM {' . $tablename . '} ' .
                $joins . ' WHERE ' . $where;

            return $DB->count_records_sql($sql);
        }
    }

    public static function update(array $fields, $params = array()) {
        global $DB;

        list($map, $trans) = self::update_helpers();

        list($setparams, $setkeys) = $trans('set', $fields);

        $set = implode(' ,', $setkeys);

        $sql = 'UPDATE {' . self::call('tablename') .'} SET ' . $set;

        if ($params and is_array($params)) {
            $wherekeys = array_keys($params);
            $whereparams = array_map($map, $wherekeys, $wherekeys);

            $where = implode(' AND ', $whereparams);

            $sql .= ' WHERE ' . $where;

            $setparams += $params;
        } else if ($params) {
            $sql .= ' WHERE ' . $params->sql();
        }

        return $DB->execute($sql, $setparams);
    }

    private static function update_helpers() {
        $map = function ($key, $field) {
            return "$key = :$field";
        };

        $trans = function ($newkey, $fields) use ($map) {
            $oldkeys = array_keys($fields);

            $newnames = function ($field) use ($newkey) {
                return "{$newkey}_{$field}";
            };

            $newkeys = array_map($newnames, $oldkeys);

            $params = array_map($map, $oldkeys, $newkeys);

            $newparams = array_combine($newkeys, array_values($fields));
            return array($newparams, $params);
        };

        return array($map, $trans);
    }

    public static function get_name() {
        $names = explode('_', get_called_class());
        return implode('_', array_slice($names, 1));
    }

    public static function tablename($alias = '') {
        $name = sprintf('enrol_%s', get_called_class() . 's');

        if (!empty($alias)) {
            return '{' . $name . '} ' . $alias;
        } else {
            return $name;
        }
    }

    /**
     *
     * @param object $db_object
     * @return ues_base
     */
    public static function upgrade($dbobject) {
        return self::with_class(function ($class) use ($dbobject) {

            $fields = $dbobject ? get_object_vars($dbobject) : array();

            // Children can handle their own instantiation.
            $self = new $class($fields);

            return $self->fill_params($fields);
        });
    }

    /**
     * Instance based interaction.
     */
    public function fill_params(array $params = array()) {
        if (!empty($params)) {
            foreach ($params as $field => $value) {
                $this->$field = $value;
            }
        }

        return $this;
    }

    public function save() {
        global $DB;

        $tablename = self::call('tablename');

        if (!isset($this->id)) {
            $this->id = $DB->insert_record($tablename, $this, true);
        } else {
            $DB->update_record($tablename, $this);
        }

        return true;
    }

    public static function delete($id) {
        global $DB;

        return $DB->delete_records(self::call('tablename'), array('id' => $id));
    }

}
