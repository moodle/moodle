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

class PartialGroupByKeyInstruction extends \Google\Collection
{
  protected $collection_key = 'sideInputs';
  protected $inputType = InstructionInput::class;
  protected $inputDataType = '';
  /**
   * The codec to use for interpreting an element in the input PTable.
   *
   * @var array[]
   */
  public $inputElementCodec;
  /**
   * If this instruction includes a combining function this is the name of the
   * intermediate store between the GBK and the CombineValues.
   *
   * @var string
   */
  public $originalCombineValuesInputStoreName;
  /**
   * If this instruction includes a combining function, this is the name of the
   * CombineValues instruction lifted into this instruction.
   *
   * @var string
   */
  public $originalCombineValuesStepName;
  protected $sideInputsType = SideInputInfo::class;
  protected $sideInputsDataType = 'array';
  /**
   * The value combining function to invoke.
   *
   * @var array[]
   */
  public $valueCombiningFn;

  /**
   * Describes the input to the partial group-by-key instruction.
   *
   * @param InstructionInput $input
   */
  public function setInput(InstructionInput $input)
  {
    $this->input = $input;
  }
  /**
   * @return InstructionInput
   */
  public function getInput()
  {
    return $this->input;
  }
  /**
   * The codec to use for interpreting an element in the input PTable.
   *
   * @param array[] $inputElementCodec
   */
  public function setInputElementCodec($inputElementCodec)
  {
    $this->inputElementCodec = $inputElementCodec;
  }
  /**
   * @return array[]
   */
  public function getInputElementCodec()
  {
    return $this->inputElementCodec;
  }
  /**
   * If this instruction includes a combining function this is the name of the
   * intermediate store between the GBK and the CombineValues.
   *
   * @param string $originalCombineValuesInputStoreName
   */
  public function setOriginalCombineValuesInputStoreName($originalCombineValuesInputStoreName)
  {
    $this->originalCombineValuesInputStoreName = $originalCombineValuesInputStoreName;
  }
  /**
   * @return string
   */
  public function getOriginalCombineValuesInputStoreName()
  {
    return $this->originalCombineValuesInputStoreName;
  }
  /**
   * If this instruction includes a combining function, this is the name of the
   * CombineValues instruction lifted into this instruction.
   *
   * @param string $originalCombineValuesStepName
   */
  public function setOriginalCombineValuesStepName($originalCombineValuesStepName)
  {
    $this->originalCombineValuesStepName = $originalCombineValuesStepName;
  }
  /**
   * @return string
   */
  public function getOriginalCombineValuesStepName()
  {
    return $this->originalCombineValuesStepName;
  }
  /**
   * Zero or more side inputs.
   *
   * @param SideInputInfo[] $sideInputs
   */
  public function setSideInputs($sideInputs)
  {
    $this->sideInputs = $sideInputs;
  }
  /**
   * @return SideInputInfo[]
   */
  public function getSideInputs()
  {
    return $this->sideInputs;
  }
  /**
   * The value combining function to invoke.
   *
   * @param array[] $valueCombiningFn
   */
  public function setValueCombiningFn($valueCombiningFn)
  {
    $this->valueCombiningFn = $valueCombiningFn;
  }
  /**
   * @return array[]
   */
  public function getValueCombiningFn()
  {
    return $this->valueCombiningFn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartialGroupByKeyInstruction::class, 'Google_Service_Dataflow_PartialGroupByKeyInstruction');
