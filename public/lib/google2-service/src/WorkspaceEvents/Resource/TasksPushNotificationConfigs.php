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

use Google\Service\WorkspaceEvents\ListTaskPushNotificationConfigResponse;
use Google\Service\WorkspaceEvents\TaskPushNotificationConfig;
use Google\Service\WorkspaceEvents\WorkspaceeventsEmpty;

/**
 * The "pushNotificationConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $workspaceeventsService = new Google\Service\WorkspaceEvents(...);
 *   $pushNotificationConfigs = $workspaceeventsService->tasks_pushNotificationConfigs;
 *  </code>
 */
class TasksPushNotificationConfigs extends \Google\Service\Resource
{
  /**
   * Set a push notification config for a task. (pushNotificationConfigs.create)
   *
   * @param string $parent Required. The parent task resource for this config.
   * Format: tasks/{task_id}
   * @param TaskPushNotificationConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string configId Required. The ID for the new config.
   * @return TaskPushNotificationConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, TaskPushNotificationConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], TaskPushNotificationConfig::class);
  }
  /**
   * Delete a push notification config for a task.
   * (pushNotificationConfigs.delete)
   *
   * @param string $name The resource name of the config to delete. Format:
   * tasks/{task_id}/pushNotificationConfigs/{config_id}
   * @param array $optParams Optional parameters.
   * @return WorkspaceeventsEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], WorkspaceeventsEmpty::class);
  }
  /**
   * Get a push notification config for a task. (pushNotificationConfigs.get)
   *
   * @param string $name The resource name of the config to retrieve. Format:
   * tasks/{task_id}/pushNotificationConfigs/{config_id}
   * @param array $optParams Optional parameters.
   * @return TaskPushNotificationConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], TaskPushNotificationConfig::class);
  }
  /**
   * Get a list of push notifications configured for a task.
   * (pushNotificationConfigs.listTasksPushNotificationConfigs)
   *
   * @param string $parent The parent task resource. Format: tasks/{task_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize For AIP-158 these fields are present. Usually not
   * used/needed. The maximum number of configurations to return. If unspecified,
   * all configs will be returned.
   * @opt_param string pageToken A page token received from a previous
   * ListTaskPushNotificationConfigRequest call. Provide this to retrieve the
   * subsequent page. When paginating, all other parameters provided to
   * `ListTaskPushNotificationConfigRequest` must match the call that provided the
   * page token.
   * @return ListTaskPushNotificationConfigResponse
   * @throws \Google\Service\Exception
   */
  public function listTasksPushNotificationConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListTaskPushNotificationConfigResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TasksPushNotificationConfigs::class, 'Google_Service_WorkspaceEvents_Resource_TasksPushNotificationConfigs');
