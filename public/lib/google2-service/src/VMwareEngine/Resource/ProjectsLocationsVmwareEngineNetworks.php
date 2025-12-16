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

use Google\Service\VMwareEngine\ListVmwareEngineNetworksResponse;
use Google\Service\VMwareEngine\Operation;
use Google\Service\VMwareEngine\VmwareEngineNetwork;

/**
 * The "vmwareEngineNetworks" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmwareengineService = new Google\Service\VMwareEngine(...);
 *   $vmwareEngineNetworks = $vmwareengineService->projects_locations_vmwareEngineNetworks;
 *  </code>
 */
class ProjectsLocationsVmwareEngineNetworks extends \Google\Service\Resource
{
  /**
   * Creates a new VMware Engine network that can be used by a private cloud.
   * (vmwareEngineNetworks.create)
   *
   * @param string $parent Required. The resource name of the location to create
   * the new VMware Engine network in. A VMware Engine network of type `LEGACY` is
   * a regional resource, and a VMware Engine network of type `STANDARD` is a
   * global resource. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/global`
   * @param VmwareEngineNetwork $postBody
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
   * @opt_param string vmwareEngineNetworkId Required. The user-provided
   * identifier of the new VMware Engine network. This identifier must be unique
   * among VMware Engine network resources within the parent and becomes the final
   * token in the name URI. The identifier must meet the following requirements: *
   * For networks of type LEGACY, adheres to the format: `{region-id}-default`.
   * Replace `{region-id}` with the region where you want to create the VMware
   * Engine network. For example, "us-central1-default". * Only contains 1-63
   * alphanumeric characters and hyphens * Begins with an alphabetical character *
   * Ends with a non-hyphen character * Not formatted as a UUID * Complies with
   * [RFC 1034](https://datatracker.ietf.org/doc/html/rfc1034) (section 3.5)
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, VmwareEngineNetwork $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a `VmwareEngineNetwork` resource. You can only delete a VMware Engine
   * network after all resources that refer to it are deleted. For example, a
   * private cloud, a network peering, and a network policy can all refer to the
   * same VMware Engine network. (vmwareEngineNetworks.delete)
   *
   * @param string $name Required. The resource name of the VMware Engine network
   * to be deleted. Resource names are schemeless URIs that follow the conventions
   * in https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/vmwareEngineNetworks/my-network`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. Checksum used to ensure that the user-
   * provided value is up to date before the server processes the request. The
   * server compares provided checksum with the current checksum of the resource.
   * If the user-provided value is out of date, this request returns an `ABORTED`
   * error.
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
   * Retrieves a `VmwareEngineNetwork` resource by its resource name. The resource
   * contains details of the VMware Engine network, such as its VMware Engine
   * network type, peered networks in a service project, and state (for example,
   * `CREATING`, `ACTIVE`, `DELETING`). (vmwareEngineNetworks.get)
   *
   * @param string $name Required. The resource name of the VMware Engine network
   * to retrieve. Resource names are schemeless URIs that follow the conventions
   * in https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/global/vmwareEngineNetworks/my-network`
   * @param array $optParams Optional parameters.
   * @return VmwareEngineNetwork
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], VmwareEngineNetwork::class);
  }
  /**
   * Lists `VmwareEngineNetwork` resources in a given project and location.
   * (vmwareEngineNetworks.listProjectsLocationsVmwareEngineNetworks)
   *
   * @param string $parent Required. The resource name of the location to query
   * for VMware Engine networks. Resource names are schemeless URIs that follow
   * the conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/global`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that matches resources returned
   * in the response. The expression must specify the field name, a comparison
   * operator, and the value that you want to use for filtering. The value must be
   * a string, a number, or a boolean. The comparison operator must be `=`, `!=`,
   * `>`, or `<`. For example, if you are filtering a list of network peerings,
   * you can exclude the ones named `example-network` by specifying `name !=
   * "example-network"`. To filter on multiple expressions, provide each separate
   * expression within parentheses. For example: ``` (name = "example-network")
   * (createTime > "2021-04-12T08:15:10.40Z") ``` By default, each expression is
   * an `AND` expression. However, you can include `AND` and `OR` expressions
   * explicitly. For example: ``` (name = "example-network-1") AND (createTime >
   * "2021-04-12T08:15:10.40Z") OR (name = "example-network-2") ```
   * @opt_param string orderBy Sorts list results by a certain order. By default,
   * returned results are ordered by `name` in ascending order. You can also sort
   * results in descending order based on the `name` value using `orderBy="name
   * desc"`. Currently, only ordering by `name` is supported.
   * @opt_param int pageSize The maximum number of results to return in one page.
   * The maximum value is coerced to 1000. The default value of this field is 500.
   * @opt_param string pageToken A page token, received from a previous
   * `ListVmwareEngineNetworks` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListVmwareEngineNetworks` must match the call that provided the page token.
   * @return ListVmwareEngineNetworksResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVmwareEngineNetworks($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListVmwareEngineNetworksResponse::class);
  }
  /**
   * Modifies a VMware Engine network resource. Only the following fields can be
   * updated: `description`. Only fields specified in `updateMask` are applied.
   * (vmwareEngineNetworks.patch)
   *
   * @param string $name Output only. Identifier. The resource name of the VMware
   * Engine network. Resource names are schemeless URIs that follow the
   * conventions in https://cloud.google.com/apis/design/resource_names. For
   * example: `projects/my-project/locations/global/vmwareEngineNetworks/my-
   * network`
   * @param VmwareEngineNetwork $postBody
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
   * fields to be overwritten in the VMware Engine network resource by the update.
   * The fields specified in the `update_mask` are relative to the resource, not
   * the full request. A field will be overwritten if it is in the mask. If the
   * user does not provide a mask then all fields will be overwritten. Only the
   * following fields can be updated: `description`.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, VmwareEngineNetwork $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVmwareEngineNetworks::class, 'Google_Service_VMwareEngine_Resource_ProjectsLocationsVmwareEngineNetworks');
