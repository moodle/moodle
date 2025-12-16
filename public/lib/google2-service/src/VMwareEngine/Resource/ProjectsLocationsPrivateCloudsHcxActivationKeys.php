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

namespace Google\Service\VMwareEngine\Resource;

use Google\Service\VMwareEngine\HcxActivationKey;
use Google\Service\VMwareEngine\ListHcxActivationKeysResponse;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\Policy;
use Google\Service\VMwareEngine\SetIamPolicyRequest;
use Google\Service\VMwareEngine\TestIamPermissionsRequest;
use Google\Service\VMwareEngine\TestIamPermissionsResponse;

/**
 * The "hcxActivationKeys" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $hcxActivationKeys = $vmwareengineService->projects_locations_privateClouds_hcxActivationKeys;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsHcxActivationKeys extends \Google\Service\Resource
{
  /**
   * Creates a new HCX activation key in a given private cloud.
   * (hcxActivationKeys.create)
   *
   * @param string $parent Required. The resource name of the private cloud to
   * create the key for. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1/privateClouds/my-cloud`
   * @param HcxActivationKey $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hcxActivationKeyId Required. The user-provided identifier
   * of the `HcxActivationKey` to be created. This identifier must be unique among
   * `HcxActivationKey` resources within the parent and becomes the final token in
   * the name URI. The identifier must meet the following requirements: * Only
   * contains 1-63 alphanumeric characters and hyphens * Begins with an
   * alphabetical character * Ends with a non-hyphen character * Not formatted as
   * a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId A request ID to identify requests. Specify a
   * unique request ID so that if you must retry your request, the server will
   * know to ignore the request if it has already been completed. The server
   * guarantees that a request doesn't result in creation of duplicate commitments
   * for at least 60 minutes. For example, consider a situation where you make an
   * initial request and the request times out. If you make the request again with
   * the same request ID, the server can check if original operation with the same
   * request ID was received, and if so, will ignore the second request. This
   * prevents clients from accidentally creating duplicate commitments. The
   * request ID must be a valid UUID with the exception that zero UUID is not
   * supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, HcxActivationKey $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Retrieves a `HcxActivationKey` resource by its resource name.
   * (hcxActivationKeys.get)
   *
   * @param string $name Required. The resource name of the HCX activation key to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateClouds/my-
   * cloud/hcxActivationKeys/my-key`
   * @param array $optParams Optional parameters.
   * @return HcxActivationKey
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], HcxActivationKey::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set.
   * (hcxActivationKeys.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Lists `HcxActivationKey` resources in a given private cloud.
   * (hcxActivationKeys.listProjectsLocationsPrivateCloudsHcxActivationKeys)
   *
   * @param string $parent Required. The resource name of the private cloud to be
   * queried for HCX activation keys. Resource names are schemeless URIs that
   * follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of HCX activation keys to return
   * in one page. The service may return fewer than this value. The maximum value
   * is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListHcxActivationKeys` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListHcxActivationKeys`
   * must match the call that provided the page token.
   * @return ListHcxActivationKeysResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsHcxActivationKeys($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListHcxActivationKeysResponse::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (hcxActivationKeys.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (hcxActivationKeys.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsHcxActivationKeys::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsHcxActivationKeys');
