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

class Task extends \Google\Model
{
  /**
   * Unspecified. Defaults to BASIC.
   */
  public const VIEW_VIEW_UNSPECIFIED = 'VIEW_UNSPECIFIED';
  /**
   * The basic view omits fields which can be large or can contain sensitive
   * data. This view does not include the body in AppEngineHttpRequest. Bodies
   * are desirable to return only when needed, because they can be large and
   * because of the sensitivity of the data that you choose to store in it.
   */
  public const VIEW_BASIC = 'BASIC';
  /**
   * All information is returned. Authorization for FULL requires
   * `cloudtasks.tasks.fullView` [Google IAM](https://cloud.google.com/iam/)
   * permission on the Queue resource.
   */
  public const VIEW_FULL = 'FULL';
  protected $appEngineHttpRequestType = AppEngineHttpRequest::class;
  protected $appEngineHttpRequestDataType = '';
  /**
   * Output only. The time that the task was created. `create_time` will be
   * truncated to the nearest second.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The number of attempts dispatched. This count includes
   * attempts which have been dispatched but haven't received a response.
   *
   * @var int
   */
  public $dispatchCount;
  /**
   * The deadline for requests sent to the worker. If the worker does not
   * respond by this deadline then the request is cancelled and the attempt is
   * marked as a `DEADLINE_EXCEEDED` failure. Cloud Tasks will retry the task
   * according to the RetryConfig. Note that when the request is cancelled,
   * Cloud Tasks will stop listening for the response, but whether the worker
   * stops processing depends on the worker. For example, if the worker is
   * stuck, it may not react to cancelled requests. The default and maximum
   * values depend on the type of request: * For HTTP tasks, the default is 10
   * minutes. The deadline must be in the interval [15 seconds, 30 minutes]. *
   * For App Engine tasks, 0 indicates that the request has the default
   * deadline. The default deadline depends on the [scaling
   * type](https://cloud.google.com/appengine/docs/standard/go/how-instances-
   * are-managed#instance_scaling) of the service: 10 minutes for standard apps
   * with automatic scaling, 24 hours for standard apps with manual and basic
   * scaling, and 60 minutes for flex apps. If the request deadline is set, it
   * must be in the interval [15 seconds, 24 hours 15 seconds]. Regardless of
   * the task's `dispatch_deadline`, the app handler will not run for longer
   * than than the service's timeout. We recommend setting the
   * `dispatch_deadline` to at most a few seconds more than the app handler's
   * timeout. For more information see
   * [Timeouts](https://cloud.google.com/tasks/docs/creating-appengine-
   * handlers#timeouts). The value must be given as a string that indicates the
   * length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration).
   * `dispatch_deadline` will be truncated to the nearest millisecond. The
   * deadline is an approximate deadline.
   *
   * @var string
   */
  public $dispatchDeadline;
  protected $firstAttemptType = Attempt::class;
  protected $firstAttemptDataType = '';
  protected $httpRequestType = HttpRequest::class;
  protected $httpRequestDataType = '';
  protected $lastAttemptType = Attempt::class;
  protected $lastAttemptDataType = '';
  /**
   * Optionally caller-specified in CreateTask. The task name. The task name
   * must have the following format:
   * `projects/PROJECT_ID/locations/LOCATION_ID/queues/QUEUE_ID/tasks/TASK_ID` *
   * `PROJECT_ID` can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-),
   * colons (:), or periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects) * `LOCATION_ID` is the canonical ID for the
   * task's location. The list of available locations can be obtained by calling
   * ListLocations. For more information, see
   * https://cloud.google.com/about/locations/. * `QUEUE_ID` can contain letters
   * ([A-Za-z]), numbers ([0-9]), or hyphens (-). The maximum length is 100
   * characters. * `TASK_ID` can contain only letters ([A-Za-z]), numbers
   * ([0-9]), hyphens (-), or underscores (_). The maximum length is 500
   * characters.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of attempts which have received a response.
   *
   * @var int
   */
  public $responseCount;
  /**
   * The time when the task is scheduled to be attempted or retried.
   * `schedule_time` will be truncated to the nearest microsecond.
   *
   * @var string
   */
  public $scheduleTime;
  /**
   * Output only. The view specifies which subset of the Task has been returned.
   *
   * @var string
   */
  public $view;

  /**
   * HTTP request that is sent to the App Engine app handler. An App Engine task
   * is a task that has AppEngineHttpRequest set.
   *
   * @param AppEngineHttpRequest $appEngineHttpRequest
   */
  public function setAppEngineHttpRequest(AppEngineHttpRequest $appEngineHttpRequest)
  {
    $this->appEngineHttpRequest = $appEngineHttpRequest;
  }
  /**
   * @return AppEngineHttpRequest
   */
  public function getAppEngineHttpRequest()
  {
    return $this->appEngineHttpRequest;
  }
  /**
   * Output only. The time that the task was created. `create_time` will be
   * truncated to the nearest second.
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
   * Output only. The number of attempts dispatched. This count includes
   * attempts which have been dispatched but haven't received a response.
   *
   * @param int $dispatchCount
   */
  public function setDispatchCount($dispatchCount)
  {
    $this->dispatchCount = $dispatchCount;
  }
  /**
   * @return int
   */
  public function getDispatchCount()
  {
    return $this->dispatchCount;
  }
  /**
   * The deadline for requests sent to the worker. If the worker does not
   * respond by this deadline then the request is cancelled and the attempt is
   * marked as a `DEADLINE_EXCEEDED` failure. Cloud Tasks will retry the task
   * according to the RetryConfig. Note that when the request is cancelled,
   * Cloud Tasks will stop listening for the response, but whether the worker
   * stops processing depends on the worker. For example, if the worker is
   * stuck, it may not react to cancelled requests. The default and maximum
   * values depend on the type of request: * For HTTP tasks, the default is 10
   * minutes. The deadline must be in the interval [15 seconds, 30 minutes]. *
   * For App Engine tasks, 0 indicates that the request has the default
   * deadline. The default deadline depends on the [scaling
   * type](https://cloud.google.com/appengine/docs/standard/go/how-instances-
   * are-managed#instance_scaling) of the service: 10 minutes for standard apps
   * with automatic scaling, 24 hours for standard apps with manual and basic
   * scaling, and 60 minutes for flex apps. If the request deadline is set, it
   * must be in the interval [15 seconds, 24 hours 15 seconds]. Regardless of
   * the task's `dispatch_deadline`, the app handler will not run for longer
   * than than the service's timeout. We recommend setting the
   * `dispatch_deadline` to at most a few seconds more than the app handler's
   * timeout. For more information see
   * [Timeouts](https://cloud.google.com/tasks/docs/creating-appengine-
   * handlers#timeouts). The value must be given as a string that indicates the
   * length of time (in seconds) followed by `s` (for "seconds"). For more
   * information on the format, see the documentation for [Duration](https://pro
   * tobuf.dev/reference/protobuf/google.protobuf/#duration).
   * `dispatch_deadline` will be truncated to the nearest millisecond. The
   * deadline is an approximate deadline.
   *
   * @param string $dispatchDeadline
   */
  public function setDispatchDeadline($dispatchDeadline)
  {
    $this->dispatchDeadline = $dispatchDeadline;
  }
  /**
   * @return string
   */
  public function getDispatchDeadline()
  {
    return $this->dispatchDeadline;
  }
  /**
   * Output only. The status of the task's first attempt. Only dispatch_time
   * will be set. The other Attempt information is not retained by Cloud Tasks.
   *
   * @param Attempt $firstAttempt
   */
  public function setFirstAttempt(Attempt $firstAttempt)
  {
    $this->firstAttempt = $firstAttempt;
  }
  /**
   * @return Attempt
   */
  public function getFirstAttempt()
  {
    return $this->firstAttempt;
  }
  /**
   * HTTP request that is sent to the worker. An HTTP task is a task that has
   * HttpRequest set.
   *
   * @param HttpRequest $httpRequest
   */
  public function setHttpRequest(HttpRequest $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return HttpRequest
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * Output only. The status of the task's last attempt.
   *
   * @param Attempt $lastAttempt
   */
  public function setLastAttempt(Attempt $lastAttempt)
  {
    $this->lastAttempt = $lastAttempt;
  }
  /**
   * @return Attempt
   */
  public function getLastAttempt()
  {
    return $this->lastAttempt;
  }
  /**
   * Optionally caller-specified in CreateTask. The task name. The task name
   * must have the following format:
   * `projects/PROJECT_ID/locations/LOCATION_ID/queues/QUEUE_ID/tasks/TASK_ID` *
   * `PROJECT_ID` can contain letters ([A-Za-z]), numbers ([0-9]), hyphens (-),
   * colons (:), or periods (.). For more information, see [Identifying
   * projects](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects#identifying_projects) * `LOCATION_ID` is the canonical ID for the
   * task's location. The list of available locations can be obtained by calling
   * ListLocations. For more information, see
   * https://cloud.google.com/about/locations/. * `QUEUE_ID` can contain letters
   * ([A-Za-z]), numbers ([0-9]), or hyphens (-). The maximum length is 100
   * characters. * `TASK_ID` can contain only letters ([A-Za-z]), numbers
   * ([0-9]), hyphens (-), or underscores (_). The maximum length is 500
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
   * Output only. The number of attempts which have received a response.
   *
   * @param int $responseCount
   */
  public function setResponseCount($responseCount)
  {
    $this->responseCount = $responseCount;
  }
  /**
   * @return int
   */
  public function getResponseCount()
  {
    return $this->responseCount;
  }
  /**
   * The time when the task is scheduled to be attempted or retried.
   * `schedule_time` will be truncated to the nearest microsecond.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
  /**
   * Output only. The view specifies which subset of the Task has been returned.
   *
   * Accepted values: VIEW_UNSPECIFIED, BASIC, FULL
   *
   * @param self::VIEW_* $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return self::VIEW_*
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Task::class, 'Google_Service_CloudTasks_Task');
