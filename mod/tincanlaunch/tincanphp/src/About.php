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

class About implements VersionableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait;

    protected $version;
    protected $extensions;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);
        }

        if (! isset($this->version)) {
            $this->setVersion(array());
        }
        if (! isset($this->extensions)) {
            $this->setExtensions(array());
        }
    }

    public function setVersion($value) { $this->version = $value; return $this; }
    public function getVersion() { return $this->version; }

    public function setExtensions($value) {
        if (! $value instanceof Extensions) {
            $value = new Extensions($value);
        }

        $this->extensions = $value;

        return $this;
    }
    public function getExtensions() { return $this->extensions; }
}
