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

namespace Google\Service\Contactcenterinsights\Resource;

use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1AuthorizedViewSet;
use Google\Service\Contactcenterinsights\GoogleCloudContactcenterinsightsV1ListAuthorizedViewSetsResponse;
use Google\Service\Contactcenterinsights\GoogleProtobufEmpty;

/**
 * The "authorizedViewSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $contactcenterinsightsService = new Google\Service\Contactcenterinsights(...);
 *   $authorizedViewSets = $contactcenterinsightsService->projects_locations_authorizedViewSets;
 *  </code>
 */
class ProjectsLocationsAuthorizedViewSets extends \Google\Service\Resource
{
  /**
   * Create AuthorizedViewSet (authorizedViewSets.create)
   *
   * @param string $parent Required. The parent resource of the AuthorizedViewSet.
   * @param GoogleCloudContactcenterinsightsV1AuthorizedViewSet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string authorizedViewSetId Optional. A unique ID for the new
   * AuthorizedViewSet. This ID will become the final component of the
   * AuthorizedViewSet's resource name. If no ID is specified, a server-generated
   * ID will be used. This value should be 4-64 characters and must match the
   * regular expression `^[a-z]([a-z0-9-]{0,61}[a-z0-9])?$`. See
   * https://google.aip.dev/122#resource-id-segments
   * @return GoogleCloudContactcenterinsightsV1AuthorizedViewSet
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudContactcenterinsightsV1AuthorizedViewSet $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudContactcenterinsightsV1AuthorizedViewSet::class);
  }
  /**
   * Deletes an AuthorizedViewSet. (authorizedViewSets.delete)
   *
   * @param string $name Required. The name of the AuthorizedViewSet to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force Optional. If set to true, all of this
   * AuthorizedViewSet's child resources will also be deleted. Otherwise, the
   * request will only succeed if it has none.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Get AuthorizedViewSet (authorizedViewSets.get)
   *
   * @param string $name Required. The name of the AuthorizedViewSet to get.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudContactcenterinsightsV1AuthorizedViewSet
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudContactcenterinsightsV1AuthorizedViewSet::class);
  }
  /**
   * List AuthorizedViewSets
   * (authorizedViewSets.listProjectsLocationsAuthorizedViewSets)
   *
   * @param string $parent Required. The parent resource of the
   * AuthorizedViewSets.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter expression to filter authorized
   * view sets listed in the response.
   * @opt_param string orderBy Optional. The order by expression to order
   * authorized view sets listed in the response.
   * @opt_param int pageSize Optional. The maximum number of view sets to return
   * in the response. If the value is zero, the service will select a default
   * size. A call might return fewer objects than requested. A non-empty
   * `next_page_token` in the response indicates that more data is available.
   * @opt_param string pageToken Optional. The value returned by the last
   * `ListAuthorizedViewSetsResponse`. This value indicates that this is a
   * continuation of a prior `ListAuthorizedViewSets` call and that the system
   * should return the next page of data.
   * @return GoogleCloudContactcenterinsightsV1ListAuthorizedViewSetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAuthorizedViewSets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudContactcenterinsightsV1ListAuthorizedViewSetsResponse::class);
  }
  /**
   * Updates an AuthorizedViewSet. (authorizedViewSets.patch)
   *
   * @param string $name Identifier. The resource name of the AuthorizedViewSet.
   * Format: projects/{project}/locations/{location}/authorizedViewSets/{authorize
   * d_view_set}
   * @param GoogleCloudContactcenterinsightsV1AuthorizedViewSet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The list of fields to be updated. All
   * possible fields can be updated by passing `*`, or a subset of the following
   * updateable fields can be provided: * `display_name`
   * @return GoogleCloudContactcenterinsightsV1AuthorizedViewSet
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudContactcenterinsightsV1AuthorizedViewSet $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudContactcenterinsightsV1AuthorizedViewSet::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsAuthorizedViewSets::class, 'Google_Service_Contactcenterinsights_Resource_ProjectsLocationsAuthorizedViewSets');
