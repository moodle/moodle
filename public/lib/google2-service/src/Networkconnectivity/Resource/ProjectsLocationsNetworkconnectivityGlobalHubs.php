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

namespace Google\Service\Networkconnectivity\Resource;

use Google\Service\Networkconnectivity\AcceptHubSpokeRequest;
use Google\Service\Networkconnectivity\AcceptSpokeUpdateRequest;
use Google\Service\Networkconnectivity\GoogleLongrunningOperation;
use Google\Service\Networkconnectivity\Hub;
use Google\Service\Networkconnectivity\ListHubSpokesResponse;
use Google\Service\Networkconnectivity\ListHubsResponse;
use Google\Service\Networkconnectivity\Policy;
use Google\Service\Networkconnectivity\QueryHubStatusResponse;
use Google\Service\Networkconnectivity\RejectHubSpokeRequest;
use Google\Service\Networkconnectivity\RejectSpokeUpdateRequest;
use Google\Service\Networkconnectivity\SetIamPolicyRequest;
use Google\Service\Networkconnectivity\TestIamPermissionsRequest;
use Google\Service\Networkconnectivity\TestIamPermissionsResponse;

/**
 * The "hubs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networkconnectivityService = new Google\Service\Networkconnectivity(...);
 *   $hubs = $networkconnectivityService->projects_locations_global_hubs;
 *  </code>
 */
class ProjectsLocationsNetworkconnectivityGlobalHubs extends \Google\Service\Resource
{
  /**
   * Accepts a proposal to attach a Network Connectivity Center spoke to a hub.
   * (hubs.acceptSpoke)
   *
   * @param string $name Required. The name of the hub into which to accept the
   * spoke.
   * @param AcceptHubSpokeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function acceptSpoke($name, AcceptHubSpokeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('acceptSpoke', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Accepts a proposal to update a Network Connectivity Center spoke in a hub.
   * (hubs.acceptSpokeUpdate)
   *
   * @param string $name Required. The name of the hub to accept spoke update.
   * @param AcceptSpokeUpdateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function acceptSpokeUpdate($name, AcceptSpokeUpdateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('acceptSpokeUpdate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Creates a new Network Connectivity Center hub in the specified project.
   * (hubs.create)
   *
   * @param string $parent Required. The parent resource.
   * @param Hub $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string hubId Required. A unique identifier for the hub.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check to see whether
   * the original operation was received. If it was, the server ignores the second
   * request. This behavior prevents clients from mistakenly creating duplicate
   * commitments. The request ID must be a valid UUID, with the exception that
   * zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Hub $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes a Network Connectivity Center hub. (hubs.delete)
   *
   * @param string $name Required. The name of the hub to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check to see whether
   * the original operation was received. If it was, the server ignores the second
   * request. This behavior prevents clients from mistakenly creating duplicate
   * commitments. The request ID must be a valid UUID, with the exception that
   * zero UUID is not supported (00000000-0000-0000-0000-000000000000).
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
   * Gets details about a Network Connectivity Center hub. (hubs.get)
   *
   * @param string $name Required. The name of the hub resource to get.
   * @param array $optParams Optional parameters.
   * @return Hub
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Hub::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (hubs.getIamPolicy)
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
   * Lists the Network Connectivity Center hubs associated with a given project.
   * (hubs.listProjectsLocationsNetworkconnectivityGlobalHubs)
   *
   * @param string $parent Required. The parent resource's name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression that filters the list of results.
   * @opt_param string orderBy Sort the results by a certain order.
   * @opt_param int pageSize The maximum number of results per page to return.
   * @opt_param string pageToken The page token.
   * @return ListHubsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsNetworkconnectivityGlobalHubs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListHubsResponse::class);
  }
  /**
   * Lists the Network Connectivity Center spokes associated with a specified hub
   * and location. The list includes both spokes that are attached to the hub and
   * spokes that have been proposed but not yet accepted. (hubs.listSpokes)
   *
   * @param string $name Required. The name of the hub.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter An expression that filters the list of results.
   * @opt_param string orderBy Sort the results by name or create_time.
   * @opt_param int pageSize The maximum number of results to return per page.
   * @opt_param string pageToken The page token.
   * @opt_param string spokeLocations A list of locations. Specify one of the
   * following: `[global]`, a single region (for example, `[us-central1]`), or a
   * combination of values (for example, `[global, us-central1, us-west1]`). If
   * the spoke_locations field is populated, the list of results includes only
   * spokes in the specified location. If the spoke_locations field is not
   * populated, the list of results includes spokes in all locations.
   * @opt_param string view The view of the spoke to return. The view that you use
   * determines which spoke fields are included in the response.
   * @return ListHubSpokesResponse
   * @throws \Google\Service\Exception
   */
  public function listSpokes($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('listSpokes', [$params], ListHubSpokesResponse::class);
  }
  /**
   * Updates the description and/or labels of a Network Connectivity Center hub.
   * (hubs.patch)
   *
   * @param string $name Immutable. The name of the hub. Hub names must be unique.
   * They use the following form:
   * `projects/{project_number}/locations/global/hubs/{hub_id}`
   * @param Hub $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server knows to ignore the request if it has already been completed. The
   * server guarantees that a request doesn't result in creation of duplicate
   * commitments for at least 60 minutes. For example, consider a situation where
   * you make an initial request and the request times out. If you make the
   * request again with the same request ID, the server can check to see whether
   * the original operation was received. If it was, the server ignores the second
   * request. This behavior prevents clients from mistakenly creating duplicate
   * commitments. The request ID must be a valid UUID, with the exception that
   * zero UUID is not supported (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. In the case of an update to an
   * existing hub, field mask is used to specify the fields to be overwritten. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field is overwritten if it is in the mask. If the user does
   * not provide a mask, then all fields are overwritten.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Hub $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Query the Private Service Connect propagation status of a Network
   * Connectivity Center hub. (hubs.queryStatus)
   *
   * @param string $name Required. The name of the hub.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * results. The filter can be used to filter the results by the following
   * fields: * `psc_propagation_status.source_spoke` *
   * `psc_propagation_status.source_group` *
   * `psc_propagation_status.source_forwarding_rule` *
   * `psc_propagation_status.target_spoke` * `psc_propagation_status.target_group`
   * * `psc_propagation_status.code` * `psc_propagation_status.message`
   * @opt_param string groupBy Optional. Aggregate the results by the specified
   * fields. A comma-separated list of any of these fields: *
   * `psc_propagation_status.source_spoke` * `psc_propagation_status.source_group`
   * * `psc_propagation_status.source_forwarding_rule` *
   * `psc_propagation_status.target_spoke` * `psc_propagation_status.target_group`
   * * `psc_propagation_status.code`
   * @opt_param string orderBy Optional. Sort the results in ascending order by
   * the specified fields. A comma-separated list of any of these fields: *
   * `psc_propagation_status.source_spoke` * `psc_propagation_status.source_group`
   * * `psc_propagation_status.source_forwarding_rule` *
   * `psc_propagation_status.target_spoke` * `psc_propagation_status.target_group`
   * * `psc_propagation_status.code` If `group_by` is set, the value of the
   * `order_by` field must be the same as or a subset of the `group_by` field.
   * @opt_param int pageSize Optional. The maximum number of results to return per
   * page.
   * @opt_param string pageToken Optional. The page token.
   * @return QueryHubStatusResponse
   * @throws \Google\Service\Exception
   */
  public function queryStatus($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('queryStatus', [$params], QueryHubStatusResponse::class);
  }
  /**
   * Rejects a Network Connectivity Center spoke from being attached to a hub. If
   * the spoke was previously in the `ACTIVE` state, it transitions to the
   * `INACTIVE` state and is no longer able to connect to other spokes that are
   * attached to the hub. (hubs.rejectSpoke)
   *
   * @param string $name Required. The name of the hub from which to reject the
   * spoke.
   * @param RejectHubSpokeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function rejectSpoke($name, RejectHubSpokeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rejectSpoke', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Rejects a proposal to update a Network Connectivity Center spoke in a hub.
   * (hubs.rejectSpokeUpdate)
   *
   * @param string $name Required. The name of the hub to reject spoke update.
   * @param RejectSpokeUpdateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function rejectSpokeUpdate($name, RejectSpokeUpdateRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rejectSpokeUpdate', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (hubs.setIamPolicy)
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
   * This operation may "fail open" without warning. (hubs.testIamPermissions)
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
class_alias(ProjectsLocationsNetworkconnectivityGlobalHubs::class, 'Google_Service_Networkconnectivity_Resource_ProjectsLocationsNetworkconnectivityGlobalHubs');
