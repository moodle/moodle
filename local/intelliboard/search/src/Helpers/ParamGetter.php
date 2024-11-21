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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace Helpers;

class ParamGetter {

    private $columns = array();
    private $tables = array();
    private $filters = array();
    private $params = array();

    private static $paramCount = 0;

    public function add($type, $data)
    {
        if (isset($this->$type) && !in_array($data, $this->$type)) {
            $array = &$this->$type;
            $array[] = $data;
        }
        return $this;
    }

    public function release() {
        $sql = 'SELECT ' . implode(',', $this->columns);
        $sql .= ' FROM ' . implode(' ', $this->tables);

        if ($this->filters) {
            $sql .= ' WHERE ' . implode(' AND ', $this->filters);
        }

        return array('sql' => $sql, 'params' => $this->params);
    }

    public function setParam($name, $value) {
        $this->params[$name] = $value;
    }

    public static function in_sql(ParamGetter $getter, $type, $filter, $params)
    {
        $filter .= ' IN(';
        foreach($params as $value) {
            $param = 'inparam' . self::$paramCount;
            $filter .= ':' . $param . ',';
            $getter->setParam($param, $value);
            self::$paramCount++;
        }

        $filter = rtrim($filter, ',') . ')';

        $getter->add($type, $filter);
    }

}