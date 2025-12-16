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

namespace Google\Service\NetworkSecurity\Resource;

use Google\Service\NetworkSecurity\AddAddressGroupItemsRequest;
use Google\Service\NetworkSecurity\AddressGroup;
use Google\Service\NetworkSecurity\CloneAddressGroupItemsRequest;
use Google\Service\NetworkSecurity\GoogleIamV1Policy;
use Google\Service\NetworkSecurity\GoogleIamV1SetIamPolicyRequest;
use Google\Service\NetworkSecurity\GoogleIamV1TestIamPermissionsRequest;
use Google\Service\NetworkSecurity\GoogleIamV1TestIamPermissionsResponse;
use Google\Service\NetworkSecurity\ListAddressGroupReferencesResponse;
use Google\Service\NetworkSecurity\ListAddressGroupsResponse;
use Google\Service\NetworkSecurity\Operation;
use Google\Service\NetworkSecurity\RemoveAddressGroupItemsRequest;

/**
 * The "addressGroups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $addressGroups = $networksecurityService->projects_locations_addressGroups;
 *  </code>
 */
class ProjectsLocationsAddressGroups extends \Google\Service\Resource
{
  /**
   * Adds items to an address group. (addressGroups.addItems)
   *
   * @param string $addressGroup Required. A name of the AddressGroup to add items
   * to. Must be in the format
   * `projects|organization/locations/{location}/addressGroups`.
   * @param AddAddressGroupItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function addItems($addressGroup, AddAddressGroupItemsRequest $postBody, $optParams = [])
  {
    $params = ['addressGroup' => $addressGroup, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('addItems', [$params], Operation::class);
  }
  /**
   * Clones items from one address group to another. (addressGroups.cloneItems)
   *
   * @param string $addressGroup Required. A name of the AddressGroup to clone
   * items to. Must be in the format
   * `projects|organization/locations/{location}/addressGroups`.
   * @param CloneAddressGroupItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function cloneItems($addressGroup, CloneAddressGroupItemsRequest $postBody, $optParams = [])
  {
    $params = ['addressGroup' => $addressGroup, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cloneItems', [$params], Operation::class);
  }
  /**
   * Creates a new address group in a given project and location.
   * (addressGroups.create)
   *
   * @param string $parent Required. The parent resource of the AddressGroup. Must
   * be in the format `projects/locations/{location}`.
   * @param AddressGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string addressGroupId Required. Short name of the AddressGroup
   * resource to be created. This value should be 1-63 characters long, containing
   * only letters, numbers, hyphens, and underscores, and should not start with a
   * number. E.g. "authz_policy".
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, AddressGroup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single address group. (addressGroups.delete)
   *
   * @param string $name Required. A name of the AddressGroup to delete. Must be
   * in the format `projects/locations/{location}/addressGroups`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets details of a single address group. (addressGroups.get)
   *
   * @param string $name Required. A name of the AddressGroup to get. Must be in
   * the format `projects/locations/{location}/addressGroups`.
   * @param array $optParams Optional parameters.
   * @return AddressGroup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], AddressGroup::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (addressGroups.getIamPolicy)
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
   * Lists address groups in a given project and location.
   * (addressGroups.listProjectsLocationsAddressGroups)
   *
   * @param string $parent Required. The project and location from which the
   * AddressGroups should be listed, specified in the format
   * `projects/locations/{location}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Maximum number of AddressGroups to return per call.
   * @opt_param string pageToken The value returned by the last
   * `ListAddressGroupsResponse` Indicates that this is a continuation of a prior
   * `ListAddressGroups` call, and that the system should return the next page of
   * data.
   * @opt_param bool returnPartialSuccess Optional. If true, allow partial
   * responses for multi-regional Aggregated List requests.
   * @return ListAddressGroupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsAddressGroups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAddressGroupsResponse::class);
  }
  /**
   * Lists references of an address group. (addressGroups.listReferences)
   *
   * @param string $addressGroup Required. A name of the AddressGroup to clone
   * items to. Must be in the format
   * `projects|organization/locations/{location}/addressGroups`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of references to return. If
   * unspecified, server will pick an appropriate default. Server may return fewer
   * items than requested. A caller should only rely on response's next_page_token
   * to determine if there are more AddressGroupUsers left to be queried.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @return ListAddressGroupReferencesResponse
   * @throws \Google\Service\Exception
   */
  public function listReferences($addressGroup, $optParams = [])
  {
    $params = ['addressGroup' => $addressGroup];
    $params = array_merge($params, $optParams);
    return $this->call('listReferences', [$params], ListAddressGroupReferencesResponse::class);
  }
  /**
   * Updates the parameters of a single address group. (addressGroups.patch)
   *
   * @param string $name Required. Name of the AddressGroup resource. It matches
   * pattern `projects/locations/{location}/addressGroups/`.
   * @param AddressGroup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes since the first
   * request. For example, consider a situation where you make an initial request
   * and the request times out. If you make the request again with the same
   * request ID, the server can check if original operation with the same request
   * ID was received, and if so, will ignore the second request. This prevents
   * clients from accidentally creating duplicate commitments. The request ID must
   * be a valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the AddressGroup resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, AddressGroup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Removes items from an address group. (addressGroups.removeItems)
   *
   * @param string $addressGroup Required. A name of the AddressGroup to remove
   * items from. Must be in the format
   * `projects|organization/locations/{location}/addressGroups`.
   * @param RemoveAddressGroupItemsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function removeItems($addressGroup, RemoveAddressGroupItemsRequest $postBody, $optParams = [])
  {
    $params = ['addressGroup' => $addressGroup, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('removeItems', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (addressGroups.setIamPolicy)
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
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (addressGroups.testIamPermissions)
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
class_alias(ProjectsLocationsAddressGroups::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsAddressGroups');
