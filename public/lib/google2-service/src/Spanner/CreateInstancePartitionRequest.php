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

namespace Google\Service\Spanner;

class CreateInstancePartitionRequest extends \Google\Model
{
  protected $instancePartitionType = InstancePartition::class;
  protected $instancePartitionDataType = '';
  /**
   * Required. The ID of the instance partition to create. Valid identifiers are
   * of the form `a-z*[a-z0-9]` and must be between 2 and 64 characters in
   * length.
   *
   * @var string
   */
  public $instancePartitionId;

  /**
   * Required. The instance partition to create. The instance_partition.name may
   * be omitted, but if specified must be `/instancePartitions/`.
   *
   * @param InstancePartition $instancePartition
   */
  public function setInstancePartition(InstancePartition $instancePartition)
  {
    $this->instancePartition = $instancePartition;
  }
  /**
   * @return InstancePartition
   */
  public function getInstancePartition()
  {
    return $this->instancePartition;
  }
  /**
   * Required. The ID of the instance partition to create. Valid identifiers are
   * of the form `a-z*[a-z0-9]` and must be between 2 and 64 characters in
   * length.
   *
   * @param string $instancePartitionId
   */
  public function setInstancePartitionId($instancePartitionId)
  {
    $this->instancePartitionId = $instancePartitionId;
  }
  /**
   * @return string
   */
  public function getInstancePartitionId()
  {
    return $this->instancePartitionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateInstancePartitionRequest::class, 'Google_Service_Spanner_CreateInstancePartitionRequest');
