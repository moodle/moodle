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

use Google\Service\CloudControlsPartnerService\ListAccessApprovalRequestsResponse;

/**
 * The "accessApprovalRequests" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudcontrolspartnerService = new Google\Service\CloudControlsPartnerService(...);
 *   $accessApprovalRequests = $cloudcontrolspartnerService->organizations_locations_customers_workloads_accessApprovalRequests;
 *  </code>
 */
class OrganizationsLocationsCustomersWorkloadsAccessApprovalRequests extends \Google\Service\Resource
{
  /**
   * Deprecated: Only returns access approval requests directly associated with an
   * assured workload folder. (accessApprovalRequests.listOrganizationsLocationsCu
   * stomersWorkloadsAccessApprovalRequests)
   *
   * @param string $parent Required. Parent resource Format: `organizations/{organ
   * ization}/locations/{location}/customers/{customer}/workloads/{workload}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filtering results.
   * @opt_param string orderBy Optional. Hint for how to order the results.
   * @opt_param int pageSize Optional. The maximum number of access requests to
   * return. The service may return fewer than this value. If unspecified, at most
   * 500 access requests will be returned.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListAccessApprovalRequests` call. Provide this to retrieve the subsequent
   * page.
   * @return ListAccessApprovalRequestsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsLocationsCustomersWorkloadsAccessApprovalRequests($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccessApprovalRequestsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsLocationsCustomersWorkloadsAccessApprovalRequests::class, 'Google_Service_CloudControlsPartnerService_Resource_OrganizationsLocationsCustomersWorkloadsAccessApprovalRequests');
