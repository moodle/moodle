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

namespace Google\Service\SecurityCommandCenter\Resource;

use Google\Service\SecurityCommandCenter\ListValuedResourcesResponse;

/**
 * The "valuedResources" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $valuedResources = $securitycenterService->organizations_simulations_attackExposureResults_valuedResources;
 *  </code>
 */
class OrganizationsSimulationsAttackExposureResultsValuedResources extends \Google\Service\Resource
{
  /**
   * Lists the valued resources for a set of simulation results and filter. (value
   * dResources.listOrganizationsSimulationsAttackExposureResultsValuedResources)
   *
   * @param string $parent Required. Name of parent to list valued resources.
   * Valid formats: `organizations/{organization}`,
   * `organizations/{organization}/simulations/{simulation}` `organizations/{organ
   * ization}/simulations/{simulation}/attackExposureResults/{attack_exposure_resu
   * lt_v2}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The filter expression that filters the valued
   * resources in the response. Supported fields: * `resource_value` supports = *
   * `resource_type` supports =
   * @opt_param string orderBy Optional. The fields by which to order the valued
   * resources response. Supported fields: * `exposed_score` * `resource_value` *
   * `resource_type` * `resource` * `display_name` Values should be a comma
   * separated list of fields. For example: `exposed_score,resource_value`. The
   * default sorting order is descending. To specify ascending or descending order
   * for a field, append a ` ASC` or a ` DESC` suffix, respectively; for example:
   * `exposed_score DESC`.
   * @opt_param int pageSize The maximum number of results to return in a single
   * response. Default is 10, minimum is 1, maximum is 1000.
   * @opt_param string pageToken The value returned by the last
   * `ListValuedResourcesResponse`; indicates that this is a continuation of a
   * prior `ListValuedResources` call, and that the system should return the next
   * page of data.
   * @return ListValuedResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSimulationsAttackExposureResultsValuedResources($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListValuedResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSimulationsAttackExposureResultsValuedResources::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsSimulationsAttackExposureResultsValuedResources');
