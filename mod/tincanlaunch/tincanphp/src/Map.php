<?php
/*
    Copyright 2014 Rustici Software

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

namespace TinCan;

abstract class Map implements VersionableInterface
{
    use FromJSONTrait;

    protected $_map;

    public function __construct() {
        if (func_num_args() == 1) {
            $this->_map = func_get_arg(0);
        }
        else {
            $this->_map = array();
        }
    }

    public function asVersion($version = null) {
        if (! $this->isEmpty()) {
            return $this->_map;
        }
    }

    public function set($code, $value) {
        $this->_map[$code] = $value;
    }
    private function _unset($code) {
        unset($this->_map[$code]);
    }

    public function isEmpty() {
        return count($this->_map) === 0;
    }

    public function __call($func, $args) {
        switch ($func) {
            case 'unset':
                return $this->_unset($args[0]);
            break;
            default:
                throw new \BadMethodCallException(get_class($this) . "::$func() does not exist");
        }
    }
}
