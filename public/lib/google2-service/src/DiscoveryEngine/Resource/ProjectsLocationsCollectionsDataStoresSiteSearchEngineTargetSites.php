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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1BatchCreateTargetSitesRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListTargetSitesResponse;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1TargetSite;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "targetSites" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $targetSites = $discoveryengineService->projects_locations_collections_dataStores_siteSearchEngine_targetSites;
 *  </code>
 */
class ProjectsLocationsCollectionsDataStoresSiteSearchEngineTargetSites extends \Google\Service\Resource
{
  /**
   * Creates TargetSite in a batch. (targetSites.batchCreate)
   *
   * @param string $parent Required. The parent resource shared by all TargetSites
   * being created. `projects/{project}/locations/{location}/collections/{collecti
   * on}/dataStores/{data_store}/siteSearchEngine`. The parent field in the
   * CreateBookRequest messages must either be empty or match this field.
   * @param GoogleCloudDiscoveryengineV1BatchCreateTargetSitesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchCreate($parent, GoogleCloudDiscoveryengineV1BatchCreateTargetSitesRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCreate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a TargetSite. (targetSites.create)
   *
   * @param string $parent Required. Parent resource name of TargetSite, such as `
   * projects/{project}/locations/{location}/collections/{collection}/dataStores/{
   * data_store}/siteSearchEngine`.
   * @param GoogleCloudDiscoveryengineV1TargetSite $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1TargetSite $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a TargetSite. (targetSites.delete)
   *
   * @param string $name Required. Full resource name of TargetSite, such as `proj
   * ects/{project}/locations/{location}/collections/{collection}/dataStores/{data
   * _store}/siteSearchEngine/targetSites/{target_site}`. If the caller does not
   * have permission to access the TargetSite, regardless of whether or not it
   * exists, a PERMISSION_DENIED error is returned. If the requested TargetSite
   * does not exist, a NOT_FOUND error is returned.
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
   * Gets a TargetSite. (targetSites.get)
   *
   * @param string $name Required. Full resource name of TargetSite, such as `proj
   * ects/{project}/locations/{location}/collections/{collection}/dataStores/{data
   * _store}/siteSearchEngine/targetSites/{target_site}`. If the caller does not
   * have permission to access the TargetSite, regardless of whether or not it
   * exists, a PERMISSION_DENIED error is returned. If the requested TargetSite
   * does not exist, a NOT_FOUND error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1TargetSite
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1TargetSite::class);
  }
  /**
   * Gets a list of TargetSites. (targetSites.listProjectsLocationsCollectionsData
   * StoresSiteSearchEngineTargetSites)
   *
   * @param string $parent Required. The parent site search engine resource name,
   * such as `projects/{project}/locations/{location}/collections/{collection}/dat
   * aStores/{data_store}/siteSearchEngine`. If the caller does not have
   * permission to list TargetSites under this site search engine, regardless of
   * whether or not this branch exists, a PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default. The
   * maximum value is 1000; values above 1000 will be coerced to 1000. If this
   * field is negative, an INVALID_ARGUMENT error is returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListTargetSites` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListTargetSites` must match the
   * call that provided the page token.
   * @return GoogleCloudDiscoveryengineV1ListTargetSitesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCollectionsDataStoresSiteSearchEngineTargetSites($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListTargetSitesResponse::class);
  }
  /**
   * Updates a TargetSite. (targetSites.patch)
   *
   * @param string $name Output only. The fully qualified resource name of the
   * target site. `projects/{project}/locations/{location}/collections/{collection
   * }/dataStores/{data_store}/siteSearchEngine/targetSites/{target_site}` The
   * `target_site_id` is system-generated.
   * @param GoogleCloudDiscoveryengineV1TargetSite $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1TargetSite $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCollectionsDataStoresSiteSearchEngineTargetSites::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCollectionsDataStoresSiteSearchEngineTargetSites');
