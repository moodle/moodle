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

use Google\Service\VMwareEngine\ExternalAddress;
use Google\Service\VMwareEngine\ListExternalAddressesResponse;
use Google\Service\VMwareEngine\Operation;

/**
 * The "externalAddresses" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $externalAddresses = $vmwareengineService->projects_locations_privateClouds_externalAddresses;
 *  </code>
 */
class ProjectsLocationsPrivateCloudsExternalAddresses extends \Google\Service\Resource
{
  /**
   * Creates a new `ExternalAddress` resource in a given private cloud. The
   * network policy that corresponds to the private cloud must have the external
   * IP address network service enabled (`NetworkPolicy.external_ip`).
   * (externalAddresses.create)
   *
   * @param string $parent Required. The resource name of the private cloud to
   * create a new external IP address in. Resource names are schemeless URIs that
   * follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param ExternalAddress $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string externalAddressId Required. The user-provided identifier of
   * the `ExternalAddress` to be created. This identifier must be unique among
   * `ExternalAddress` resources within the parent and becomes the final token in
   * the name URI. The identifier must meet the following requirements: * Only
   * contains 1-63 alphanumeric characters and hyphens * Begins with an
   * alphabetical character * Ends with a non-hyphen character * Not formatted as
   * a UUID * Complies with [RFC
   * 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if the original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, ExternalAddress $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single external IP address. When you delete an external IP address,
   * connectivity between the external IP address and the corresponding internal
   * IP address is lost. (externalAddresses.delete)
   *
   * @param string $name Required. The resource name of the external IP address to
   * delete. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/externalAddresses/my-ip`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if the original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
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
   * Gets details of a single external IP address. (externalAddresses.get)
   *
   * @param string $name Required. The resource name of the external IP address to
   * retrieve. Resource names are schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/externalAddresses/my-ip`
   * @param array $optParams Optional parameters.
   * @return ExternalAddress
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ExternalAddress::class);
  }
  /**
   * Lists external IP addresses assigned to VMware workload VMs in a given
   * private cloud.
   * (externalAddresses.listProjectsLocationsPrivateCloudsExternalAddresses)
   *
   * @param string $parent Required. The resource name of the private cloud to be
   * queried for external IP addresses. Resource names are schemeless URIs that
   * follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-central1-a/privateClouds/my-cloud`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of IP addresses, you
   * can exclude the ones named `example-ip` by specifying `name != "example-ip"`.
   * To filter on multiple expressions, provide each separate expression within
   * parentheses. For example: ``` (name = "example-ip") (createTime >
   * "2021-04-12T08:15:10.40Z") ``` By default, each expression is an `AND`
   * expression. However, you can include `AND` and `OR` expressions explicitly.
   * For example: ``` (name = "example-ip-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-ip-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of external IP addresses to return
   * in one page. The service may return fewer than this value. The maximum value
   * is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListExternalAddresses` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListExternalAddresses`
   * must match the call that provided the page token.
   * @return ListExternalAddressesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPrivateCloudsExternalAddresses($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListExternalAddressesResponse::class);
  }
  /**
   * Updates the parameters of a single external IP address. Only fields specified
   * in `update_mask` are applied. During operation processing, the resource is
   * temporarily in the `ACTIVE` state before the operation fully completes. For
   * that period of time, you can't update the resource. Use the operation status
   * to determine when the processing fully completes. (externalAddresses.patch)
   *
   * @param string $name Output only. Identifier. The resource name of this
   * external IP address. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/us-central1-a/privateClouds/my-
   * cloud/externalAddresses/my-address`
   * @param ExternalAddress $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check if the original
   * operation with the same request ID was received, and if so, will ignore the
   * second request. This prevents clients from accidentally creating duplicate
   * commitments. The request ID must be a valid UUID with the exception that zero
   * UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `ExternalAddress` resource by the update. The
   * fields specified in the `update_mask` are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, ExternalAddress $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPrivateCloudsExternalAddresses::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsPrivateCloudsExternalAddresses');
