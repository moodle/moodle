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

use Google\Service\BigQueryReservation\Assignment;
use Google\Service\BigQueryReservation\BigqueryreservationEmpty;
use Google\Service\BigQueryReservation\ListAssignmentsResponse;
use Google\Service\BigQueryReservation\MoveAssignmentRequest;
use Google\Service\BigQueryReservation\Policy;
use Google\Service\BigQueryReservation\SetIamPolicyRequest;
use Google\Service\BigQueryReservation\TestIamPermissionsRequest;
use Google\Service\BigQueryReservation\TestIamPermissionsResponse;

/**
 * The "assignments" collection of methods.
 * Typical usage is:
 *  <code>
 *   $bigqueryreservationService = new Google\Service\BigQueryReservation(...);
 *   $assignments = $bigqueryreservationService->projects_locations_reservations_assignments;
 *  </code>
 */
class ProjectsLocationsReservationsAssignments extends \Google\Service\Resource
{
  /**
   * Creates an assignment object which allows the given project to submit jobs of
   * a certain type using slots from the specified reservation. Currently a
   * resource (project, folder, organization) can only have one assignment per
   * each (job_type, location) combination, and that reservation will be used for
   * all jobs of the matching type. Different assignments can be created on
   * different levels of the projects, folders or organization hierarchy. During
   * query execution, the assignment is looked up at the project, folder and
   * organization levels in that order. The first assignment found is applied to
   * the query. When creating assignments, it does not matter if other assignments
   * exist at higher levels. Example: * The organization `organizationA` contains
   * two projects, `project1` and `project2`. * Assignments for all three entities
   * (`organizationA`, `project1`, and `project2`) could all be created and mapped
   * to the same or different reservations. "None" assignments represent an
   * absence of the assignment. Projects assigned to None use on-demand pricing.
   * To create a "None" assignment, use "none" as a reservation_id in the parent.
   * Example parent: `projects/myproject/locations/US/reservations/none`. Returns
   * `google.rpc.Code.PERMISSION_DENIED` if user does not have 'bigquery.admin'
   * permissions on the project using the reservation and the project that owns
   * this reservation. Returns `google.rpc.Code.INVALID_ARGUMENT` when location of
   * the assignment does not match location of the reservation.
   * (assignments.create)
   *
   * @param string $parent Required. The parent resource name of the assignment
   * E.g. `projects/myproject/locations/US/reservations/team1-prod`
   * @param Assignment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string assignmentId The optional assignment ID. Assignment name
   * will be generated automatically if this field is empty. This field must only
   * contain lower case alphanumeric characters or dashes. Max length is 64
   * characters.
   * @return Assignment
   * @throws \Google\Service\Exception
   */
  public function create($parent, Assignment $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Assignment::class);
  }
  /**
   * Deletes a assignment. No expansion will happen. Example: * Organization
   * `organizationA` contains two projects, `project1` and `project2`. *
   * Reservation `res1` exists and was created previously. * CreateAssignment was
   * used previously to define the following associations between entities and
   * reservations: `` and `` In this example, deletion of the `` assignment won't
   * affect the other assignment ``. After said deletion, queries from `project1`
   * will still use `res1` while queries from `project2` will switch to use on-
   * demand mode. (assignments.delete)
   *
   * @param string $name Required. Name of the resource, e.g.
   * `projects/myproject/locations/US/reservations/team1-prod/assignments/123`
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
   * Gets the access control policy for a resource. May return: * A`NOT_FOUND`
   * error if the resource doesn't exist or you don't have the permission to view
   * it. * An empty policy if the resource exists but doesn't have a set policy.
   * Supported resources are: - Reservations - ReservationAssignments To call this
   * method, you must have the following Google IAM permissions: -
   * `bigqueryreservation.reservations.getIamPolicy` to get policies on
   * reservations. (assignments.getIamPolicy)
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
   * Lists assignments. Only explicitly created assignments will be returned.
   * Example: * Organization `organizationA` contains two projects, `project1` and
   * `project2`. * Reservation `res1` exists and was created previously. *
   * CreateAssignment was used previously to define the following associations
   * between entities and reservations: `` and `` In this example, ListAssignments
   * will just return the above two assignments for reservation `res1`, and no
   * expansion/merge will happen. The wildcard "-" can be used for reservations in
   * the request. In that case all assignments belongs to the specified project
   * and location will be listed. **Note** "-" cannot be used for projects nor
   * locations. (assignments.listProjectsLocationsReservationsAssignments)
   *
   * @param string $parent Required. The parent resource name e.g.:
   * `projects/myproject/locations/US/reservations/team1-prod` Or:
   * `projects/myproject/locations/US/reservations/-`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of items to return per page.
   * @opt_param string pageToken The next_page_token value returned from a
   * previous List request, if any.
   * @return ListAssignmentsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsReservationsAssignments($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAssignmentsResponse::class);
  }
  /**
   * Moves an assignment under a new reservation. This differs from removing an
   * existing assignment and recreating a new one by providing a transactional
   * change that ensures an assignee always has an associated reservation.
   * (assignments.move)
   *
   * @param string $name Required. The resource name of the assignment, e.g.
   * `projects/myproject/locations/US/reservations/team1-prod/assignments/123`
   * @param MoveAssignmentRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Assignment
   * @throws \Google\Service\Exception
   */
  public function move($name, MoveAssignmentRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('move', [$params], Assignment::class);
  }
  /**
   * Updates an existing assignment. Only the `priority` field can be updated.
   * (assignments.patch)
   *
   * @param string $name Output only. Name of the resource. E.g.:
   * `projects/myproject/locations/US/reservations/team1-prod/assignments/123`.
   * The assignment_id must only contain lower case alphanumeric characters or
   * dashes and the max length is 64 characters.
   * @param Assignment $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Standard field mask for the set of fields to be
   * updated.
   * @return Assignment
   * @throws \Google\Service\Exception
   */
  public function patch($name, Assignment $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Assignment::class);
  }
  /**
   * Sets an access control policy for a resource. Replaces any existing policy.
   * Supported resources are: - Reservations To call this method, you must have
   * the following Google IAM permissions: -
   * `bigqueryreservation.reservations.setIamPolicy` to set policies on
   * reservations. (assignments.setIamPolicy)
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
   * (assignments.testIamPermissions)
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
class_alias(ProjectsLocationsReservationsAssignments::class, 'Google_Service_BigQueryReservation_Resource_ProjectsLocationsReservationsAssignments');
