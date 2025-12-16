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

namespace Google\Service\WorkspaceEvents\Resource;

use Google\Service\WorkspaceEvents\CancelTaskRequest;
use Google\Service\WorkspaceEvents\StreamResponse;
use Google\Service\WorkspaceEvents\Task;

/**
 * The "tasks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workspaceeventsService = new Google\Service\WorkspaceEvents(...);
 *   $tasks = $workspaceeventsService->tasks;
 *  </code>
 */
class Tasks extends \Google\Service\Resource
{
  /**
   * Cancel a task from the agent. If supported one should expect no more task
   * updates for the task. (tasks.cancel)
   *
   * @param string $name The resource name of the task to cancel. Format:
   * tasks/{task_id}
   * @param CancelTaskRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Task
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelTaskRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], Task::class);
  }
  /**
   * Get the current state of a task from the agent. (tasks.get)
   *
   * @param string $name Required. The resource name of the task. Format:
   * tasks/{task_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int historyLength The number of most recent messages from the
   * task's history to retrieve.
   * @return Task
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Task::class);
  }
  /**
   * TaskSubscription is a streaming call that will return a stream of task update
   * events. This attaches the stream to an existing in process task. If the task
   * is complete the stream will return the completed task (like GetTask) and
   * close the stream. (tasks.subscribe)
   *
   * @param string $name The resource name of the task to subscribe to. Format:
   * tasks/{task_id}
   * @param array $optParams Optional parameters.
   * @return StreamResponse
   * @throws \Google\Service\Exception
   */
  public function subscribe($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('subscribe', [$params], StreamResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Tasks::class, 'Google_Service_WorkspaceEvents_Resource_Tasks');
