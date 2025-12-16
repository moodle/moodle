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

class CreateInstancePartitionMetadata extends \Google\Model
{
  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is guaranteed to succeed) and
   * cannot be cancelled again.
   *
   * @var string
   */
  public $cancelTime;
  /**
   * The time at which this operation failed or was completed successfully.
   *
   * @var string
   */
  public $endTime;
  protected $instancePartitionType = InstancePartition::class;
  protected $instancePartitionDataType = '';
  /**
   * The time at which the CreateInstancePartition request was received.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time at which this operation was cancelled. If set, this operation is
   * in the process of undoing itself (which is guaranteed to succeed) and
   * cannot be cancelled again.
   *
   * @param string $cancelTime
   */
  public function setCancelTime($cancelTime)
  {
    $this->cancelTime = $cancelTime;
  }
  /**
   * @return string
   */
  public function getCancelTime()
  {
    return $this->cancelTime;
  }
  /**
   * The time at which this operation failed or was completed successfully.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The instance partition being created.
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
   * The time at which the CreateInstancePartition request was received.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateInstancePartitionMetadata::class, 'Google_Service_Spanner_CreateInstancePartitionMetadata');
