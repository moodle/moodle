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

namespace Google\Service\WorkloadManager;

class Execution extends \Google\Collection
{
  /**
   * The original CG
   */
  public const ENGINE_ENGINE_UNSPECIFIED = 'ENGINE_UNSPECIFIED';
  /**
   * SlimCG / Scanner
   */
  public const ENGINE_ENGINE_SCANNER = 'ENGINE_SCANNER';
  /**
   * Evaluation Engine V2
   */
  public const ENGINE_V2 = 'V2';
  /**
   * type of execution is unspecified
   */
  public const RUN_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * type of execution is one time
   */
  public const RUN_TYPE_ONE_TIME = 'ONE_TIME';
  /**
   * type of execution is scheduled
   */
  public const RUN_TYPE_SCHEDULED = 'SCHEDULED';
  /**
   * state of execution is unspecified
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * the execution is running in backend service
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * the execution run success
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * the execution run failed
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'ruleResults';
  /**
   * Output only. [Output only] End time stamp
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. Engine
   *
   * @var string
   */
  public $engine;
  /**
   * Output only. [Output only] Evaluation ID
   *
   * @var string
   */
  public $evaluationId;
  protected $externalDataSourcesType = ExternalDataSources::class;
  protected $externalDataSourcesDataType = 'array';
  /**
   * Output only. [Output only] Inventory time stamp
   *
   * @var string
   */
  public $inventoryTime;
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * The name of execution resource. The format is projects/{project}/locations/
   * {location}/evaluations/{evaluation}/executions/{execution}
   *
   * @var string
   */
  public $name;
  protected $noticesType = Notice::class;
  protected $noticesDataType = 'array';
  protected $resultSummaryType = Summary::class;
  protected $resultSummaryDataType = '';
  protected $ruleResultsType = RuleExecutionResult::class;
  protected $ruleResultsDataType = 'array';
  /**
   * type represent whether the execution executed directly by user or scheduled
   * according evaluation.schedule field.
   *
   * @var string
   */
  public $runType;
  /**
   * Output only. [Output only] Start time stamp
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. [Output only] State
   *
   * @var string
   */
  public $state;

  /**
   * Output only. [Output only] End time stamp
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Optional. Engine
   *
   * Accepted values: ENGINE_UNSPECIFIED, ENGINE_SCANNER, V2
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * Output only. [Output only] Evaluation ID
   *
   * @param string $evaluationId
   */
  public function setEvaluationId($evaluationId)
  {
    $this->evaluationId = $evaluationId;
  }
  /**
   * @return string
   */
  public function getEvaluationId()
  {
    return $this->evaluationId;
  }
  /**
   * Optional. External data sources
   *
   * @param ExternalDataSources[] $externalDataSources
   */
  public function setExternalDataSources($externalDataSources)
  {
    $this->externalDataSources = $externalDataSources;
  }
  /**
   * @return ExternalDataSources[]
   */
  public function getExternalDataSources()
  {
    return $this->externalDataSources;
  }
  /**
   * Output only. [Output only] Inventory time stamp
   *
   * @param string $inventoryTime
   */
  public function setInventoryTime($inventoryTime)
  {
    $this->inventoryTime = $inventoryTime;
  }
  /**
   * @return string
   */
  public function getInventoryTime()
  {
    return $this->inventoryTime;
  }
  /**
   * Labels as key value pairs
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The name of execution resource. The format is projects/{project}/locations/
   * {location}/evaluations/{evaluation}/executions/{execution}
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
   * Output only. Additional information generated by the execution
   *
   * @param Notice[] $notices
   */
  public function setNotices($notices)
  {
    $this->notices = $notices;
  }
  /**
   * @return Notice[]
   */
  public function getNotices()
  {
    return $this->notices;
  }
  /**
   * Output only. [Output only] Result summary for the execution
   *
   * @param Summary $resultSummary
   */
  public function setResultSummary(Summary $resultSummary)
  {
    $this->resultSummary = $resultSummary;
  }
  /**
   * @return Summary
   */
  public function getResultSummary()
  {
    return $this->resultSummary;
  }
  /**
   * Output only. execution result summary per rule
   *
   * @param RuleExecutionResult[] $ruleResults
   */
  public function setRuleResults($ruleResults)
  {
    $this->ruleResults = $ruleResults;
  }
  /**
   * @return RuleExecutionResult[]
   */
  public function getRuleResults()
  {
    return $this->ruleResults;
  }
  /**
   * type represent whether the execution executed directly by user or scheduled
   * according evaluation.schedule field.
   *
   * Accepted values: TYPE_UNSPECIFIED, ONE_TIME, SCHEDULED
   *
   * @param self::RUN_TYPE_* $runType
   */
  public function setRunType($runType)
  {
    $this->runType = $runType;
  }
  /**
   * @return self::RUN_TYPE_*
   */
  public function getRunType()
  {
    return $this->runType;
  }
  /**
   * Output only. [Output only] Start time stamp
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. [Output only] State
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Execution::class, 'Google_Service_WorkloadManager_Execution');
