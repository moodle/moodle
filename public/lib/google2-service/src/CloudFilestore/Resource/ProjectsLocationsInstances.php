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

namespace Google\Service\CloudFilestore\Resource;

use Google\Service\CloudFilestore\Instance;
use Google\Service\CloudFilestore\ListInstancesResponse;
use Google\Service\CloudFilestore\Operation;
use Google\Service\CloudFilestore\PauseReplicaRequest;
use Google\Service\CloudFilestore\PromoteReplicaRequest;
use Google\Service\CloudFilestore\RestoreInstanceRequest;
use Google\Service\CloudFilestore\ResumeReplicaRequest;
use Google\Service\CloudFilestore\RevertInstanceRequest;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $fileService = new Google\Service\CloudFilestore(...);
 *   $instances = $fileService->projects_locations_instances;
 *  </code>
 */
class ProjectsLocationsInstances extends \Google\Service\Resource
{
  /**
   * Creates an instance. When creating from a backup, the capacity of the new
   * instance needs to be equal to or larger than the capacity of the backup (and
   * also equal to or larger than the minimum capacity of the tier).
   * (instances.create)
   *
   * @param string $parent Required. The instance's project and location, in the
   * format `projects/{project_id}/locations/{location}`. In Filestore, locations
   * map to Google Cloud zones, for example **us-west1-b**.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instanceId Required. The name of the instance to create.
   * The name must be unique for the specified project and location.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Instance $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an instance. (instances.delete)
   *
   * @param string $name Required. The instance resource name, in the format
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool force If set to true, all snapshots of the instance will also
   * be deleted. (Otherwise, the request will only work if the instance has no
   * snapshots.)
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
   * Gets the details of a specific instance. (instances.get)
   *
   * @param string $name Required. The instance resource name, in the format
   * `projects/{project_id}/locations/{location}/instances/{instance_id}`.
   * @param array $optParams Optional parameters.
   * @return Instance
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Instance::class);
  }
  /**
   * Lists all instances in a project for either a specified location or for all
   * locations. (instances.listProjectsLocationsInstances)
   *
   * @param string $parent Required. The project and location for which to
   * retrieve instance information, in the format
   * `projects/{project_id}/locations/{location}`. In Cloud Filestore, locations
   * map to Google Cloud zones, for example **us-west1-b**. To retrieve instance
   * information for all locations, use "-" for the `{location}` value.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter List filter.
   * @opt_param string orderBy Sort results. Supported values are "name", "name
   * desc" or "" (unsorted).
   * @opt_param int pageSize The maximum number of items to return.
   * @opt_param string pageToken The next_page_token value to use if there are
   * additional results to retrieve for this list request.
   * @return ListInstancesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsInstances($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancesResponse::class);
  }
  /**
   * Updates the settings of a specific instance. (instances.patch)
   *
   * @param string $name Output only. The resource name of the instance, in the
   * format `projects/{project}/locations/{location}/instances/{instance}`.
   * @param Instance $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Mask of fields to update. At least one path must
   * be supplied in this field. The elements of the repeated paths field may only
   * include these fields: * "description" * "file_shares" * "labels" *
   * "performance_config" * "deletion_protection_enabled" *
   * "deletion_protection_reason"
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Instance $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Pause the standby instance (replica). WARNING: This operation makes the
   * standby instance's NFS filesystem writable. Any data written to the standby
   * instance while paused will be lost when the replica is resumed or promoted.
   * (instances.pauseReplica)
   *
   * @param string $name Required. The resource name of the instance, in the
   * format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * @param PauseReplicaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function pauseReplica($name, PauseReplicaRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pauseReplica', [$params], Operation::class);
  }
  /**
   * Promote the standby instance (replica). (instances.promoteReplica)
   *
   * @param string $name Required. The resource name of the instance, in the
   * format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * @param PromoteReplicaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function promoteReplica($name, PromoteReplicaRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('promoteReplica', [$params], Operation::class);
  }
  /**
   * Restores an existing instance's file share from a backup. The capacity of the
   * instance needs to be equal to or larger than the capacity of the backup (and
   * also equal to or larger than the minimum capacity of the tier).
   * (instances.restore)
   *
   * @param string $name Required. The resource name of the instance, in the
   * format
   * `projects/{project_number}/locations/{location_id}/instances/{instance_id}`.
   * @param RestoreInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restore($name, RestoreInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restore', [$params], Operation::class);
  }
  /**
   * Resume the standby instance (replica). WARNING: Any data written to the
   * standby instance while paused will be lost when the replica is resumed.
   * (instances.resumeReplica)
   *
   * @param string $name Required. The resource name of the instance, in the
   * format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * @param ResumeReplicaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resumeReplica($name, ResumeReplicaRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resumeReplica', [$params], Operation::class);
  }
  /**
   * Revert an existing instance's file system to a specified snapshot.
   * (instances.revert)
   *
   * @param string $name Required. The resource name of the instance, in the
   * format
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`.
   * @param RevertInstanceRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function revert($name, RevertInstanceRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('revert', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsInstances::class, 'Google_Service_CloudFilestore_Resource_ProjectsLocationsInstances');
