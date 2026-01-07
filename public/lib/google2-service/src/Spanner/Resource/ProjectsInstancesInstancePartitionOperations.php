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

use Google\Service\Spanner\ListInstancePartitionOperationsResponse;

/**
 * The "instancePartitionOperations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $spannerService = new Google\Service\Spanner(...);
 *   $instancePartitionOperations = $spannerService->projects_instances_instancePartitionOperations;
 *  </code>
 */
class ProjectsInstancesInstancePartitionOperations extends \Google\Service\Resource
{
  /**
   * Lists instance partition long-running operations in the given instance. An
   * instance partition operation has a name of the form
   * `projects//instances//instancePartitions//operations/`. The long-running
   * operation metadata field type `metadata.type_url` describes the type of the
   * metadata. Operations returned include those that have
   * completed/failed/canceled within the last 7 days, and pending operations.
   * Operations returned are ordered by `operation.metadata.value.start_time` in
   * descending order starting from the most recently started operation.
   * Authorization requires `spanner.instancePartitionOperations.list` permission
   * on the resource parent. (instancePartitionOperations.listProjectsInstancesIns
   * tancePartitionOperations)
   *
   * @param string $parent Required. The parent instance of the instance partition
   * operations. Values are of the form `projects//instances/`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression that filters the list of
   * returned operations. A filter expression consists of a field name, a
   * comparison operator, and a value for filtering. The value must be a string, a
   * number, or a boolean. The comparison operator must be one of: `<`, `>`, `<=`,
   * `>=`, `!=`, `=`, or `:`. Colon `:` is the contains operator. Filter rules are
   * not case sensitive. The following fields in the Operation are eligible for
   * filtering: * `name` - The name of the long-running operation * `done` - False
   * if the operation is in progress, else true. * `metadata.@type` - the type of
   * metadata. For example, the type string for CreateInstancePartitionMetadata is
   * `type.googleapis.com/google.spanner.admin.instance.v1.CreateInstancePartition
   * Metadata`. * `metadata.` - any field in metadata.value. `metadata.@type` must
   * be specified first, if filtering on metadata fields. * `error` - Error
   * associated with the long-running operation. * `response.@type` - the type of
   * response. * `response.` - any field in response.value. You can combine
   * multiple expressions by enclosing each expression in parentheses. By default,
   * expressions are combined with AND logic. However, you can specify AND, OR,
   * and NOT logic explicitly. Here are a few examples: * `done:true` - The
   * operation is complete. * `(metadata.@type=` \ `type.googleapis.com/google.spa
   * nner.admin.instance.v1.CreateInstancePartitionMetadata) AND` \
   * `(metadata.instance_partition.name:custom-instance-partition) AND` \
   * `(metadata.start_time < \"2021-03-28T14:50:00Z\") AND` \ `(error:*)` - Return
   * operations where: * The operation's metadata type is
   * CreateInstancePartitionMetadata. * The instance partition name contains
   * "custom-instance-partition". * The operation started before
   * 2021-03-28T14:50:00Z. * The operation resulted in an error.
   * @opt_param string instancePartitionDeadline Optional. Deadline used while
   * retrieving metadata for instance partition operations. Instance partitions
   * whose operation metadata cannot be retrieved within this deadline will be
   * added to unreachable_instance_partitions in
   * ListInstancePartitionOperationsResponse.
   * @opt_param int pageSize Optional. Number of operations to be returned in the
   * response. If 0 or less, defaults to the server's maximum allowed page size.
   * @opt_param string pageToken Optional. If non-empty, `page_token` should
   * contain a next_page_token from a previous
   * ListInstancePartitionOperationsResponse to the same `parent` and with the
   * same `filter`.
   * @return ListInstancePartitionOperationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsInstancesInstancePartitionOperations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListInstancePartitionOperationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsInstancesInstancePartitionOperations::class, 'Google_Service_Spanner_Resource_ProjectsInstancesInstancePartitionOperations');
