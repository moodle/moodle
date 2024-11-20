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

class Result implements VersionableInterface, ComparableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait, SignatureComparisonTrait;

    protected $score;
    protected $success;
    protected $completion;
    protected $duration;
    protected $response;
    protected $extensions;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);
        }

        if (! isset($this->extensions)) {
            $this->setExtensions(array());
        }
    }

    private function _asVersion(&$result, $version) {
        //
        // empty string is an invalid duration
        //
        if (isset($result['duration']) && $result['duration'] == '') {
            unset($result['duration']);
        }
    }

    public function setScore($value) {
        if (! $value instanceof Score && is_array($value)) {
            $value = new Score($value);
        }

        $this->score = $value;

        return $this;
    }
    public function getScore() { return $this->score; }

    public function setSuccess($value) { $this->success = (bool) $value; return $this; }
    public function getSuccess() { return $this->success; }
    public function setCompletion($value) { $this->completion = (bool) $value; return $this; }
    public function getCompletion() { return $this->completion; }
    public function setDuration($value) { $this->duration = $value; return $this; }
    public function getDuration() { return $this->duration; }
    public function setResponse($value) { $this->response = $value; return $this; }
    public function getResponse() { return $this->response; }

    public function setExtensions($value) {
        if (! $value instanceof Extensions) {
            $value = new Extensions($value);
        }

        $this->extensions = $value;

        return $this;
    }
    public function getExtensions() { return $this->extensions; }
}
