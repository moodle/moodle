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

abstract class StatementBase implements VersionableInterface, ComparableInterface
{
    use ArraySetterTrait, FromJSONTrait, AsVersionTrait, SignatureComparisonTrait;

    protected $actor;
    protected $verb;
    protected $target;
    protected $result;
    protected $context;

    //
    // timestamp *must* store a string because DateTime doesn't
    // support sub-second precision, the setter will take a DateTime and convert
    // it to the proper ISO8601 representation, but if a user needs sub-second
    // precision as afforded by the spec they will have to create their own,
    // they can see TinCan\Util::getTimestamp for an example of how to do so
    //
    // based on the signature comparison tests it seems that DateTime can store
    // subsecond precisions, but just not output them as part of ISO handling?
    // it might be possible to switch to a DateTime and just do manual formatting
    // still with the subsecond value (need to research it fully)
    //
    protected $timestamp;

    public function __construct() {
        if (func_num_args() == 1) {
            $arg = func_get_arg(0);

            $this->_fromArray($arg);

            //
            // 'object' isn't in the list of properties so ._fromArray doesn't
            // pick it up correctly, but 'target' and 'object' shouldn't be in
            // the args at the same time, so handle 'object' here
            //
            if (isset($arg['object'])) {
                $this->setObject($arg['object']);
            }
        }
    }

    private function _asVersion(&$result, $version) {
        if (isset($result['target'])) {
            $result['object'] = $result['target'];
            unset($result['target']);
        }
    }

    public function compareWithSignature($fromSig) {
        foreach (array('actor', 'verb', 'target', 'context', 'result') as $property) {
            if (! isset($this->$property) && ! isset($fromSig->$property)) {
                continue;
            }
            if (isset($this->$property) && ! isset($fromSig->$property)) {
                return array('success' => false, 'reason' => "Comparison of $property failed: value not in signature");
            }
            if (isset($fromSig->$property) && ! isset($this->$property)) {
                return array('success' => false, 'reason' => "Comparison of $property failed: value not in this");
            }

            $result = $this->$property->compareWithSignature($fromSig->$property);
            if (! $result['success']) {
                return array('success' => false, 'reason' => "Comparison of $property failed: " . $result['reason']);
            }
        }

        if (isset($this->timestamp) || isset($fromSig->timestamp)) {
            if (isset($this->timestamp) && ! isset($fromSig->timestamp)) {
                return array('success' => false, 'reason' => 'Comparison of timestamp failed: value not in signature');
            }
            if (isset($fromSig->timestamp) && ! isset($this->timestamp)) {
                return array('success' => false, 'reason' => 'Comparison of timestamp failed: value not in this');
            }

            $a = new \DateTime ($this->timestamp);
            $b = new \DateTime ($fromSig->timestamp);

            if ($a != $b) {
                return array('success' => false, 'reason' => 'Comparison of timestamp failed: value is not the same');
            }

            //
            // DateTime's diff doesn't take into account subsecond precision
            // even though it can store it, so manually check that
            //
            if ($a->format('u') !== $b->format('u')) {
                return array('success' => false, 'reason' => 'Comparison of timestamp failed: value is not the same');
            }
        }

        return array('success' => true, 'reason' => null);
    }

    public function setActor($value) {
        if ((! $value instanceof Agent && ! $value instanceof Group) && is_array($value)) {
            if (isset($value['objectType']) && $value['objectType'] === 'Group') {
                $value = new Group($value);
            }
            else {
                $value = new Agent($value);
            }
        }

        $this->actor = $value;

        return $this;
    }
    public function getActor() { return $this->actor; }

    public function setVerb($value) {
        if (! $value instanceof Verb) {
            $value = new Verb($value);
        }

        $this->verb = $value;

        return $this;
    }
    public function getVerb() { return $this->verb; }

    public function setTarget($value) {
        if (! $value instanceof StatementTargetInterface && is_array($value)) {
            if (isset($value['objectType'])) {
                if ($value['objectType'] === 'Activity') {
                    $value = new Activity($value);
                }
                elseif ($value['objectType'] === 'Agent') {
                    $value = new Agent($value);
                }
                elseif ($value['objectType'] === 'Group') {
                    $value = new Group($value);
                }
                elseif ($value['objectType'] === 'StatementRef') {
                    $value = new StatementRef($value);
                }
                elseif ($value['objectType'] === 'SubStatement') {
                    $value = new SubStatement($value);
                }
                else {
                    throw new \InvalidArgumentException('arg1 must implement the StatementTargetInterface objectType not recognized:' . $value['objectType']);
                }
            }
            else {
                $value = new Activity($value);
            }
        }

        $this->target = $value;

        return $this;
    }
    public function getTarget() { return $this->target; }

    // sugar methods
    public function setObject($value) { return $this->setTarget($value); }
    public function getObject() { return $this->getTarget(); }

    public function setResult($value) {
        if (! $value instanceof Result && is_array($value)) {
            $value = new Result($value);
        }

        $this->result = $value;

        return $this;
    }
    public function getResult() { return $this->result; }

    public function setContext($value) {
        if (! $value instanceof Context && is_array($value)) {
            $value = new Context($value);
        }

        $this->context = $value;

        return $this;
    }
    public function getContext() { return $this->context; }

    public function setTimestamp($value) {
        if (isset($value)) {
            if ($value instanceof \DateTime) {
                // Use format('c') instead of format(\DateTime::ISO8601) due to bug in format(\DateTime::ISO8601) that generates an invalid timestamp.
                $value = $value->format('c');
            }
            elseif (is_string($value)) {
                $value = $value;
            }
            else {
                throw new \InvalidArgumentException('type of arg1 must be string or DateTime');
            }
        }

        $this->timestamp = $value;

        return $this;
    }
    public function getTimestamp() { return $this->timestamp; }
}
