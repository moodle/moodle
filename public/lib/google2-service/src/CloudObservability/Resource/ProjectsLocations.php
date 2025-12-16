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

namespace Google\Service\CloudObservability\Resource;

use Google\Service\CloudObservability\ListLocationsResponse;
use Google\Service\CloudObservability\Location;
use Google\Service\CloudObservability\Operation;
use Google\Service\CloudObservability\Settings;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $observabilityService = new Google\Service\CloudObservability(...);
 *   $locations = $observabilityService->projects_locations;
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
   * Get Settings (locations.getSettings)
   *
   * @param string $name Required. Name of the settings to retrieve. Name format:
   * "projects/[PROJECT_ID]/locations/[LOCATION]/settings"
   * "folders/[FOLDER_ID]/locations/[LOCATION]/settings"
   * "organizations/[ORGANIZATION_ID]/locations/[LOCATION]/settings"
   * @param array $optParams Optional parameters.
   * @return Settings
   * @throws \Google\Service\Exception
   */
  public function getSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSettings', [$params], Settings::class);
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
   * Update Settings (locations.updateSettings)
   *
   * @param string $name Identifier. The resource name of the settings.
   * @param Settings $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. The field mask specifying which fields
   * of the settings are to be updated.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function updateSettings($name, Settings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateSettings', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_CloudObservability_Resource_ProjectsLocations');
