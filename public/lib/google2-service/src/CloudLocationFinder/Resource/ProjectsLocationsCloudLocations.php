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

namespace Google\Service\CloudLocationFinder\Resource;

use Google\Service\CloudLocationFinder\CloudLocation;
use Google\Service\CloudLocationFinder\ListCloudLocationsResponse;
use Google\Service\CloudLocationFinder\SearchCloudLocationsResponse;

/**
 * The "cloudLocations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cloudlocationfinderService = new Google\Service\CloudLocationFinder(...);
 *   $cloudLocations = $cloudlocationfinderService->projects_locations_cloudLocations;
 *  </code>
 */
class ProjectsLocationsCloudLocations extends \Google\Service\Resource
{
  /**
   * Retrieves a resource containing information about a cloud location.
   * (cloudLocations.get)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   * @return CloudLocation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CloudLocation::class);
  }
  /**
   * Lists cloud locations under a given project and location.
   * (cloudLocations.listProjectsLocationsCloudLocations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * cloud locations. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter expression that filters resources
   * listed in the response. The expression is in the form of field=value. For
   * example, 'cloud_location_type=CLOUD_LOCATION_TYPE_REGION'. Multiple filter
   * queries are space-separated. For example,
   * 'cloud_location_type=CLOUD_LOCATION_TYPE_REGION territory_code="US"' By
   * default, each expression is an AND expression. However, you can include AND
   * and OR expressions explicitly.
   * @opt_param int pageSize Optional. The maximum number of cloud locations to
   * return per page. The service might return fewer cloud locations than this
   * value. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Provide page token returned by a previous
   * 'ListCloudLocations' call to retrieve the next page of results. When
   * paginating, all other parameters provided to 'ListCloudLocations' must match
   * the call that provided the page token.
   * @return ListCloudLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCloudLocations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCloudLocationsResponse::class);
  }
  /**
   * Searches for cloud locations from a given source location.
   * (cloudLocations.search)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * cloud locations. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of cloud locations to
   * return. The service might return fewer cloud locations than this value. If
   * unspecified, server will pick an appropriate default.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return. Provide Page token returned by a previous
   * 'ListCloudLocations' call to retrieve the next page of results. When
   * paginating, all other parameters provided to 'ListCloudLocations' must match
   * the call that provided the page token.
   * @opt_param string query Optional. The query string in search query syntax.
   * While filter is used to filter the search results by attributes, query is
   * used to specify the search requirements.
   * @opt_param string sourceCloudLocation Required. The source cloud location to
   * search from. Example search can be searching nearby cloud locations from the
   * source cloud location by latency.
   * @return SearchCloudLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function search($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('search', [$params], SearchCloudLocationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCloudLocations::class, 'Google_Service_CloudLocationFinder_Resource_ProjectsLocationsCloudLocations');
