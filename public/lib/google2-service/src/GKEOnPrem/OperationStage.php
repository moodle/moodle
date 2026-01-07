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

namespace Google\Service\GKEOnPrem;

class OperationStage extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STAGE_STAGE_UNSPECIFIED = 'STAGE_UNSPECIFIED';
  /**
   * Preflight checks are running.
   */
  public const STAGE_PREFLIGHT_CHECK = 'PREFLIGHT_CHECK';
  /**
   * Resource is being configured.
   */
  public const STAGE_CONFIGURE = 'CONFIGURE';
  /**
   * Resource is being deployed.
   */
  public const STAGE_DEPLOY = 'DEPLOY';
  /**
   * Waiting for the resource to become healthy.
   */
  public const STAGE_HEALTH_CHECK = 'HEALTH_CHECK';
  /**
   * Resource is being updated.
   */
  public const STAGE_UPDATE = 'UPDATE';
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The stage is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The stage is running
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The stage has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The stage has failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'metrics';
  /**
   * Time the stage ended.
   *
   * @var string
   */
  public $endTime;
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  /**
   * The high-level stage of the operation.
   *
   * @var string
   */
  public $stage;
  /**
   * Time the stage started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. State of the stage.
   *
   * @var string
   */
  public $state;

  /**
   * Time the stage ended.
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
   * Progress metric bundle.
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The high-level stage of the operation.
   *
   * Accepted values: STAGE_UNSPECIFIED, PREFLIGHT_CHECK, CONFIGURE, DEPLOY,
   * HEALTH_CHECK, UPDATE
   *
   * @param self::STAGE_* $stage
   */
  public function setStage($stage)
  {
    $this->stage = $stage;
  }
  /**
   * @return self::STAGE_*
   */
  public function getStage()
  {
    return $this->stage;
  }
  /**
   * Time the stage started.
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
   * Output only. State of the stage.
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
class_alias(OperationStage::class, 'Google_Service_GKEOnPrem_OperationStage');
