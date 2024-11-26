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

class Extensions extends Map implements ComparableInterface
{
    public function compareWithSignature($fromSig) {
        $sigMap = $fromSig->_map;

        $keys = array_unique(
            array_merge(
                isset($this->_map) ? array_keys($this->_map) : array(),
                isset($sigMap) ? array_keys($sigMap) : array()
            )
        );

        foreach ($keys as $key) {
            if (! isset($sigMap[$key])) {
                return array('success' => false, 'reason' => "$key not in signature");
            }
            if (! isset($this->_map[$key])) {
                return array('success' => false, 'reason' => "$key not in this");
            }
            if ($this->_map[$key] != $sigMap[$key]) {
                return array('success' => false, 'reason' => "$key does not match");
            }
        }

        return array('success' => true, 'reason' => null);
    }
}
