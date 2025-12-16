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

namespace Google\Service\NetAppFiles;

class Snapshot extends \Google\Model
{
  /**
   * Unspecified Snapshot State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Snapshot State is Ready
   */
  public const STATE_READY = 'READY';
  /**
   * Snapshot State is Creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Snapshot State is Deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Snapshot State is Updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Snapshot State is Disabled
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Snapshot State is Error
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Output only. The time when the snapshot was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the snapshot with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the snapshot. Format: `projects/{project_i
   * d}/locations/{location}/volumes/{volume_id}/snapshots/{snapshot_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The snapshot state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the storage pool
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Output only. Current storage usage for the snapshot in bytes.
   *
   * @var 
   */
  public $usedBytes;

  /**
   * Output only. The time when the snapshot was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A description of the snapshot with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Resource labels to represent user provided metadata.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Identifier. The resource name of the snapshot. Format: `projects/{project_i
   * d}/locations/{location}/volumes/{volume_id}/snapshots/{snapshot_id}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The snapshot state.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, DELETING, UPDATING,
   * DISABLED, ERROR
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. State details of the storage pool
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  public function setUsedBytes($usedBytes)
  {
    $this->usedBytes = $usedBytes;
  }
  public function getUsedBytes()
  {
    return $this->usedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Snapshot::class, 'Google_Service_NetAppFiles_Snapshot');
