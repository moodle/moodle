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

namespace Google\Service\DeveloperConnect\Resource;

use Google\Service\DeveloperConnect\DeploymentEvent;
use Google\Service\DeveloperConnect\ListDeploymentEventsResponse;

/**
 * The "deploymentEvents" collection of methods.
 * Typical usage is:
 *  <code>
 *   $developerconnectService = new Google\Service\DeveloperConnect(...);
 *   $deploymentEvents = $developerconnectService->projects_locations_insightsConfigs_deploymentEvents;
 *  </code>
 */
class ProjectsLocationsInsightsConfigsDeploymentEvents extends \Google\Service\Resource
{
  /**
   * Gets a single Deployment Event. (deploymentEvents.get)
   *
   * @param string $name Required. The name of the deployment event to retrieve.
   * Format: projects/{project}/locations/{location}/insightsConfigs/{insights_con
   * fig}/deploymentEvents/{uuid}
   * @param array $optParams Optional parameters.
   * @return DeploymentEvent
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DeploymentEvent::class);
  }
  /**
   * Lists Deployment Events in a given insights config.
   * (deploymentEvents.listProjectsLocationsInsightsConfigsDeploymentEvents)
   *
   * @param string $parent Required. The parent insights config that owns this
   * collection of deployment events. Format:
   * projects/{project}/locations/{location}/insightsConfigs/{insights_config}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression that matches a subset of
   * the DeploymentEvents. https://google.aip.dev/160.
   * @opt_param int pageSize Optional. The maximum number of deployment events to
   * return. The service may return fewer than this value. If unspecified, at most
   * 50 deployment events will be returned. The maximum value is 1000; values
   * above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDeploymentEvents` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListDeploymentEvents` must
   * match the call that provided the page token.
   * @return ListDeploymentEventsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInsightsConfigsDeploymentEvents($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDeploymentEventsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInsightsConfigsDeploymentEvents::class, 'Google_Service_DeveloperConnect_Resource_ProjectsLocationsInsightsConfigsDeploymentEvents');
