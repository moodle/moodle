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

namespace Google\Service\APIManagement\Resource;

use Google\Service\APIManagement\Entitlement;
use Google\Service\APIManagement\ListApiObservationTagsResponse;
use Google\Service\APIManagement\ListLocationsResponse;
use Google\Service\APIManagement\Location;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apimService = new Google\Service\APIManagement(...);
 *   $locations = $apimService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Gets information about a location. (locations.get)
   *
   * @param string $name Resource name for the location.
   * @param array $optParams Optional parameters.
   * @return Location
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Location::class);
  }
  /**
   * GetEntitlement returns the entitlement for the provided project.
   * (locations.getEntitlement)
   *
   * @param string $name Required. The entitlement resource name Format:
   * projects/{project}/locations/{location}/entitlement
   * @param array $optParams Optional parameters.
   * @return Entitlement
   * @throws \Google\Service\Exception
   */
  public function getEntitlement($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getEntitlement', [$params], Entitlement::class);
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
   * @return ListLocationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListLocationsResponse::class);
  }
  /**
   * ListApiObservationTags lists all extant tags on any observation in the given
   * project. (locations.listApiObservationTags)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * tags. Format: projects/{project}/locations/{location}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of tags to return. The
   * service may return fewer than this value. If unspecified, at most 10 tags
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListApiObservationTags` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListApiObservationTags`
   * must match the call that provided the page token.
   * @return ListApiObservationTagsResponse
   * @throws \Google\Service\Exception
   */
  public function listApiObservationTags($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listApiObservationTags', [$params], ListApiObservationTagsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_APIManagement_Resource_ProjectsLocations');
