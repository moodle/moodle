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

namespace Google\Service\APIhub\Resource;

use Google\Service\APIhub\GoogleCloudApihubV1CollectApiDataRequest;
use Google\Service\APIhub\GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse;
use Google\Service\APIhub\GoogleCloudApihubV1RetrieveApiViewsResponse;
use Google\Service\APIhub\GoogleCloudApihubV1SearchResourcesRequest;
use Google\Service\APIhub\GoogleCloudApihubV1SearchResourcesResponse;
use Google\Service\APIhub\GoogleCloudLocationListLocationsResponse;
use Google\Service\APIhub\GoogleCloudLocationLocation;
use Google\Service\APIhub\GoogleLongrunningOperation;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apihubService = new Google\Service\APIhub(...);
 *   $locations = $apihubService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Collect API data from a source and push it to Hub's collect layer.
   * (locations.collectApiData)
   *
   * @param string $location Required. The regional location of the API hub
   * instance and its resources. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudApihubV1CollectApiDataRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function collectApiData($location, GoogleCloudApihubV1CollectApiDataRequest $postBody, $optParams = [])
  {
    $params = ['location' => $location, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('collectApiData', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets information about a location. (locations.get)
   *
   * @param string $name Resource name for the location.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudLocationLocation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudLocationLocation::class);
  }
  /**
   * Lists information about the supported locations for this service.
   * (locations.listProjectsLocations)
   *
   * @param string $name The resource that owns the locations collection, if
   * applicable.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string extraLocationTypes Optional. Do not use this field. It is
   * unsupported and is ignored unless explicitly documented otherwise. This is
   * primarily for internal usage.
   * @opt_param string filter A filter to narrow down results to a preferred
   * subset. The filtering language accepts strings like `"displayName=tokyo"`,
   * and is documented in more detail in [AIP-160](https://google.aip.dev/160).
   * @opt_param int pageSize The maximum number of results to return. If not set,
   * the service selects a default.
   * @opt_param string pageToken A page token received from the `next_page_token`
   * field in the response. Send that page token to receive the subsequent page.
   * @return GoogleCloudLocationListLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudLocationListLocationsResponse::class);
  }
  /**
   * Look up a runtime project attachment. This API can be called in the context
   * of any project. (locations.lookupRuntimeProjectAttachment)
   *
   * @param string $name Required. Runtime project ID to look up runtime project
   * attachment for. Lookup happens across all regions. Expected format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse
   * @throws \Google\Service\Exception
   */
  public function lookupRuntimeProjectAttachment($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('lookupRuntimeProjectAttachment', [$params], GoogleCloudApihubV1LookupRuntimeProjectAttachmentResponse::class);
  }
  /**
   * Retrieve API views. (locations.retrieveApiViews)
   *
   * @param string $parent Required. The parent resource name. Format:
   * `projects/{project}/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter expression.
   * @opt_param int pageSize Optional. The maximum number of results to return.
   * Default to 100.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `RetrieveApiViews` call. Provide this to retrieve the subsequent page.
   * @opt_param string view Required. The view type to return.
   * @return GoogleCloudApihubV1RetrieveApiViewsResponse
   * @throws \Google\Service\Exception
   */
  public function retrieveApiViews($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('retrieveApiViews', [$params], GoogleCloudApihubV1RetrieveApiViewsResponse::class);
  }
  /**
   * Search across API-Hub resources. (locations.searchResources)
   *
   * @param string $location Required. The resource name of the location which
   * will be of the type `projects/{project_id}/locations/{location_id}`. This
   * field is used to identify the instance of API-Hub in which resources should
   * be searched.
   * @param GoogleCloudApihubV1SearchResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApihubV1SearchResourcesResponse
   * @throws \Google\Service\Exception
   */
  public function searchResources($location, GoogleCloudApihubV1SearchResourcesRequest $postBody, $optParams = [])
  {
    $params = ['location' => $location, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchResources', [$params], GoogleCloudApihubV1SearchResourcesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_APIhub_Resource_ProjectsLocations');
