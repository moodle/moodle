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

namespace Google\Service\CloudDeploy\Resource;

use Google\Service\CloudDeploy\AutomationRun;
use Google\Service\CloudDeploy\CancelAutomationRunRequest;
use Google\Service\CloudDeploy\CancelAutomationRunResponse;
use Google\Service\CloudDeploy\ListAutomationRunsResponse;

/**
 * The "automationRuns" collection of methods.
 * Typical usage is:
 *  <code>
 *   $clouddeployService = new Google\Service\CloudDeploy(...);
 *   $automationRuns = $clouddeployService->projects_locations_deliveryPipelines_automationRuns;
 *  </code>
 */
class ProjectsLocationsDeliveryPipelinesAutomationRuns extends \Google\Service\Resource
{
  /**
   * Cancels an AutomationRun. The `state` of the `AutomationRun` after cancelling
   * is `CANCELLED`. `CancelAutomationRun` can be called on AutomationRun in the
   * state `IN_PROGRESS` and `PENDING`; AutomationRun in a different state returns
   * an `FAILED_PRECONDITION` error. (automationRuns.cancel)
   *
   * @param string $name Required. Name of the `AutomationRun`. Format is `project
   * s/{project}/locations/{location}/deliveryPipelines/{delivery_pipeline}/automa
   * tionRuns/{automation_run}`.
   * @param CancelAutomationRunRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelAutomationRunResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelAutomationRunRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelAutomationRunResponse::class);
  }
  /**
   * Gets details of a single AutomationRun. (automationRuns.get)
   *
   * @param string $name Required. Name of the `AutomationRun`. Format must be `pr
   * ojects/{project}/locations/{location}/deliveryPipelines/{delivery_pipeline}/a
   * utomationRuns/{automation_run}`.
   * @param array $optParams Optional parameters.
   * @return AutomationRun
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AutomationRun::class);
  }
  /**
   * Lists AutomationRuns in a given project and location.
   * (automationRuns.listProjectsLocationsDeliveryPipelinesAutomationRuns)
   *
   * @param string $parent Required. The parent `Delivery Pipeline`, which owns
   * this collection of automationRuns. Format must be `projects/{project}/locatio
   * ns/{location}/deliveryPipelines/{delivery_pipeline}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter automationRuns to be returned. All fields can
   * be used in the filter.
   * @opt_param string orderBy Field to sort by.
   * @opt_param int pageSize The maximum number of automationRuns to return. The
   * service may return fewer than this value. If unspecified, at most 50
   * automationRuns will be returned. The maximum value is 1000; values above 1000
   * will be set to 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListAutomationRuns` call. Provide this to retrieve the subsequent page. When
   * paginating, all other provided parameters match the call that provided the
   * page token.
   * @return ListAutomationRunsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDeliveryPipelinesAutomationRuns($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAutomationRunsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDeliveryPipelinesAutomationRuns::class, 'Google_Service_CloudDeploy_Resource_ProjectsLocationsDeliveryPipelinesAutomationRuns');
