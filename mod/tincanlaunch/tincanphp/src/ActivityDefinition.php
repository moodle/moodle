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

class ActivityDefinition implements VersionableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait;

    protected $type;
    protected $name;
    protected $description;
    protected $moreInfo;
    protected $extensions;
    protected $interactionType;
    protected $correctResponsesPattern;
    protected $choices;
    protected $scale;
    protected $source;
    protected $target;
    protected $steps;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);
        }

        foreach (
            [
                'name',
                'description',
                'extensions',
            ] as $k
        ) {
            $method = 'set' . ucfirst($k);

            if (! isset($this->$k)) {
                $this->$method(array());
            }
        }
    }

    // FEATURE: check URI?
    public function setType($value) { $this->type = $value; return $this; }
    public function getType() { return $this->type; }

    public function setName($value) {
        if (! $value instanceof LanguageMap) {
            $value = new LanguageMap($value);
        }

        $this->name = $value;

        return $this;
    }
    public function getName() { return $this->name; }

    public function setDescription($value) {
        if (! $value instanceof LanguageMap) {
            $value = new LanguageMap($value);
        }

        $this->description = $value;

        return $this;
    }
    public function getDescription() { return $this->description; }

    public function setMoreInfo($value) { $this->moreInfo = $value; return $this; }
    public function getMoreInfo() { return $this->moreInfo; }

    public function setExtensions($value) {
        if (! $value instanceof Extensions) {
            $value = new Extensions($value);
        }

        $this->extensions = $value;

        return $this;
    }
    public function getExtensions() { return $this->extensions; }

    public function setInteractionType($value) { $this->interactionType = $value; return $this; }
    public function getInteractionType() { return $this->interactionType; }
    public function setCorrectResponsesPattern($value) { $this->correctResponsesPattern = $value; return $this; }
    public function getCorrectResponsesPattern() { return $this->correctResponsesPattern; }

    // TODO: make these arrays of InteractionComponents
    public function setChoices($value) { $this->choices = $value; return $this; }
    public function getChoices() { return $this->choices; }
    public function setScale($value) { $this->scale = $value; return $this; }
    public function getScale() { return $this->scale; }
    public function setSource($value) { $this->source = $value; return $this; }
    public function getSource() { return $this->source; }
    public function setTarget($value) { $this->target = $value; return $this; }
    public function getTarget() { return $this->target; }
    public function setSteps($value) { $this->steps = $value; return $this; }
    public function getSteps() { return $this->steps; }
}
