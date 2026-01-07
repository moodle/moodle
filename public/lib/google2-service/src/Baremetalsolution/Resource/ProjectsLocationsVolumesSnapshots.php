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

namespace Google\Service\Baremetalsolution\Resource;

use Google\Service\Baremetalsolution\BaremetalsolutionEmpty;
use Google\Service\Baremetalsolution\ListVolumeSnapshotsResponse;
use Google\Service\Baremetalsolution\Operation;
use Google\Service\Baremetalsolution\RestoreVolumeSnapshotRequest;
use Google\Service\Baremetalsolution\VolumeSnapshot;

/**
 * The "snapshots" collection of methods.
 * Typical usage is:
 *  <code>
 *   $baremetalsolutionService = new Google\Service\Baremetalsolution(...);
 *   $snapshots = $baremetalsolutionService->projects_locations_volumes_snapshots;
 *  </code>
 */
class ProjectsLocationsVolumesSnapshots extends \Google\Service\Resource
{
  /**
   * Takes a snapshot of a boot volume. Returns INVALID_ARGUMENT if called for a
   * non-boot volume. (snapshots.create)
   *
   * @param string $parent Required. The volume to snapshot.
   * @param VolumeSnapshot $postBody
   * @param array $optParams Optional parameters.
   * @return VolumeSnapshot
   * @throws \Google\Service\Exception
   */
  public function create($parent, VolumeSnapshot $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], VolumeSnapshot::class);
  }
  /**
   * Deletes a volume snapshot. Returns INVALID_ARGUMENT if called for a non-boot
   * volume. (snapshots.delete)
   *
   * @param string $name Required. The name of the snapshot to delete.
   * @param array $optParams Optional parameters.
   * @return BaremetalsolutionEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], BaremetalsolutionEmpty::class);
  }
  /**
   * Returns the specified snapshot resource. Returns INVALID_ARGUMENT if called
   * for a non-boot volume. (snapshots.get)
   *
   * @param string $name Required. The name of the snapshot.
   * @param array $optParams Optional parameters.
   * @return VolumeSnapshot
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], VolumeSnapshot::class);
  }
  /**
   * Retrieves the list of snapshots for the specified volume. Returns a response
   * with an empty list of snapshots if called for a non-boot volume.
   * (snapshots.listProjectsLocationsVolumesSnapshots)
   *
   * @param string $parent Required. Parent value for ListVolumesRequest.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Requested page size. The server might return fewer
   * items than requested. If unspecified, server will pick an appropriate
   * default.
   * @opt_param string pageToken A token identifying a page of results from the
   * server.
   * @return ListVolumeSnapshotsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVolumesSnapshots($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListVolumeSnapshotsResponse::class);
  }
  /**
   * Uses the specified snapshot to restore its parent volume. Returns
   * INVALID_ARGUMENT if called for a non-boot volume.
   * (snapshots.restoreVolumeSnapshot)
   *
   * @param string $volumeSnapshot Required. Name of the snapshot which will be
   * used to restore its parent volume.
   * @param RestoreVolumeSnapshotRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restoreVolumeSnapshot($volumeSnapshot, RestoreVolumeSnapshotRequest $postBody, $optParams = [])
  {
    $params = ['volumeSnapshot' => $volumeSnapshot, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restoreVolumeSnapshot', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVolumesSnapshots::class, 'Google_Service_Baremetalsolution_Resource_ProjectsLocationsVolumesSnapshots');
