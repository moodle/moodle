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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1FetchSitemapsResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1Sitemap;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "sitemaps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $sitemaps = $discoveryengineService->projects_locations_collections_dataStores_siteSearchEngine_sitemaps;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresSiteSearchEngineSitemaps extends \Google\Service\Resource
{
  /**
   * Creates a Sitemap. (sitemaps.create)
   *
   * @param string $parent Required. Parent resource name of the SiteSearchEngine,
   * such as `projects/locations/collections/dataStores/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1Sitemap $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1Sitemap $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Sitemap. (sitemaps.delete)
   *
   * @param string $name Required. Full resource name of Sitemap, such as `project
   * s/{project}/locations/{location}/collections/{collection}/dataStores/{data_st
   * ore}/siteSearchEngine/sitemaps/{sitemap}`. If the caller does not have
   * permission to access the Sitemap, regardless of whether or not it exists, a
   * PERMISSION_DENIED error is returned. If the requested Sitemap does not exist,
   * a NOT_FOUND error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Fetch Sitemaps in a DataStore. (sitemaps.fetch)
   *
   * @param string $parent Required. Parent resource name of the SiteSearchEngine,
   * such as `projects/locations/collections/dataStores/siteSearchEngine`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string matcher.urisMatcher.uris The Sitemap uris.
   * @return GoogleCloudDiscoveryengineV1FetchSitemapsResponse
   * @throws \Google\Service\Exception
   */
  public function fetch($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('fetch', [$params], GoogleCloudDiscoveryengineV1FetchSitemapsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresSiteSearchEngineSitemaps::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresSiteSearchEngineSitemaps');
