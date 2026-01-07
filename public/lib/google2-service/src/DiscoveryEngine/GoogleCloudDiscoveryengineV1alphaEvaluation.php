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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaEvaluation extends \Google\Collection
{
  /**
   * The evaluation is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The service is preparing to run the evaluation.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The evaluation is in progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The evaluation completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The evaluation failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'errorSamples';
  /**
   * Output only. Timestamp the Evaluation was created at.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Timestamp the Evaluation was completed at.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  protected $errorSamplesType = GoogleRpcStatus::class;
  protected $errorSamplesDataType = 'array';
  protected $evaluationSpecType = GoogleCloudDiscoveryengineV1alphaEvaluationEvaluationSpec::class;
  protected $evaluationSpecDataType = '';
  /**
   * Identifier. The full resource name of the Evaluation, in the format of
   * `projects/{project}/locations/{location}/evaluations/{evaluation}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
   *
   * @var string
   */
  public $name;
  protected $qualityMetricsType = GoogleCloudDiscoveryengineV1alphaQualityMetrics::class;
  protected $qualityMetricsDataType = '';
  /**
   * Output only. The state of the evaluation.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Timestamp the Evaluation was created at.
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
   * Output only. Timestamp the Evaluation was completed at.
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
   * Output only. The error that occurred during evaluation. Only populated when
   * the evaluation's state is FAILED.
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
   * Output only. A sample of errors encountered while processing the request.
   *
   * @param GoogleRpcStatus[] $errorSamples
   */
  public function setErrorSamples($errorSamples)
  {
    $this->errorSamples = $errorSamples;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrorSamples()
  {
    return $this->errorSamples;
  }
  /**
   * Required. The specification of the evaluation.
   *
   * @param GoogleCloudDiscoveryengineV1alphaEvaluationEvaluationSpec $evaluationSpec
   */
  public function setEvaluationSpec(GoogleCloudDiscoveryengineV1alphaEvaluationEvaluationSpec $evaluationSpec)
  {
    $this->evaluationSpec = $evaluationSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaEvaluationEvaluationSpec
   */
  public function getEvaluationSpec()
  {
    return $this->evaluationSpec;
  }
  /**
   * Identifier. The full resource name of the Evaluation, in the format of
   * `projects/{project}/locations/{location}/evaluations/{evaluation}`. This
   * field must be a UTF-8 encoded string with a length limit of 1024
   * characters.
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
   * Output only. The metrics produced by the evaluation, averaged across all
   * SampleQuerys in the SampleQuerySet. Only populated when the evaluation's
   * state is SUCCEEDED.
   *
   * @param GoogleCloudDiscoveryengineV1alphaQualityMetrics $qualityMetrics
   */
  public function setQualityMetrics(GoogleCloudDiscoveryengineV1alphaQualityMetrics $qualityMetrics)
  {
    $this->qualityMetrics = $qualityMetrics;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaQualityMetrics
   */
  public function getQualityMetrics()
  {
    return $this->qualityMetrics;
  }
  /**
   * Output only. The state of the evaluation.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED, FAILED
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
class_alias(GoogleCloudDiscoveryengineV1alphaEvaluation::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaEvaluation');
