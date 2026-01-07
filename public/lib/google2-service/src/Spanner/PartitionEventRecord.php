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

class PartitionEventRecord extends \Google\Collection
{
  protected $collection_key = 'moveOutEvents';
  /**
   * Indicates the commit timestamp at which the key range change occurred.
   * DataChangeRecord.commit_timestamps, PartitionStartRecord.start_timestamps,
   * PartitionEventRecord.commit_timestamps, and
   * PartitionEndRecord.end_timestamps can have the same value in the same
   * partition.
   *
   * @var string
   */
  public $commitTimestamp;
  protected $moveInEventsType = MoveInEvent::class;
  protected $moveInEventsDataType = 'array';
  protected $moveOutEventsType = MoveOutEvent::class;
  protected $moveOutEventsDataType = 'array';
  /**
   * Unique partition identifier describing the partition this event occurred
   * on. partition_token is equal to the partition token of the change stream
   * partition currently queried to return this PartitionEventRecord.
   *
   * @var string
   */
  public $partitionToken;
  /**
   * Record sequence numbers are unique and monotonically increasing (but not
   * necessarily contiguous) for a specific timestamp across record types in the
   * same partition. To guarantee ordered processing, the reader should process
   * records (of potentially different types) in record_sequence order for a
   * specific timestamp in the same partition.
   *
   * @var string
   */
  public $recordSequence;

  /**
   * Indicates the commit timestamp at which the key range change occurred.
   * DataChangeRecord.commit_timestamps, PartitionStartRecord.start_timestamps,
   * PartitionEventRecord.commit_timestamps, and
   * PartitionEndRecord.end_timestamps can have the same value in the same
   * partition.
   *
   * @param string $commitTimestamp
   */
  public function setCommitTimestamp($commitTimestamp)
  {
    $this->commitTimestamp = $commitTimestamp;
  }
  /**
   * @return string
   */
  public function getCommitTimestamp()
  {
    return $this->commitTimestamp;
  }
  /**
   * Set when one or more key ranges are moved into the change stream partition
   * identified by partition_token. Example: Two key ranges are moved into
   * partition (P1) from partition (P2) and partition (P3) in a single
   * transaction at timestamp T. The PartitionEventRecord returned in P1 will
   * reflect the move as: PartitionEventRecord { commit_timestamp: T
   * partition_token: "P1" move_in_events { source_partition_token: "P2" }
   * move_in_events { source_partition_token: "P3" } } The PartitionEventRecord
   * returned in P2 will reflect the move as: PartitionEventRecord {
   * commit_timestamp: T partition_token: "P2" move_out_events {
   * destination_partition_token: "P1" } } The PartitionEventRecord returned in
   * P3 will reflect the move as: PartitionEventRecord { commit_timestamp: T
   * partition_token: "P3" move_out_events { destination_partition_token: "P1" }
   * }
   *
   * @param MoveInEvent[] $moveInEvents
   */
  public function setMoveInEvents($moveInEvents)
  {
    $this->moveInEvents = $moveInEvents;
  }
  /**
   * @return MoveInEvent[]
   */
  public function getMoveInEvents()
  {
    return $this->moveInEvents;
  }
  /**
   * Set when one or more key ranges are moved out of the change stream
   * partition identified by partition_token. Example: Two key ranges are moved
   * out of partition (P1) to partition (P2) and partition (P3) in a single
   * transaction at timestamp T. The PartitionEventRecord returned in P1 will
   * reflect the move as: PartitionEventRecord { commit_timestamp: T
   * partition_token: "P1" move_out_events { destination_partition_token: "P2" }
   * move_out_events { destination_partition_token: "P3" } } The
   * PartitionEventRecord returned in P2 will reflect the move as:
   * PartitionEventRecord { commit_timestamp: T partition_token: "P2"
   * move_in_events { source_partition_token: "P1" } } The PartitionEventRecord
   * returned in P3 will reflect the move as: PartitionEventRecord {
   * commit_timestamp: T partition_token: "P3" move_in_events {
   * source_partition_token: "P1" } }
   *
   * @param MoveOutEvent[] $moveOutEvents
   */
  public function setMoveOutEvents($moveOutEvents)
  {
    $this->moveOutEvents = $moveOutEvents;
  }
  /**
   * @return MoveOutEvent[]
   */
  public function getMoveOutEvents()
  {
    return $this->moveOutEvents;
  }
  /**
   * Unique partition identifier describing the partition this event occurred
   * on. partition_token is equal to the partition token of the change stream
   * partition currently queried to return this PartitionEventRecord.
   *
   * @param string $partitionToken
   */
  public function setPartitionToken($partitionToken)
  {
    $this->partitionToken = $partitionToken;
  }
  /**
   * @return string
   */
  public function getPartitionToken()
  {
    return $this->partitionToken;
  }
  /**
   * Record sequence numbers are unique and monotonically increasing (but not
   * necessarily contiguous) for a specific timestamp across record types in the
   * same partition. To guarantee ordered processing, the reader should process
   * records (of potentially different types) in record_sequence order for a
   * specific timestamp in the same partition.
   *
   * @param string $recordSequence
   */
  public function setRecordSequence($recordSequence)
  {
    $this->recordSequence = $recordSequence;
  }
  /**
   * @return string
   */
  public function getRecordSequence()
  {
    return $this->recordSequence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionEventRecord::class, 'Google_Service_Spanner_PartitionEventRecord');
