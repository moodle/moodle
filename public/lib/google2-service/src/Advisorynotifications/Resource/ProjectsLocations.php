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

namespace Google\Service\Advisorynotifications\Resource;

use Google\Service\Advisorynotifications\GoogleCloudAdvisorynotificationsV1Settings;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $advisorynotificationsService = new Google\Service\Advisorynotifications(...);
 *   $locations = $advisorynotificationsService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * Get notification settings. (locations.getSettings)
   *
   * @param string $name Required. The resource name of the settings to retrieve.
   * Format: organizations/{organization}/locations/{location}/settings or
   * projects/{projects}/locations/{location}/settings.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAdvisorynotificationsV1Settings
   * @throws \Google\Service\Exception
   */
  public function getSettings($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getSettings', [$params], GoogleCloudAdvisorynotificationsV1Settings::class);
  }
  /**
   * Update notification settings. (locations.updateSettings)
   *
   * @param string $name Identifier. The resource name of the settings to
   * retrieve. Format: organizations/{organization}/locations/{location}/settings
   * or projects/{projects}/locations/{location}/settings.
   * @param GoogleCloudAdvisorynotificationsV1Settings $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAdvisorynotificationsV1Settings
   * @throws \Google\Service\Exception
   */
  public function updateSettings($name, GoogleCloudAdvisorynotificationsV1Settings $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateSettings', [$params], GoogleCloudAdvisorynotificationsV1Settings::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Advisorynotifications_Resource_ProjectsLocations');
