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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1ListApiDebugSessionsResponse;

/**
 * The "debugsessions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $debugsessions = $apigeeService->organizations_apis_debugsessions;
 *  </code>
 */
class OrganizationsApisDebugsessions extends \Google\Service\Resource
{
  /**
   * Lists debug sessions that are currently active in the given API Proxy.
   * (debugsessions.listOrganizationsApisDebugsessions)
   *
   * @param string $parent Required. The name of the API Proxy for which to list
   * debug sessions. Must be of the form:
   * `organizations/{organization}/apis/{api}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of debug sessions to return.
   * The page size defaults to 25.
   * @opt_param string pageToken Optional. Page token, returned from a previous
   * ListApiDebugSessions call, that you can use to retrieve the next page.
   * @return GoogleCloudApigeeV1ListApiDebugSessionsResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsApisDebugsessions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListApiDebugSessionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsApisDebugsessions::class, 'Google_Service_Apigee_Resource_OrganizationsApisDebugsessions');
