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

namespace Google\Service\Bigquery;

class StagePerformanceChangeInsight extends \Google\Model
{
  protected $inputDataChangeType = InputDataChange::class;
  protected $inputDataChangeDataType = '';
  /**
   * Output only. The stage id that the insight mapped to.
   *
   * @var string
   */
  public $stageId;

  /**
   * Output only. Input data change insight of the query stage.
   *
   * @param InputDataChange $inputDataChange
   */
  public function setInputDataChange(InputDataChange $inputDataChange)
  {
    $this->inputDataChange = $inputDataChange;
  }
  /**
   * @return InputDataChange
   */
  public function getInputDataChange()
  {
    return $this->inputDataChange;
  }
  /**
   * Output only. The stage id that the insight mapped to.
   *
   * @param string $stageId
   */
  public function setStageId($stageId)
  {
    $this->stageId = $stageId;
  }
  /**
   * @return string
   */
  public function getStageId()
  {
    return $this->stageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StagePerformanceChangeInsight::class, 'Google_Service_Bigquery_StagePerformanceChangeInsight');
