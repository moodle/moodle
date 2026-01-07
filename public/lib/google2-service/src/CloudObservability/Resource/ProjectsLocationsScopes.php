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

namespace Google\Service\CloudObservability\Resource;

use Google\Service\CloudObservability\Scope;

/**
 * The "scopes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $observabilityService = new Google\Service\CloudObservability(...);
 *   $scopes = $observabilityService->projects_locations_scopes;
 *  </code>
 */
class ProjectsLocationsScopes extends \Google\Service\Resource
{
  /**
   * Gets details of a single Scope. (scopes.get)
   *
   * @param string $name Required. Name of the resource. The format is:
   * projects/{project}/locations/{location}/scopes/{scope} The `{location}` field
   * must be set to `global`. The `{scope}` field must be set to `_Default`.
   * @param array $optParams Optional parameters.
   * @return Scope
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Scope::class);
  }
  /**
   * Updates the parameters of a single Scope. (scopes.patch)
   *
   * @param string $name Identifier. Name of the resource. The format is:
   * projects/{project}/locations/{location}/scopes/{scope} The `{location}` field
   * must be set to `global`. The `{scope}` field must be set to `_Default`.
   * @param Scope $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Scope resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field is overwritten when it is in the mask. If the user does not
   * provide a mask, then all fields present in the request are overwritten.
   * @return Scope
   * @throws \Google\Service\Exception
   */
  public function patch($name, Scope $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Scope::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsScopes::class, 'Google_Service_CloudObservability_Resource_ProjectsLocationsScopes');
