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

namespace Google\Service\Bigquery;

class JobConfigurationTableCopy extends \Google\Collection
{
  /**
   * Unspecified operation type.
   */
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * The source and destination table have the same table type.
   */
  public const OPERATION_TYPE_COPY = 'COPY';
  /**
   * The source table type is TABLE and the destination table type is SNAPSHOT.
   */
  public const OPERATION_TYPE_SNAPSHOT = 'SNAPSHOT';
  /**
   * The source table type is SNAPSHOT and the destination table type is TABLE.
   */
  public const OPERATION_TYPE_RESTORE = 'RESTORE';
  /**
   * The source and destination table have the same table type, but only bill
   * for unique data.
   */
  public const OPERATION_TYPE_CLONE = 'CLONE';
  protected $collection_key = 'sourceTables';
  /**
   * Optional. Specifies whether the job is allowed to create new tables. The
   * following values are supported: * CREATE_IF_NEEDED: If the table does not
   * exist, BigQuery creates the table. * CREATE_NEVER: The table must already
   * exist. If it does not, a 'notFound' error is returned in the job result.
   * The default value is CREATE_IF_NEEDED. Creation, truncation and append
   * actions occur as one atomic update upon job completion.
   *
   * @var string
   */
  public $createDisposition;
  protected $destinationEncryptionConfigurationType = EncryptionConfiguration::class;
  protected $destinationEncryptionConfigurationDataType = '';
  /**
   * Optional. The time when the destination table expires. Expired tables will
   * be deleted and their storage reclaimed.
   *
   * @var string
   */
  public $destinationExpirationTime;
  protected $destinationTableType = TableReference::class;
  protected $destinationTableDataType = '';
  /**
   * Optional. Supported operation types in table copy job.
   *
   * @var string
   */
  public $operationType;
  protected $sourceTableType = TableReference::class;
  protected $sourceTableDataType = '';
  protected $sourceTablesType = TableReference::class;
  protected $sourceTablesDataType = 'array';
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the table data and uses the schema and
   * table constraints from the source table. * WRITE_APPEND: If the table
   * already exists, BigQuery appends the data to the table. * WRITE_EMPTY: If
   * the table already exists and contains data, a 'duplicate' error is returned
   * in the job result. The default value is WRITE_EMPTY. Each action is atomic
   * and only occurs if BigQuery is able to complete the job successfully.
   * Creation, truncation and append actions occur as one atomic update upon job
   * completion.
   *
   * @var string
   */
  public $writeDisposition;

  /**
   * Optional. Specifies whether the job is allowed to create new tables. The
   * following values are supported: * CREATE_IF_NEEDED: If the table does not
   * exist, BigQuery creates the table. * CREATE_NEVER: The table must already
   * exist. If it does not, a 'notFound' error is returned in the job result.
   * The default value is CREATE_IF_NEEDED. Creation, truncation and append
   * actions occur as one atomic update upon job completion.
   *
   * @param string $createDisposition
   */
  public function setCreateDisposition($createDisposition)
  {
    $this->createDisposition = $createDisposition;
  }
  /**
   * @return string
   */
  public function getCreateDisposition()
  {
    return $this->createDisposition;
  }
  /**
   * Custom encryption configuration (e.g., Cloud KMS keys).
   *
   * @param EncryptionConfiguration $destinationEncryptionConfiguration
   */
  public function setDestinationEncryptionConfiguration(EncryptionConfiguration $destinationEncryptionConfiguration)
  {
    $this->destinationEncryptionConfiguration = $destinationEncryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getDestinationEncryptionConfiguration()
  {
    return $this->destinationEncryptionConfiguration;
  }
  /**
   * Optional. The time when the destination table expires. Expired tables will
   * be deleted and their storage reclaimed.
   *
   * @param string $destinationExpirationTime
   */
  public function setDestinationExpirationTime($destinationExpirationTime)
  {
    $this->destinationExpirationTime = $destinationExpirationTime;
  }
  /**
   * @return string
   */
  public function getDestinationExpirationTime()
  {
    return $this->destinationExpirationTime;
  }
  /**
   * [Required] The destination table.
   *
   * @param TableReference $destinationTable
   */
  public function setDestinationTable(TableReference $destinationTable)
  {
    $this->destinationTable = $destinationTable;
  }
  /**
   * @return TableReference
   */
  public function getDestinationTable()
  {
    return $this->destinationTable;
  }
  /**
   * Optional. Supported operation types in table copy job.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, COPY, SNAPSHOT, RESTORE, CLONE
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * [Pick one] Source table to copy.
   *
   * @param TableReference $sourceTable
   */
  public function setSourceTable(TableReference $sourceTable)
  {
    $this->sourceTable = $sourceTable;
  }
  /**
   * @return TableReference
   */
  public function getSourceTable()
  {
    return $this->sourceTable;
  }
  /**
   * [Pick one] Source tables to copy.
   *
   * @param TableReference[] $sourceTables
   */
  public function setSourceTables($sourceTables)
  {
    $this->sourceTables = $sourceTables;
  }
  /**
   * @return TableReference[]
   */
  public function getSourceTables()
  {
    return $this->sourceTables;
  }
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the table data and uses the schema and
   * table constraints from the source table. * WRITE_APPEND: If the table
   * already exists, BigQuery appends the data to the table. * WRITE_EMPTY: If
   * the table already exists and contains data, a 'duplicate' error is returned
   * in the job result. The default value is WRITE_EMPTY. Each action is atomic
   * and only occurs if BigQuery is able to complete the job successfully.
   * Creation, truncation and append actions occur as one atomic update upon job
   * completion.
   *
   * @param string $writeDisposition
   */
  public function setWriteDisposition($writeDisposition)
  {
    $this->writeDisposition = $writeDisposition;
  }
  /**
   * @return string
   */
  public function getWriteDisposition()
  {
    return $this->writeDisposition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfigurationTableCopy::class, 'Google_Service_Bigquery_JobConfigurationTableCopy');
