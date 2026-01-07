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

class ComputationTopology extends \Google\Collection
{
  protected $collection_key = 'stateFamilies';
  /**
   * The ID of the computation.
   *
   * @var string
   */
  public $computationId;
  protected $inputsType = StreamLocation::class;
  protected $inputsDataType = 'array';
  protected $keyRangesType = KeyRangeLocation::class;
  protected $keyRangesDataType = 'array';
  protected $outputsType = StreamLocation::class;
  protected $outputsDataType = 'array';
  protected $stateFamiliesType = StateFamilyConfig::class;
  protected $stateFamiliesDataType = 'array';
  /**
   * The system stage name.
   *
   * @var string
   */
  public $systemStageName;

  /**
   * The ID of the computation.
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
   * The inputs to the computation.
   *
   * @param StreamLocation[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return StreamLocation[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * The key ranges processed by the computation.
   *
   * @param KeyRangeLocation[] $keyRanges
   */
  public function setKeyRanges($keyRanges)
  {
    $this->keyRanges = $keyRanges;
  }
  /**
   * @return KeyRangeLocation[]
   */
  public function getKeyRanges()
  {
    return $this->keyRanges;
  }
  /**
   * The outputs from the computation.
   *
   * @param StreamLocation[] $outputs
   */
  public function setOutputs($outputs)
  {
    $this->outputs = $outputs;
  }
  /**
   * @return StreamLocation[]
   */
  public function getOutputs()
  {
    return $this->outputs;
  }
  /**
   * The state family values.
   *
   * @param StateFamilyConfig[] $stateFamilies
   */
  public function setStateFamilies($stateFamilies)
  {
    $this->stateFamilies = $stateFamilies;
  }
  /**
   * @return StateFamilyConfig[]
   */
  public function getStateFamilies()
  {
    return $this->stateFamilies;
  }
  /**
   * The system stage name.
   *
   * @param string $systemStageName
   */
  public function setSystemStageName($systemStageName)
  {
    $this->systemStageName = $systemStageName;
  }
  /**
   * @return string
   */
  public function getSystemStageName()
  {
    return $this->systemStageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputationTopology::class, 'Google_Service_Dataflow_ComputationTopology');
