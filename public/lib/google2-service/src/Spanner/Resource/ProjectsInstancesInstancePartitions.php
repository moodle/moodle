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

namespace Google\Service\Spanner\Resource;

use Google\Service\Spanner\CreateInstancePartitionRequest;
use Google\Service\Spanner\InstancePartition;
use Google\Service\Spanner\ListInstancePartitionsResponse;
use Google\Service\Spanner\Operation;
use Google\Service\Spanner\SpannerEmpty;
use Google\Service\Spanner\UpdateInstancePartitionRequest;

/**
 * The "instancePartitions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $spannerService = new Google\Service\Spanner(...);
 *   $instancePartitions = $spannerService->projects_instances_instancePartitions;
 *  </code>
 */
class ProjectsInstancesInstancePartitions extends \Google\Service\Resource
{
  /**
   * Creates an instance partition and begins preparing it to be used. The
   * returned long-running operation can be used to track the progress of
   * preparing the new instance partition. The instance partition name is assigned
   * by the caller. If the named instance partition already exists,
   * `CreateInstancePartition` returns `ALREADY_EXISTS`. Immediately upon
   * completion of this request: * The instance partition is readable via the API,
   * with all requested attributes but no allocated resources. Its state is
   * `CREATING`. Until completion of the returned operation: * Cancelling the
   * operation renders the instance partition immediately unreadable via the API.
   * * The instance partition can be deleted. * All other attempts to modify the
   * instance partition are rejected. Upon completion of the returned operation: *
   * Billing for all successfully-allocated resources begins (some types may have
   * lower than the requested levels). * Databases can start using this instance
   * partition. * The instance partition's allocated resource levels are readable
   * via the API. * The instance partition's state becomes `READY`. The returned
   * long-running operation will have a name of the format `/operations/` and can
   * be used to track creation of the instance partition. The metadata field type
   * is CreateInstancePartitionMetadata. The response field type is
   * InstancePartition, if successful. (instancePartitions.create)
   *
   * @param string $parent Required. The name of the instance in which to create
   * the instance partition. Values are of the form `projects//instances/`.
   * @param CreateInstancePartitionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, CreateInstancePartitionRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes an existing instance partition. Requires that the instance partition
   * is not used by any database or backup and is not the default instance
   * partition of an instance. Authorization requires
   * `spanner.instancePartitions.delete` permission on the resource name.
   * (instancePartitions.delete)
   *
   * @param string $name Required. The name of the instance partition to be
   * deleted. Values are of the form `projects/{project}/instances/{instance}/inst
   * ancePartitions/{instance_partition}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string etag Optional. If not empty, the API only deletes the
   * instance partition when the etag provided matches the current status of the
   * requested instance partition. Otherwise, deletes the instance partition
   * without checking the current status of the requested instance partition.
   * @return SpannerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], SpannerEmpty::class);
  }
  /**
   * Gets information about a particular instance partition.
   * (instancePartitions.get)
   *
   * @param string $name Required. The name of the requested instance partition.
   * Values are of the form `projects/{project}/instances/{instance}/instanceParti
   * tions/{instance_partition}`.
   * @param array $optParams Optional parameters.
   * @return InstancePartition
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], InstancePartition::class);
  }
  /**
   * Lists all instance partitions for the given instance.
   * (instancePartitions.listProjectsInstancesInstancePartitions)
   *
   * @param string $parent Required. The instance whose instance partitions should
   * be listed. Values are of the form `projects//instances/`. Use `{instance} =
   * '-'` to list instance partitions for all Instances in a project, e.g.,
   * `projects/myproject/instances/-`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string instancePartitionDeadline Optional. Deadline used while
   * retrieving metadata for instance partitions. Instance partitions whose
   * metadata cannot be retrieved within this deadline will be added to
   * unreachable in ListInstancePartitionsResponse.
   * @opt_param int pageSize Number of instance partitions to be returned in the
   * response. If 0 or less, defaults to the server's maximum allowed page size.
   * @opt_param string pageToken If non-empty, `page_token` should contain a
   * next_page_token from a previous ListInstancePartitionsResponse.
   * @return ListInstancePartitionsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesInstancePartitions($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancePartitionsResponse::class);
  }
  /**
   * Updates an instance partition, and begins allocating or releasing resources
   * as requested. The returned long-running operation can be used to track the
   * progress of updating the instance partition. If the named instance partition
   * does not exist, returns `NOT_FOUND`. Immediately upon completion of this
   * request: * For resource types for which a decrease in the instance
   * partition's allocation has been requested, billing is based on the newly-
   * requested level. Until completion of the returned operation: * Cancelling the
   * operation sets its metadata's cancel_time, and begins restoring resources to
   * their pre-request values. The operation is guaranteed to succeed at undoing
   * all resource changes, after which point it terminates with a `CANCELLED`
   * status. * All other attempts to modify the instance partition are rejected. *
   * Reading the instance partition via the API continues to give the pre-request
   * resource levels. Upon completion of the returned operation: * Billing begins
   * for all successfully-allocated resources (some types may have lower than the
   * requested levels). * All newly-reserved resources are available for serving
   * the instance partition's tables. * The instance partition's new resource
   * levels are readable via the API. The returned long-running operation will
   * have a name of the format `/operations/` and can be used to track the
   * instance partition modification. The metadata field type is
   * UpdateInstancePartitionMetadata. The response field type is
   * InstancePartition, if successful. Authorization requires
   * `spanner.instancePartitions.update` permission on the resource name.
   * (instancePartitions.patch)
   *
   * @param string $name Required. A unique identifier for the instance partition.
   * Values are of the form
   * `projects//instances//instancePartitions/a-z*[a-z0-9]`. The final segment of
   * the name must be between 2 and 64 characters in length. An instance
   * partition's name cannot be changed after the instance partition is created.
   * @param UpdateInstancePartitionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, UpdateInstancePartitionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsInstancesInstancePartitions::class, 'Google_Service_Spanner_Resource_ProjectsInstancesInstancePartitions');
