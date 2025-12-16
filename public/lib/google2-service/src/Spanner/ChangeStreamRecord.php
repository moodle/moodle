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

class ChangeStreamRecord extends \Google\Model
{
  protected $dataChangeRecordType = DataChangeRecord::class;
  protected $dataChangeRecordDataType = '';
  protected $heartbeatRecordType = HeartbeatRecord::class;
  protected $heartbeatRecordDataType = '';
  protected $partitionEndRecordType = PartitionEndRecord::class;
  protected $partitionEndRecordDataType = '';
  protected $partitionEventRecordType = PartitionEventRecord::class;
  protected $partitionEventRecordDataType = '';
  protected $partitionStartRecordType = PartitionStartRecord::class;
  protected $partitionStartRecordDataType = '';

  /**
   * Data change record describing a data change for a change stream partition.
   *
   * @param DataChangeRecord $dataChangeRecord
   */
  public function setDataChangeRecord(DataChangeRecord $dataChangeRecord)
  {
    $this->dataChangeRecord = $dataChangeRecord;
  }
  /**
   * @return DataChangeRecord
   */
  public function getDataChangeRecord()
  {
    return $this->dataChangeRecord;
  }
  /**
   * Heartbeat record describing a heartbeat for a change stream partition.
   *
   * @param HeartbeatRecord $heartbeatRecord
   */
  public function setHeartbeatRecord(HeartbeatRecord $heartbeatRecord)
  {
    $this->heartbeatRecord = $heartbeatRecord;
  }
  /**
   * @return HeartbeatRecord
   */
  public function getHeartbeatRecord()
  {
    return $this->heartbeatRecord;
  }
  /**
   * Partition end record describing a terminated change stream partition.
   *
   * @param PartitionEndRecord $partitionEndRecord
   */
  public function setPartitionEndRecord(PartitionEndRecord $partitionEndRecord)
  {
    $this->partitionEndRecord = $partitionEndRecord;
  }
  /**
   * @return PartitionEndRecord
   */
  public function getPartitionEndRecord()
  {
    return $this->partitionEndRecord;
  }
  /**
   * Partition event record describing key range changes for a change stream
   * partition.
   *
   * @param PartitionEventRecord $partitionEventRecord
   */
  public function setPartitionEventRecord(PartitionEventRecord $partitionEventRecord)
  {
    $this->partitionEventRecord = $partitionEventRecord;
  }
  /**
   * @return PartitionEventRecord
   */
  public function getPartitionEventRecord()
  {
    return $this->partitionEventRecord;
  }
  /**
   * Partition start record describing a new change stream partition.
   *
   * @param PartitionStartRecord $partitionStartRecord
   */
  public function setPartitionStartRecord(PartitionStartRecord $partitionStartRecord)
  {
    $this->partitionStartRecord = $partitionStartRecord;
  }
  /**
   * @return PartitionStartRecord
   */
  public function getPartitionStartRecord()
  {
    return $this->partitionStartRecord;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChangeStreamRecord::class, 'Google_Service_Spanner_ChangeStreamRecord');
