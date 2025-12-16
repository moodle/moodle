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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1EvaluationRun extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The evaluation run is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The evaluation run is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The evaluation run has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The evaluation run has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The evaluation run has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The evaluation run is performing inference.
   */
  public const STATE_INFERENCE = 'INFERENCE';
  /**
   * The evaluation run is performing rubric generation.
   */
  public const STATE_GENERATING_RUBRICS = 'GENERATING_RUBRICS';
  /**
   * Output only. Time when the evaluation run was completed.
   *
   * @var string
   */
  public $completionTime;
  /**
   * Output only. Time when the evaluation run was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataSourceType = GoogleCloudAiplatformV1EvaluationRunDataSource::class;
  protected $dataSourceDataType = '';
  /**
   * Required. The display name of the Evaluation Run.
   *
   * @var string
   */
  public $displayName;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $evaluationConfigType = GoogleCloudAiplatformV1EvaluationRunEvaluationConfig::class;
  protected $evaluationConfigDataType = '';
  protected $evaluationResultsType = GoogleCloudAiplatformV1EvaluationResults::class;
  protected $evaluationResultsDataType = '';
  /**
   * Output only. The specific evaluation set of the evaluation run. For runs
   * with an evaluation set input, this will be that same set. For runs with
   * BigQuery input, it's the sampled BigQuery dataset.
   *
   * @var string
   */
  public $evaluationSetSnapshot;
  protected $inferenceConfigsType = GoogleCloudAiplatformV1EvaluationRunInferenceConfig::class;
  protected $inferenceConfigsDataType = 'map';
  /**
   * Optional. Labels for the evaluation run.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Metadata about the evaluation run, can be used by the caller to
   * store additional tracking information about the evaluation run.
   *
   * @var array
   */
  public $metadata;
  /**
   * Identifier. The resource name of the EvaluationRun. This is a unique
   * identifier. Format:
   * `projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the evaluation run.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Time when the evaluation run was completed.
   *
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
   * Output only. Time when the evaluation run was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The data source for the evaluation run.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunDataSource $dataSource
   */
  public function setDataSource(GoogleCloudAiplatformV1EvaluationRunDataSource $dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunDataSource
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Required. The display name of the Evaluation Run.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Only populated when the evaluation run's state is FAILED or
   * CANCELLED.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Required. The configuration used for the evaluation.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunEvaluationConfig $evaluationConfig
   */
  public function setEvaluationConfig(GoogleCloudAiplatformV1EvaluationRunEvaluationConfig $evaluationConfig)
  {
    $this->evaluationConfig = $evaluationConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunEvaluationConfig
   */
  public function getEvaluationConfig()
  {
    return $this->evaluationConfig;
  }
  /**
   * Output only. The results of the evaluation run. Only populated when the
   * evaluation run's state is SUCCEEDED.
   *
   * @param GoogleCloudAiplatformV1EvaluationResults $evaluationResults
   */
  public function setEvaluationResults(GoogleCloudAiplatformV1EvaluationResults $evaluationResults)
  {
    $this->evaluationResults = $evaluationResults;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationResults
   */
  public function getEvaluationResults()
  {
    return $this->evaluationResults;
  }
  /**
   * Output only. The specific evaluation set of the evaluation run. For runs
   * with an evaluation set input, this will be that same set. For runs with
   * BigQuery input, it's the sampled BigQuery dataset.
   *
   * @param string $evaluationSetSnapshot
   */
  public function setEvaluationSetSnapshot($evaluationSetSnapshot)
  {
    $this->evaluationSetSnapshot = $evaluationSetSnapshot;
  }
  /**
   * @return string
   */
  public function getEvaluationSetSnapshot()
  {
    return $this->evaluationSetSnapshot;
  }
  /**
   * Optional. The candidate to inference config map for the evaluation run. The
   * candidate can be up to 128 characters long and can consist of any UTF-8
   * characters.
   *
   * @param GoogleCloudAiplatformV1EvaluationRunInferenceConfig[] $inferenceConfigs
   */
  public function setInferenceConfigs($inferenceConfigs)
  {
    $this->inferenceConfigs = $inferenceConfigs;
  }
  /**
   * @return GoogleCloudAiplatformV1EvaluationRunInferenceConfig[]
   */
  public function getInferenceConfigs()
  {
    return $this->inferenceConfigs;
  }
  /**
   * Optional. Labels for the evaluation run.
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
   * Optional. Metadata about the evaluation run, can be used by the caller to
   * store additional tracking information about the evaluation run.
   *
   * @param array $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Identifier. The resource name of the EvaluationRun. This is a unique
   * identifier. Format:
   * `projects/{project}/locations/{location}/evaluationRuns/{evaluation_run}`
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
   * Output only. The state of the evaluation run.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED, FAILED,
   * CANCELLED, INFERENCE, GENERATING_RUBRICS
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
class_alias(GoogleCloudAiplatformV1EvaluationRun::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1EvaluationRun');
