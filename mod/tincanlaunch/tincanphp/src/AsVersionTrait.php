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

use DomainException;

/**
 * Basic implementation of the VersionableInterface
 */
trait AsVersionTrait
{
    /**
     * Collects defined object properties for a given version into an array
     *
     * @param  mixed $version
     * @return array
     */
    public function asVersion($version) {
        $result = array();

        foreach (get_object_vars($this) as $property => $value) {
            //
            // skip properties that start with an underscore to allow
            // storing information that isn't included in statement
            // structure etc. (see Attachment.content for example)
            //
            if (strpos($property, '_') === 0) {
                continue;
            }

            if ($value instanceof VersionableInterface) {
                $value = $value->asVersion($version);
            }
            elseif (is_array($value) && !empty($value)) {
                $tmp_value = array();
                foreach ($value as $element) {
                    if ($element instanceof VersionableInterface) {
                        array_push($tmp_value, $element->asVersion($version));
                    }
                    else {
                        array_push($tmp_value, $element);
                    }
                }
                $value = $tmp_value;
            }

            if (isset($value) && (!is_array($value) || !empty($value))) {
                $result[$property] = $value;
            }
        }

        if (method_exists($this, '_asVersion')) {
            $this->_asVersion($result, $version);
        }

        return $result;
    }

    /**
     * Prevent external mutation
     *
     * @param  string $property
     * @param  mixed  $value
     * @throws DomainException
     */
    final public function __set($property, $value) {
        throw new DomainException(__CLASS__ . ' is immutable');
    }
}
