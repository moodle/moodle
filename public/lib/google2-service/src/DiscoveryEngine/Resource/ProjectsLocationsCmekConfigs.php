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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1CmekConfig;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1ListCmekConfigsResponse;
use Google\Service\DiscoveryEngine\GoogleLongrunningOperation;

/**
 * The "cmekConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $cmekConfigs = $discoveryengineService->projects_locations_cmekConfigs;
 *  </code>
 */
class ProjectsLocationsCmekConfigs extends \Google\Service\Resource
{
  /**
   * De-provisions a CmekConfig. (cmekConfigs.delete)
   *
   * @param string $name Required. The resource name of the CmekConfig to delete,
   * such as `projects/{project}/locations/{location}/cmekConfigs/{cmek_config}`.
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
   * Gets the CmekConfig. (cmekConfigs.get)
   *
   * @param string $name Required. Resource name of CmekConfig, such as
   * `projects/locations/cmekConfig` or `projects/locations/cmekConfigs`. If the
   * caller does not have permission to access the CmekConfig, regardless of
   * whether or not it exists, a PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1CmekConfig
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudDiscoveryengineV1CmekConfig::class);
  }
  /**
   * Lists all the CmekConfigs with the project.
   * (cmekConfigs.listProjectsLocationsCmekConfigs)
   *
   * @param string $parent Required. The parent location resource name, such as
   * `projects/{project}/locations/{location}`. If the caller does not have
   * permission to list CmekConfigs under this location, regardless of whether or
   * not a CmekConfig exists, a PERMISSION_DENIED error is returned.
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1ListCmekConfigsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCmekConfigs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudDiscoveryengineV1ListCmekConfigsResponse::class);
  }
  /**
   * Provisions a CMEK key for use in a location of a customer's project. This
   * method will also conduct location validation on the provided cmekConfig to
   * make sure the key is valid and can be used in the selected location.
   * (cmekConfigs.patch)
   *
   * @param string $name Required. The name of the CmekConfig of the form
   * `projects/{project}/locations/{location}/cmekConfig` or
   * `projects/{project}/locations/{location}/cmekConfigs/{cmek_config}`.
   * @param GoogleCloudDiscoveryengineV1CmekConfig $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool setDefault Set the following CmekConfig as the default to be
   * used for child resources if one is not specified.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudDiscoveryengineV1CmekConfig $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCmekConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsCmekConfigs');
