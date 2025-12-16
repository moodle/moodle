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

class RateLimits extends \Google\Model
{
  /**
   * Output only. The max burst size. Max burst size limits how fast tasks in
   * queue are processed when many tasks are in the queue and the rate is high.
   * This field allows the queue to have a high rate so processing starts
   * shortly after a task is enqueued, but still limits resource usage when many
   * tasks are enqueued in a short period of time. The [token
   * bucket](https://wikipedia.org/wiki/Token_Bucket) algorithm is used to
   * control the rate of task dispatches. Each queue has a token bucket that
   * holds tokens, up to the maximum specified by `max_burst_size`. Each time a
   * task is dispatched, a token is removed from the bucket. Tasks will be
   * dispatched until the queue's bucket runs out of tokens. The bucket will be
   * continuously refilled with new tokens based on max_dispatches_per_second.
   * Cloud Tasks will pick the value of `max_burst_size` based on the value of
   * max_dispatches_per_second. For queues that were created or updated using
   * `queue.yaml/xml`, `max_burst_size` is equal to [bucket_size](https://cloud.
   * google.com/appengine/docs/standard/python/config/queueref#bucket_size).
   * Since `max_burst_size` is output only, if UpdateQueue is called on a queue
   * created by `queue.yaml/xml`, `max_burst_size` will be reset based on the
   * value of max_dispatches_per_second, regardless of whether
   * max_dispatches_per_second is updated.
   *
   * @var int
   */
  public $maxBurstSize;
  /**
   * The maximum number of concurrent tasks that Cloud Tasks allows to be
   * dispatched for this queue. After this threshold has been reached, Cloud
   * Tasks stops dispatching tasks until the number of concurrent requests
   * decreases. If unspecified when the queue is created, Cloud Tasks will pick
   * the default. The maximum allowed value is 5,000. This field has the same
   * meaning as [max_concurrent_requests in queue.yaml/xml](https://cloud.google
   * .com/appengine/docs/standard/python/config/queueref#max_concurrent_requests
   * ).
   *
   * @var int
   */
  public $maxConcurrentDispatches;
  /**
   * The maximum rate at which tasks are dispatched from this queue. If
   * unspecified when the queue is created, Cloud Tasks will pick the default. *
   * The maximum allowed value is 500. This field has the same meaning as [rate
   * in queue.yaml/xml](https://cloud.google.com/appengine/docs/standard/python/
   * config/queueref#rate).
   *
   * @var 
   */
  public $maxDispatchesPerSecond;

  /**
   * Output only. The max burst size. Max burst size limits how fast tasks in
   * queue are processed when many tasks are in the queue and the rate is high.
   * This field allows the queue to have a high rate so processing starts
   * shortly after a task is enqueued, but still limits resource usage when many
   * tasks are enqueued in a short period of time. The [token
   * bucket](https://wikipedia.org/wiki/Token_Bucket) algorithm is used to
   * control the rate of task dispatches. Each queue has a token bucket that
   * holds tokens, up to the maximum specified by `max_burst_size`. Each time a
   * task is dispatched, a token is removed from the bucket. Tasks will be
   * dispatched until the queue's bucket runs out of tokens. The bucket will be
   * continuously refilled with new tokens based on max_dispatches_per_second.
   * Cloud Tasks will pick the value of `max_burst_size` based on the value of
   * max_dispatches_per_second. For queues that were created or updated using
   * `queue.yaml/xml`, `max_burst_size` is equal to [bucket_size](https://cloud.
   * google.com/appengine/docs/standard/python/config/queueref#bucket_size).
   * Since `max_burst_size` is output only, if UpdateQueue is called on a queue
   * created by `queue.yaml/xml`, `max_burst_size` will be reset based on the
   * value of max_dispatches_per_second, regardless of whether
   * max_dispatches_per_second is updated.
   *
   * @param int $maxBurstSize
   */
  public function setMaxBurstSize($maxBurstSize)
  {
    $this->maxBurstSize = $maxBurstSize;
  }
  /**
   * @return int
   */
  public function getMaxBurstSize()
  {
    return $this->maxBurstSize;
  }
  /**
   * The maximum number of concurrent tasks that Cloud Tasks allows to be
   * dispatched for this queue. After this threshold has been reached, Cloud
   * Tasks stops dispatching tasks until the number of concurrent requests
   * decreases. If unspecified when the queue is created, Cloud Tasks will pick
   * the default. The maximum allowed value is 5,000. This field has the same
   * meaning as [max_concurrent_requests in queue.yaml/xml](https://cloud.google
   * .com/appengine/docs/standard/python/config/queueref#max_concurrent_requests
   * ).
   *
   * @param int $maxConcurrentDispatches
   */
  public function setMaxConcurrentDispatches($maxConcurrentDispatches)
  {
    $this->maxConcurrentDispatches = $maxConcurrentDispatches;
  }
  /**
   * @return int
   */
  public function getMaxConcurrentDispatches()
  {
    return $this->maxConcurrentDispatches;
  }
  public function setMaxDispatchesPerSecond($maxDispatchesPerSecond)
  {
    $this->maxDispatchesPerSecond = $maxDispatchesPerSecond;
  }
  public function getMaxDispatchesPerSecond()
  {
    return $this->maxDispatchesPerSecond;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RateLimits::class, 'Google_Service_CloudTasks_RateLimits');
