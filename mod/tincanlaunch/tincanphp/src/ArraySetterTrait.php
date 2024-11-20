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

trait ArraySetterTrait
{
    private function _fromArray($options) {
        foreach (get_object_vars($this) as $k => $v) {
            $method = 'set' . ucfirst($k);
            if (isset($options[$k]) && method_exists($this, $method)) {
                $this->$method($options[$k]);
            }
        }
    }
}
