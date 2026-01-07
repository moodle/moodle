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

namespace Google\Service\CloudAlloyDBAdmin\Resource;

use Google\Service\CloudAlloyDBAdmin\Backup;
use Google\Service\CloudAlloyDBAdmin\ListBackupsResponse;
use Google\Service\CloudAlloyDBAdmin\Operation;

/**
 * The "backups" collection of methods.
 * Typical usage is:
 *  <code>
 *   $alloydbService = new Google\Service\CloudAlloyDBAdmin(...);
 *   $backups = $alloydbService->projects_locations_backups;
 *  </code>
 */
class ProjectsLocationsBackups extends \Google\Service\Resource
{
  /**
   * Creates a new Backup in a given project and location. (backups.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string backupId Required. ID of the requesting object.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Backup $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Backup. (backups.delete)
   *
   * @param string $name Required. Name of the resource. For the required format,
   * see the comment on the Backup.name field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. The current etag of the Backup. If an etag
   * is provided and does not match the current etag of the Backup, deletion will
   * be blocked and an ABORTED error will be returned.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
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
   * Gets details of a single Backup. (backups.get)
   *
   * @param string $name Required. Name of the resource
   * @param array $optParams Optional parameters.
   *
   * @opt_param string view Optional. The view of the backup to return.
   * @return Backup
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Backup::class);
  }
  /**
   * Lists Backups in a given project and location.
   * (backups.listProjectsLocationsBackups)
   *
   * @param string $parent Required. Parent value for ListBackupsRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Hint for how to order the results
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @opt_param string view Optional. The view of the backup to return.
   * @return ListBackupsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsBackups($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListBackupsResponse::class);
  }
  /**
   * Updates the parameters of a single Backup. (backups.patch)
   *
   * @param string $name Output only. The name of the backup resource with the
   * format: * projects/{project}/locations/{region}/backups/{backup_id} where the
   * cluster and backup ID segments should satisfy the regex expression
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters of lowercase letters,
   * numbers, and dashes, starting with a letter, and ending with a letter or
   * number. For more details see https://google.aip.dev/122. The prefix of the
   * backup resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}
   * @param Backup $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool allowMissing Optional. If set to true, update succeeds even
   * if instance is not found. In that case, a new backup is created and
   * `update_mask` is ignored.
   * @opt_param string requestId Optional. An optional request ID to identify
   * requests. Specify a unique request ID so that if you must retry your request,
   * the server ignores the request if it has already been completed. The server
   * guarantees that for at least 60 minutes since the first request. For example,
   * consider a situation where you make an initial request and the request times
   * out. If you make the request again with the same request ID, the server can
   * check if the original operation with the same request ID was received, and if
   * so, ignores the second request. This prevents clients from accidentally
   * creating duplicate commitments. The request ID must be a valid UUID with the
   * exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the Backup resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @opt_param bool validateOnly Optional. If set, the backend validates the
   * request, but doesn't actually execute it.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Backup $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsBackups::class, 'Google_Service_CloudAlloyDBAdmin_Resource_ProjectsLocationsBackups');
