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

class PartitionStartRecord extends \Google\Collection
{
  protected $collection_key = 'partitionTokens';
  /**
   * Unique partition identifiers to be used in queries.
   *
   * @var string[]
   */
  public $partitionTokens;
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
   * Start timestamp at which the partitions should be queried to return change
   * stream records with timestamps >= start_timestamp.
   * DataChangeRecord.commit_timestamps, PartitionStartRecord.start_timestamps,
   * PartitionEventRecord.commit_timestamps, and
   * PartitionEndRecord.end_timestamps can have the same value in the same
   * partition.
   *
   * @var string
   */
  public $startTimestamp;

  /**
   * Unique partition identifiers to be used in queries.
   *
   * @param string[] $partitionTokens
   */
  public function setPartitionTokens($partitionTokens)
  {
    $this->partitionTokens = $partitionTokens;
  }
  /**
   * @return string[]
   */
  public function getPartitionTokens()
  {
    return $this->partitionTokens;
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
  /**
   * Start timestamp at which the partitions should be queried to return change
   * stream records with timestamps >= start_timestamp.
   * DataChangeRecord.commit_timestamps, PartitionStartRecord.start_timestamps,
   * PartitionEventRecord.commit_timestamps, and
   * PartitionEndRecord.end_timestamps can have the same value in the same
   * partition.
   *
   * @param string $startTimestamp
   */
  public function setStartTimestamp($startTimestamp)
  {
    $this->startTimestamp = $startTimestamp;
  }
  /**
   * @return string
   */
  public function getStartTimestamp()
  {
    return $this->startTimestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionStartRecord::class, 'Google_Service_Spanner_PartitionStartRecord');
