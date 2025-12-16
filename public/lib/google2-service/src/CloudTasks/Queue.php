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

class Queue extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The queue is running. Tasks can be dispatched. If the queue was created
   * using Cloud Tasks and the queue has had no activity (method calls or task
   * dispatches) for 30 days, the queue may take a few minutes to re-activate.
   * Some method calls may return NOT_FOUND and tasks may not be dispatched for
   * a few minutes until the queue has been re-activated.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Tasks are paused by the user. If the queue is paused then Cloud Tasks will
   * stop delivering tasks from it, but more tasks can still be added to it by
   * the user.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The queue is disabled. A queue becomes `DISABLED` when [queue.yaml](https:/
   * /cloud.google.com/appengine/docs/python/config/queueref) or [queue.xml](htt
   * ps://cloud.google.com/appengine/docs/standard/java/config/queueref) is
   * uploaded which does not contain the queue. You cannot directly disable a
   * queue. When a queue is disabled, tasks can still be added to a queue but
   * the tasks are not dispatched. To permanently delete this queue and all of
   * its tasks, call DeleteQueue.
   */
  public const STATE_DISABLED = 'DISABLED';
  protected $appEngineRoutingOverrideType = AppEngineRouting::class;
  protected $appEngineRoutingOverrideDataType = '';
  protected $httpTargetType = HttpTarget::class;
  protected $httpTargetDataType = '';
  /**
   * Caller-specified and required in CreateQueue, after which it becomes output
   * only. The queue name. The queue name must have the following format:
   * `projects/PROJECT_ID/locations/LOCATION_ID/queues/QUEUE_ID` * `PROJECT_ID`
   * can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-), colons (:),
   * or periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects) * `LOCATION_ID` is the canonical ID for the
   * queue's location. The list of available locations can be obtained by
   * calling ListLocations. For more information, see
   * https://cloud.google.com/about/locations/. * `QUEUE_ID` can contain letters
   * ([A-Za-z]), numbers ([0-9]), or hyphens (-). The maximum length is 100
   * characters.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The last time this queue was purged. All tasks that were
   * created before this time were purged. A queue can be purged using
   * PurgeQueue, the [App Engine Task Queue SDK, or the Cloud Console](https://c
   * loud.google.com/appengine/docs/standard/python/taskqueue/push/deleting-
   * tasks-and-queues#purging_all_tasks_from_a_queue). Purge time will be
   * truncated to the nearest microsecond. Purge time will be unset if the queue
   * has never been purged.
   *
   * @var string
   */
  public $purgeTime;
  protected $rateLimitsType = RateLimits::class;
  protected $rateLimitsDataType = '';
  protected $retryConfigType = RetryConfig::class;
  protected $retryConfigDataType = '';
  protected $stackdriverLoggingConfigType = StackdriverLoggingConfig::class;
  protected $stackdriverLoggingConfigDataType = '';
  /**
   * Output only. The state of the queue. `state` can only be changed by calling
   * PauseQueue, ResumeQueue, or uploading [queue.yaml/xml](https://cloud.google
   * .com/appengine/docs/python/config/queueref). UpdateQueue cannot be used to
   * change `state`.
   *
   * @var string
   */
  public $state;

  /**
   * Overrides for task-level app_engine_routing. These settings apply only to
   * App Engine tasks in this queue. Http tasks are not affected. If set,
   * `app_engine_routing_override` is used for all App Engine tasks in the
   * queue, no matter what the setting is for the task-level app_engine_routing.
   *
   * @param AppEngineRouting $appEngineRoutingOverride
   */
  public function setAppEngineRoutingOverride(AppEngineRouting $appEngineRoutingOverride)
  {
    $this->appEngineRoutingOverride = $appEngineRoutingOverride;
  }
  /**
   * @return AppEngineRouting
   */
  public function getAppEngineRoutingOverride()
  {
    return $this->appEngineRoutingOverride;
  }
  /**
   * Modifies HTTP target for HTTP tasks.
   *
   * @param HttpTarget $httpTarget
   */
  public function setHttpTarget(HttpTarget $httpTarget)
  {
    $this->httpTarget = $httpTarget;
  }
  /**
   * @return HttpTarget
   */
  public function getHttpTarget()
  {
    return $this->httpTarget;
  }
  /**
   * Caller-specified and required in CreateQueue, after which it becomes output
   * only. The queue name. The queue name must have the following format:
   * `projects/PROJECT_ID/locations/LOCATION_ID/queues/QUEUE_ID` * `PROJECT_ID`
   * can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-), colons (:),
   * or periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects) * `LOCATION_ID` is the canonical ID for the
   * queue's location. The list of available locations can be obtained by
   * calling ListLocations. For more information, see
   * https://cloud.google.com/about/locations/. * `QUEUE_ID` can contain letters
   * ([A-Za-z]), numbers ([0-9]), or hyphens (-). The maximum length is 100
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
   * Output only. The last time this queue was purged. All tasks that were
   * created before this time were purged. A queue can be purged using
   * PurgeQueue, the [App Engine Task Queue SDK, or the Cloud Console](https://c
   * loud.google.com/appengine/docs/standard/python/taskqueue/push/deleting-
   * tasks-and-queues#purging_all_tasks_from_a_queue). Purge time will be
   * truncated to the nearest microsecond. Purge time will be unset if the queue
   * has never been purged.
   *
   * @param string $purgeTime
   */
  public function setPurgeTime($purgeTime)
  {
    $this->purgeTime = $purgeTime;
  }
  /**
   * @return string
   */
  public function getPurgeTime()
  {
    return $this->purgeTime;
  }
  /**
   * Rate limits for task dispatches. rate_limits and retry_config are related
   * because they both control task attempts. However they control task attempts
   * in different ways: * rate_limits controls the total rate of dispatches from
   * a queue (i.e. all traffic dispatched from the queue, regardless of whether
   * the dispatch is from a first attempt or a retry). * retry_config controls
   * what happens to particular a task after its first attempt fails. That is,
   * retry_config controls task retries (the second attempt, third attempt,
   * etc). The queue's actual dispatch rate is the result of: * Number of tasks
   * in the queue * User-specified throttling: rate_limits, retry_config, and
   * the queue's state. * System throttling due to `429` (Too Many Requests) or
   * `503` (Service Unavailable) responses from the worker, high error rates, or
   * to smooth sudden large traffic spikes.
   *
   * @param RateLimits $rateLimits
   */
  public function setRateLimits(RateLimits $rateLimits)
  {
    $this->rateLimits = $rateLimits;
  }
  /**
   * @return RateLimits
   */
  public function getRateLimits()
  {
    return $this->rateLimits;
  }
  /**
   * Settings that determine the retry behavior. * For tasks created using Cloud
   * Tasks: the queue-level retry settings apply to all tasks in the queue that
   * were created using Cloud Tasks. Retry settings cannot be set on individual
   * tasks. * For tasks created using the App Engine SDK: the queue-level retry
   * settings apply to all tasks in the queue which do not have retry settings
   * explicitly set on the task and were created by the App Engine SDK. See [App
   * Engine documentation](https://cloud.google.com/appengine/docs/standard/pyth
   * on/taskqueue/push/retrying-tasks).
   *
   * @param RetryConfig $retryConfig
   */
  public function setRetryConfig(RetryConfig $retryConfig)
  {
    $this->retryConfig = $retryConfig;
  }
  /**
   * @return RetryConfig
   */
  public function getRetryConfig()
  {
    return $this->retryConfig;
  }
  /**
   * Configuration options for writing logs to [Stackdriver
   * Logging](https://cloud.google.com/logging/docs/). If this field is unset,
   * then no logs are written.
   *
   * @param StackdriverLoggingConfig $stackdriverLoggingConfig
   */
  public function setStackdriverLoggingConfig(StackdriverLoggingConfig $stackdriverLoggingConfig)
  {
    $this->stackdriverLoggingConfig = $stackdriverLoggingConfig;
  }
  /**
   * @return StackdriverLoggingConfig
   */
  public function getStackdriverLoggingConfig()
  {
    return $this->stackdriverLoggingConfig;
  }
  /**
   * Output only. The state of the queue. `state` can only be changed by calling
   * PauseQueue, ResumeQueue, or uploading [queue.yaml/xml](https://cloud.google
   * .com/appengine/docs/python/config/queueref). UpdateQueue cannot be used to
   * change `state`.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, PAUSED, DISABLED
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
class_alias(Queue::class, 'Google_Service_CloudTasks_Queue');
