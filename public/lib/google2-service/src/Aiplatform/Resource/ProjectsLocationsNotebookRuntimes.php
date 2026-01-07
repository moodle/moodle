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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1AssignNotebookRuntimeRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListNotebookRuntimesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1NotebookRuntime;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1StartNotebookRuntimeRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1StopNotebookRuntimeRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1UpgradeNotebookRuntimeRequest;
use Google\Service\Aiplatform\GoogleLongrunningOperation;

/**
 * The "notebookRuntimes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $notebookRuntimes = $aiplatformService->projects_locations_notebookRuntimes;
 *  </code>
 */
class ProjectsLocationsNotebookRuntimes extends \Google\Service\Resource
{
  /**
   * Assigns a NotebookRuntime to a user for a particular Notebook file. This
   * method will either returns an existing assignment or generates a new one.
   * (notebookRuntimes.assign)
   *
   * @param string $parent Required. The resource name of the Location to get the
   * NotebookRuntime assignment. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1AssignNotebookRuntimeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function assign($parent, GoogleCloudAiplatformV1AssignNotebookRuntimeRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('assign', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a NotebookRuntime. (notebookRuntimes.delete)
   *
   * @param string $name Required. The name of the NotebookRuntime resource to be
   * deleted. Instead of checking whether the name is in valid NotebookRuntime
   * resource name format, directly throw NotFound exception if there is no such
   * NotebookRuntime in spanner.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a NotebookRuntime. (notebookRuntimes.get)
   *
   * @param string $name Required. The name of the NotebookRuntime resource.
   * Instead of checking whether the name is in valid NotebookRuntime resource
   * name format, directly throw NotFound exception if there is no such
   * NotebookRuntime in spanner.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1NotebookRuntime
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1NotebookRuntime::class);
  }
  /**
   * Lists NotebookRuntimes in a Location.
   * (notebookRuntimes.listProjectsLocationsNotebookRuntimes)
   *
   * @param string $parent Required. The resource name of the Location from which
   * to list the NotebookRuntimes. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the results of
   * the request. For field names both snake_case and camelCase are supported. *
   * `notebookRuntime` supports = and !=. `notebookRuntime` represents the
   * NotebookRuntime ID, i.e. the last segment of the NotebookRuntime's resource
   * name. * `displayName` supports = and != and regex. *
   * `notebookRuntimeTemplate` supports = and !=. `notebookRuntimeTemplate`
   * represents the NotebookRuntimeTemplate ID, i.e. the last segment of the
   * NotebookRuntimeTemplate's resource name. * `healthState` supports = and !=.
   * healthState enum: [HEALTHY, UNHEALTHY, HEALTH_STATE_UNSPECIFIED]. *
   * `runtimeState` supports = and !=. runtimeState enum:
   * [RUNTIME_STATE_UNSPECIFIED, RUNNING, BEING_STARTED, BEING_STOPPED, STOPPED,
   * BEING_UPGRADED, ERROR, INVALID]. * `runtimeUser` supports = and !=. * API
   * version is UI only: `uiState` supports = and !=. uiState enum:
   * [UI_RESOURCE_STATE_UNSPECIFIED, UI_RESOURCE_STATE_BEING_CREATED,
   * UI_RESOURCE_STATE_ACTIVE, UI_RESOURCE_STATE_BEING_DELETED,
   * UI_RESOURCE_STATE_CREATION_FAILED]. * `notebookRuntimeType` supports = and
   * !=. notebookRuntimeType enum: [USER_DEFINED, ONE_CLICK]. * `machineType`
   * supports = and !=. * `acceleratorType` supports = and !=. Some examples: *
   * `notebookRuntime="notebookRuntime123"` * `displayName="myDisplayName"` and
   * `displayName=~"myDisplayNameRegex"` *
   * `notebookRuntimeTemplate="notebookRuntimeTemplate321"` *
   * `healthState=HEALTHY` * `runtimeState=RUNNING` *
   * `runtimeUser="test@google.com"` * `uiState=UI_RESOURCE_STATE_BEING_DELETED` *
   * `notebookRuntimeType=USER_DEFINED` * `machineType=e2-standard-4` *
   * `acceleratorType=NVIDIA_TESLA_T4`
   * @opt_param string orderBy Optional. A comma-separated list of fields to order
   * by, sorted in ascending order. Use "desc" after a field name for descending.
   * Supported fields: * `display_name` * `create_time` * `update_time` Example:
   * `display_name, create_time desc`.
   * @opt_param int pageSize Optional. The standard list page size.
   * @opt_param string pageToken Optional. The standard list page token. Typically
   * obtained via ListNotebookRuntimesResponse.next_page_token of the previous
   * NotebookService.ListNotebookRuntimes call.
   * @opt_param string readMask Optional. Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListNotebookRuntimesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNotebookRuntimes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListNotebookRuntimesResponse::class);
  }
  /**
   * Starts a NotebookRuntime. (notebookRuntimes.start)
   *
   * @param string $name Required. The name of the NotebookRuntime resource to be
   * started. Instead of checking whether the name is in valid NotebookRuntime
   * resource name format, directly throw NotFound exception if there is no such
   * NotebookRuntime in spanner.
   * @param GoogleCloudAiplatformV1StartNotebookRuntimeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function start($name, GoogleCloudAiplatformV1StartNotebookRuntimeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('start', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Stops a NotebookRuntime. (notebookRuntimes.stop)
   *
   * @param string $name Required. The name of the NotebookRuntime resource to be
   * stopped. Instead of checking whether the name is in valid NotebookRuntime
   * resource name format, directly throw NotFound exception if there is no such
   * NotebookRuntime in spanner.
   * @param GoogleCloudAiplatformV1StopNotebookRuntimeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function stop($name, GoogleCloudAiplatformV1StopNotebookRuntimeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Upgrades a NotebookRuntime. (notebookRuntimes.upgrade)
   *
   * @param string $name Required. The name of the NotebookRuntime resource to be
   * upgrade. Instead of checking whether the name is in valid NotebookRuntime
   * resource name format, directly throw NotFound exception if there is no such
   * NotebookRuntime in spanner.
   * @param GoogleCloudAiplatformV1UpgradeNotebookRuntimeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function upgrade($name, GoogleCloudAiplatformV1UpgradeNotebookRuntimeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('upgrade', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsNotebookRuntimes::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsNotebookRuntimes');
