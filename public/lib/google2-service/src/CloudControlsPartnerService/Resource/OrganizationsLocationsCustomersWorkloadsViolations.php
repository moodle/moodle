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

use Google\Service\CloudControlsPartnerService\ListViolationsResponse;
use Google\Service\CloudControlsPartnerService\Violation;

/**
 * The "violations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcontrolspartnerService = new Google\Service\CloudControlsPartnerService(...);
 *   $violations = $cloudcontrolspartnerService->organizations_locations_customers_workloads_violations;
 *  </code>
 */
class OrganizationsLocationsCustomersWorkloadsViolations extends \Google\Service\Resource
{
  /**
   * Gets details of a single Violation. (violations.get)
   *
   * @param string $name Required. Format: `organizations/{organization}/locations
   * /{location}/customers/{customer}/workloads/{workload}/violations/{violation}`
   * @param array $optParams Optional parameters.
   * @return Violation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Violation::class);
  }
  /**
   * Lists Violations for a workload Callers may also choose to read across
   * multiple Customers or for a single customer as per
   * [AIP-159](https://google.aip.dev/159) by using '-' (the hyphen or dash
   * character) as a wildcard character instead of {customer} & {workload}.
   * Format: `organizations/{organization}/locations/{location}/customers/{custome
   * r}/workloads/{workload}`
   * (violations.listOrganizationsLocationsCustomersWorkloadsViolations)
   *
   * @param string $parent Required. Parent resource Format `organizations/{organi
   * zation}/locations/{location}/customers/{customer}/workloads/{workload}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results
   * @opt_param string interval.endTime Optional. Exclusive end of the interval.
   * If specified, a Timestamp matching this interval will have to be before the
   * end.
   * @opt_param string interval.startTime Optional. Inclusive start of the
   * interval. If specified, a Timestamp matching this interval will have to be
   * the same or after the start.
   * @opt_param string orderBy Optional. Hint for how to order the results
   * @opt_param int pageSize Optional. The maximum number of customers row to
   * return. The service may return fewer than this value. If unspecified, at most
   * 10 customers will be returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListViolations` call. Provide this to retrieve the subsequent page.
   * @return ListViolationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsCustomersWorkloadsViolations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListViolationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsCustomersWorkloadsViolations::class, 'Google_Service_CloudControlsPartnerService_Resource_OrganizationsLocationsCustomersWorkloadsViolations');
