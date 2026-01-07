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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaFailurePolicy extends \Google\Model
{
  /**
   * UNSPECIFIED.
   */
  public const RETRY_STRATEGY_RETRY_STRATEGY_UNSPECIFIED = 'RETRY_STRATEGY_UNSPECIFIED';
  /**
   * Ignores the failure of this task. The rest of the integration will be
   * executed Assuming this task succeeded.
   */
  public const RETRY_STRATEGY_IGNORE = 'IGNORE';
  /**
   * Causes a permanent failure of the task. However, if the last task(s) of
   * event was successfully completed despite the failure of this task, it has
   * no impact on the integration.
   */
  public const RETRY_STRATEGY_NONE = 'NONE';
  /**
   * Causes a permanent failure of the event. It is different from NONE because
   * this will mark the event as FAILED by shutting down the event execution.
   */
  public const RETRY_STRATEGY_FATAL = 'FATAL';
  /**
   * The task will be retried from the failed task onwards after a fixed delay.
   * A max-retry count is required to be specified with this strategy. A jitter
   * is added to each exponential interval so that concurrently failing tasks of
   * the same type do not end up retrying after the exact same exponential
   * interval. max_retries and interval_in_seconds must be specified.
   */
  public const RETRY_STRATEGY_FIXED_INTERVAL = 'FIXED_INTERVAL';
  /**
   * The task will be retried from the failed task onwards after a fixed delay
   * that linearly increases with each retry attempt. A jitter is added to each
   * exponential interval so that concurrently failing tasks of the same type do
   * not end up retrying after the exact same exponential interval. A max-retry
   * count is required to be specified with this strategy. max_retries and
   * interval_in_seconds must be specified.
   */
  public const RETRY_STRATEGY_LINEAR_BACKOFF = 'LINEAR_BACKOFF';
  /**
   * The task will be retried after an exponentially increasing period of time
   * with each failure. A jitter is added to each exponential interval so that
   * concurrently failing tasks of the same type do not end up retrying after
   * the exact same exponential interval. A max-retry count is required to be
   * specified with this strategy. `max_retries` and `interval_in_seconds` must
   * be specified.
   */
  public const RETRY_STRATEGY_EXPONENTIAL_BACKOFF = 'EXPONENTIAL_BACKOFF';
  /**
   * The entire integration will be restarted with the initial parameters that
   * were set when the event was fired. A max-retry count is required to be
   * specified with this strategy. `max_retries` and `interval_in_seconds` must
   * be specified.
   */
  public const RETRY_STRATEGY_RESTART_INTEGRATION_WITH_BACKOFF = 'RESTART_INTEGRATION_WITH_BACKOFF';
  /**
   * Optional. The string condition that will be evaluated to determine if the
   * task should be retried with this failure policy.
   *
   * @var string
   */
  public $condition;
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_INTEGRATION_WITH_BACKOFF. Defines the
   * initial interval in seconds for backoff.
   *
   * @var string
   */
  public $intervalTime;
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_INTEGRATION_WITH_BACKOFF. Defines the
   * number of times the task will be retried if failed.
   *
   * @var int
   */
  public $maxRetries;
  /**
   * Defines what happens to the task upon failure.
   *
   * @var string
   */
  public $retryStrategy;

  /**
   * Optional. The string condition that will be evaluated to determine if the
   * task should be retried with this failure policy.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_INTEGRATION_WITH_BACKOFF. Defines the
   * initial interval in seconds for backoff.
   *
   * @param string $intervalTime
   */
  public function setIntervalTime($intervalTime)
  {
    $this->intervalTime = $intervalTime;
  }
  /**
   * @return string
   */
  public function getIntervalTime()
  {
    return $this->intervalTime;
  }
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_INTEGRATION_WITH_BACKOFF. Defines the
   * number of times the task will be retried if failed.
   *
   * @param int $maxRetries
   */
  public function setMaxRetries($maxRetries)
  {
    $this->maxRetries = $maxRetries;
  }
  /**
   * @return int
   */
  public function getMaxRetries()
  {
    return $this->maxRetries;
  }
  /**
   * Defines what happens to the task upon failure.
   *
   * Accepted values: RETRY_STRATEGY_UNSPECIFIED, IGNORE, NONE, FATAL,
   * FIXED_INTERVAL, LINEAR_BACKOFF, EXPONENTIAL_BACKOFF,
   * RESTART_INTEGRATION_WITH_BACKOFF
   *
   * @param self::RETRY_STRATEGY_* $retryStrategy
   */
  public function setRetryStrategy($retryStrategy)
  {
    $this->retryStrategy = $retryStrategy;
  }
  /**
   * @return self::RETRY_STRATEGY_*
   */
  public function getRetryStrategy()
  {
    return $this->retryStrategy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaFailurePolicy::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaFailurePolicy');
