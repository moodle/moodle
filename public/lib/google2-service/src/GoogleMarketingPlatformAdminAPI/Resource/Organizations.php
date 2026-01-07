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

namespace Google\Service\GoogleMarketingPlatformAdminAPI\Resource;

use Google\Service\GoogleMarketingPlatformAdminAPI\FindSalesPartnerManagedClientsRequest;
use Google\Service\GoogleMarketingPlatformAdminAPI\FindSalesPartnerManagedClientsResponse;
use Google\Service\GoogleMarketingPlatformAdminAPI\ListOrganizationsResponse;
use Google\Service\GoogleMarketingPlatformAdminAPI\Organization;
use Google\Service\GoogleMarketingPlatformAdminAPI\ReportPropertyUsageRequest;
use Google\Service\GoogleMarketingPlatformAdminAPI\ReportPropertyUsageResponse;

/**
 * The "organizations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $marketingplatformadminService = new Google\Service\GoogleMarketingPlatformAdminAPI(...);
 *   $organizations = $marketingplatformadminService->organizations;
 *  </code>
 */
class Organizations extends \Google\Service\Resource
{
  /**
   * Returns a list of clients managed by the sales partner organization. User
   * needs to be an OrgAdmin/BillingAdmin on the sales partner organization in
   * order to view the end clients. (organizations.findSalesPartnerManagedClients)
   *
   * @param string $organization Required. The name of the sales partner
   * organization. Format: organizations/{org_id}
   * @param FindSalesPartnerManagedClientsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return FindSalesPartnerManagedClientsResponse
   * @throws \Google\Service\Exception
   */
  public function findSalesPartnerManagedClients($organization, FindSalesPartnerManagedClientsRequest $postBody, $optParams = [])
  {
    $params = ['organization' => $organization, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('findSalesPartnerManagedClients', [$params], FindSalesPartnerManagedClientsResponse::class);
  }
  /**
   * Lookup for a single organization. (organizations.get)
   *
   * @param string $name Required. The name of the Organization to retrieve.
   * Format: organizations/{org_id}
   * @param array $optParams Optional parameters.
   * @return Organization
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Organization::class);
  }
  /**
   * Returns a list of organizations that the user has access to.
   * (organizations.listOrganizations)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of organizations to
   * return in one call. The service may return fewer than this value. If
   * unspecified, at most 50 organizations will be returned. The maximum value is
   * 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListOrganizations call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListOrganizations` must match
   * the call that provided the page token.
   * @return ListOrganizationsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizations($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListOrganizationsResponse::class);
  }
  /**
   * Get the usage and billing data for properties within the organization for the
   * specified month. Per direct client org, user needs to be
   * OrgAdmin/BillingAdmin on the organization in order to view the billing and
   * usage data. Per sales partner client org, user needs to be
   * OrgAdmin/BillingAdmin on the sales partner org in order to view the billing
   * and usage data, or OrgAdmin/BillingAdmin on the sales partner client org in
   * order to view the usage data only. (organizations.reportPropertyUsage)
   *
   * @param string $organization Required. Specifies the organization whose
   * property usage will be listed. Format: organizations/{org_id}
   * @param ReportPropertyUsageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return ReportPropertyUsageResponse
   * @throws \Google\Service\Exception
   */
  public function reportPropertyUsage($organization, ReportPropertyUsageRequest $postBody, $optParams = [])
  {
    $params = ['organization' => $organization, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reportPropertyUsage', [$params], ReportPropertyUsageResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Organizations::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_Resource_Organizations');
