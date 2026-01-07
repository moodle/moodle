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

namespace Google\Service\BigtableAdmin;

class Table extends \Google\Model
{
  /**
   * The user did not specify a granularity. Should not be returned. When
   * specified during table creation, MILLIS will be used.
   */
  public const GRANULARITY_TIMESTAMP_GRANULARITY_UNSPECIFIED = 'TIMESTAMP_GRANULARITY_UNSPECIFIED';
  /**
   * The table keeps data versioned at a granularity of 1ms.
   */
  public const GRANULARITY_MILLIS = 'MILLIS';
  protected $automatedBackupPolicyType = AutomatedBackupPolicy::class;
  protected $automatedBackupPolicyDataType = '';
  protected $changeStreamConfigType = ChangeStreamConfig::class;
  protected $changeStreamConfigDataType = '';
  protected $clusterStatesType = ClusterState::class;
  protected $clusterStatesDataType = 'map';
  protected $columnFamiliesType = ColumnFamily::class;
  protected $columnFamiliesDataType = 'map';
  /**
   * Set to true to make the table protected against data loss. i.e. deleting
   * the following resources through Admin APIs are prohibited: * The table. *
   * The column families in the table. * The instance containing the table. Note
   * one can still delete the data stored in the table through Data APIs.
   *
   * @var bool
   */
  public $deletionProtection;
  /**
   * Immutable. The granularity (i.e. `MILLIS`) at which timestamps are stored
   * in this table. Timestamps not matching the granularity will be rejected. If
   * unspecified at creation time, the value will be set to `MILLIS`. Views:
   * `SCHEMA_VIEW`, `FULL`.
   *
   * @var string
   */
  public $granularity;
  /**
   * The unique name of the table. Values are of the form
   * `projects/{project}/instances/{instance}/tables/_a-zA-Z0-9*`. Views:
   * `NAME_ONLY`, `SCHEMA_VIEW`, `REPLICATION_VIEW`, `STATS_VIEW`, `FULL`
   *
   * @var string
   */
  public $name;
  protected $restoreInfoType = RestoreInfo::class;
  protected $restoreInfoDataType = '';
  protected $rowKeySchemaType = GoogleBigtableAdminV2TypeStruct::class;
  protected $rowKeySchemaDataType = '';
  protected $statsType = TableStats::class;
  protected $statsDataType = '';
  protected $tieredStorageConfigType = TieredStorageConfig::class;
  protected $tieredStorageConfigDataType = '';

  /**
   * If specified, automated backups are enabled for this table. Otherwise,
   * automated backups are disabled.
   *
   * @param AutomatedBackupPolicy $automatedBackupPolicy
   */
  public function setAutomatedBackupPolicy(AutomatedBackupPolicy $automatedBackupPolicy)
  {
    $this->automatedBackupPolicy = $automatedBackupPolicy;
  }
  /**
   * @return AutomatedBackupPolicy
   */
  public function getAutomatedBackupPolicy()
  {
    return $this->automatedBackupPolicy;
  }
  /**
   * If specified, enable the change stream on this table. Otherwise, the change
   * stream is disabled and the change stream is not retained.
   *
   * @param ChangeStreamConfig $changeStreamConfig
   */
  public function setChangeStreamConfig(ChangeStreamConfig $changeStreamConfig)
  {
    $this->changeStreamConfig = $changeStreamConfig;
  }
  /**
   * @return ChangeStreamConfig
   */
  public function getChangeStreamConfig()
  {
    return $this->changeStreamConfig;
  }
  /**
   * Output only. Map from cluster ID to per-cluster table state. If it could
   * not be determined whether or not the table has data in a particular cluster
   * (for example, if its zone is unavailable), then there will be an entry for
   * the cluster with UNKNOWN `replication_status`. Views: `REPLICATION_VIEW`,
   * `ENCRYPTION_VIEW`, `FULL`
   *
   * @param ClusterState[] $clusterStates
   */
  public function setClusterStates($clusterStates)
  {
    $this->clusterStates = $clusterStates;
  }
  /**
   * @return ClusterState[]
   */
  public function getClusterStates()
  {
    return $this->clusterStates;
  }
  /**
   * The column families configured for this table, mapped by column family ID.
   * Views: `SCHEMA_VIEW`, `STATS_VIEW`, `FULL`
   *
   * @param ColumnFamily[] $columnFamilies
   */
  public function setColumnFamilies($columnFamilies)
  {
    $this->columnFamilies = $columnFamilies;
  }
  /**
   * @return ColumnFamily[]
   */
  public function getColumnFamilies()
  {
    return $this->columnFamilies;
  }
  /**
   * Set to true to make the table protected against data loss. i.e. deleting
   * the following resources through Admin APIs are prohibited: * The table. *
   * The column families in the table. * The instance containing the table. Note
   * one can still delete the data stored in the table through Data APIs.
   *
   * @param bool $deletionProtection
   */
  public function setDeletionProtection($deletionProtection)
  {
    $this->deletionProtection = $deletionProtection;
  }
  /**
   * @return bool
   */
  public function getDeletionProtection()
  {
    return $this->deletionProtection;
  }
  /**
   * Immutable. The granularity (i.e. `MILLIS`) at which timestamps are stored
   * in this table. Timestamps not matching the granularity will be rejected. If
   * unspecified at creation time, the value will be set to `MILLIS`. Views:
   * `SCHEMA_VIEW`, `FULL`.
   *
   * Accepted values: TIMESTAMP_GRANULARITY_UNSPECIFIED, MILLIS
   *
   * @param self::GRANULARITY_* $granularity
   */
  public function setGranularity($granularity)
  {
    $this->granularity = $granularity;
  }
  /**
   * @return self::GRANULARITY_*
   */
  public function getGranularity()
  {
    return $this->granularity;
  }
  /**
   * The unique name of the table. Values are of the form
   * `projects/{project}/instances/{instance}/tables/_a-zA-Z0-9*`. Views:
   * `NAME_ONLY`, `SCHEMA_VIEW`, `REPLICATION_VIEW`, `STATS_VIEW`, `FULL`
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
   * Output only. If this table was restored from another data source (e.g. a
   * backup), this field will be populated with information about the restore.
   *
   * @param RestoreInfo $restoreInfo
   */
  public function setRestoreInfo(RestoreInfo $restoreInfo)
  {
    $this->restoreInfo = $restoreInfo;
  }
  /**
   * @return RestoreInfo
   */
  public function getRestoreInfo()
  {
    return $this->restoreInfo;
  }
  /**
   * The row key schema for this table. The schema is used to decode the raw row
   * key bytes into a structured format. The order of field declarations in this
   * schema is important, as it reflects how the raw row key bytes are
   * structured. Currently, this only affects how the key is read via a
   * GoogleSQL query from the ExecuteQuery API. For a SQL query, the _key column
   * is still read as raw bytes. But queries can reference the key fields by
   * name, which will be decoded from _key using provided type and encoding.
   * Queries that reference key fields will fail if they encounter an invalid
   * row key. For example, if _key = "some_id#2024-04-30#\x00\x13\x00\xf3" with
   * the following schema: { fields { field_name: "id" type { string { encoding:
   * utf8_bytes {} } } } fields { field_name: "date" type { string { encoding:
   * utf8_bytes {} } } } fields { field_name: "product_code" type { int64 {
   * encoding: big_endian_bytes {} } } } encoding { delimited_bytes { delimiter:
   * "#" } } } The decoded key parts would be: id = "some_id", date =
   * "2024-04-30", product_code = 1245427 The query "SELECT _key, product_code
   * FROM table" will return two columns:
   * /------------------------------------------------------\ | _key |
   * product_code | | --------------------------------------|--------------| |
   * "some_id#2024-04-30#\x00\x13\x00\xf3" | 1245427 |
   * \------------------------------------------------------/ The schema has the
   * following invariants: (1) The decoded field values are order-preserved. For
   * read, the field values will be decoded in sorted mode from the raw bytes.
   * (2) Every field in the schema must specify a non-empty name. (3) Every
   * field must specify a type with an associated encoding. The type is limited
   * to scalar types only: Array, Map, Aggregate, and Struct are not allowed.
   * (4) The field names must not collide with existing column family names and
   * reserved keywords "_key" and "_timestamp". The following update operations
   * are allowed for row_key_schema: - Update from an empty schema to a new
   * schema. - Remove the existing schema. This operation requires setting the
   * `ignore_warnings` flag to `true`, since it might be a backward incompatible
   * change. Without the flag, the update request will fail with an
   * INVALID_ARGUMENT error. Any other row key schema update operation (e.g.
   * update existing schema columns names or types) is currently unsupported.
   *
   * @param GoogleBigtableAdminV2TypeStruct $rowKeySchema
   */
  public function setRowKeySchema(GoogleBigtableAdminV2TypeStruct $rowKeySchema)
  {
    $this->rowKeySchema = $rowKeySchema;
  }
  /**
   * @return GoogleBigtableAdminV2TypeStruct
   */
  public function getRowKeySchema()
  {
    return $this->rowKeySchema;
  }
  /**
   * Output only. Only available with STATS_VIEW, this includes summary
   * statistics about the entire table contents. For statistics about a specific
   * column family, see ColumnFamilyStats in the mapped ColumnFamily collection
   * above.
   *
   * @param TableStats $stats
   */
  public function setStats(TableStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return TableStats
   */
  public function getStats()
  {
    return $this->stats;
  }
  /**
   * Rules to specify what data is stored in each storage tier. Different tiers
   * store data differently, providing different trade-offs between cost and
   * performance. Different parts of a table can be stored separately on
   * different tiers. If a config is specified, tiered storage is enabled for
   * this table. Otherwise, tiered storage is disabled. Only SSD instances can
   * configure tiered storage.
   *
   * @param TieredStorageConfig $tieredStorageConfig
   */
  public function setTieredStorageConfig(TieredStorageConfig $tieredStorageConfig)
  {
    $this->tieredStorageConfig = $tieredStorageConfig;
  }
  /**
   * @return TieredStorageConfig
   */
  public function getTieredStorageConfig()
  {
    return $this->tieredStorageConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_BigtableAdmin_Table');
