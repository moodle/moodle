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

namespace Google\Service\Integrations\Resource;

use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaExecuteIntegrationsResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaListIntegrationsResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaScheduleIntegrationsResponse;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTestIntegrationsRequest;
use Google\Service\Integrations\GoogleCloudIntegrationsV1alphaTestIntegrationsResponse;

/**
 * The "integrations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $integrationsService = new Google\Service\Integrations(...);
 *   $integrations = $integrationsService->projects_locations_products_integrations;
 *  </code>
 */
class ProjectsLocationsProductsIntegrations extends \Google\Service\Resource
{
  /**
   * Executes integrations synchronously by passing the trigger id in the request
   * body. The request is not returned until the requested executions are either
   * fulfilled or experienced an error. If the integration name is not specified
   * (passing `-`), all of the associated integration under the given trigger_id
   * will be executed. Otherwise only the specified integration for the given
   * `trigger_id` is executed. This is helpful for execution the integration from
   * UI. (integrations.execute)
   *
   * @param string $name Required. The integration resource name.
   * @param GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaExecuteIntegrationsResponse
   * @throws \Google\Service\Exception
   */
  public function execute($name, GoogleCloudIntegrationsV1alphaExecuteIntegrationsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('execute', [$params], GoogleCloudIntegrationsV1alphaExecuteIntegrationsResponse::class);
  }
  /**
   * Returns the list of all integrations in the specified project.
   * (integrations.listProjectsLocationsProductsIntegrations)
   *
   * @param string $parent Required. Project and location from which the
   * integrations should be listed. Format: projects/{project}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter on fields of IntegrationVersion. Fields can
   * be compared with literal values by use of ":" (containment), "=" (equality),
   * ">" (greater), "<" (less than), >=" (greater than or equal to), "<=" (less
   * than or equal to), and "!=" (inequality) operators. Negation, conjunction,
   * and disjunction are written using NOT, AND, and OR keywords. For example,
   * organization_id=\"1\" AND state=ACTIVE AND description:"test". Filtering
   * cannot be performed on repeated fields like `task_config`.
   * @opt_param string orderBy The results would be returned in order you
   * specified here. Supported sort keys are: Descending sort order by
   * "last_modified_time", "created_time", "snapshot_number". Ascending sort order
   * by the integration name.
   * @opt_param int pageSize The page size for the resquest.
   * @opt_param string pageToken The page token for the resquest.
   * @return GoogleCloudIntegrationsV1alphaListIntegrationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsProductsIntegrations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudIntegrationsV1alphaListIntegrationsResponse::class);
  }
  /**
   * Schedules an integration for execution by passing the trigger id and the
   * scheduled time in the request body. (integrations.schedule)
   *
   * @param string $name The integration resource name.
   * @param GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaScheduleIntegrationsResponse
   * @throws \Google\Service\Exception
   */
  public function schedule($name, GoogleCloudIntegrationsV1alphaScheduleIntegrationsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('schedule', [$params], GoogleCloudIntegrationsV1alphaScheduleIntegrationsResponse::class);
  }
  /**
   * Execute the integration in draft state (integrations.test)
   *
   * @param string $name Output only. Auto-generated primary key.
   * @param GoogleCloudIntegrationsV1alphaTestIntegrationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudIntegrationsV1alphaTestIntegrationsResponse
   * @throws \Google\Service\Exception
   */
  public function test($name, GoogleCloudIntegrationsV1alphaTestIntegrationsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('test', [$params], GoogleCloudIntegrationsV1alphaTestIntegrationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsProductsIntegrations::class, 'Google_Service_Integrations_Resource_ProjectsLocationsProductsIntegrations');
