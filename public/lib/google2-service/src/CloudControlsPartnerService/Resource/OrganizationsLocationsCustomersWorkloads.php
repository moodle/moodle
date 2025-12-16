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

namespace Google\Service\CloudControlsPartnerService\Resource;

use Google\Service\CloudControlsPartnerService\EkmConnections;
use Google\Service\CloudControlsPartnerService\ListWorkloadsResponse;
use Google\Service\CloudControlsPartnerService\PartnerPermissions;
use Google\Service\CloudControlsPartnerService\Workload;

/**
 * The "workloads" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcontrolspartnerService = new Google\Service\CloudControlsPartnerService(...);
 *   $workloads = $cloudcontrolspartnerService->organizations_locations_customers_workloads;
 *  </code>
 */
class OrganizationsLocationsCustomersWorkloads extends \Google\Service\Resource
{
  /**
   * Gets details of a single workload (workloads.get)
   *
   * @param string $name Required. Format: `organizations/{organization}/locations
   * /{location}/customers/{customer}/workloads/{workload}`
   * @param array $optParams Optional parameters.
   * @return Workload
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Workload::class);
  }
  /**
   * Gets the EKM connections associated with a workload
   * (workloads.getEkmConnections)
   *
   * @param string $name Required. Format: `organizations/{organization}/locations
   * /{location}/customers/{customer}/workloads/{workload}/ekmConnections`
   * @param array $optParams Optional parameters.
   * @return EkmConnections
   * @throws \Google\Service\Exception
   */
  public function getEkmConnections($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getEkmConnections', [$params], EkmConnections::class);
  }
  /**
   * Gets the partner permissions granted for a workload
   * (workloads.getPartnerPermissions)
   *
   * @param string $name Required. Name of the resource to get in the format: `org
   * anizations/{organization}/locations/{location}/customers/{customer}/workloads
   * /{workload}/partnerPermissions`
   * @param array $optParams Optional parameters.
   * @return PartnerPermissions
   * @throws \Google\Service\Exception
   */
  public function getPartnerPermissions($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getPartnerPermissions', [$params], PartnerPermissions::class);
  }
  /**
   * Lists customer workloads for a given customer org id
   * (workloads.listOrganizationsLocationsCustomersWorkloads)
   *
   * @param string $parent Required. Parent resource Format:
   * `organizations/{organization}/locations/{location}/customers/{customer}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize The maximum number of workloads to return. The
   * service may return fewer than this value. If unspecified, at most 500
   * workloads will be returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListWorkloads` call. Provide this to retrieve the subsequent page.
   * @return ListWorkloadsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsCustomersWorkloads($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListWorkloadsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsCustomersWorkloads::class, 'Google_Service_CloudControlsPartnerService_Resource_OrganizationsLocationsCustomersWorkloads');
