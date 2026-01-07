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

use Google\Service\NetworkSecurity\InterceptEndpointGroupAssociation;
use Google\Service\NetworkSecurity\ListInterceptEndpointGroupAssociationsResponse;
use Google\Service\NetworkSecurity\Operation;

/**
 * The "interceptEndpointGroupAssociations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $networksecurityService = new Google\Service\NetworkSecurity(...);
 *   $interceptEndpointGroupAssociations = $networksecurityService->projects_locations_interceptEndpointGroupAssociations;
 *  </code>
 */
class ProjectsLocationsInterceptEndpointGroupAssociations extends \Google\Service\Resource
{
  /**
   * Creates an association in a given project and location. See
   * https://google.aip.dev/133. (interceptEndpointGroupAssociations.create)
   *
   * @param string $parent Required. The parent resource where this association
   * will be created. Format: projects/{project}/locations/{location}
   * @param InterceptEndpointGroupAssociation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string interceptEndpointGroupAssociationId Optional. The ID to use
   * for the new association, which will become the final component of the
   * endpoint group's resource name. If not provided, the server will generate a
   * unique ID.
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, InterceptEndpointGroupAssociation $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an association. See https://google.aip.dev/135.
   * (interceptEndpointGroupAssociations.delete)
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
   * (interceptEndpointGroupAssociations.get)
   *
   * @param string $name Required. The name of the association to retrieve.
   * Format: projects/{project}/locations/{location}/interceptEndpointGroupAssocia
   * tions/{intercept_endpoint_group_association}
   * @param array $optParams Optional parameters.
   * @return InterceptEndpointGroupAssociation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InterceptEndpointGroupAssociation::class);
  }
  /**
   * Lists associations in a given project and location. See
   * https://google.aip.dev/132. (interceptEndpointGroupAssociations.listProjectsL
   * ocationsInterceptEndpointGroupAssociations)
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
   * `ListInterceptEndpointGroups` call. Provide this to retrieve the subsequent
   * page. When paginating, all other parameters provided to
   * `ListInterceptEndpointGroups` must match the call that provided the page
   * token. See https://google.aip.dev/158 for more details.
   * @return ListInterceptEndpointGroupAssociationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInterceptEndpointGroupAssociations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInterceptEndpointGroupAssociationsResponse::class);
  }
  /**
   * Updates an association. See https://google.aip.dev/134.
   * (interceptEndpointGroupAssociations.patch)
   *
   * @param string $name Immutable. Identifier. The resource name of this endpoint
   * group association, for example:
   * `projects/123456789/locations/global/interceptEndpointGroupAssociations/my-
   * eg-association`. See https://google.aip.dev/122 for more details.
   * @param InterceptEndpointGroupAssociation $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A unique identifier for this request.
   * Must be a UUID4. This request is only idempotent if a `request_id` is
   * provided. See https://google.aip.dev/155 for more details.
   * @opt_param string updateMask Optional. The list of fields to update. Fields
   * are specified relative to the association (e.g. `description`; *not*
   * `intercept_endpoint_group_association.description`). See
   * https://google.aip.dev/161 for more details.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, InterceptEndpointGroupAssociation $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInterceptEndpointGroupAssociations::class, 'Google_Service_NetworkSecurity_Resource_ProjectsLocationsInterceptEndpointGroupAssociations');
