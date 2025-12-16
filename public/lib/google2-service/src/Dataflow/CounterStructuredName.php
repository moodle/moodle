<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Dataflow;

class CounterStructuredName extends \Google\Model
{
  /**
   * Counter was created by the Dataflow system.
   */
  public const ORIGIN_SYSTEM = 'SYSTEM';
  /**
   * Counter was created by the user.
   */
  public const ORIGIN_USER = 'USER';
  /**
   * Counter portion has not been set.
   */
  public const PORTION_ALL = 'ALL';
  /**
   * Counter reports a key.
   */
  public const PORTION_KEY = 'KEY';
  /**
   * Counter reports a value.
   */
  public const PORTION_VALUE = 'VALUE';
  /**
   * Name of the optimized step being executed by the workers.
   *
   * @var string
   */
  public $componentStepName;
  /**
   * Name of the stage. An execution step contains multiple component steps.
   *
   * @var string
   */
  public $executionStepName;
  /**
   * Index of an input collection that's being read from/written to as a side
   * input. The index identifies a step's side inputs starting by 1 (e.g. the
   * first side input has input_index 1, the third has input_index 3). Side
   * inputs are identified by a pair of (original_step_name, input_index). This
   * field helps uniquely identify them.
   *
   * @var int
   */
  public $inputIndex;
  /**
   * Counter name. Not necessarily globally-unique, but unique within the
   * context of the other fields. Required.
   *
   * @var string
   */
  public $name;
  /**
   * One of the standard Origins defined above.
   *
   * @var string
   */
  public $origin;
  /**
   * A string containing a more specific namespace of the counter's origin.
   *
   * @var string
   */
  public $originNamespace;
  /**
   * The step name requesting an operation, such as GBK. I.e. the ParDo causing
   * a read/write from shuffle to occur, or a read from side inputs.
   *
   * @var string
   */
  public $originalRequestingStepName;
  /**
   * System generated name of the original step in the user's graph, before
   * optimization.
   *
   * @var string
   */
  public $originalStepName;
  /**
   * Portion of this counter, either key or value.
   *
   * @var string
   */
  public $portion;
  /**
   * ID of a particular worker.
   *
   * @var string
   */
  public $workerId;

  /**
   * Name of the optimized step being executed by the workers.
   *
   * @param string $componentStepName
   */
  public function setComponentStepName($componentStepName)
  {
    $this->componentStepName = $componentStepName;
  }
  /**
   * @return string
   */
  public function getComponentStepName()
  {
    return $this->componentStepName;
  }
  /**
   * Name of the stage. An execution step contains multiple component steps.
   *
   * @param string $executionStepName
   */
  public function setExecutionStepName($executionStepName)
  {
    $this->executionStepName = $executionStepName;
  }
  /**
   * @return string
   */
  public function getExecutionStepName()
  {
    return $this->executionStepName;
  }
  /**
   * Index of an input collection that's being read from/written to as a side
   * input. The index identifies a step's side inputs starting by 1 (e.g. the
   * first side input has input_index 1, the third has input_index 3). Side
   * inputs are identified by a pair of (original_step_name, input_index). This
   * field helps uniquely identify them.
   *
   * @param int $inputIndex
   */
  public function setInputIndex($inputIndex)
  {
    $this->inputIndex = $inputIndex;
  }
  /**
   * @return int
   */
  public function getInputIndex()
  {
    return $this->inputIndex;
  }
  /**
   * Counter name. Not necessarily globally-unique, but unique within the
   * context of the other fields. Required.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * One of the standard Origins defined above.
   *
   * Accepted values: SYSTEM, USER
   *
   * @param self::ORIGIN_* $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return self::ORIGIN_*
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * A string containing a more specific namespace of the counter's origin.
   *
   * @param string $originNamespace
   */
  public function setOriginNamespace($originNamespace)
  {
    $this->originNamespace = $originNamespace;
  }
  /**
   * @return string
   */
  public function getOriginNamespace()
  {
    return $this->originNamespace;
  }
  /**
   * The step name requesting an operation, such as GBK. I.e. the ParDo causing
   * a read/write from shuffle to occur, or a read from side inputs.
   *
   * @param string $originalRequestingStepName
   */
  public function setOriginalRequestingStepName($originalRequestingStepName)
  {
    $this->originalRequestingStepName = $originalRequestingStepName;
  }
  /**
   * @return string
   */
  public function getOriginalRequestingStepName()
  {
    return $this->originalRequestingStepName;
  }
  /**
   * System generated name of the original step in the user's graph, before
   * optimization.
   *
   * @param string $originalStepName
   */
  public function setOriginalStepName($originalStepName)
  {
    $this->originalStepName = $originalStepName;
  }
  /**
   * @return string
   */
  public function getOriginalStepName()
  {
    return $this->originalStepName;
  }
  /**
   * Portion of this counter, either key or value.
   *
   * Accepted values: ALL, KEY, VALUE
   *
   * @param self::PORTION_* $portion
   */
  public function setPortion($portion)
  {
    $this->portion = $portion;
  }
  /**
   * @return self::PORTION_*
   */
  public function getPortion()
  {
    return $this->portion;
  }
  /**
   * ID of a particular worker.
   *
   * @param string $workerId
   */
  public function setWorkerId($workerId)
  {
    $this->workerId = $workerId;
  }
  /**
   * @return string
   */
  public function getWorkerId()
  {
    return $this->workerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CounterStructuredName::class, 'Google_Service_Dataflow_CounterStructuredName');
