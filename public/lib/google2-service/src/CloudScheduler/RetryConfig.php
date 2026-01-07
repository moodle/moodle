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

namespace Google\Service\CloudScheduler;

class RetryConfig extends \Google\Model
{
  /**
   * The maximum amount of time to wait before retrying a job after it fails.
   * The default value of this field is 1 hour.
   *
   * @var string
   */
  public $maxBackoffDuration;
  /**
   * The time between retries will double `max_doublings` times. A job's retry
   * interval starts at min_backoff_duration, then doubles `max_doublings`
   * times, then increases linearly, and finally retries at intervals of
   * max_backoff_duration up to retry_count times. For examples, see [Retry
   * jobs](/scheduler/docs/configuring/retry-jobs#max-doublings). The default
   * value of this field is 5.
   *
   * @var int
   */
  public $maxDoublings;
  /**
   * The time limit for retrying a failed job, measured from the time when an
   * execution was first attempted. If specified with retry_count, the job will
   * be retried until both limits are reached. The default value for
   * max_retry_duration is zero, which means retry duration is unlimited.
   * However, if retry_count is also 0, a job attempt won't be retried if it
   * fails.
   *
   * @var string
   */
  public $maxRetryDuration;
  /**
   * The minimum amount of time to wait before retrying a job after it fails.
   * The default value of this field is 5 seconds.
   *
   * @var string
   */
  public $minBackoffDuration;
  /**
   * The number of attempts that the system will make to run a job using the
   * exponential backoff procedure described by max_doublings. The default value
   * of retry_count is zero. If retry_count is 0 (and if max_retry_duration is
   * also 0), a job attempt won't be retried if it fails. Instead, Cloud
   * Scheduler system will wait for the next scheduled execution time. Setting
   * retry_count to 0 doesn't prevent failed jobs from running according to
   * schedule after the failure. If retry_count is set to a non-zero number,
   * Cloud Scheduler will retry the failed job, using exponential backoff, for
   * retry_count times until the job succeeds or the number of retries is
   * exhausted. Note that the next scheduled execution time might be skipped if
   * the retries continue through that time. Values greater than 5 and negative
   * values are not allowed.
   *
   * @var int
   */
  public $retryCount;

  /**
   * The maximum amount of time to wait before retrying a job after it fails.
   * The default value of this field is 1 hour.
   *
   * @param string $maxBackoffDuration
   */
  public function setMaxBackoffDuration($maxBackoffDuration)
  {
    $this->maxBackoffDuration = $maxBackoffDuration;
  }
  /**
   * @return string
   */
  public function getMaxBackoffDuration()
  {
    return $this->maxBackoffDuration;
  }
  /**
   * The time between retries will double `max_doublings` times. A job's retry
   * interval starts at min_backoff_duration, then doubles `max_doublings`
   * times, then increases linearly, and finally retries at intervals of
   * max_backoff_duration up to retry_count times. For examples, see [Retry
   * jobs](/scheduler/docs/configuring/retry-jobs#max-doublings). The default
   * value of this field is 5.
   *
   * @param int $maxDoublings
   */
  public function setMaxDoublings($maxDoublings)
  {
    $this->maxDoublings = $maxDoublings;
  }
  /**
   * @return int
   */
  public function getMaxDoublings()
  {
    return $this->maxDoublings;
  }
  /**
   * The time limit for retrying a failed job, measured from the time when an
   * execution was first attempted. If specified with retry_count, the job will
   * be retried until both limits are reached. The default value for
   * max_retry_duration is zero, which means retry duration is unlimited.
   * However, if retry_count is also 0, a job attempt won't be retried if it
   * fails.
   *
   * @param string $maxRetryDuration
   */
  public function setMaxRetryDuration($maxRetryDuration)
  {
    $this->maxRetryDuration = $maxRetryDuration;
  }
  /**
   * @return string
   */
  public function getMaxRetryDuration()
  {
    return $this->maxRetryDuration;
  }
  /**
   * The minimum amount of time to wait before retrying a job after it fails.
   * The default value of this field is 5 seconds.
   *
   * @param string $minBackoffDuration
   */
  public function setMinBackoffDuration($minBackoffDuration)
  {
    $this->minBackoffDuration = $minBackoffDuration;
  }
  /**
   * @return string
   */
  public function getMinBackoffDuration()
  {
    return $this->minBackoffDuration;
  }
  /**
   * The number of attempts that the system will make to run a job using the
   * exponential backoff procedure described by max_doublings. The default value
   * of retry_count is zero. If retry_count is 0 (and if max_retry_duration is
   * also 0), a job attempt won't be retried if it fails. Instead, Cloud
   * Scheduler system will wait for the next scheduled execution time. Setting
   * retry_count to 0 doesn't prevent failed jobs from running according to
   * schedule after the failure. If retry_count is set to a non-zero number,
   * Cloud Scheduler will retry the failed job, using exponential backoff, for
   * retry_count times until the job succeeds or the number of retries is
   * exhausted. Note that the next scheduled execution time might be skipped if
   * the retries continue through that time. Values greater than 5 and negative
   * values are not allowed.
   *
   * @param int $retryCount
   */
  public function setRetryCount($retryCount)
  {
    $this->retryCount = $retryCount;
  }
  /**
   * @return int
   */
  public function getRetryCount()
  {
    return $this->retryCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetryConfig::class, 'Google_Service_CloudScheduler_RetryConfig');
