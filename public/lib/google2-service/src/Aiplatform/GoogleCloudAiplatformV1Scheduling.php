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

class GoogleCloudAiplatformV1Scheduling extends \Google\Model
{
  /**
   * Strategy will default to STANDARD.
   */
  public const STRATEGY_STRATEGY_UNSPECIFIED = 'STRATEGY_UNSPECIFIED';
  /**
   * Deprecated. Regular on-demand provisioning strategy.
   *
   * @deprecated
   */
  public const STRATEGY_ON_DEMAND = 'ON_DEMAND';
  /**
   * Deprecated. Low cost by making potential use of spot resources.
   *
   * @deprecated
   */
  public const STRATEGY_LOW_COST = 'LOW_COST';
  /**
   * Standard provisioning strategy uses regular on-demand resources.
   */
  public const STRATEGY_STANDARD = 'STANDARD';
  /**
   * Spot provisioning strategy uses spot resources.
   */
  public const STRATEGY_SPOT = 'SPOT';
  /**
   * Flex Start strategy uses DWS to queue for resources.
   */
  public const STRATEGY_FLEX_START = 'FLEX_START';
  /**
   * Optional. Indicates if the job should retry for internal errors after the
   * job starts running. If true, overrides
   * `Scheduling.restart_job_on_worker_restart` to false.
   *
   * @var bool
   */
  public $disableRetries;
  /**
   * Optional. This is the maximum duration that a job will wait for the
   * requested resources to be provisioned if the scheduling strategy is set to
   * [Strategy.DWS_FLEX_START]. If set to 0, the job will wait indefinitely. The
   * default is 24 hours.
   *
   * @var string
   */
  public $maxWaitDuration;
  /**
   * Optional. Restarts the entire CustomJob if a worker gets restarted. This
   * feature can be used by distributed training jobs that are not resilient to
   * workers leaving and joining a job.
   *
   * @var bool
   */
  public $restartJobOnWorkerRestart;
  /**
   * Optional. This determines which type of scheduling strategy to use.
   *
   * @var string
   */
  public $strategy;
  /**
   * Optional. The maximum job running time. The default is 7 days.
   *
   * @var string
   */
  public $timeout;

  /**
   * Optional. Indicates if the job should retry for internal errors after the
   * job starts running. If true, overrides
   * `Scheduling.restart_job_on_worker_restart` to false.
   *
   * @param bool $disableRetries
   */
  public function setDisableRetries($disableRetries)
  {
    $this->disableRetries = $disableRetries;
  }
  /**
   * @return bool
   */
  public function getDisableRetries()
  {
    return $this->disableRetries;
  }
  /**
   * Optional. This is the maximum duration that a job will wait for the
   * requested resources to be provisioned if the scheduling strategy is set to
   * [Strategy.DWS_FLEX_START]. If set to 0, the job will wait indefinitely. The
   * default is 24 hours.
   *
   * @param string $maxWaitDuration
   */
  public function setMaxWaitDuration($maxWaitDuration)
  {
    $this->maxWaitDuration = $maxWaitDuration;
  }
  /**
   * @return string
   */
  public function getMaxWaitDuration()
  {
    return $this->maxWaitDuration;
  }
  /**
   * Optional. Restarts the entire CustomJob if a worker gets restarted. This
   * feature can be used by distributed training jobs that are not resilient to
   * workers leaving and joining a job.
   *
   * @param bool $restartJobOnWorkerRestart
   */
  public function setRestartJobOnWorkerRestart($restartJobOnWorkerRestart)
  {
    $this->restartJobOnWorkerRestart = $restartJobOnWorkerRestart;
  }
  /**
   * @return bool
   */
  public function getRestartJobOnWorkerRestart()
  {
    return $this->restartJobOnWorkerRestart;
  }
  /**
   * Optional. This determines which type of scheduling strategy to use.
   *
   * Accepted values: STRATEGY_UNSPECIFIED, ON_DEMAND, LOW_COST, STANDARD, SPOT,
   * FLEX_START
   *
   * @param self::STRATEGY_* $strategy
   */
  public function setStrategy($strategy)
  {
    $this->strategy = $strategy;
  }
  /**
   * @return self::STRATEGY_*
   */
  public function getStrategy()
  {
    return $this->strategy;
  }
  /**
   * Optional. The maximum job running time. The default is 7 days.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Scheduling::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Scheduling');
