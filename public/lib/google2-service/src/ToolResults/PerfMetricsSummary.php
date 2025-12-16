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

class PerfMetricsSummary extends \Google\Collection
{
  protected $collection_key = 'perfMetrics';
  protected $appStartTimeType = AppStartTime::class;
  protected $appStartTimeDataType = '';
  /**
   * A tool results execution ID. @OutputOnly
   *
   * @var string
   */
  public $executionId;
  protected $graphicsStatsType = GraphicsStats::class;
  protected $graphicsStatsDataType = '';
  /**
   * A tool results history ID. @OutputOnly
   *
   * @var string
   */
  public $historyId;
  protected $perfEnvironmentType = PerfEnvironment::class;
  protected $perfEnvironmentDataType = '';
  /**
   * Set of resource collected
   *
   * @var string[]
   */
  public $perfMetrics;
  /**
   * The cloud project @OutputOnly
   *
   * @var string
   */
  public $projectId;
  /**
   * A tool results step ID. @OutputOnly
   *
   * @var string
   */
  public $stepId;

  /**
   * @param AppStartTime $appStartTime
   */
  public function setAppStartTime(AppStartTime $appStartTime)
  {
    $this->appStartTime = $appStartTime;
  }
  /**
   * @return AppStartTime
   */
  public function getAppStartTime()
  {
    return $this->appStartTime;
  }
  /**
   * A tool results execution ID. @OutputOnly
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * Graphics statistics for the entire run. Statistics are reset at the
   * beginning of the run and collected at the end of the run.
   *
   * @deprecated
   * @param GraphicsStats $graphicsStats
   */
  public function setGraphicsStats(GraphicsStats $graphicsStats)
  {
    $this->graphicsStats = $graphicsStats;
  }
  /**
   * @deprecated
   * @return GraphicsStats
   */
  public function getGraphicsStats()
  {
    return $this->graphicsStats;
  }
  /**
   * A tool results history ID. @OutputOnly
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * Describes the environment in which the performance metrics were collected
   *
   * @param PerfEnvironment $perfEnvironment
   */
  public function setPerfEnvironment(PerfEnvironment $perfEnvironment)
  {
    $this->perfEnvironment = $perfEnvironment;
  }
  /**
   * @return PerfEnvironment
   */
  public function getPerfEnvironment()
  {
    return $this->perfEnvironment;
  }
  /**
   * Set of resource collected
   *
   * @param string[] $perfMetrics
   */
  public function setPerfMetrics($perfMetrics)
  {
    $this->perfMetrics = $perfMetrics;
  }
  /**
   * @return string[]
   */
  public function getPerfMetrics()
  {
    return $this->perfMetrics;
  }
  /**
   * The cloud project @OutputOnly
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * A tool results step ID. @OutputOnly
   *
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerfMetricsSummary::class, 'Google_Service_ToolResults_PerfMetricsSummary');
