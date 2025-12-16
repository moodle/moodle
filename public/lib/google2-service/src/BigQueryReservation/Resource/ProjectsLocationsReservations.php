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

namespace Google\Service\BigQueryReservation\Resource;

use Google\Service\BigQueryReservation\BigqueryreservationEmpty;
use Google\Service\BigQueryReservation\FailoverReservationRequest;
use Google\Service\BigQueryReservation\ListReservationsResponse;
use Google\Service\BigQueryReservation\Policy;
use Google\Service\BigQueryReservation\Reservation;
use Google\Service\BigQueryReservation\SetIamPolicyRequest;
use Google\Service\BigQueryReservation\TestIamPermissionsRequest;
use Google\Service\BigQueryReservation\TestIamPermissionsResponse;

/**
 * The "reservations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryreservationService = new Google\Service\BigQueryReservation(...);
 *   $reservations = $bigqueryreservationService->projects_locations_reservations;
 *  </code>
 */
class ProjectsLocationsReservations extends \Google\Service\Resource
{
  /**
   * Creates a new reservation resource. (reservations.create)
   *
   * @param string $parent Required. Project, location. E.g.,
   * `projects/myproject/locations/US`
   * @param Reservation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string reservationId The reservation ID. It must only contain
   * lower case alphanumeric characters or dashes. It must start with a letter and
   * must not end with a dash. Its maximum length is 64 characters.
   * @return Reservation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Reservation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Reservation::class);
  }
  /**
   * Deletes a reservation. Returns `google.rpc.Code.FAILED_PRECONDITION` when
   * reservation has assignments. (reservations.delete)
   *
   * @param string $name Required. Resource name of the reservation to retrieve.
   * E.g., `projects/myproject/locations/US/reservations/team1-prod`
   * @param array $optParams Optional parameters.
   * @return BigqueryreservationEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BigqueryreservationEmpty::class);
  }
  /**
   * Fail over a reservation to the secondary location. The operation should be
   * done in the current secondary location, which will be promoted to the new
   * primary location for the reservation. Attempting to failover a reservation in
   * the current primary location will fail with the error code
   * `google.rpc.Code.FAILED_PRECONDITION`. (reservations.failoverReservation)
   *
   * @param string $name Required. Resource name of the reservation to failover.
   * E.g., `projects/myproject/locations/US/reservations/team1-prod`
   * @param FailoverReservationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Reservation
   * @throws \Google\Service\Exception
   */
  public function failoverReservation($name, FailoverReservationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('failoverReservation', [$params], Reservation::class);
  }
  /**
   * Returns information about the reservation. (reservations.get)
   *
   * @param string $name Required. Resource name of the reservation to retrieve.
   * E.g., `projects/myproject/locations/US/reservations/team1-prod`
   * @param array $optParams Optional parameters.
   * @return Reservation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Reservation::class);
  }
  /**
   * Gets the access control policy for a resource. May return: * A`NOT_FOUND`
   * error if the resource doesn't exist or you don't have the permission to view
   * it. * An empty policy if the resource exists but doesn't have a set policy.
   * Supported resources are: - Reservations - ReservationAssignments To call this
   * method, you must have the following Google IAM permissions: -
   * `bigqueryreservation.reservations.getIamPolicy` to get policies on
   * reservations. (reservations.getIamPolicy)
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
   * Lists all the reservations for the project in the specified location.
   * (reservations.listProjectsLocationsReservations)
   *
   * @param string $parent Required. The parent resource name containing project
   * and location, e.g.: `projects/myproject/locations/US`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return per page.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @return ListReservationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReservations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReservationsResponse::class);
  }
  /**
   * Updates an existing reservation resource. (reservations.patch)
   *
   * @param string $name Identifier. The resource name of the reservation, e.g.,
   * `projects/locations/reservations/team1-prod`. The reservation_id must only
   * contain lower case alphanumeric characters or dashes. It must start with a
   * letter and must not end with a dash. Its maximum length is 64 characters.
   * @param Reservation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Standard field mask for the set of fields to be
   * updated.
   * @return Reservation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Reservation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Reservation::class);
  }
  /**
   * Sets an access control policy for a resource. Replaces any existing policy.
   * Supported resources are: - Reservations To call this method, you must have
   * the following Google IAM permissions: -
   * `bigqueryreservation.reservations.setIamPolicy` to set policies on
   * reservations. (reservations.setIamPolicy)
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
   * Gets your permissions on a resource. Returns an empty set of permissions if
   * the resource doesn't exist. Supported resources are: - Reservations No Google
   * IAM permissions are required to call this method.
   * (reservations.testIamPermissions)
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
class_alias(ProjectsLocationsReservations::class, 'Google_Service_BigQueryReservation_Resource_ProjectsLocationsReservations');
