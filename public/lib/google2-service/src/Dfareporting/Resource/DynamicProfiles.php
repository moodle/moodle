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

namespace Google\Service\Dfareporting\Resource;

use Google\Service\Dfareporting\DynamicProfile;
use Google\Service\Dfareporting\DynamicProfileGenerateCodeResponse;

/**
 * The "dynamicProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Google\Service\Dfareporting(...);
 *   $dynamicProfiles = $dfareportingService->dynamicProfiles;
 *  </code>
 */
class DynamicProfiles extends \Google\Service\Resource
{
  /**
   * Generates code for a dynamic profile. (dynamicProfiles.generateCode)
   *
   * @param string $dynamicProfileId Required. Dynamic profile ID.
   * @param array $optParams Optional parameters.
   * @return DynamicProfileGenerateCodeResponse
   * @throws \Google\Service\Exception
   */
  public function generateCode($dynamicProfileId, $optParams = [])
  {
    $params = ['dynamicProfileId' => $dynamicProfileId];
    $params = array_merge($params, $optParams);
    return $this->call('generateCode', [$params], DynamicProfileGenerateCodeResponse::class);
  }
  /**
   * Gets a dynamic profile by ID. (dynamicProfiles.get)
   *
   * @param string $dynamicProfileId Required. Dynamic profile ID.
   * @param array $optParams Optional parameters.
   * @return DynamicProfile
   * @throws \Google\Service\Exception
   */
  public function get($dynamicProfileId, $optParams = [])
  {
    $params = ['dynamicProfileId' => $dynamicProfileId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DynamicProfile::class);
  }
  /**
   * Inserts a new dynamic profile. (dynamicProfiles.insert)
   *
   * @param DynamicProfile $postBody
   * @param array $optParams Optional parameters.
   * @return DynamicProfile
   * @throws \Google\Service\Exception
   */
  public function insert(DynamicProfile $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], DynamicProfile::class);
  }
  /**
   * Publish for a dynamic profile. (dynamicProfiles.publish)
   *
   * @param string $dynamicProfileId Required. Dynamic profile ID.
   * @param array $optParams Optional parameters.
   * @throws \Google\Service\Exception
   */
  public function publish($dynamicProfileId, $optParams = [])
  {
    $params = ['dynamicProfileId' => $dynamicProfileId];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params]);
  }
  /**
   * Updates an existing dynamic profile. (dynamicProfiles.update)
   *
   * @param DynamicProfile $postBody
   * @param array $optParams Optional parameters.
   * @return DynamicProfile
   * @throws \Google\Service\Exception
   */
  public function update(DynamicProfile $postBody, $optParams = [])
  {
    $params = ['postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], DynamicProfile::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicProfiles::class, 'Google_Service_Dfareporting_Resource_DynamicProfiles');
