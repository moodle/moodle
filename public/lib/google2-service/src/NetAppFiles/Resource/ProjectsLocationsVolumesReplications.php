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

use Google\Service\NetAppFiles\EstablishPeeringRequest;
use Google\Service\NetAppFiles\ListReplicationsResponse;
use Google\Service\NetAppFiles\Operation;
use Google\Service\NetAppFiles\Replication;
use Google\Service\NetAppFiles\ResumeReplicationRequest;
use Google\Service\NetAppFiles\ReverseReplicationDirectionRequest;
use Google\Service\NetAppFiles\StopReplicationRequest;
use Google\Service\NetAppFiles\SyncReplicationRequest;

/**
 * The "replications" collection of methods.
 * Typical usage is:
 *  <code>
 *   $netappService = new Google\Service\NetAppFiles(...);
 *   $replications = $netappService->projects_locations_volumes_replications;
 *  </code>
 */
class ProjectsLocationsVolumesReplications extends \Google\Service\Resource
{
  /**
   * Create a new replication for a volume. (replications.create)
   *
   * @param string $parent Required. The NetApp volume to create the replications
   * of, in the format
   * `projects/{project_id}/locations/{location}/volumes/{volume_id}`
   * @param Replication $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string replicationId Required. ID of the replication to create.
   * Must be unique within the parent resource. Must contain only letters, numbers
   * and hyphen, with the first character a letter, the last a letter or a number,
   * and a 63 character maximum.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Replication $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a replication. (replications.delete)
   *
   * @param string $name Required. The replication resource name, in the format
   * `projects/locations/volumes/replications/{replication_id}`
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
   * Establish replication peering. (replications.establishPeering)
   *
   * @param string $name Required. The resource name of the replication, in the
   * format of projects/{project_id}/locations/{location}/volumes/{volume_id}/repl
   * ications/{replication_id}.
   * @param EstablishPeeringRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function establishPeering($name, EstablishPeeringRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('establishPeering', [$params], Operation::class);
  }
  /**
   * Describe a replication for a volume. (replications.get)
   *
   * @param string $name Required. The replication resource name, in the format `p
   * rojects/{project_id}/locations/{location}/volumes/{volume_id}/replications/{r
   * eplication_id}`
   * @param array $optParams Optional parameters.
   * @return Replication
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Replication::class);
  }
  /**
   * Returns descriptions of all replications for a volume.
   * (replications.listProjectsLocationsVolumesReplications)
   *
   * @param string $parent Required. The volume for which to retrieve replication
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
   * @return ListReplicationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsVolumesReplications($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReplicationsResponse::class);
  }
  /**
   * Updates the settings of a specific replication. (replications.patch)
   *
   * @param string $name Identifier. The resource name of the Replication. Format:
   * `projects/{project_id}/locations/{location}/volumes/{volume_id}/replications/
   * {replication_id}`.
   * @param Replication $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. Mask of fields to update. At least one
   * path must be supplied in this field.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, Replication $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Resume Cross Region Replication. (replications.resume)
   *
   * @param string $name Required. The resource name of the replication, in the
   * format of projects/{project_id}/locations/{location}/volumes/{volume_id}/repl
   * ications/{replication_id}.
   * @param ResumeReplicationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resume($name, ResumeReplicationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], Operation::class);
  }
  /**
   * Reverses direction of replication. Source becomes destination and destination
   * becomes source. (replications.reverseDirection)
   *
   * @param string $name Required. The resource name of the replication, in the
   * format of projects/{project_id}/locations/{location}/volumes/{volume_id}/repl
   * ications/{replication_id}.
   * @param ReverseReplicationDirectionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reverseDirection($name, ReverseReplicationDirectionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reverseDirection', [$params], Operation::class);
  }
  /**
   * Stop Cross Region Replication. (replications.stop)
   *
   * @param string $name Required. The resource name of the replication, in the
   * format of projects/{project_id}/locations/{location}/volumes/{volume_id}/repl
   * ications/{replication_id}.
   * @param StopReplicationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function stop($name, StopReplicationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('stop', [$params], Operation::class);
  }
  /**
   * Syncs the replication. This will invoke one time volume data transfer from
   * source to destination. (replications.sync)
   *
   * @param string $name Required. The resource name of the replication, in the
   * format of projects/{project_id}/locations/{location}/volumes/{volume_id}/repl
   * ications/{replication_id}.
   * @param SyncReplicationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function sync($name, SyncReplicationRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('sync', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsVolumesReplications::class, 'Google_Service_NetAppFiles_Resource_ProjectsLocationsVolumesReplications');
