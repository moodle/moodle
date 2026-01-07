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

namespace Google\Service\AdSensePlatform\Resource;

use Google\Service\AdSensePlatform\AdsenseplatformEmpty;
use Google\Service\AdSensePlatform\ListSitesResponse;
use Google\Service\AdSensePlatform\RequestSiteReviewResponse;
use Google\Service\AdSensePlatform\Site;

/**
 * The "sites" collection of methods.
 * Typical usage is:
 *  <code>
 *   $adsenseplatformService = new Google\Service\AdSensePlatform(...);
 *   $sites = $adsenseplatformService->platforms_accounts_sites;
 *  </code>
 */
class PlatformsAccountsSites extends \Google\Service\Resource
{
  /**
   * Creates a site for a specified account. (sites.create)
   *
   * @param string $parent Required. Account to create site. Format:
   * platforms/{platform}/accounts/{account_id}
   * @param Site $postBody
   * @param array $optParams Optional parameters.
   * @return Site
   * @throws \Google\Service\Exception
   */
  public function create($parent, Site $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Site::class);
  }
  /**
   * Deletes a site from a specified account. (sites.delete)
   *
   * @param string $name Required. The name of the site to delete. Format:
   * platforms/{platform}/accounts/{account}/sites/{site}
   * @param array $optParams Optional parameters.
   * @return AdsenseplatformEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], AdsenseplatformEmpty::class);
  }
  /**
   * Gets a site from a specified sub-account. (sites.get)
   *
   * @param string $name Required. The name of the site to retrieve. Format:
   * platforms/{platform}/accounts/{account}/sites/{site}
   * @param array $optParams Optional parameters.
   * @return Site
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Site::class);
  }
  /**
   * Lists sites for a specific account. (sites.listPlatformsAccountsSites)
   *
   * @param string $parent Required. The account which owns the sites. Format:
   * platforms/{platform}/accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of sites to include in the
   * response, used for paging. If unspecified, at most 10000 sites will be
   * returned. The maximum value is 10000; values above 10000 will be coerced to
   * 10000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListSites` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListSites` must match the call
   * that provided the page token.
   * @return ListSitesResponse
   * @throws \Google\Service\Exception
   */
  public function listPlatformsAccountsSites($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSitesResponse::class);
  }
  /**
   * Requests the review of a site. The site should be in REQUIRES_REVIEW or
   * NEEDS_ATTENTION state. Note: Make sure you place an [ad
   * tag](https://developers.google.com/adsense/platforms/direct/ad-tags) on your
   * site before requesting a review. (sites.requestReview)
   *
   * @param string $name Required. The name of the site to submit for review.
   * Format: platforms/{platform}/accounts/{account}/sites/{site}
   * @param array $optParams Optional parameters.
   * @return RequestSiteReviewResponse
   * @throws \Google\Service\Exception
   */
  public function requestReview($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('requestReview', [$params], RequestSiteReviewResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlatformsAccountsSites::class, 'Google_Service_AdSensePlatform_Resource_PlatformsAccountsSites');
