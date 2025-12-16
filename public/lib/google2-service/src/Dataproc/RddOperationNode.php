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

namespace Google\Service\Dataproc;

class RddOperationNode extends \Google\Model
{
  public const OUTPUT_DETERMINISTIC_LEVEL_DETERMINISTIC_LEVEL_UNSPECIFIED = 'DETERMINISTIC_LEVEL_UNSPECIFIED';
  public const OUTPUT_DETERMINISTIC_LEVEL_DETERMINISTIC_LEVEL_DETERMINATE = 'DETERMINISTIC_LEVEL_DETERMINATE';
  public const OUTPUT_DETERMINISTIC_LEVEL_DETERMINISTIC_LEVEL_UNORDERED = 'DETERMINISTIC_LEVEL_UNORDERED';
  public const OUTPUT_DETERMINISTIC_LEVEL_DETERMINISTIC_LEVEL_INDETERMINATE = 'DETERMINISTIC_LEVEL_INDETERMINATE';
  /**
   * @var bool
   */
  public $barrier;
  /**
   * @var bool
   */
  public $cached;
  /**
   * @var string
   */
  public $callsite;
  /**
   * @var string
   */
  public $name;
  /**
   * @var int
   */
  public $nodeId;
  /**
   * @var string
   */
  public $outputDeterministicLevel;

  /**
   * @param bool $barrier
   */
  public function setBarrier($barrier)
  {
    $this->barrier = $barrier;
  }
  /**
   * @return bool
   */
  public function getBarrier()
  {
    return $this->barrier;
  }
  /**
   * @param bool $cached
   */
  public function setCached($cached)
  {
    $this->cached = $cached;
  }
  /**
   * @return bool
   */
  public function getCached()
  {
    return $this->cached;
  }
  /**
   * @param string $callsite
   */
  public function setCallsite($callsite)
  {
    $this->callsite = $callsite;
  }
  /**
   * @return string
   */
  public function getCallsite()
  {
    return $this->callsite;
  }
  /**
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
   * @param int $nodeId
   */
  public function setNodeId($nodeId)
  {
    $this->nodeId = $nodeId;
  }
  /**
   * @return int
   */
  public function getNodeId()
  {
    return $this->nodeId;
  }
  /**
   * @param self::OUTPUT_DETERMINISTIC_LEVEL_* $outputDeterministicLevel
   */
  public function setOutputDeterministicLevel($outputDeterministicLevel)
  {
    $this->outputDeterministicLevel = $outputDeterministicLevel;
  }
  /**
   * @return self::OUTPUT_DETERMINISTIC_LEVEL_*
   */
  public function getOutputDeterministicLevel()
  {
    return $this->outputDeterministicLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RddOperationNode::class, 'Google_Service_Dataproc_RddOperationNode');
