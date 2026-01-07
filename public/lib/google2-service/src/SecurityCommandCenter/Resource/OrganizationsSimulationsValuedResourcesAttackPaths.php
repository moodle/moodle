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

use Google\Service\SecurityCommandCenter\ListAttackPathsResponse;

/**
 * The "attackPaths" collection of methods.
 * Typical usage is:
 *  <code>
 *   $securitycenterService = new Google\Service\SecurityCommandCenter(...);
 *   $attackPaths = $securitycenterService->organizations_simulations_valuedResources_attackPaths;
 *  </code>
 */
class OrganizationsSimulationsValuedResourcesAttackPaths extends \Google\Service\Resource
{
  /**
   * Lists the attack paths for a set of simulation results or valued resources
   * and filter.
   * (attackPaths.listOrganizationsSimulationsValuedResourcesAttackPaths)
   *
   * @param string $parent Required. Name of parent to list attack paths. Valid
   * formats: `organizations/{organization}`,
   * `organizations/{organization}/simulations/{simulation}` `organizations/{organ
   * ization}/simulations/{simulation}/attackExposureResults/{attack_exposure_resu
   * lt_v2}` `organizations/{organization}/simulations/{simulation}/valuedResource
   * s/{valued_resource}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The filter expression that filters the attack path
   * in the response. Supported fields: * `valued_resources` supports =
   * @opt_param int pageSize The maximum number of results to return in a single
   * response. Default is 10, minimum is 1, maximum is 1000.
   * @opt_param string pageToken The value returned by the last
   * `ListAttackPathsResponse`; indicates that this is a continuation of a prior
   * `ListAttackPaths` call, and that the system should return the next page of
   * data.
   * @return ListAttackPathsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSimulationsValuedResourcesAttackPaths($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAttackPathsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSimulationsValuedResourcesAttackPaths::class, 'Google_Service_SecurityCommandCenter_Resource_OrganizationsSimulationsValuedResourcesAttackPaths');
