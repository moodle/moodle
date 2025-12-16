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

namespace Google\Service\SaaSServiceManagement\Resource;

use Google\Service\SaaSServiceManagement\ListUnitsResponse;
use Google\Service\SaaSServiceManagement\SaasservicemgmtEmpty;
use Google\Service\SaaSServiceManagement\Unit;

/**
 * The "units" collection of methods.
 * Typical usage is:
 *  <code>
 *   $saasservicemgmtService = new Google\Service\SaaSServiceManagement(...);
 *   $units = $saasservicemgmtService->projects_locations_units;
 *  </code>
 */
class ProjectsLocationsUnits extends \Google\Service\Resource
{
  /**
   * Create a new unit. (units.create)
   *
   * @param string $parent Required. The parent of the unit.
   * @param Unit $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string unitId Required. The ID value for the new unit.
   * @opt_param bool validateOnly If "validate_only" is set to true, the service
   * will try to validate that this request would succeed, but will not actually
   * make changes.
   * @return Unit
   * @throws \Google\Service\Exception
   */
  public function create($parent, Unit $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Unit::class);
  }
  /**
   * Delete a single unit. (units.delete)
   *
   * @param string $name Required. The resource name of the resource within a
   * service.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag The etag known to the client for the expected state of
   * the unit. This is used with state-changing methods to prevent accidental
   * overwrites when multiple user agents might be acting in parallel on the same
   * resource. An etag wildcard provide optimistic concurrency based on the
   * expected existence of the unit. The Any wildcard (`*`) requires that the
   * resource must already exists, and the Not Any wildcard (`!*`) requires that
   * it must not.
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly If "validate_only" is set to true, the service
   * will try to validate that this request would succeed, but will not actually
   * make changes.
   * @return SaasservicemgmtEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SaasservicemgmtEmpty::class);
  }
  /**
   * Retrieve a single unit. (units.get)
   *
   * @param string $name Required. The resource name of the resource within a
   * service.
   * @param array $optParams Optional parameters.
   * @return Unit
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Unit::class);
  }
  /**
   * Retrieve a collection of units. (units.listProjectsLocationsUnits)
   *
   * @param string $parent Required. The parent of the unit.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filter the list as specified in
   * https://google.aip.dev/160.
   * @opt_param string orderBy Order results as specified in
   * https://google.aip.dev/132.
   * @opt_param int pageSize The maximum number of units to send per page.
   * @opt_param string pageToken The page token: If the next_page_token from a
   * previous response is provided, this request will send the subsequent page.
   * @return ListUnitsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsUnits($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUnitsResponse::class);
  }
  /**
   * Update a single unit. (units.patch)
   *
   * @param string $name Identifier. The resource name (full URI of the resource)
   * following the standard naming scheme:
   * "projects/{project}/locations/{location}/units/{unit}"
   * @param Unit $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId An optional request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request times out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Field mask is used to specify the fields to be
   * overwritten in the Unit resource by the update. The fields specified in the
   * update_mask are relative to the resource, not the full request. A field will
   * be overwritten if it is in the mask. If the user does not provide a mask then
   * all fields in the Unit will be overwritten.
   * @opt_param bool validateOnly If "validate_only" is set to true, the service
   * will try to validate that this request would succeed, but will not actually
   * make changes.
   * @return Unit
   * @throws \Google\Service\Exception
   */
  public function patch($name, Unit $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Unit::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsUnits::class, 'Google_Service_SaaSServiceManagement_Resource_ProjectsLocationsUnits');
