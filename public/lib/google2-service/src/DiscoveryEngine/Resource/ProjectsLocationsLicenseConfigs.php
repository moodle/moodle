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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1LicenseConfig;

/**
 * The "licenseConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $licenseConfigs = $discoveryengineService->projects_locations_licenseConfigs;
 *  </code>
 */
class ProjectsLocationsLicenseConfigs extends \Google\Service\Resource
{
  /**
   * Creates a LicenseConfig (licenseConfigs.create)
   *
   * @param string $parent Required. The parent resource name, such as
   * `projects/{project}/locations/{location}`.
   * @param GoogleCloudDiscoveryengineV1LicenseConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string licenseConfigId Optional. The ID to use for the
   * LicenseConfig, which will become the final component of the LicenseConfig's
   * resource name. We are using the tier (product edition) name as the license
   * config id such as `search` or `search_and_assistant`.
   * @return GoogleCloudDiscoveryengineV1LicenseConfig
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudDiscoveryengineV1LicenseConfig $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudDiscoveryengineV1LicenseConfig::class);
  }
  /**
   * Gets a LicenseConfig. (licenseConfigs.get)
   *
   * @param string $name Required. Full resource name of LicenseConfig, such as
   * `projects/{project}/locations/{location}/licenseConfigs`. If the caller does
   * not have permission to access the LicenseConfig, regardless of whether or not
   * it exists, a PERMISSION_DENIED error is returned. If the requested
   * LicenseConfig does not exist, a NOT_FOUND error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1LicenseConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1LicenseConfig::class);
  }
  /**
   * Updates the LicenseConfig (licenseConfigs.patch)
   *
   * @param string $name Immutable. Identifier. The fully qualified resource name
   * of the license config. Format:
   * `projects/{project}/locations/{location}/licenseConfigs/{license_config}`
   * @param GoogleCloudDiscoveryengineV1LicenseConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Indicates which fields in the provided
   * LicenseConfig to update. If an unsupported or unknown field is provided, an
   * INVALID_ARGUMENT error is returned.
   * @return GoogleCloudDiscoveryengineV1LicenseConfig
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1LicenseConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudDiscoveryengineV1LicenseConfig::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsLicenseConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsLicenseConfigs');
