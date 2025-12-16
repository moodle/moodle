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

namespace Google\Service\ToolResults;

class MultiStep extends \Google\Model
{
  /**
   * Unique int given to each step. Ranges from 0(inclusive) to total number of
   * steps(exclusive). The primary step is 0.
   *
   * @var int
   */
  public $multistepNumber;
  protected $primaryStepType = PrimaryStep::class;
  protected $primaryStepDataType = '';
  /**
   * Step Id of the primary (original) step, which might be this step.
   *
   * @var string
   */
  public $primaryStepId;

  /**
   * Unique int given to each step. Ranges from 0(inclusive) to total number of
   * steps(exclusive). The primary step is 0.
   *
   * @param int $multistepNumber
   */
  public function setMultistepNumber($multistepNumber)
  {
    $this->multistepNumber = $multistepNumber;
  }
  /**
   * @return int
   */
  public function getMultistepNumber()
  {
    return $this->multistepNumber;
  }
  /**
   * Present if it is a primary (original) step.
   *
   * @param PrimaryStep $primaryStep
   */
  public function setPrimaryStep(PrimaryStep $primaryStep)
  {
    $this->primaryStep = $primaryStep;
  }
  /**
   * @return PrimaryStep
   */
  public function getPrimaryStep()
  {
    return $this->primaryStep;
  }
  /**
   * Step Id of the primary (original) step, which might be this step.
   *
   * @param string $primaryStepId
   */
  public function setPrimaryStepId($primaryStepId)
  {
    $this->primaryStepId = $primaryStepId;
  }
  /**
   * @return string
   */
  public function getPrimaryStepId()
  {
    return $this->primaryStepId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MultiStep::class, 'Google_Service_ToolResults_MultiStep');
