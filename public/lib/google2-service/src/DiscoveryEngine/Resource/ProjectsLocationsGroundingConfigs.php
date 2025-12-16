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

use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1CheckGroundingRequest;
use Google\Service\DiscoveryEngine\GoogleCloudDiscoveryengineV1CheckGroundingResponse;

/**
 * The "groundingConfigs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $discoveryengineService = new Google\Service\DiscoveryEngine(...);
 *   $groundingConfigs = $discoveryengineService->projects_locations_groundingConfigs;
 *  </code>
 */
class ProjectsLocationsGroundingConfigs extends \Google\Service\Resource
{
  /**
   * Performs a grounding check. (groundingConfigs.check)
   *
   * @param string $groundingConfig Required. The resource name of the grounding
   * config, such as
   * `projects/locations/global/groundingConfigs/default_grounding_config`.
   * @param GoogleCloudDiscoveryengineV1CheckGroundingRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudDiscoveryengineV1CheckGroundingResponse
   * @throws \Google\Service\Exception
   */
  public function check($groundingConfig, GoogleCloudDiscoveryengineV1CheckGroundingRequest $postBody, $optParams = [])
  {
    $params = ['groundingConfig' => $groundingConfig, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('check', [$params], GoogleCloudDiscoveryengineV1CheckGroundingResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsGroundingConfigs::class, 'Google_Service_DiscoveryEngine_Resource_ProjectsLocationsGroundingConfigs');
