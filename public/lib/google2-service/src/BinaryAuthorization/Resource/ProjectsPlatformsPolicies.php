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

namespace Google\Service\BinaryAuthorization\Resource;

use Google\Service\BinaryAuthorization\BinaryauthorizationEmpty;
use Google\Service\BinaryAuthorization\ListPlatformPoliciesResponse;
use Google\Service\BinaryAuthorization\PlatformPolicy;

/**
 * The "policies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $binaryauthorizationService = new Google\Service\BinaryAuthorization(...);
 *   $policies = $binaryauthorizationService->projects_platforms_policies;
 *  </code>
 */
class ProjectsPlatformsPolicies extends \Google\Service\Resource
{
  /**
   * Creates a platform policy, and returns a copy of it. Returns `NOT_FOUND` if
   * the project or platform doesn't exist, `INVALID_ARGUMENT` if the request is
   * malformed, `ALREADY_EXISTS` if the policy already exists, and
   * `INVALID_ARGUMENT` if the policy contains a platform-specific policy that
   * does not match the platform value specified in the URL. (policies.create)
   *
   * @param string $parent Required. The parent of this platform policy.
   * @param PlatformPolicy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string policyId Required. The platform policy ID.
   * @return PlatformPolicy
   * @throws \Google\Service\Exception
   */
  public function create($parent, PlatformPolicy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], PlatformPolicy::class);
  }
  /**
   * Deletes a platform policy. Returns `NOT_FOUND` if the policy doesn't exist.
   * (policies.delete)
   *
   * @param string $name Required. The name of the platform policy to delete, in
   * the format `projects/platforms/policies`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. Used to prevent deleting the policy when
   * another request has updated it since it was retrieved.
   * @return BinaryauthorizationEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BinaryauthorizationEmpty::class);
  }
  /**
   * Gets a platform policy. Returns `NOT_FOUND` if the policy doesn't exist.
   * (policies.get)
   *
   * @param string $name Required. The name of the platform policy to retrieve in
   * the format `projects/platforms/policies`.
   * @param array $optParams Optional parameters.
   * @return PlatformPolicy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PlatformPolicy::class);
  }
  /**
   * Lists platform policies owned by a project in the specified platform. Returns
   * `INVALID_ARGUMENT` if the project or the platform doesn't exist.
   * (policies.listProjectsPlatformsPolicies)
   *
   * @param string $parent Required. The resource name of the platform associated
   * with the platform policies using the format `projects/platforms`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. The server may return fewer
   * results than requested. If unspecified, the server picks an appropriate
   * default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return. Typically, this is the value of
   * ListPlatformPoliciesResponse.next_page_token returned from the previous call
   * to the `ListPlatformPolicies` method.
   * @return ListPlatformPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsPlatformsPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPlatformPoliciesResponse::class);
  }
  /**
   * Replaces a platform policy. Returns `NOT_FOUND` if the policy doesn't exist.
   * (policies.replacePlatformPolicy)
   *
   * @param string $name Output only. The relative resource name of the Binary
   * Authorization platform policy, in the form of `projects/platforms/policies`.
   * @param PlatformPolicy $postBody
   * @param array $optParams Optional parameters.
   * @return PlatformPolicy
   * @throws \Google\Service\Exception
   */
  public function replacePlatformPolicy($name, PlatformPolicy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('replacePlatformPolicy', [$params], PlatformPolicy::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsPlatformsPolicies::class, 'Google_Service_BinaryAuthorization_Resource_ProjectsPlatformsPolicies');
