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

namespace Google\Service\ChecksService\Resource;

use Google\Service\ChecksService\ChecksEmpty;
use Google\Service\ChecksService\FindPrivacyPolicyRequest;
use Google\Service\ChecksService\ListPrivacyPoliciesResponse;
use Google\Service\ChecksService\Operation;
use Google\Service\ChecksService\PrivacyPolicy;

/**
 * The "privacyPolicies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $checksService = new Google\Service\ChecksService(...);
 *   $privacyPolicies = $checksService->projects_privacyPolicies;
 *  </code>
 */
class ProjectsPrivacyPolicies extends \Google\Service\Resource
{
  /**
   * Deletes a privacy policy. (privacyPolicies.delete)
   *
   * @param string $name Required. Resource name of the privacy policy.
   * @param array $optParams Optional parameters.
   * @return ChecksEmpty
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ChecksEmpty::class);
  }
  /**
   * Finds the privacy policy of a given website. (privacyPolicies.find)
   *
   * @param string $parent Required. Resource name of the GCP project to which
   * PrivacyPolicy resources will be added, in the format:
   * `projects/{projectNumber}`.
   * @param FindPrivacyPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   */
  public function find($parent, FindPrivacyPolicyRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('find', [$params], Operation::class);
  }
  /**
   * Gets a privacy policy. (privacyPolicies.get)
   *
   * @param string $name Required. Resource name of the privacy policy.
   * @param array $optParams Optional parameters.
   * @return PrivacyPolicy
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PrivacyPolicy::class);
  }
  /**
   * Lists privacy policies. (privacyPolicies.listProjectsPrivacyPolicies)
   *
   * @param string $parent Required. Resource name of the parent project, in the
   * format `projects/{projectNumber}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. A filter string to filters results. The
   * filter syntax is defined by AIP-160 (https://google.aip.dev/160).
   * @opt_param int pageSize Optional. The maximum number of results to return. If
   * unspecified, at most 50 results will be returned. The maximum value is 1000;
   * values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListPrivacyPoliciesRequest` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListPrivacyPoliciesRequest` must match the call that provided the page
   * token.
   * @return ListPrivacyPoliciesResponse
   */
  public function listProjectsPrivacyPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPrivacyPoliciesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsPrivacyPolicies::class, 'Google_Service_ChecksService_Resource_ProjectsPrivacyPolicies');
