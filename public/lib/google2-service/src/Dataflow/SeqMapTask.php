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

class SeqMapTask extends \Google\Collection
{
  protected $collection_key = 'outputInfos';
  protected $inputsType = SideInputInfo::class;
  protected $inputsDataType = 'array';
  /**
   * The user-provided name of the SeqDo operation.
   *
   * @var string
   */
  public $name;
  protected $outputInfosType = SeqMapTaskOutputInfo::class;
  protected $outputInfosDataType = 'array';
  /**
   * System-defined name of the stage containing the SeqDo operation. Unique
   * across the workflow.
   *
   * @var string
   */
  public $stageName;
  /**
   * System-defined name of the SeqDo operation. Unique across the workflow.
   *
   * @var string
   */
  public $systemName;
  /**
   * The user function to invoke.
   *
   * @var array[]
   */
  public $userFn;

  /**
   * Information about each of the inputs.
   *
   * @param SideInputInfo[] $inputs
   */
  public function setInputs($inputs)
  {
    $this->inputs = $inputs;
  }
  /**
   * @return SideInputInfo[]
   */
  public function getInputs()
  {
    return $this->inputs;
  }
  /**
   * The user-provided name of the SeqDo operation.
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
   * Information about each of the outputs.
   *
   * @param SeqMapTaskOutputInfo[] $outputInfos
   */
  public function setOutputInfos($outputInfos)
  {
    $this->outputInfos = $outputInfos;
  }
  /**
   * @return SeqMapTaskOutputInfo[]
   */
  public function getOutputInfos()
  {
    return $this->outputInfos;
  }
  /**
   * System-defined name of the stage containing the SeqDo operation. Unique
   * across the workflow.
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
   * System-defined name of the SeqDo operation. Unique across the workflow.
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
   * The user function to invoke.
   *
   * @param array[] $userFn
   */
  public function setUserFn($userFn)
  {
    $this->userFn = $userFn;
  }
  /**
   * @return array[]
   */
  public function getUserFn()
  {
    return $this->userFn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SeqMapTask::class, 'Google_Service_Dataflow_SeqMapTask');
