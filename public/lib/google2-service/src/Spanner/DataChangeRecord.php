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

class DataChangeRecord extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const MOD_TYPE_MOD_TYPE_UNSPECIFIED = 'MOD_TYPE_UNSPECIFIED';
  /**
   * Indicates data was inserted.
   */
  public const MOD_TYPE_INSERT = 'INSERT';
  /**
   * Indicates existing data was updated.
   */
  public const MOD_TYPE_UPDATE = 'UPDATE';
  /**
   * Indicates existing data was deleted.
   */
  public const MOD_TYPE_DELETE = 'DELETE';
  /**
   * Not specified.
   */
  public const VALUE_CAPTURE_TYPE_VALUE_CAPTURE_TYPE_UNSPECIFIED = 'VALUE_CAPTURE_TYPE_UNSPECIFIED';
  /**
   * Records both old and new values of the modified watched columns.
   */
  public const VALUE_CAPTURE_TYPE_OLD_AND_NEW_VALUES = 'OLD_AND_NEW_VALUES';
  /**
   * Records only new values of the modified watched columns.
   */
  public const VALUE_CAPTURE_TYPE_NEW_VALUES = 'NEW_VALUES';
  /**
   * Records new values of all watched columns, including modified and
   * unmodified columns.
   */
  public const VALUE_CAPTURE_TYPE_NEW_ROW = 'NEW_ROW';
  /**
   * Records the new values of all watched columns, including modified and
   * unmodified columns. Also records the old values of the modified columns.
   */
  public const VALUE_CAPTURE_TYPE_NEW_ROW_AND_OLD_VALUES = 'NEW_ROW_AND_OLD_VALUES';
  protected $collection_key = 'mods';
  protected $columnMetadataType = ColumnMetadata::class;
  protected $columnMetadataDataType = 'array';
  /**
   * Indicates the timestamp in which the change was committed.
   * DataChangeRecord.commit_timestamps, PartitionStartRecord.start_timestamps,
   * PartitionEventRecord.commit_timestamps, and
   * PartitionEndRecord.end_timestamps can have the same value in the same
   * partition.
   *
   * @var string
   */
  public $commitTimestamp;
  /**
   * Indicates whether this is the last record for a transaction in the current
   * partition. Clients can use this field to determine when all records for a
   * transaction in the current partition have been received.
   *
   * @var bool
   */
  public $isLastRecordInTransactionInPartition;
  /**
   * Indicates whether the transaction is a system transaction. System
   * transactions include those issued by time-to-live (TTL), column backfill,
   * etc.
   *
   * @var bool
   */
  public $isSystemTransaction;
  /**
   * Describes the type of change.
   *
   * @var string
   */
  public $modType;
  protected $modsType = Mod::class;
  protected $modsDataType = 'array';
  /**
   * Indicates the number of partitions that return data change records for this
   * transaction. This value can be helpful in assembling all records associated
   * with a particular transaction.
   *
   * @var int
   */
  public $numberOfPartitionsInTransaction;
  /**
   * Indicates the number of data change records that are part of this
   * transaction across all change stream partitions. This value can be used to
   * assemble all the records associated with a particular transaction.
   *
   * @var int
   */
  public $numberOfRecordsInTransaction;
  /**
   * Record sequence numbers are unique and monotonically increasing (but not
   * necessarily contiguous) for a specific timestamp across record types in the
   * same partition. To guarantee ordered processing, the reader should process
   * records (of potentially different types) in record_sequence order for a
   * specific timestamp in the same partition. The record sequence number
   * ordering across partitions is only meaningful in the context of a specific
   * transaction. Record sequence numbers are unique across partitions for a
   * specific transaction. Sort the DataChangeRecords for the same
   * server_transaction_id by record_sequence to reconstruct the ordering of the
   * changes within the transaction.
   *
   * @var string
   */
  public $recordSequence;
  /**
   * Provides a globally unique string that represents the transaction in which
   * the change was committed. Multiple transactions can have the same commit
   * timestamp, but each transaction has a unique server_transaction_id.
   *
   * @var string
   */
  public $serverTransactionId;
  /**
   * Name of the table affected by the change.
   *
   * @var string
   */
  public $table;
  /**
   * Indicates the transaction tag associated with this transaction.
   *
   * @var string
   */
  public $transactionTag;
  /**
   * Describes the value capture type that was specified in the change stream
   * configuration when this change was captured.
   *
   * @var string
   */
  public $valueCaptureType;

  /**
   * Provides metadata describing the columns associated with the mods listed
   * below.
   *
   * @param ColumnMetadata[] $columnMetadata
   */
  public function setColumnMetadata($columnMetadata)
  {
    $this->columnMetadata = $columnMetadata;
  }
  /**
   * @return ColumnMetadata[]
   */
  public function getColumnMetadata()
  {
    return $this->columnMetadata;
  }
  /**
   * Indicates the timestamp in which the change was committed.
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
   * Indicates whether this is the last record for a transaction in the current
   * partition. Clients can use this field to determine when all records for a
   * transaction in the current partition have been received.
   *
   * @param bool $isLastRecordInTransactionInPartition
   */
  public function setIsLastRecordInTransactionInPartition($isLastRecordInTransactionInPartition)
  {
    $this->isLastRecordInTransactionInPartition = $isLastRecordInTransactionInPartition;
  }
  /**
   * @return bool
   */
  public function getIsLastRecordInTransactionInPartition()
  {
    return $this->isLastRecordInTransactionInPartition;
  }
  /**
   * Indicates whether the transaction is a system transaction. System
   * transactions include those issued by time-to-live (TTL), column backfill,
   * etc.
   *
   * @param bool $isSystemTransaction
   */
  public function setIsSystemTransaction($isSystemTransaction)
  {
    $this->isSystemTransaction = $isSystemTransaction;
  }
  /**
   * @return bool
   */
  public function getIsSystemTransaction()
  {
    return $this->isSystemTransaction;
  }
  /**
   * Describes the type of change.
   *
   * Accepted values: MOD_TYPE_UNSPECIFIED, INSERT, UPDATE, DELETE
   *
   * @param self::MOD_TYPE_* $modType
   */
  public function setModType($modType)
  {
    $this->modType = $modType;
  }
  /**
   * @return self::MOD_TYPE_*
   */
  public function getModType()
  {
    return $this->modType;
  }
  /**
   * Describes the changes that were made.
   *
   * @param Mod[] $mods
   */
  public function setMods($mods)
  {
    $this->mods = $mods;
  }
  /**
   * @return Mod[]
   */
  public function getMods()
  {
    return $this->mods;
  }
  /**
   * Indicates the number of partitions that return data change records for this
   * transaction. This value can be helpful in assembling all records associated
   * with a particular transaction.
   *
   * @param int $numberOfPartitionsInTransaction
   */
  public function setNumberOfPartitionsInTransaction($numberOfPartitionsInTransaction)
  {
    $this->numberOfPartitionsInTransaction = $numberOfPartitionsInTransaction;
  }
  /**
   * @return int
   */
  public function getNumberOfPartitionsInTransaction()
  {
    return $this->numberOfPartitionsInTransaction;
  }
  /**
   * Indicates the number of data change records that are part of this
   * transaction across all change stream partitions. This value can be used to
   * assemble all the records associated with a particular transaction.
   *
   * @param int $numberOfRecordsInTransaction
   */
  public function setNumberOfRecordsInTransaction($numberOfRecordsInTransaction)
  {
    $this->numberOfRecordsInTransaction = $numberOfRecordsInTransaction;
  }
  /**
   * @return int
   */
  public function getNumberOfRecordsInTransaction()
  {
    return $this->numberOfRecordsInTransaction;
  }
  /**
   * Record sequence numbers are unique and monotonically increasing (but not
   * necessarily contiguous) for a specific timestamp across record types in the
   * same partition. To guarantee ordered processing, the reader should process
   * records (of potentially different types) in record_sequence order for a
   * specific timestamp in the same partition. The record sequence number
   * ordering across partitions is only meaningful in the context of a specific
   * transaction. Record sequence numbers are unique across partitions for a
   * specific transaction. Sort the DataChangeRecords for the same
   * server_transaction_id by record_sequence to reconstruct the ordering of the
   * changes within the transaction.
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
   * Provides a globally unique string that represents the transaction in which
   * the change was committed. Multiple transactions can have the same commit
   * timestamp, but each transaction has a unique server_transaction_id.
   *
   * @param string $serverTransactionId
   */
  public function setServerTransactionId($serverTransactionId)
  {
    $this->serverTransactionId = $serverTransactionId;
  }
  /**
   * @return string
   */
  public function getServerTransactionId()
  {
    return $this->serverTransactionId;
  }
  /**
   * Name of the table affected by the change.
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * Indicates the transaction tag associated with this transaction.
   *
   * @param string $transactionTag
   */
  public function setTransactionTag($transactionTag)
  {
    $this->transactionTag = $transactionTag;
  }
  /**
   * @return string
   */
  public function getTransactionTag()
  {
    return $this->transactionTag;
  }
  /**
   * Describes the value capture type that was specified in the change stream
   * configuration when this change was captured.
   *
   * Accepted values: VALUE_CAPTURE_TYPE_UNSPECIFIED, OLD_AND_NEW_VALUES,
   * NEW_VALUES, NEW_ROW, NEW_ROW_AND_OLD_VALUES
   *
   * @param self::VALUE_CAPTURE_TYPE_* $valueCaptureType
   */
  public function setValueCaptureType($valueCaptureType)
  {
    $this->valueCaptureType = $valueCaptureType;
  }
  /**
   * @return self::VALUE_CAPTURE_TYPE_*
   */
  public function getValueCaptureType()
  {
    return $this->valueCaptureType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataChangeRecord::class, 'Google_Service_Spanner_DataChangeRecord');
