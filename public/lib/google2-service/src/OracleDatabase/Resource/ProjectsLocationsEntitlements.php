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

namespace Google\Service\OracleDatabase\Resource;

use Google\Service\OracleDatabase\ListEntitlementsResponse;

/**
 * The "entitlements" collection of methods.
 * Typical usage is:
 *  <code>
 *   $oracledatabaseService = new Google\Service\OracleDatabase(...);
 *   $entitlements = $oracledatabaseService->projects_locations_entitlements;
 *  </code>
 */
class ProjectsLocationsEntitlements extends \Google\Service\Resource
{
  /**
   * Lists the entitlements in a given project.
   * (entitlements.listProjectsLocationsEntitlements)
   *
   * @param string $parent Required. The parent value for the entitlement in the
   * following format: projects/{project}/locations/{location}.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of items to return. If
   * unspecified, a maximum of 50 entitlements will be returned. The maximum value
   * is 1000.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * the server should return.
   * @return ListEntitlementsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEntitlements($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListEntitlementsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEntitlements::class, 'Google_Service_OracleDatabase_Resource_ProjectsLocationsEntitlements');
