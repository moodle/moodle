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

namespace Google\Service\Iam\Resource;

use Google\Service\Iam\GoogleIamV2ListPoliciesResponse;
use Google\Service\Iam\GoogleIamV2Policy;
use Google\Service\Iam\GoogleLongrunningOperation;

/**
 * The "policies" collection of methods.
 * Typical usage is:
 *  <code>
 *   $iamService = new Google\Service\Iam(...);
 *   $policies = $iamService->policies;
 *  </code>
 */
class Policies extends \Google\Service\Resource
{
  /**
   * Creates a policy. (policies.createPolicy)
   *
   * @param string $parent Required. The resource that the policy is attached to,
   * along with the kind of policy to create. Format:
   * `policies/{attachment_point}/denypolicies` The attachment point is identified
   * by its URL-encoded full resource name, which means that the forward-slash
   * character, `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies`. For organizations and folders, use the numeric ID in
   * the full resource name. For projects, you can use the alphanumeric or the
   * numeric ID.
   * @param GoogleIamV2Policy $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string policyId The ID to use for this policy, which will become
   * the final component of the policy's resource name. The ID must contain 3 to
   * 63 characters. It can contain lowercase letters and numbers, as well as
   * dashes (`-`) and periods (`.`). The first character must be a lowercase
   * letter.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function createPolicy($parent, GoogleIamV2Policy $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createPolicy', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a policy. This action is permanent. (policies.delete)
   *
   * @param string $name Required. The resource name of the policy to delete.
   * Format: `policies/{attachment_point}/denypolicies/{policy_id}` Use the URL-
   * encoded full resource name, which means that the forward-slash character,
   * `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies/my-policy`. For organizations and folders, use the
   * numeric ID in the full resource name. For projects, you can use the
   * alphanumeric or the numeric ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The expected `etag` of the policy to delete.
   * If the value does not match the value that is stored in IAM, the request
   * fails with a `409` error code and `ABORTED` status. If you omit this field,
   * the policy is deleted regardless of its current `etag`.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a policy. (policies.get)
   *
   * @param string $name Required. The resource name of the policy to retrieve.
   * Format: `policies/{attachment_point}/denypolicies/{policy_id}` Use the URL-
   * encoded full resource name, which means that the forward-slash character,
   * `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies/my-policy`. For organizations and folders, use the
   * numeric ID in the full resource name. For projects, you can use the
   * alphanumeric or the numeric ID.
   * @param array $optParams Optional parameters.
   * @return GoogleIamV2Policy
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleIamV2Policy::class);
  }
  /**
   * Retrieves the policies of the specified kind that are attached to a resource.
   * The response lists only policy metadata. In particular, policy rules are
   * omitted. (policies.listPolicies)
   *
   * @param string $parent Required. The resource that the policy is attached to,
   * along with the kind of policy to list. Format:
   * `policies/{attachment_point}/denypolicies` The attachment point is identified
   * by its URL-encoded full resource name, which means that the forward-slash
   * character, `/`, must be written as `%2F`. For example,
   * `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies`. For organizations and folders, use the numeric ID in
   * the full resource name. For projects, you can use the alphanumeric or the
   * numeric ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of policies to return. IAM ignores
   * this value and uses the value 1000.
   * @opt_param string pageToken A page token received in a ListPoliciesResponse.
   * Provide this token to retrieve the next page.
   * @return GoogleIamV2ListPoliciesResponse
   * @throws \Google\Service\Exception
   */
  public function listPolicies($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('listPolicies', [$params], GoogleIamV2ListPoliciesResponse::class);
  }
  /**
   * Updates the specified policy. You can update only the rules and the display
   * name for the policy. To update a policy, you should use a read-modify-write
   * loop: 1. Use GetPolicy to read the current version of the policy. 2. Modify
   * the policy as needed. 3. Use `UpdatePolicy` to write the updated policy. This
   * pattern helps prevent conflicts between concurrent updates. (policies.update)
   *
   * @param string $name Immutable. The resource name of the `Policy`, which must
   * be unique. Format: `policies/{attachment_point}/denypolicies/{policy_id}` The
   * attachment point is identified by its URL-encoded full resource name, which
   * means that the forward-slash character, `/`, must be written as `%2F`. For
   * example, `policies/cloudresourcemanager.googleapis.com%2Fprojects%2Fmy-
   * project/denypolicies/my-deny-policy`. For organizations and folders, use the
   * numeric ID in the full resource name. For projects, requests can use the
   * alphanumeric or the numeric ID. Responses always contain the numeric ID.
   * @param GoogleIamV2Policy $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function update($name, GoogleIamV2Policy $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Policies::class, 'Google_Service_Iam_Resource_Policies');
