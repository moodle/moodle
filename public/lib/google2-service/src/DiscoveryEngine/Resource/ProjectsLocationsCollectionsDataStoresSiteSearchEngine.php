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

namespace Google\Service\DiscoveryEngine\Resource;

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1BatchVerifyTargetSitesRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1DisableAdvancedSiteSearchRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1EnableAdvancedSiteSearchRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1FetchDomainVerificationStatusResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1RecrawlUrisRequest;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "siteSearchEngine" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $siteSearchEngine = $discoveryengineService->projects_locations_collections_dataStores_siteSearchEngine;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresSiteSearchEngine extends \Google\Service\Resource
{
  /**
   * Verify target sites' ownership and validity. This API sends all the target
   * sites under site search engine for verification.
   * (siteSearchEngine.batchVerifyTargetSites)
   *
   * @param string $parent Required. The parent resource shared by all TargetSites
   * being verified. `projects/{project}/locations/{location}/collections/{collect
   * ion}/dataStores/{data_store}/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1BatchVerifyTargetSitesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchVerifyTargetSites($parent, GoogleCloudDiscoveryengineV1BatchVerifyTargetSitesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchVerifyTargetSites', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Downgrade from advanced site search to basic site search.
   * (siteSearchEngine.disableAdvancedSiteSearch)
   *
   * @param string $siteSearchEngine Required. Full resource name of the
   * SiteSearchEngine, such as `projects/{project}/locations/{location}/dataStores
   * /{data_store_id}/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1DisableAdvancedSiteSearchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function disableAdvancedSiteSearch($siteSearchEngine, GoogleCloudDiscoveryengineV1DisableAdvancedSiteSearchRequest $postBody, $optParams = [])
  {
    $params = ['siteSearchEngine' => $siteSearchEngine, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('disableAdvancedSiteSearch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Upgrade from basic site search to advanced site search.
   * (siteSearchEngine.enableAdvancedSiteSearch)
   *
   * @param string $siteSearchEngine Required. Full resource name of the
   * SiteSearchEngine, such as `projects/{project}/locations/{location}/dataStores
   * /{data_store_id}/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1EnableAdvancedSiteSearchRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function enableAdvancedSiteSearch($siteSearchEngine, GoogleCloudDiscoveryengineV1EnableAdvancedSiteSearchRequest $postBody, $optParams = [])
  {
    $params = ['siteSearchEngine' => $siteSearchEngine, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('enableAdvancedSiteSearch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Returns list of target sites with its domain verification status. This method
   * can only be called under data store with BASIC_SITE_SEARCH state at the
   * moment. (siteSearchEngine.fetchDomainVerificationStatus)
   *
   * @param string $siteSearchEngine Required. The site search engine resource
   * under which we fetch all the domain verification status. `projects/{project}/
   * locations/{location}/collections/{collection}/dataStores/{data_store}/siteSea
   * rchEngine`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default. The
   * maximum value is 1000; values above 1000 will be coerced to 1000. If this
   * field is negative, an INVALID_ARGUMENT error is returned.
   * @opt_param string pageToken A page token, received from a previous
   * `FetchDomainVerificationStatus` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `FetchDomainVerificationStatus` must match the call that provided the page
   * token.
   * @return GoogleCloudDiscoveryengineV1FetchDomainVerificationStatusResponse
   * @throws \Google\Service\Exception
   */
  public function fetchDomainVerificationStatus($siteSearchEngine, $optParams = [])
  {
    $params = ['siteSearchEngine' => $siteSearchEngine];
    $params = array_merge($params, $optParams);
    return $this->call('fetchDomainVerificationStatus', [$params], GoogleCloudDiscoveryengineV1FetchDomainVerificationStatusResponse::class);
  }
  /**
   * Request on-demand recrawl for a list of URIs. (siteSearchEngine.recrawlUris)
   *
   * @param string $siteSearchEngine Required. Full resource name of the
   * SiteSearchEngine, such as
   * `projects/locations/collections/dataStores/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1RecrawlUrisRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function recrawlUris($siteSearchEngine, GoogleCloudDiscoveryengineV1RecrawlUrisRequest $postBody, $optParams = [])
  {
    $params = ['siteSearchEngine' => $siteSearchEngine, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('recrawlUris', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresSiteSearchEngine::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresSiteSearchEngine');
