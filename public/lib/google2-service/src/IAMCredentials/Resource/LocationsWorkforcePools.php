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

namespace Google\Service\IAMCredentials\Resource;

use Google\Service\IAMCredentials\WorkforcePoolAllowedLocations;

/**
 * The "workforcePools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iamcredentialsService = new Google\Service\IAMCredentials(...);
 *   $workforcePools = $iamcredentialsService->locations_workforcePools;
 *  </code>
 */
class LocationsWorkforcePools extends \Google\Service\Resource
{
  /**
   * Returns the trust boundary info for a given workforce pool.
   * (workforcePools.getAllowedLocations)
   *
   * @param string $name Required. Resource name of workforce pool.
   * @param array $optParams Optional parameters.
   * @return WorkforcePoolAllowedLocations
   * @throws \Google\Service\Exception
   */
  public function getAllowedLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getAllowedLocations', [$params], WorkforcePoolAllowedLocations::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LocationsWorkforcePools::class, 'Google_Service_IAMCredentials_Resource_LocationsWorkforcePools');
