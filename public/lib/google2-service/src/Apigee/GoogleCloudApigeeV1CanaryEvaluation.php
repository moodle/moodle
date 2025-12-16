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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1CanaryEvaluation extends \Google\Model
{
  /**
   * No state has been specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The canary evaluation is still in progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The canary evaluation has finished.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Verdict is not available yet.
   */
  public const VERDICT_VERDICT_UNSPECIFIED = 'VERDICT_UNSPECIFIED';
  /**
   * No verdict reached.
   */
  public const VERDICT_NONE = 'NONE';
  /**
   * Evaluation is not good.
   */
  public const VERDICT_FAIL = 'FAIL';
  /**
   * Evaluation is good.
   */
  public const VERDICT_PASS = 'PASS';
  /**
   * Required. The stable version that is serving requests.
   *
   * @var string
   */
  public $control;
  /**
   * Output only. Create time of the canary evaluation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. End time for the evaluation's analysis.
   *
   * @var string
   */
  public $endTime;
  protected $metricLabelsType = GoogleCloudApigeeV1CanaryEvaluationMetricLabels::class;
  protected $metricLabelsDataType = '';
  /**
   * Output only. Name of the canary evalution.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Start time for the canary evaluation's analysis.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current state of the canary evaluation.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The newer version that is serving requests.
   *
   * @var string
   */
  public $treatment;
  /**
   * Output only. The resulting verdict of the canary evaluations: NONE, PASS,
   * or FAIL.
   *
   * @var string
   */
  public $verdict;

  /**
   * Required. The stable version that is serving requests.
   *
   * @param string $control
   */
  public function setControl($control)
  {
    $this->control = $control;
  }
  /**
   * @return string
   */
  public function getControl()
  {
    return $this->control;
  }
  /**
   * Output only. Create time of the canary evaluation.
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
   * Required. End time for the evaluation's analysis.
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
   * Required. Labels used to filter the metrics used for a canary evaluation.
   *
   * @param GoogleCloudApigeeV1CanaryEvaluationMetricLabels $metricLabels
   */
  public function setMetricLabels(GoogleCloudApigeeV1CanaryEvaluationMetricLabels $metricLabels)
  {
    $this->metricLabels = $metricLabels;
  }
  /**
   * @return GoogleCloudApigeeV1CanaryEvaluationMetricLabels
   */
  public function getMetricLabels()
  {
    return $this->metricLabels;
  }
  /**
   * Output only. Name of the canary evalution.
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
   * Required. Start time for the canary evaluation's analysis.
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
   * Output only. The current state of the canary evaluation.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED
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
  /**
   * Required. The newer version that is serving requests.
   *
   * @param string $treatment
   */
  public function setTreatment($treatment)
  {
    $this->treatment = $treatment;
  }
  /**
   * @return string
   */
  public function getTreatment()
  {
    return $this->treatment;
  }
  /**
   * Output only. The resulting verdict of the canary evaluations: NONE, PASS,
   * or FAIL.
   *
   * Accepted values: VERDICT_UNSPECIFIED, NONE, FAIL, PASS
   *
   * @param self::VERDICT_* $verdict
   */
  public function setVerdict($verdict)
  {
    $this->verdict = $verdict;
  }
  /**
   * @return self::VERDICT_*
   */
  public function getVerdict()
  {
    return $this->verdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1CanaryEvaluation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1CanaryEvaluation');
