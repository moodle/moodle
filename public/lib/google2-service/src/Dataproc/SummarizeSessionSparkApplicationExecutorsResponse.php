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

class SummarizeSessionSparkApplicationExecutorsResponse extends \Google\Model
{
  protected $activeExecutorSummaryType = ConsolidatedExecutorSummary::class;
  protected $activeExecutorSummaryDataType = '';
  /**
   * Spark Application Id
   *
   * @var string
   */
  public $applicationId;
  protected $deadExecutorSummaryType = ConsolidatedExecutorSummary::class;
  protected $deadExecutorSummaryDataType = '';
  protected $totalExecutorSummaryType = ConsolidatedExecutorSummary::class;
  protected $totalExecutorSummaryDataType = '';

  /**
   * Consolidated summary for active executors.
   *
   * @param ConsolidatedExecutorSummary $activeExecutorSummary
   */
  public function setActiveExecutorSummary(ConsolidatedExecutorSummary $activeExecutorSummary)
  {
    $this->activeExecutorSummary = $activeExecutorSummary;
  }
  /**
   * @return ConsolidatedExecutorSummary
   */
  public function getActiveExecutorSummary()
  {
    return $this->activeExecutorSummary;
  }
  /**
   * Spark Application Id
   *
   * @param string $applicationId
   */
  public function setApplicationId($applicationId)
  {
    $this->applicationId = $applicationId;
  }
  /**
   * @return string
   */
  public function getApplicationId()
  {
    return $this->applicationId;
  }
  /**
   * Consolidated summary for dead executors.
   *
   * @param ConsolidatedExecutorSummary $deadExecutorSummary
   */
  public function setDeadExecutorSummary(ConsolidatedExecutorSummary $deadExecutorSummary)
  {
    $this->deadExecutorSummary = $deadExecutorSummary;
  }
  /**
   * @return ConsolidatedExecutorSummary
   */
  public function getDeadExecutorSummary()
  {
    return $this->deadExecutorSummary;
  }
  /**
   * Overall consolidated summary for all executors.
   *
   * @param ConsolidatedExecutorSummary $totalExecutorSummary
   */
  public function setTotalExecutorSummary(ConsolidatedExecutorSummary $totalExecutorSummary)
  {
    $this->totalExecutorSummary = $totalExecutorSummary;
  }
  /**
   * @return ConsolidatedExecutorSummary
   */
  public function getTotalExecutorSummary()
  {
    return $this->totalExecutorSummary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SummarizeSessionSparkApplicationExecutorsResponse::class, 'Google_Service_Dataproc_SummarizeSessionSparkApplicationExecutorsResponse');
