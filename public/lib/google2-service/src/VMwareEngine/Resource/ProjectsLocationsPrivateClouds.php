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

use Google\Service\VMwareEngine\AcceleratePrivateCloudDeletionRequest;
use Google\Service\VMwareEngine\Credentials;
use Google\Service\VMwareEngine\DnsForwarding;
use Google\Service\VMwareEngine\ListPrivateCloudsResponse;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\Policy;
use Google\Service\VMwareEngine\PrivateCloud;
use Google\Service\VMwareEngine\ResetNsxCredentialsRequest;
use Google\Service\VMwareEngine\ResetVcenterCredentialsRequest;
use Google\Service\VMwareEngine\SetIamPolicyRequest;
use Google\Service\VMwareEngine\TestIamPermissionsRequest;
use Google\Service\VMwareEngine\TestIamPermissionsResponse;
use Google\Service\VMwareEngine\UndeletePrivateCloudRequest;

/**
 * The "privateClouds" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $privateClouds = $vmwareengineService->projects_locations_privateClouds;
 *  </code>
 */
class ProjectsLocationsPrivateClouds extends \Google\Service\Resource
{
  /**
   * Creates a new `PrivateCloud` resource in a given project and location.
   * Private clouds of type `STANDARD` and `TIME_LIMITED` are zonal resources,
   * `STRETCHED` private clouds are regional. Creating a private cloud also
   * creates a [management cluster](https://cloud.google.com/vmware-
   * engine/docs/concepts-vmware-components) for that private cloud.
   * (privateClouds.create)
   *
   * @param string $parent Required. The resource name of the location to create
   * the new private cloud in. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a`
   * @param PrivateCloud $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string privateCloudId Required. The user-provided identifier of
   * the private cloud to be created. This identifier must be unique among each
   * `PrivateCloud` within the parent and becomes the final token in the name URI.
   * The identifier must meet the following requirements: * Only contains 1-63
   * alphanumeric characters and hyphens * Begins with an alphabetical character *
   * Ends with a non-hyphen character * Not formatted as a UUID * Complies with
   * [RFC 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. True if you want the request to be
   * validated and not executed; false otherwise.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, PrivateCloud $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Schedules a `PrivateCloud` resource for deletion. A `PrivateCloud` resource
   * scheduled for deletion has `PrivateCloud.state` set to `DELETED` and
   * `expireTime` set to the time when deletion is final and can no longer be
   * reversed. The delete operation is marked as done as soon as the
   * `PrivateCloud` is successfully scheduled for deletion (this also applies when
   * `delayHours` is set to zero), and the operation is not kept in pending state
   * until `PrivateCloud` is purged. `PrivateCloud` can be restored using
   * `UndeletePrivateCloud` method before the `expireTime` elapses. When
   * `expireTime` is reached, deletion is final and all private cloud resources
   * are irreversibly removed and billing stops. During the final removal process,
   * `PrivateCloud.state` is set to `PURGING`. `PrivateCloud` can be polled using
   * standard `GET` method for the whole period of deletion and purging. It will
   * not be returned only when it is completely purged. (privateClouds.delete)
   *
   * @param string $name Required. The resource name of the private cloud to
   * delete. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int delayHours Optional. Time delay of the deletion specified in
   * hours. The default value is `3`. Specifying a non-zero value for this field
   * changes the value of `PrivateCloud.state` to `DELETED` and sets `expire_time`
   * to the planned deletion time. Deletion can be cancelled before `expire_time`
   * elapses using VmwareEngine.UndeletePrivateCloud. Specifying a value of `0`
   * for this field instead begins the deletion process and ceases billing
   * immediately. During the final deletion process, the value of
   * `PrivateCloud.state` becomes `PURGING`.
   * @opt_param bool force Optional. If set to true, cascade delete is enabled and
   * all children of this private cloud resource are also deleted. When this flag
   * is set to false, the private cloud will not be deleted if there are any
   * children other than the management cluster. The management cluster is always
   * deleted.
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
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
   * Retrieves a `PrivateCloud` resource by its resource name. (privateClouds.get)
   *
   * @param string $name Required. The resource name of the private cloud to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   * @return PrivateCloud
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PrivateCloud::class);
  }
  /**
   * Gets details of the `DnsForwarding` config. (privateClouds.getDnsForwarding)
   *
   * @param string $name Required. The resource name of a `DnsForwarding` to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/dnsForwarding`
   * @param array $optParams Optional parameters.
   * @return DnsForwarding
   * @throws \Google\Service\Exception
   */
  public function getDnsForwarding($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDnsForwarding', [$params], DnsForwarding::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (privateClouds.getIamPolicy)
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
   * Lists `PrivateCloud` resources in a given project and location.
   * (privateClouds.listProjectsLocationsPrivateClouds)
   *
   * @param string $parent Required. The resource name of the private cloud to be
   * queried for clusters. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of private clouds, you
   * can exclude the ones named `example-pc` by specifying `name != "example-pc"`.
   * You can also filter nested fields. For example, you could specify
   * `networkConfig.managementCidr = "192.168.0.0/24"` to include private clouds
   * only if they have a matching address in their network configuration. To
   * filter on multiple expressions, provide each separate expression within
   * parentheses. For example: ``` (name = "example-pc") (createTime >
   * "2021-04-12T08:15:10.40Z") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "private-cloud-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "private-cloud-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of private clouds to return in one
   * page. The service may return fewer than this value. The maximum value is
   * coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListPrivateClouds` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListPrivateClouds` must match
   * the call that provided the page token.
   * @return ListPrivateCloudsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateClouds($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPrivateCloudsResponse::class);
  }
  /**
   * Modifies a `PrivateCloud` resource. Only the following fields can be updated:
   * `description`. Only fields specified in `updateMask` are applied. During
   * operation processing, the resource is temporarily in the `ACTIVE` state
   * before the operation fully completes. For that period of time, you can't
   * update the resource. Use the operation status to determine when the
   * processing fully completes. (privateClouds.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * private cloud. Resource names are schemeless URIs that follow the conventions
   * in https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param PrivateCloud $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. The request ID must be a valid UUID
   * with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `PrivateCloud` resource by the update. The
   * fields specified in `updateMask` are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, PrivateCloud $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Accelerates the deletion of a private cloud that is currently in soft
   * deletion A `PrivateCloud` resource in soft deletion has `PrivateCloud.state`
   * set to `SOFT_DELETED` and `PrivateCloud.expireTime` set to the time when
   * deletion can no longer be reversed. (privateClouds.privateCloudDeletionNow)
   *
   * @param string $name Required. The resource name of the private cloud in
   * softdeletion. Resource names are schemeless URIs that follow the conventions
   * in https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param AcceleratePrivateCloudDeletionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function privateCloudDeletionNow($name, AcceleratePrivateCloudDeletionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('privateCloudDeletionNow', [$params], Operation::class);
  }
  /**
   * Resets credentials of the NSX appliance. (privateClouds.resetNsxCredentials)
   *
   * @param string $privateCloud Required. The resource name of the private cloud
   * to reset credentials for. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param ResetNsxCredentialsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resetNsxCredentials($privateCloud, ResetNsxCredentialsRequest $postBody, $optParams = [])
  {
    $params = ['privateCloud' => $privateCloud, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resetNsxCredentials', [$params], Operation::class);
  }
  /**
   * Resets credentials of the Vcenter appliance.
   * (privateClouds.resetVcenterCredentials)
   *
   * @param string $privateCloud Required. The resource name of the private cloud
   * to reset credentials for. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param ResetVcenterCredentialsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resetVcenterCredentials($privateCloud, ResetVcenterCredentialsRequest $postBody, $optParams = [])
  {
    $params = ['privateCloud' => $privateCloud, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resetVcenterCredentials', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (privateClouds.setIamPolicy)
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
   * Gets details of credentials for NSX appliance.
   * (privateClouds.showNsxCredentials)
   *
   * @param string $privateCloud Required. The resource name of the private cloud
   * to be queried for credentials. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   * @return Credentials
   * @throws \Google\Service\Exception
   */
  public function showNsxCredentials($privateCloud, $optParams = [])
  {
    $params = ['privateCloud' => $privateCloud];
    $params = array_merge($params, $optParams);
    return $this->call('showNsxCredentials', [$params], Credentials::class);
  }
  /**
   * Gets details of credentials for Vcenter appliance.
   * (privateClouds.showVcenterCredentials)
   *
   * @param string $privateCloud Required. The resource name of the private cloud
   * to be queried for credentials. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string username Optional. The username of the user to be queried
   * for credentials. The default value of this field is CloudOwner@gve.local. The
   * provided value must be one of the following: CloudOwner@gve.local, solution-
   * user-01@gve.local, solution-user-02@gve.local, solution-user-03@gve.local,
   * solution-user-04@gve.local, solution-user-05@gve.local, zertoadmin@gve.local.
   * @return Credentials
   * @throws \Google\Service\Exception
   */
  public function showVcenterCredentials($privateCloud, $optParams = [])
  {
    $params = ['privateCloud' => $privateCloud];
    $params = array_merge($params, $optParams);
    return $this->call('showVcenterCredentials', [$params], Credentials::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (privateClouds.testIamPermissions)
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
  /**
   * Restores a private cloud that was previously scheduled for deletion by
   * `DeletePrivateCloud`. A `PrivateCloud` resource scheduled for deletion has
   * `PrivateCloud.state` set to `DELETED` and `PrivateCloud.expireTime` set to
   * the time when deletion can no longer be reversed. (privateClouds.undelete)
   *
   * @param string $name Required. The resource name of the private cloud
   * scheduled for deletion. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param UndeletePrivateCloudRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function undelete($name, UndeletePrivateCloudRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('undelete', [$params], Operation::class);
  }
  /**
   * Updates the parameters of the `DnsForwarding` config, like associated
   * domains. Only fields specified in `update_mask` are applied.
   * (privateClouds.updateDnsForwarding)
   *
   * @param string $name Output only. Identifier. The resource name of this DNS
   * profile. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/dnsForwarding`
   * @param DnsForwarding $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `DnsForwarding` resource by the update. The
   * fields specified in the `update_mask` are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function updateDnsForwarding($name, DnsForwarding $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('updateDnsForwarding', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateClouds::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateClouds');
