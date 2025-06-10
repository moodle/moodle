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

interface meta_information {
    public function save_meta($meta);
    public function fill_meta();
    public static function meta_fields($fields);
    public static function get_meta($parentid);
    public static function get_meta_names();
    public static function metatablename($alias = '');
    public static function delete_meta($params);
    public static function delete_all_meta($params);
    public static function update_meta($params);
}

abstract class ues_dao extends ues_base implements meta_information {

    /** Public api to interact with ues_dao's */
    public static function get_meta($parentid) {
        global $DB;

        $params = array(self::call('get_name'). 'id' => $parentid);
        $res = $DB->get_records(self::call('metatablename'), $params);

        return $res;
    }

    public static function get_meta_names() {
        global $DB;

        $meta = self::call('metatablename');
        $names = $DB->get_records_sql('SELECT DISTINCT(name) FROM {'.$meta.'}');

        return array_keys($names);
    }

    public static function by_id($id, $meta = false) {
        return self::get(array('id' => $id), $meta);
    }

    /**
     * For whatever class calls this 'get' function, return a function that
     * will get the current element (starting with the first element) of an
     * array that contains all matches to $params, $meta, and $fields.
     *
     * @param $params Array of fieldname=>value pairs that filter the query.
     * @param $meta   true if the params contain metadata.
     * @param $fields Names of fields to return in query result.
     * @return        A function that will get the current element of an array
     *                of matches to the query.
    */
    public static function get($params, $meta = false, $fields = '*') {
        return self::with_class(function ($class) use ($params, $meta, $fields) {
            return current($class::get_all($params, $meta, '', $fields));
        });
    }

    private static function meta_sql_builder($params) {

        $metafields = self::call('meta_fields', $params);

        $tablename = self::call('tablename');

        $alpha = range('a', 'x');

        $name = self::call('get_name');

        $tables = array('{'.$tablename.'} z');
        $filters = array('z.id = a.'.$name.'id');

        foreach ($metafields as $i => $key) {
            $letter = $alpha[$i];
            $tables[] = self::call('metatablename', $letter);
            $filters[] = $letter.'.name' . " = '" . $key ."'";

            if (method_exists($params[$key], 'sql')) {
                $filter = $params[$key]->sql($letter.'.value');
            } else {
                $filter = $letter.'.value' . " = '" . $params[$key] . "'";
            }

            $filters[] = $filter;

            unset($params[$key]);

            if ($i > 0) {
                $i --;
                $prev = $alpha[$i];
                $filters[] = "{$letter}.{$name}id = {$prev}.{$name}id";
            }
        }

        foreach ($params as $key => $f) {
            if (method_exists($params[$key], 'sql')) {
                $filter = $f->is_aliased() ? $f->sql() : $f->sql('z.'.$key);
            } else {
                $filter = "z.$key = '$f'";
            }

            $filters[] = $filter;
        }

        return array(
            implode(', ', $tables),
            implode(' AND ', $filters)
        );
    }

    /**
     * Get all records of the descendant type filtered by the params passed
     * with or without meta, specific fields, etc
     */
    public static function get_all($params = array(), $meta = false, $sort = '', $fields = '*', $offset = 0, $limit = 0) {
        global $DB;

        $handler = function ($object) use ($meta) {
            if ($meta) {
                $object->fill_meta();
            }
            return $object;
        };

        $containsmeta = self::call('params_contains_meta', $params);

        if ($containsmeta) {
            $zfields = array_map(function($field) {
                return 'z.' . $field;
            },
            explode(',', $fields));

            list($send, $joins) = self::strip_joins($params);
            list($tables, $filters) = self::meta_sql_builder($send);

            $order = empty($sort) ? '' : ' ORDER BY ' . $sort;

            $sql = "SELECT ". implode(',', $zfields) . ' FROM ' .
                $tables . $joins . ' WHERE ' . $filters . $order;

            $res = $DB->get_records_sql($sql, array(), $offset, $limit);
        } else {
            return self::get_all_internal($params, $sort, $fields, $offset, $limit, $handler);
        }

        $ret = array();
        foreach ($res as $r) {
            $temp = self::call('upgrade', $r);
            $ret[$r->id] = $handler($temp);
        }

        return $ret;
    }

    /**
     * @param array | ues_dao_filter_builder $params
     *
     */
    public static function count($params = array()) {
        global $DB;

        if (self::call('params_contains_meta', $params)) {
            $send = is_array($params) ? $params : $params->get();

            list($send, $joins) = self::strip_joins($params);
            list($tables, $filters) = self::meta_sql_builder($send);

            $sql = 'SELECT COUNT(z.id) FROM ' . $tables . $joins .
                ' WHERE ' . $filters;

            return $DB->count_records_sql($sql);
        } else {
            return parent::count($params);
        }
    }

    public static function metatablename($alias = '') {
        $name = sprintf('enrol_%smeta', get_called_class());

        if (empty($alias)) {
            return $name;
        } else {
            return '{' . $name . '} ' . $alias;
        }
    }

    /**
     * @param type $object
     * @param type $params
     * @return ues_dao
     *
     */
    public static function upgrade_and_get($object, $params) {
        return self::with_class(function ($class) use ($object, $params) {
            $ues = $class::upgrade($object);

            if ($prev = $class::get($params)) {
                $ues->id = $prev->id;
            }

            return $ues;
        });
    }

    public static function delete($id) {
        self::delete_meta(array(self::call('get_name').'id' => $id));

        return parent::delete($id);
    }

    public static function delete_all($params = array()) {

        $metatable = self::call('metatablename');
        $name = self::call('get_name');

        $records = self::get_all($params);

        $handler = function ($tablename) use ($records, $metatable, $name) {
            global $DB;

            $ids = implode(',', array_keys($records));

            $DB->delete_records_select($metatable, $name.'id in ('.$ids.')');
        };

        if (self::call('params_contains_meta', $params)) {
            $ids = array_keys($records);

            return self::delete_all_internal(ues::where()->id->in($ids), $handler);
        } else {
            return self::delete_all_internal($params, $handler);
        }
    }

    public static function delete_meta($params = array()) {
        global $DB;

        $metafields = self::call('meta_fields', $params);

        $queryparams = array();
        if ($metafields) {
            foreach ($metafields as $field) {
                $queryparams['name'] = $field;
                $queryparams['value'] = $params[$field];
                unset($params[$field]);
            }
        }

        $metatable = self::call('metatablename');

        return $DB->delete_records($metatable, $params + $queryparams);
    }

    public static function delete_all_meta($params = array()) {
        global $DB;

        $todelete = $DB->get_records(self::call('tablename'), $params);

        $ids = implode(',', array_keys($todelete));

        return $DB->delete_records_select(self::call('metatablename'), null,
            self::call('get_name').'id in ('.$ids.')');
    }

    public static function update_meta($params = array()) {
        global $DB;

        $metafields = self::call('meta_fields', $params);

        if (empty($metafields)) {
            return true;
        }

        $field = current($metafields);
        $query = array('name' => $field, 'value' => $params[$field]);

        $metatable = self::call('metatablename');

        $sql = 'UPDATE {'. $metatable .'} SET value = :value WHERE name = :name';

        return $DB->execute($sql, $query);
    }

    public function fill_meta() {
        $meta = self::call('get_meta', $this->id);

        foreach ($meta as $m) {
            $this->{$m->name} = $m->value;
        }

        return $this;
    }

    public function save() {

        $saved = parent::save();

        $fields = get_object_vars($this);

        $extra = $this->meta_fields($fields);

        if (empty($extra)) {
            return $saved;
        }

        $fun = function ($e) use ($fields) {
            return $fields[$e];
        };

        $meta = array_combine($extra, array_map($fun, $extra));

        $this->save_meta($meta);

        return $saved;
    }

    public function save_meta($meta) {
        global $DB;

        $dbs = self::call('get_meta', $this->id);

        $metatable = self::call('metatablename');
        $parentref = self::call('get_name');

        // Update Pre-existing changes.
        foreach ($dbs as $db) {
            // Exists and changed, then write.
            if (isset($meta[$db->name]) and $db->value != $meta[$db->name]) {
                $db->value = $meta[$db->name];
                $DB->update_record($metatable, $db);
            }

            unset($meta[$db->name]);
        }

        // Persist other changes.
        foreach ($meta as $name => $value) {
            $m = new stdClass;

            $m->{$parentref. 'id'} = $this->id;
            $m->name = $name;
            $m->value = $value;

            $m->id = $DB->insert_record($metatable, $m, true);

            $dbs[$m->id] = $m;
        }
    }

    public static function params_contains_meta($params) {
        $name = self::call('get_name');

        foreach ($params as $field => $i) {
            if (preg_match('/^'.$name.'_/', $field)) {
                return true;
            }
        }

        return false;
    }

    public static function meta_fields($fields) {
        $name = self::call('get_name');

        $meta = array();

        foreach (array_keys($fields) as $field) {
            if ($field == 'id') {
                continue;
            }

            if (preg_match('/^'.$name.'_/', $field)) {
                $meta[] = $field;
            }
        }

        return $meta;
    }
}
