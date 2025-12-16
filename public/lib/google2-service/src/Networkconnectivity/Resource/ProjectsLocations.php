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

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\CheckConsumerConfigRequest;
use Google\Service\Networkconnectivity\CheckConsumerConfigResponse;
use Google\Service\Networkconnectivity\ListLocationsResponse;
use Google\Service\Networkconnectivity\Location;

/**
 * The "locations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $locations = $networkconnectivityService->projects_locations;
 *  </code>
 */
class ProjectsLocations extends \Google\Service\Resource
{
  /**
   * CheckConsumerConfig validates the consumer network and project for potential
   * PSC connection creation. This method performs several checks, including: -
   * Validating the existence and permissions of the service class. - Ensuring the
   * consumer network exists and is accessible. - Verifying XPN relationships if
   * applicable. - Checking for compatible IP versions between the consumer
   * network and the requested version. This method performs a dynamic IAM check
   * for the `networkconnectivity.serviceClasses.use` permission on the service
   * class resource in the Prepare phase. (locations.checkConsumerConfig)
   *
   * @param string $location Required. The location resource path. Example: -
   * projects/{project}/locations/{location}
   * @param CheckConsumerConfigRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CheckConsumerConfigResponse
   * @throws \Google\Service\Exception
   */
  public function checkConsumerConfig($location, CheckConsumerConfigRequest $postBody, $optParams = [])
  {
    $params = ['location' => $location, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('checkConsumerConfig', [$params], CheckConsumerConfigResponse::class);
  }
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocations::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocations');
