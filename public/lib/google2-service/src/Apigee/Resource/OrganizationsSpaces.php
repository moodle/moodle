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

use Google\Service\Apigee\GoogleCloudApigeeV1ListSpacesResponse;
use Google\Service\Apigee\GoogleCloudApigeeV1Space;
use Google\Service\Apigee\GoogleIamV1Policy;
use Google\Service\Apigee\GoogleIamV1SetIamPolicyRequest;
use Google\Service\Apigee\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\Apigee\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "spaces" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $spaces = $apigeeService->organizations_spaces;
 *  </code>
 */
class OrganizationsSpaces extends \Google\Service\Resource
{
  /**
   * Create a space under an organization. (spaces.create)
   *
   * @param string $parent Required. Name of the Google Cloud project in which to
   * associate the Apigee space. Pass the information as a query parameter using
   * the following structure in your request: `organizations/`
   * @param GoogleCloudApigeeV1Space $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string spaceId Required. Resource ID of the space.
   * @return GoogleCloudApigeeV1Space
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudApigeeV1Space $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudApigeeV1Space::class);
  }
  /**
   * Deletes an organization space. (spaces.delete)
   *
   * @param string $name Required. Apigee organization space name in the following
   * format: `organizations/{org}/spaces/{space}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Get a space under an Organization. (spaces.get)
   *
   * @param string $name Required. Apigee organization space name in the following
   * format: `organizations/{org}/spaces/{space}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1Space
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudApigeeV1Space::class);
  }
  /**
   * Callers must have apigee.spaces.getIamPolicy. (spaces.getIamPolicy)
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
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Lists spaces under an organization. (spaces.listOrganizationsSpaces)
   *
   * @param string $parent Required. Use the following structure in your request:
   * `organizations`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of spaces to return. The
   * service may return fewer than this value. If unspecified, at most 50 spaces
   * will be returned. The maximum value is 1000; values above 1000 will be
   * coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListSpaces` call. Provide this to retrieve the subsequent page. When
   * paginating, all parameters must match the original call.
   * @return GoogleCloudApigeeV1ListSpacesResponse
   * @throws \Google\Service\Exception
   */
  public function listOrganizationsSpaces($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudApigeeV1ListSpacesResponse::class);
  }
  /**
   * Updates a space. (spaces.patch)
   *
   * @param string $name Required. Name of the space in the following format:
   * `organizations/{org}/spaces/{space_id}`.
   * @param GoogleCloudApigeeV1Space $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. List of fields to be updated. Fields
   * that can be updated: display_name.
   * @return GoogleCloudApigeeV1Space
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudApigeeV1Space $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudApigeeV1Space::class);
  }
  /**
   * IAM META APIs Callers must have apigee.spaces.setIamPolicy.
   * (spaces.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, GoogleIamV1SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], GoogleIamV1Policy::class);
  }
  /**
   * Callers don't need any permissions. (spaces.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param GoogleIamV1TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleIamV1TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, GoogleIamV1TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], GoogleIamV1TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsSpaces::class, 'Google_Service_Apigee_Resource_OrganizationsSpaces');
