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

use Google\Service\BinaryAuthorization\EvaluateGkePolicyRequest;
use Google\Service\BinaryAuthorization\EvaluateGkePolicyResponse;

/**
 * The "policies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $binaryauthorizationService = new Google\Service\BinaryAuthorization(...);
 *   $policies = $binaryauthorizationService->projects_platforms_gke_policies;
 *  </code>
 */
class ProjectsPlatformsGkePolicies extends \Google\Service\Resource
{
  /**
   * Evaluates a Kubernetes object versus a GKE platform policy. Returns
   * `NOT_FOUND` if the policy doesn't exist, `INVALID_ARGUMENT` if the policy or
   * request is malformed and `PERMISSION_DENIED` if the client does not have
   * sufficient permissions. (policies.evaluate)
   *
   * @param string $name Required. The name of the platform policy to evaluate in
   * the format `projects/platforms/policies`.
   * @param EvaluateGkePolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return EvaluateGkePolicyResponse
   * @throws \Google\Service\Exception
   */
  public function evaluate($name, EvaluateGkePolicyRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('evaluate', [$params], EvaluateGkePolicyResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsPlatformsGkePolicies::class, 'Google_Service_BinaryAuthorization_Resource_ProjectsPlatformsGkePolicies');
