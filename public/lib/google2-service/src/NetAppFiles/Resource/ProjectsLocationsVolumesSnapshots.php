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

use Google\Service\NetAppFiles\ListSnapshotsResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\Snapshot;

/**
 * The "snapshots" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $snapshots = $netappService->projects_locations_volumes_snapshots;
 *  </code>
 */
class ProjectsLocationsVolumesSnapshots extends \Google\Service\Resource
{
  /**
   * Create a new snapshot for a volume. (snapshots.create)
   *
   * @param string $parent Required. The NetApp volume to create the snapshots of,
   * in the format
   * `projects/{project_id}/locations/{location}/volumes/{volume_id}`
   * @param Snapshot $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string snapshotId Required. ID of the snapshot to create. Must be
   * unique within the parent resource. Must contain only letters, numbers and
   * hyphen, with the first character a letter, the last a letter or a number, and
   * a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Snapshot $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a snapshot. (snapshots.delete)
   *
   * @param string $name Required. The snapshot resource name, in the format
   * `projects/locations/volumes/snapshots/{snapshot_id}`
   * @param array $optParams Optional parameters.
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
   * Describe a snapshot for a volume. (snapshots.get)
   *
   * @param string $name Required. The snapshot resource name, in the format `proj
   * ects/{project_id}/locations/{location}/volumes/{volume_id}/snapshots/{snapsho
   * t_id}`
   * @param array $optParams Optional parameters.
   * @return Snapshot
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Snapshot::class);
  }
  /**
   * Returns descriptions of all snapshots for a volume.
   * (snapshots.listProjectsLocationsVolumesSnapshots)
   *
   * @param string $parent Required. The volume for which to retrieve snapshot
   * information, in the format
   * `projects/{project_id}/locations/{location}/volumes/{volume_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter List filter.
   * @opt_param string orderBy Sort results. Supported values are "name", "name
   * desc" or "" (unsorted).
   * @opt_param int pageSize The maximum number of items to return.
   * @opt_param string pageToken The next_page_token value to use if there are
   * additional results to retrieve for this list request.
   * @return ListSnapshotsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVolumesSnapshots($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListSnapshotsResponse::class);
  }
  /**
   * Updates the settings of a specific snapshot. (snapshots.patch)
   *
   * @param string $name Identifier. The resource name of the snapshot. Format: `p
   * rojects/{project_id}/locations/{location}/volumes/{volume_id}/snapshots/{snap
   * shot_id}`.
   * @param Snapshot $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Mask of fields to update. At least one
   * path must be supplied in this field.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Snapshot $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVolumesSnapshots::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsVolumesSnapshots');
