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

namespace Google\Service\CloudTasks;

class RetryConfig extends \Google\Model
{
  /**
   * Number of attempts per task. Cloud Tasks will attempt the task
   * `max_attempts` times (that is, if the first attempt fails, then there will
   * be `max_attempts - 1` retries). Must be >= -1. If unspecified when the
   * queue is created, Cloud Tasks will pick the default. -1 indicates unlimited
   * attempts. This field has the same meaning as [task_retry_limit in queue.yam
   * l/xml](https://cloud.google.com/appengine/docs/standard/python/config/queue
   * ref#retry_parameters). Note: Cloud Tasks stops retrying only when
   * `max_attempts` and `max_retry_duration` are both satisfied. When the task
   * has been attempted `max_attempts` times and when the `max_retry_duration`
   * time has passed, no further attempts are made, and the task is deleted. If
   * you want your task to retry infinitely, you must set `max_attempts` to -1
   * and `max_retry_duration` to 0.
   *
   * @var int
   */
  public $maxAttempts;
  /**
   * A task will be scheduled for retry between min_backoff and max_backoff
   * duration after it fails, if the queue's RetryConfig specifies that the task
   * should be retried. If unspecified when the queue is created, Cloud Tasks
   * will pick the default. The value must be given as a string that indicates
   * the length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration). `max_backoff` will
   * be truncated to the nearest second. This field has the same meaning as
   * [max_backoff_seconds in queue.yaml/xml](https://cloud.google.com/appengine/
   * docs/standard/python/config/queueref#retry_parameters).
   *
   * @var string
   */
  public $maxBackoff;
  /**
   * The time between retries will double `max_doublings` times. A task's retry
   * interval starts at min_backoff, then doubles `max_doublings` times, then
   * increases linearly, and finally retries at intervals of max_backoff up to
   * max_attempts times. For example, if min_backoff is 10s, max_backoff is
   * 300s, and `max_doublings` is 3, then the a task will first be retried in
   * 10s. The retry interval will double three times, and then increase linearly
   * by 2^3 * 10s. Finally, the task will retry at intervals of max_backoff
   * until the task has been attempted max_attempts times. Thus, the requests
   * will retry at 10s, 20s, 40s, 80s, 160s, 240s, 300s, 300s, .... If
   * unspecified when the queue is created, Cloud Tasks will pick the default.
   * This field has the same meaning as [max_doublings in queue.yaml/xml](https:
   * //cloud.google.com/appengine/docs/standard/python/config/queueref#retry_par
   * ameters).
   *
   * @var int
   */
  public $maxDoublings;
  /**
   * If positive, `max_retry_duration` specifies the time limit for retrying a
   * failed task, measured from when the task was first attempted. Once
   * `max_retry_duration` time has passed *and* the task has been attempted
   * max_attempts times, no further attempts will be made and the task will be
   * deleted. If zero, then the task age is unlimited. If unspecified when the
   * queue is created, Cloud Tasks will pick the default. The value must be
   * given as a string that indicates the length of time (in seconds) followed
   * by `s` (for "seconds"). For the maximum possible value or the format, see
   * the documentation for [Duration](https://protobuf.dev/reference/protobuf/go
   * ogle.protobuf/#duration). `max_retry_duration` will be truncated to the
   * nearest second. This field has the same meaning as [task_age_limit in queue
   * .yaml/xml](https://cloud.google.com/appengine/docs/standard/python/config/q
   * ueueref#retry_parameters).
   *
   * @var string
   */
  public $maxRetryDuration;
  /**
   * A task will be scheduled for retry between min_backoff and max_backoff
   * duration after it fails, if the queue's RetryConfig specifies that the task
   * should be retried. If unspecified when the queue is created, Cloud Tasks
   * will pick the default. The value must be given as a string that indicates
   * the length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration). `min_backoff` will
   * be truncated to the nearest second. This field has the same meaning as
   * [min_backoff_seconds in queue.yaml/xml](https://cloud.google.com/appengine/
   * docs/standard/python/config/queueref#retry_parameters).
   *
   * @var string
   */
  public $minBackoff;

  /**
   * Number of attempts per task. Cloud Tasks will attempt the task
   * `max_attempts` times (that is, if the first attempt fails, then there will
   * be `max_attempts - 1` retries). Must be >= -1. If unspecified when the
   * queue is created, Cloud Tasks will pick the default. -1 indicates unlimited
   * attempts. This field has the same meaning as [task_retry_limit in queue.yam
   * l/xml](https://cloud.google.com/appengine/docs/standard/python/config/queue
   * ref#retry_parameters). Note: Cloud Tasks stops retrying only when
   * `max_attempts` and `max_retry_duration` are both satisfied. When the task
   * has been attempted `max_attempts` times and when the `max_retry_duration`
   * time has passed, no further attempts are made, and the task is deleted. If
   * you want your task to retry infinitely, you must set `max_attempts` to -1
   * and `max_retry_duration` to 0.
   *
   * @param int $maxAttempts
   */
  public function setMaxAttempts($maxAttempts)
  {
    $this->maxAttempts = $maxAttempts;
  }
  /**
   * @return int
   */
  public function getMaxAttempts()
  {
    return $this->maxAttempts;
  }
  /**
   * A task will be scheduled for retry between min_backoff and max_backoff
   * duration after it fails, if the queue's RetryConfig specifies that the task
   * should be retried. If unspecified when the queue is created, Cloud Tasks
   * will pick the default. The value must be given as a string that indicates
   * the length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration). `max_backoff` will
   * be truncated to the nearest second. This field has the same meaning as
   * [max_backoff_seconds in queue.yaml/xml](https://cloud.google.com/appengine/
   * docs/standard/python/config/queueref#retry_parameters).
   *
   * @param string $maxBackoff
   */
  public function setMaxBackoff($maxBackoff)
  {
    $this->maxBackoff = $maxBackoff;
  }
  /**
   * @return string
   */
  public function getMaxBackoff()
  {
    return $this->maxBackoff;
  }
  /**
   * The time between retries will double `max_doublings` times. A task's retry
   * interval starts at min_backoff, then doubles `max_doublings` times, then
   * increases linearly, and finally retries at intervals of max_backoff up to
   * max_attempts times. For example, if min_backoff is 10s, max_backoff is
   * 300s, and `max_doublings` is 3, then the a task will first be retried in
   * 10s. The retry interval will double three times, and then increase linearly
   * by 2^3 * 10s. Finally, the task will retry at intervals of max_backoff
   * until the task has been attempted max_attempts times. Thus, the requests
   * will retry at 10s, 20s, 40s, 80s, 160s, 240s, 300s, 300s, .... If
   * unspecified when the queue is created, Cloud Tasks will pick the default.
   * This field has the same meaning as [max_doublings in queue.yaml/xml](https:
   * //cloud.google.com/appengine/docs/standard/python/config/queueref#retry_par
   * ameters).
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
   * If positive, `max_retry_duration` specifies the time limit for retrying a
   * failed task, measured from when the task was first attempted. Once
   * `max_retry_duration` time has passed *and* the task has been attempted
   * max_attempts times, no further attempts will be made and the task will be
   * deleted. If zero, then the task age is unlimited. If unspecified when the
   * queue is created, Cloud Tasks will pick the default. The value must be
   * given as a string that indicates the length of time (in seconds) followed
   * by `s` (for "seconds"). For the maximum possible value or the format, see
   * the documentation for [Duration](https://protobuf.dev/reference/protobuf/go
   * ogle.protobuf/#duration). `max_retry_duration` will be truncated to the
   * nearest second. This field has the same meaning as [task_age_limit in queue
   * .yaml/xml](https://cloud.google.com/appengine/docs/standard/python/config/q
   * ueueref#retry_parameters).
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
   * A task will be scheduled for retry between min_backoff and max_backoff
   * duration after it fails, if the queue's RetryConfig specifies that the task
   * should be retried. If unspecified when the queue is created, Cloud Tasks
   * will pick the default. The value must be given as a string that indicates
   * the length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration). `min_backoff` will
   * be truncated to the nearest second. This field has the same meaning as
   * [min_backoff_seconds in queue.yaml/xml](https://cloud.google.com/appengine/
   * docs/standard/python/config/queueref#retry_parameters).
   *
   * @param string $minBackoff
   */
  public function setMinBackoff($minBackoff)
  {
    $this->minBackoff = $minBackoff;
  }
  /**
   * @return string
   */
  public function getMinBackoff()
  {
    return $this->minBackoff;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetryConfig::class, 'Google_Service_CloudTasks_RetryConfig');
