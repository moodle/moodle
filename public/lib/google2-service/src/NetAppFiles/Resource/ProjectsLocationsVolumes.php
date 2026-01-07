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

namespace Google\Service\NetAppFiles\Resource;

use Google\Service\NetAppFiles\ListVolumesResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\RestoreBackupFilesRequest;
use Google\Service\NetAppFiles\RevertVolumeRequest;
use Google\Service\NetAppFiles\Volume;

/**
 * The "volumes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $volumes = $netappService->projects_locations_volumes;
 *  </code>
 */
class ProjectsLocationsVolumes extends \Google\Service\Resource
{
  /**
   * Creates a new Volume in a given project and location. (volumes.create)
   *
   * @param string $parent Required. Value for parent.
   * @param Volume $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string volumeId Required. Id of the requesting volume. Must be
   * unique within the parent resource. Must contain only letters, numbers and
   * hyphen, with the first character a letter, the last a letter or a number, and
   * a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Volume $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single Volume. (volumes.delete)
   *
   * @param string $name Required. Name of the volume
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If this field is set as true, CCFE will not block the
   * volume resource deletion even if it has any snapshots resource. (Otherwise,
   * the request will only work if the volume has no snapshots.)
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
   * Gets details of a single Volume. (volumes.get)
   *
   * @param string $name Required. Name of the volume
   * @param array $optParams Optional parameters.
   * @return Volume
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Volume::class);
  }
  /**
   * Lists Volumes in a given project. (volumes.listProjectsLocationsVolumes)
   *
   * @param string $parent Required. Parent value for ListVolumesRequest
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Filtering results
   * @opt_param string orderBy Hint for how to order the results
   * @opt_param int pageSize Requested page size. Server may return fewer items
   * than requested. If unspecified, the server will pick an appropriate default.
   * @opt_param string pageToken A token identifying a page of results the server
   * should return.
   * @return ListVolumesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVolumes($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListVolumesResponse::class);
  }
  /**
   * Updates the parameters of a single Volume. (volumes.patch)
   *
   * @param string $name Identifier. Name of the volume
   * @param Volume $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Field mask is used to specify the
   * fields to be overwritten in the Volume resource by the update. The fields
   * specified in the update_mask are relative to the resource, not the full
   * request. A field will be overwritten if it is in the mask. If the user does
   * not provide a mask then all fields will be overwritten.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Volume $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Restore files from a backup to a volume. (volumes.restore)
   *
   * @param string $name Required. The volume resource name, in the format
   * `projects/{project_id}/locations/{location}/volumes/{volume_id}`
   * @param RestoreBackupFilesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreBackupFilesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
  /**
   * Revert an existing volume to a specified snapshot. Warning! This operation
   * will permanently revert all changes made after the snapshot was created.
   * (volumes.revert)
   *
   * @param string $name Required. The resource name of the volume, in the format
   * of projects/{project_id}/locations/{location}/volumes/{volume_id}.
   * @param RevertVolumeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function revert($name, RevertVolumeRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('revert', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVolumes::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsVolumes');
