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

class StreamingComputationConfig extends \Google\Collection
{
  protected $collection_key = 'instructions';
  /**
   * Unique identifier for this computation.
   *
   * @var string
   */
  public $computationId;
  protected $instructionsType = ParallelInstruction::class;
  protected $instructionsDataType = 'array';
  /**
   * Stage name of this computation.
   *
   * @var string
   */
  public $stageName;
  /**
   * System defined name for this computation.
   *
   * @var string
   */
  public $systemName;
  /**
   * Map from user name of stateful transforms in this stage to their state
   * family.
   *
   * @var string[]
   */
  public $transformUserNameToStateFamily;

  /**
   * Unique identifier for this computation.
   *
   * @param string $computationId
   */
  public function setComputationId($computationId)
  {
    $this->computationId = $computationId;
  }
  /**
   * @return string
   */
  public function getComputationId()
  {
    return $this->computationId;
  }
  /**
   * Instructions that comprise the computation.
   *
   * @param ParallelInstruction[] $instructions
   */
  public function setInstructions($instructions)
  {
    $this->instructions = $instructions;
  }
  /**
   * @return ParallelInstruction[]
   */
  public function getInstructions()
  {
    return $this->instructions;
  }
  /**
   * Stage name of this computation.
   *
   * @param string $stageName
   */
  public function setStageName($stageName)
  {
    $this->stageName = $stageName;
  }
  /**
   * @return string
   */
  public function getStageName()
  {
    return $this->stageName;
  }
  /**
   * System defined name for this computation.
   *
   * @param string $systemName
   */
  public function setSystemName($systemName)
  {
    $this->systemName = $systemName;
  }
  /**
   * @return string
   */
  public function getSystemName()
  {
    return $this->systemName;
  }
  /**
   * Map from user name of stateful transforms in this stage to their state
   * family.
   *
   * @param string[] $transformUserNameToStateFamily
   */
  public function setTransformUserNameToStateFamily($transformUserNameToStateFamily)
  {
    $this->transformUserNameToStateFamily = $transformUserNameToStateFamily;
  }
  /**
   * @return string[]
   */
  public function getTransformUserNameToStateFamily()
  {
    return $this->transformUserNameToStateFamily;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamingComputationConfig::class, 'Google_Service_Dataflow_StreamingComputationConfig');
