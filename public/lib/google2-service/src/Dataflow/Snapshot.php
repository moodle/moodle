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

namespace Google\Service\Dataflow;

class Snapshot extends \Google\Collection
{
  /**
   * Unknown state.
   */
  public const STATE_UNKNOWN_SNAPSHOT_STATE = 'UNKNOWN_SNAPSHOT_STATE';
  /**
   * Snapshot intent to create has been persisted, snapshotting of state has not
   * yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Snapshotting is being performed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Snapshot has been created and is ready to be used.
   */
  public const STATE_READY = 'READY';
  /**
   * Snapshot failed to be created.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Snapshot has been deleted.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'pubsubMetadata';
  /**
   * The time this snapshot was created.
   *
   * @var string
   */
  public $creationTime;
  /**
   * User specified description of the snapshot. Maybe empty.
   *
   * @var string
   */
  public $description;
  /**
   * The disk byte size of the snapshot. Only available for snapshots in READY
   * state.
   *
   * @var string
   */
  public $diskSizeBytes;
  /**
   * The unique ID of this snapshot.
   *
   * @var string
   */
  public $id;
  /**
   * The project this snapshot belongs to.
   *
   * @var string
   */
  public $projectId;
  protected $pubsubMetadataType = PubsubSnapshotMetadata::class;
  protected $pubsubMetadataDataType = 'array';
  /**
   * Cloud region where this snapshot lives in, e.g., "us-central1".
   *
   * @var string
   */
  public $region;
  /**
   * The job this snapshot was created from.
   *
   * @var string
   */
  public $sourceJobId;
  /**
   * State of the snapshot.
   *
   * @var string
   */
  public $state;
  /**
   * The time after which this snapshot will be automatically deleted.
   *
   * @var string
   */
  public $ttl;

  /**
   * The time this snapshot was created.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * User specified description of the snapshot. Maybe empty.
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
   * The disk byte size of the snapshot. Only available for snapshots in READY
   * state.
   *
   * @param string $diskSizeBytes
   */
  public function setDiskSizeBytes($diskSizeBytes)
  {
    $this->diskSizeBytes = $diskSizeBytes;
  }
  /**
   * @return string
   */
  public function getDiskSizeBytes()
  {
    return $this->diskSizeBytes;
  }
  /**
   * The unique ID of this snapshot.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The project this snapshot belongs to.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Pub/Sub snapshot metadata.
   *
   * @param PubsubSnapshotMetadata[] $pubsubMetadata
   */
  public function setPubsubMetadata($pubsubMetadata)
  {
    $this->pubsubMetadata = $pubsubMetadata;
  }
  /**
   * @return PubsubSnapshotMetadata[]
   */
  public function getPubsubMetadata()
  {
    return $this->pubsubMetadata;
  }
  /**
   * Cloud region where this snapshot lives in, e.g., "us-central1".
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * The job this snapshot was created from.
   *
   * @param string $sourceJobId
   */
  public function setSourceJobId($sourceJobId)
  {
    $this->sourceJobId = $sourceJobId;
  }
  /**
   * @return string
   */
  public function getSourceJobId()
  {
    return $this->sourceJobId;
  }
  /**
   * State of the snapshot.
   *
   * Accepted values: UNKNOWN_SNAPSHOT_STATE, PENDING, RUNNING, READY, FAILED,
   * DELETED
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
   * The time after which this snapshot will be automatically deleted.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Snapshot::class, 'Google_Service_Dataflow_Snapshot');
