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

use Google\Service\IAMCredentials\WorkloadIdentityPoolAllowedLocations;

/**
 * The "workloadIdentityPools" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iamcredentialsService = new Google\Service\IAMCredentials(...);
 *   $workloadIdentityPools = $iamcredentialsService->projects_locations_workloadIdentityPools;
 *  </code>
 */
class ProjectsLocationsWorkloadIdentityPools extends \Google\Service\Resource
{
  /**
   * Returns the trust boundary info for a given workload identity pool.
   * (workloadIdentityPools.getAllowedLocations)
   *
   * @param string $name Required. Resource name of workload identity pool.
   * @param array $optParams Optional parameters.
   * @return WorkloadIdentityPoolAllowedLocations
   * @throws \Google\Service\Exception
   */
  public function getAllowedLocations($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getAllowedLocations', [$params], WorkloadIdentityPoolAllowedLocations::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsWorkloadIdentityPools::class, 'Google_Service_IAMCredentials_Resource_ProjectsLocationsWorkloadIdentityPools');
