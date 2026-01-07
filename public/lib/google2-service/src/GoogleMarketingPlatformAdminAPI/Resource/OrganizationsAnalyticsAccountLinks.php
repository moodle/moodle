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

use Google\Service\GoogleMarketingPlatformAdminAPI\AnalyticsAccountLink;
use Google\Service\GoogleMarketingPlatformAdminAPI\ListAnalyticsAccountLinksResponse;
use Google\Service\GoogleMarketingPlatformAdminAPI\MarketingplatformadminEmpty;
use Google\Service\GoogleMarketingPlatformAdminAPI\SetPropertyServiceLevelRequest;
use Google\Service\GoogleMarketingPlatformAdminAPI\SetPropertyServiceLevelResponse;

/**
 * The "analyticsAccountLinks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $marketingplatformadminService = new Google\Service\GoogleMarketingPlatformAdminAPI(...);
 *   $analyticsAccountLinks = $marketingplatformadminService->organizations_analyticsAccountLinks;
 *  </code>
 */
class OrganizationsAnalyticsAccountLinks extends \Google\Service\Resource
{
  /**
   * Creates the link between the Analytics account and the Google Marketing
   * Platform organization. User needs to be an org user, and admin on the
   * Analytics account to create the link. If the account is already linked to an
   * organization, user needs to unlink the account from the current organization,
   * then try link again. (analyticsAccountLinks.create)
   *
   * @param string $parent Required. The parent resource where this Analytics
   * account link will be created. Format: organizations/{org_id}
   * @param AnalyticsAccountLink $postBody
   * @param array $optParams Optional parameters.
   * @return AnalyticsAccountLink
   * @throws \Google\Service\Exception
   */
  public function create($parent, AnalyticsAccountLink $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AnalyticsAccountLink::class);
  }
  /**
   * Deletes the AnalyticsAccountLink, which detaches the Analytics account from
   * the Google Marketing Platform organization. User needs to be an org user, and
   * admin on the Analytics account in order to delete the link.
   * (analyticsAccountLinks.delete)
   *
   * @param string $name Required. The name of the Analytics account link to
   * delete. Format:
   * organizations/{org_id}/analyticsAccountLinks/{analytics_account_link_id}
   * @param array $optParams Optional parameters.
   * @return MarketingplatformadminEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], MarketingplatformadminEmpty::class);
  }
  /**
   * Lists the Google Analytics accounts link to the specified Google Marketing
   * Platform organization.
   * (analyticsAccountLinks.listOrganizationsAnalyticsAccountLinks)
   *
   * @param string $parent Required. The parent organization, which owns this
   * collection of Analytics account links. Format: organizations/{org_id}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of Analytics account
   * links to return in one call. The service may return fewer than this value. If
   * unspecified, at most 50 Analytics account links will be returned. The maximum
   * value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * ListAnalyticsAccountLinks call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListAnalyticsAccountLinks`
   * must match the call that provided the page token.
   * @return ListAnalyticsAccountLinksResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsAnalyticsAccountLinks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAnalyticsAccountLinksResponse::class);
  }
  /**
   * Updates the service level for an Analytics property.
   * (analyticsAccountLinks.setPropertyServiceLevel)
   *
   * @param string $analyticsAccountLink Required. The parent AnalyticsAccountLink
   * scope where this property is in. Format:
   * organizations/{org_id}/analyticsAccountLinks/{analytics_account_link_id}
   * @param SetPropertyServiceLevelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SetPropertyServiceLevelResponse
   * @throws \Google\Service\Exception
   */
  public function setPropertyServiceLevel($analyticsAccountLink, SetPropertyServiceLevelRequest $postBody, $optParams = [])
  {
    $params = ['analyticsAccountLink' => $analyticsAccountLink, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setPropertyServiceLevel', [$params], SetPropertyServiceLevelResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsAnalyticsAccountLinks::class, 'Google_Service_GoogleMarketingPlatformAdminAPI_Resource_OrganizationsAnalyticsAccountLinks');
