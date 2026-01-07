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

class EnterpriseCrmEventbusProtoFailurePolicy extends \Google\Model
{
  public const RETRY_STRATEGY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Ignores the failure of this task. The rest of the workflow will be executed
   * Assuming this task succeeded.
   */
  public const RETRY_STRATEGY_IGNORE = 'IGNORE';
  /**
   * Causes a permanent failure of the task. However, if the last task(s) of
   * event was successfully completed despite the failure of this task, it has
   * no impact on the workflow.
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
   * interval. Max_num_retries and interval_in_seconds must be specified.
   */
  public const RETRY_STRATEGY_FIXED_INTERVAL = 'FIXED_INTERVAL';
  /**
   * The task will be retried from the failed task onwards after a fixed delay
   * that linearly increases with each retry attempt. A jitter is added to each
   * exponential interval so that concurrently failing tasks of the same type do
   * not end up retrying after the exact same exponential interval. A max-retry
   * count is required to be specified with this strategy. Max_num_retries and
   * interval_in_seconds must be specified.
   */
  public const RETRY_STRATEGY_LINEAR_BACKOFF = 'LINEAR_BACKOFF';
  /**
   * The task will be retried after an exponentially increasing period of time
   * with each failure. A jitter is added to each exponential interval so that
   * concurrently failing tasks of the same type do not end up retrying after
   * the exact same exponential interval. A max-retry count is required to be
   * specified with this strategy. `max_num_retries` and `interval_in_seconds`
   * must be specified.
   */
  public const RETRY_STRATEGY_EXPONENTIAL_BACKOFF = 'EXPONENTIAL_BACKOFF';
  /**
   * The entire workflow will be restarted with the initial parameters that were
   * set when the event was fired. A max-retry count is required to be specified
   * with this strategy. `max_num_retries` and `interval_in_seconds` must be
   * specified.
   */
  public const RETRY_STRATEGY_RESTART_WORKFLOW_WITH_BACKOFF = 'RESTART_WORKFLOW_WITH_BACKOFF';
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_WORKFLOW_WITH_BACKOFF. Defines the
   * initial interval for backoff.
   *
   * @var string
   */
  public $intervalInSeconds;
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_WORKFLOW_WITH_BACKOFF. Defines the
   * number of times the task will be retried if failed.
   *
   * @var int
   */
  public $maxNumRetries;
  /**
   * Optional. The retry condition that will be evaluated for this failure
   * policy with the corresponding retry strategy.
   *
   * @var string
   */
  public $retryCondition;
  /**
   * Defines what happens to the task upon failure.
   *
   * @var string
   */
  public $retryStrategy;

  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_WORKFLOW_WITH_BACKOFF. Defines the
   * initial interval for backoff.
   *
   * @param string $intervalInSeconds
   */
  public function setIntervalInSeconds($intervalInSeconds)
  {
    $this->intervalInSeconds = $intervalInSeconds;
  }
  /**
   * @return string
   */
  public function getIntervalInSeconds()
  {
    return $this->intervalInSeconds;
  }
  /**
   * Required if retry_strategy is FIXED_INTERVAL or
   * LINEAR/EXPONENTIAL_BACKOFF/RESTART_WORKFLOW_WITH_BACKOFF. Defines the
   * number of times the task will be retried if failed.
   *
   * @param int $maxNumRetries
   */
  public function setMaxNumRetries($maxNumRetries)
  {
    $this->maxNumRetries = $maxNumRetries;
  }
  /**
   * @return int
   */
  public function getMaxNumRetries()
  {
    return $this->maxNumRetries;
  }
  /**
   * Optional. The retry condition that will be evaluated for this failure
   * policy with the corresponding retry strategy.
   *
   * @param string $retryCondition
   */
  public function setRetryCondition($retryCondition)
  {
    $this->retryCondition = $retryCondition;
  }
  /**
   * @return string
   */
  public function getRetryCondition()
  {
    return $this->retryCondition;
  }
  /**
   * Defines what happens to the task upon failure.
   *
   * Accepted values: UNSPECIFIED, IGNORE, NONE, FATAL, FIXED_INTERVAL,
   * LINEAR_BACKOFF, EXPONENTIAL_BACKOFF, RESTART_WORKFLOW_WITH_BACKOFF
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
class_alias(EnterpriseCrmEventbusProtoFailurePolicy::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoFailurePolicy');
