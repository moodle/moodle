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

class SqlExecutionUiData extends \Google\Collection
{
  protected $collection_key = 'stages';
  /**
   * @var string
   */
  public $completionTime;
  /**
   * @var string
   */
  public $description;
  /**
   * @var string
   */
  public $details;
  /**
   * @var string
   */
  public $errorMessage;
  /**
   * @var string
   */
  public $executionId;
  /**
   * @var string[]
   */
  public $jobs;
  /**
   * @var string[]
   */
  public $metricValues;
  /**
   * @var bool
   */
  public $metricValuesIsNull;
  protected $metricsType = SqlPlanMetric::class;
  protected $metricsDataType = 'array';
  /**
   * @var string[]
   */
  public $modifiedConfigs;
  /**
   * @var string
   */
  public $physicalPlanDescription;
  /**
   * @var string
   */
  public $rootExecutionId;
  /**
   * @var string[]
   */
  public $stages;
  /**
   * @var string
   */
  public $submissionTime;

  /**
   * @param string $completionTime
   */
  public function setCompletionTime($completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return string
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
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
   * @param string[] $jobs
   */
  public function setJobs($jobs)
  {
    $this->jobs = $jobs;
  }
  /**
   * @return string[]
   */
  public function getJobs()
  {
    return $this->jobs;
  }
  /**
   * @param string[] $metricValues
   */
  public function setMetricValues($metricValues)
  {
    $this->metricValues = $metricValues;
  }
  /**
   * @return string[]
   */
  public function getMetricValues()
  {
    return $this->metricValues;
  }
  /**
   * @param bool $metricValuesIsNull
   */
  public function setMetricValuesIsNull($metricValuesIsNull)
  {
    $this->metricValuesIsNull = $metricValuesIsNull;
  }
  /**
   * @return bool
   */
  public function getMetricValuesIsNull()
  {
    return $this->metricValuesIsNull;
  }
  /**
   * @param SqlPlanMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return SqlPlanMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * @param string[] $modifiedConfigs
   */
  public function setModifiedConfigs($modifiedConfigs)
  {
    $this->modifiedConfigs = $modifiedConfigs;
  }
  /**
   * @return string[]
   */
  public function getModifiedConfigs()
  {
    return $this->modifiedConfigs;
  }
  /**
   * @param string $physicalPlanDescription
   */
  public function setPhysicalPlanDescription($physicalPlanDescription)
  {
    $this->physicalPlanDescription = $physicalPlanDescription;
  }
  /**
   * @return string
   */
  public function getPhysicalPlanDescription()
  {
    return $this->physicalPlanDescription;
  }
  /**
   * @param string $rootExecutionId
   */
  public function setRootExecutionId($rootExecutionId)
  {
    $this->rootExecutionId = $rootExecutionId;
  }
  /**
   * @return string
   */
  public function getRootExecutionId()
  {
    return $this->rootExecutionId;
  }
  /**
   * @param string[] $stages
   */
  public function setStages($stages)
  {
    $this->stages = $stages;
  }
  /**
   * @return string[]
   */
  public function getStages()
  {
    return $this->stages;
  }
  /**
   * @param string $submissionTime
   */
  public function setSubmissionTime($submissionTime)
  {
    $this->submissionTime = $submissionTime;
  }
  /**
   * @return string
   */
  public function getSubmissionTime()
  {
    return $this->submissionTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlExecutionUiData::class, 'Google_Service_Dataproc_SqlExecutionUiData');
