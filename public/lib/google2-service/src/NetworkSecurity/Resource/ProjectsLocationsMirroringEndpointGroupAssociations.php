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

use Google\Service\NetworkSecurity\ListMirroringEndpointGroupAssociationsResponse;
use Google\Service\NetworkSecurity\MirroringEndpointGroupAssociation;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "mirroringEndpointGroupAssociations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $mirroringEndpointGroupAssociations = $networksecurityService->projects_locations_mirroringEndpointGroupAssociations;
 *  </code>
 */
class ProjectsLocationsMirroringEndpointGroupAssociations extends \Google\Service\Resource
{
  /**
   * Creates an association in a given project and location. See
   * https://google.aip.dev/133. (mirroringEndpointGroupAssociations.create)
   *
   * @param string $parent Required. The parent resource where this association
   * will be created. Format: projects/{project}/locations/{location}
   * @param MirroringEndpointGroupAssociation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string mirroringEndpointGroupAssociationId Optional. The ID to use
   * for the new association, which will become the final component of the
   * endpoint group's resource name. If not provided, the server will generate a
   * unique ID.
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, MirroringEndpointGroupAssociation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an association. See https://google.aip.dev/135.
   * (mirroringEndpointGroupAssociations.delete)
   *
   * @param string $name Required. The association to delete.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
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
   * Gets a specific association. See https://google.aip.dev/131.
   * (mirroringEndpointGroupAssociations.get)
   *
   * @param string $name Required. The name of the association to retrieve.
   * Format: projects/{project}/locations/{location}/mirroringEndpointGroupAssocia
   * tions/{mirroring_endpoint_group_association}
   * @param array $optParams Optional parameters.
   * @return MirroringEndpointGroupAssociation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], MirroringEndpointGroupAssociation::class);
  }
  /**
   * Lists associations in a given project and location. See
   * https://google.aip.dev/132. (mirroringEndpointGroupAssociations.listProjectsL
   * ocationsMirroringEndpointGroupAssociations)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * associations. Example: `projects/123456789/locations/global`. See
   * https://google.aip.dev/132 for more details.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. Filter expression. See
   * https://google.aip.dev/160#filtering for more details.
   * @opt_param string orderBy Optional. Sort expression. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Optional. Requested page size. Server may return
   * fewer items than requested. If unspecified, server will pick an appropriate
   * default. See https://google.aip.dev/158 for more details.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListMirroringEndpointGroups` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListMirroringEndpointGroups` must match the call that provided the page
   * token. See https://google.aip.dev/158 for more details.
   * @return ListMirroringEndpointGroupAssociationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsMirroringEndpointGroupAssociations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListMirroringEndpointGroupAssociationsResponse::class);
  }
  /**
   * Updates an association. See https://google.aip.dev/134.
   * (mirroringEndpointGroupAssociations.patch)
   *
   * @param string $name Immutable. Identifier. The resource name of this endpoint
   * group association, for example:
   * `projects/123456789/locations/global/mirroringEndpointGroupAssociations/my-
   * eg-association`. See https://google.aip.dev/122 for more details.
   * @param MirroringEndpointGroupAssociation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @opt_param string updateMask Optional. The list of fields to update. Fields
   * are specified relative to the association (e.g. `description`; *not*
   * `mirroring_endpoint_group_association.description`). See
   * https://google.aip.dev/161 for more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, MirroringEndpointGroupAssociation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsMirroringEndpointGroupAssociations::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsMirroringEndpointGroupAssociations');
