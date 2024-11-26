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

class Attachment implements VersionableInterface, ComparableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait, SignatureComparisonTrait;

    static private $signatureSkipProperties = array('display', 'description');

    protected $usageType;
    protected $display;
    protected $description;
    protected $contentType;
    protected $length;
    protected $sha2;
    protected $fileUrl;
    protected $_content;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);

            if (isset($arg['content'])) {
                $this->setContent($arg['content']);
            }
        }

        foreach (
            [
                'display',
                'description',
            ] as $k
        ) {
            $method = 'set' . ucfirst($k);

            if (! isset($this->$k)) {
                $this->$method(array());
            }
        }
    }

    public function setUsageType($value) { $this->usageType = $value; return $this; }
    public function getUsageType() { return $this->usageType; }

    public function setDisplay($value) {
        if (! $value instanceof LanguageMap) {
            $value = new LanguageMap($value);
        }

        $this->display = $value;

        return $this;
    }
    public function getDisplay() { return $this->display; }

    public function setDescription($value) {
        if (! $value instanceof LanguageMap) {
            $value = new LanguageMap($value);
        }

        $this->description = $value;

        return $this;
    }
    public function getDescription() { return $this->description; }

    public function setContentType($value) { $this->contentType = $value; return $this; }
    public function getContentType() { return $this->contentType; }
    public function setLength($value) { $this->length = $value; return $this; }
    public function getLength() { return $this->length; }
    public function setSha2($value) { $this->sha2 = $value; return $this; }
    public function getSha2() { return $this->sha2; }
    public function setFileUrl($value) { $this->fileUrl = $value; return $this; }
    public function getFileUrl() { return $this->fileUrl; }

    public function setContent($value) {
        $this->_content = $value;
        $this->setLength(strlen($value));
        $this->setSha2(hash("sha256", $value));
        return $this;
    }
    public function getContent() { return $this->_content; }
    public function hasContent() { return isset($this->_content); }
}
