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

namespace Google\Service\MigrationCenterAPI\Resource;

use Google\Service\MigrationCenterAPI\ListPreferenceSetsResponse;
use Google\Service\MigrationCenterAPI\Operation;
use Google\Service\MigrationCenterAPI\PreferenceSet;

/**
 * The "preferenceSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $migrationcenterService = new Google\Service\MigrationCenterAPI(...);
 *   $preferenceSets = $migrationcenterService->projects_locations_preferenceSets;
 *  </code>
 */
class ProjectsLocationsPreferenceSets extends \Google\Service\Resource
{
  /**
   * Creates a new preference set in a given project and location.
   * (preferenceSets.create)
   *
   * @param string $parent Required. Value for parent.
   * @param PreferenceSet $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string preferenceSetId Required. User specified ID for the
   * preference set. It will become the last component of the preference set name.
   * The ID must be unique within the project, must conform with RFC-1034, is
   * restricted to lower-cased letters, and has a maximum length of 63 characters.
   * The ID must match the regular expression `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`.
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
  public function create($parent, PreferenceSet $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a preference set. (preferenceSets.delete)
   *
   * @param string $name Required. Name of the group resource.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server will know to ignore the request if it has already been completed.
   * The server will guarantee that for at least 60 minutes after the first
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
   * Gets the details of a preference set. (preferenceSets.get)
   *
   * @param string $name Required. Name of the resource.
   * @param array $optParams Optional parameters.
   * @return PreferenceSet
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], PreferenceSet::class);
  }
  /**
   * Lists all the preference sets in a given project and location.
   * (preferenceSets.listProjectsLocationsPreferenceSets)
   *
   * @param string $parent Required. Parent value for `ListPreferenceSetsRequest`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Field to sort by. See
   * https://google.aip.dev/132#ordering for more details.
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, at most 500 preference sets will be returned.
   * The maximum value is 1000; values above 1000 will be coerced to 1000.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListPreferenceSetsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPreferenceSets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListPreferenceSetsResponse::class);
  }
  /**
   * Updates the parameters of a preference set. (preferenceSets.patch)
   *
   * @param string $name Output only. Name of the preference set.
   * @param PreferenceSet $postBody
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
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the `PreferenceSet` resource by the update. The
   * values specified in the `update_mask` field are relative to the resource, not
   * the full request. A field will be overwritten if it is in the mask. A single
   * * value in the mask lets you to overwrite all fields.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, PreferenceSet $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPreferenceSets::class, 'Google_Service_MigrationCenterAPI_Resource_ProjectsLocationsPreferenceSets');
