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

class Table extends \Google\Collection
{
  /**
   * Unspecified will default to using ROUND_HALF_AWAY_FROM_ZERO.
   */
  public const DEFAULT_ROUNDING_MODE_ROUNDING_MODE_UNSPECIFIED = 'ROUNDING_MODE_UNSPECIFIED';
  /**
   * ROUND_HALF_AWAY_FROM_ZERO rounds half values away from zero when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5, 1.6, 1.7, 1.8, 1.9 => 2
   */
  public const DEFAULT_ROUNDING_MODE_ROUND_HALF_AWAY_FROM_ZERO = 'ROUND_HALF_AWAY_FROM_ZERO';
  /**
   * ROUND_HALF_EVEN rounds half values to the nearest even value when applying
   * precision and scale upon writing of NUMERIC and BIGNUMERIC values. For
   * Scale: 0 1.1, 1.2, 1.3, 1.4 => 1 1.5 => 2 1.6, 1.7, 1.8, 1.9 => 2 2.5 => 2
   */
  public const DEFAULT_ROUNDING_MODE_ROUND_HALF_EVEN = 'ROUND_HALF_EVEN';
  /**
   * No managed table type specified.
   */
  public const MANAGED_TABLE_TYPE_MANAGED_TABLE_TYPE_UNSPECIFIED = 'MANAGED_TABLE_TYPE_UNSPECIFIED';
  /**
   * The managed table is a native BigQuery table.
   */
  public const MANAGED_TABLE_TYPE_NATIVE = 'NATIVE';
  /**
   * The managed table is a BigLake table for Apache Iceberg in BigQuery.
   */
  public const MANAGED_TABLE_TYPE_BIGLAKE = 'BIGLAKE';
  protected $collection_key = 'replicas';
  protected $biglakeConfigurationType = BigLakeConfiguration::class;
  protected $biglakeConfigurationDataType = '';
  protected $cloneDefinitionType = CloneDefinition::class;
  protected $cloneDefinitionDataType = '';
  protected $clusteringType = Clustering::class;
  protected $clusteringDataType = '';
  /**
   * Output only. The time when this table was created, in milliseconds since
   * the epoch.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Optional. Defines the default collation specification of new STRING fields
   * in the table. During table creation or update, if a STRING field is added
   * to this table without explicit collation specified, then the table inherits
   * the table default collation. A change to this field affects only fields
   * added afterwards, and does not alter the existing fields. The following
   * values are supported: * 'und:ci': undetermined locale, case insensitive. *
   * '': empty string. Default to case-sensitive behavior.
   *
   * @var string
   */
  public $defaultCollation;
  /**
   * Optional. Defines the default rounding mode specification of new decimal
   * fields (NUMERIC OR BIGNUMERIC) in the table. During table creation or
   * update, if a decimal field is added to this table without an explicit
   * rounding mode specified, then the field inherits the table default rounding
   * mode. Changing this field doesn't affect existing fields.
   *
   * @var string
   */
  public $defaultRoundingMode;
  /**
   * Optional. A user-friendly description of this table.
   *
   * @var string
   */
  public $description;
  protected $encryptionConfigurationType = EncryptionConfiguration::class;
  protected $encryptionConfigurationDataType = '';
  /**
   * Output only. A hash of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The time when this table expires, in milliseconds since the
   * epoch. If not present, the table will persist indefinitely. Expired tables
   * will be deleted and their storage reclaimed. The defaultTableExpirationMs
   * property of the encapsulating dataset can be used to set a default
   * expirationTime on newly created tables.
   *
   * @var string
   */
  public $expirationTime;
  protected $externalCatalogTableOptionsType = ExternalCatalogTableOptions::class;
  protected $externalCatalogTableOptionsDataType = '';
  protected $externalDataConfigurationType = ExternalDataConfiguration::class;
  protected $externalDataConfigurationDataType = '';
  /**
   * Optional. A descriptive name for this table.
   *
   * @var string
   */
  public $friendlyName;
  /**
   * Output only. An opaque ID uniquely identifying the table.
   *
   * @var string
   */
  public $id;
  /**
   * The type of resource ID.
   *
   * @var string
   */
  public $kind;
  /**
   * The labels associated with this table. You can use these to organize and
   * group your tables. Label keys and values can be no longer than 63
   * characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label values
   * are optional. Label keys must start with a letter and each label in the
   * list must have a different key.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The time when this table was last modified, in milliseconds
   * since the epoch.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * Output only. The geographic location where the table resides. This value is
   * inherited from the dataset.
   *
   * @var string
   */
  public $location;
  /**
   * Optional. If set, overrides the default managed table type configured in
   * the dataset.
   *
   * @var string
   */
  public $managedTableType;
  protected $materializedViewType = MaterializedViewDefinition::class;
  protected $materializedViewDataType = '';
  protected $materializedViewStatusType = MaterializedViewStatus::class;
  protected $materializedViewStatusDataType = '';
  /**
   * Optional. The maximum staleness of data that could be returned when the
   * table (or stale MV) is queried. Staleness encoded as a string encoding of
   * sql IntervalValue type.
   *
   * @var string
   */
  public $maxStaleness;
  protected $modelType = ModelDefinition::class;
  protected $modelDataType = '';
  /**
   * Output only. Number of logical bytes that are less than 90 days old.
   *
   * @var string
   */
  public $numActiveLogicalBytes;
  /**
   * Output only. Number of physical bytes less than 90 days old. This data is
   * not kept in real time, and might be delayed by a few seconds to a few
   * minutes.
   *
   * @var string
   */
  public $numActivePhysicalBytes;
  /**
   * Output only. The size of this table in logical bytes, excluding any data in
   * the streaming buffer.
   *
   * @var string
   */
  public $numBytes;
  /**
   * Output only. Number of physical bytes used by current live data storage.
   * This data is not kept in real time, and might be delayed by a few seconds
   * to a few minutes.
   *
   * @var string
   */
  public $numCurrentPhysicalBytes;
  /**
   * Output only. The number of logical bytes in the table that are considered
   * "long-term storage".
   *
   * @var string
   */
  public $numLongTermBytes;
  /**
   * Output only. Number of logical bytes that are more than 90 days old.
   *
   * @var string
   */
  public $numLongTermLogicalBytes;
  /**
   * Output only. Number of physical bytes more than 90 days old. This data is
   * not kept in real time, and might be delayed by a few seconds to a few
   * minutes.
   *
   * @var string
   */
  public $numLongTermPhysicalBytes;
  /**
   * Output only. The number of partitions present in the table or materialized
   * view. This data is not kept in real time, and might be delayed by a few
   * seconds to a few minutes.
   *
   * @var string
   */
  public $numPartitions;
  /**
   * Output only. The physical size of this table in bytes. This includes
   * storage used for time travel.
   *
   * @var string
   */
  public $numPhysicalBytes;
  /**
   * Output only. The number of rows of data in this table, excluding any data
   * in the streaming buffer.
   *
   * @var string
   */
  public $numRows;
  /**
   * Output only. Number of physical bytes used by time travel storage (deleted
   * or changed data). This data is not kept in real time, and might be delayed
   * by a few seconds to a few minutes.
   *
   * @var string
   */
  public $numTimeTravelPhysicalBytes;
  /**
   * Output only. Total number of logical bytes in the table or materialized
   * view.
   *
   * @var string
   */
  public $numTotalLogicalBytes;
  /**
   * Output only. The physical size of this table in bytes. This also includes
   * storage used for time travel. This data is not kept in real time, and might
   * be delayed by a few seconds to a few minutes.
   *
   * @var string
   */
  public $numTotalPhysicalBytes;
  protected $partitionDefinitionType = PartitioningDefinition::class;
  protected $partitionDefinitionDataType = '';
  protected $rangePartitioningType = RangePartitioning::class;
  protected $rangePartitioningDataType = '';
  protected $replicasType = TableReference::class;
  protected $replicasDataType = 'array';
  /**
   * Optional. If set to true, queries over this table require a partition
   * filter that can be used for partition elimination to be specified.
   *
   * @var bool
   */
  public $requirePartitionFilter;
  /**
   * [Optional] The tags associated with this table. Tag keys are globally
   * unique. See additional information on
   * [tags](https://cloud.google.com/iam/docs/tags-access-control#definitions).
   * An object containing a list of "key": value pairs. The key is the
   * namespaced friendly name of the tag key, e.g. "12345/environment" where
   * 12345 is parent id. The value is the friendly short name of the tag value,
   * e.g. "production".
   *
   * @var string[]
   */
  public $resourceTags;
  protected $restrictionsType = RestrictionConfig::class;
  protected $restrictionsDataType = '';
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  /**
   * Output only. A URL that can be used to access this resource again.
   *
   * @var string
   */
  public $selfLink;
  protected $snapshotDefinitionType = SnapshotDefinition::class;
  protected $snapshotDefinitionDataType = '';
  protected $streamingBufferType = Streamingbuffer::class;
  protected $streamingBufferDataType = '';
  protected $tableConstraintsType = TableConstraints::class;
  protected $tableConstraintsDataType = '';
  protected $tableReferenceType = TableReference::class;
  protected $tableReferenceDataType = '';
  protected $tableReplicationInfoType = TableReplicationInfo::class;
  protected $tableReplicationInfoDataType = '';
  protected $timePartitioningType = TimePartitioning::class;
  protected $timePartitioningDataType = '';
  /**
   * Output only. Describes the table type. The following values are supported:
   * * `TABLE`: A normal BigQuery table. * `VIEW`: A virtual table defined by a
   * SQL query. * `EXTERNAL`: A table that references data stored in an external
   * storage system, such as Google Cloud Storage. * `MATERIALIZED_VIEW`: A
   * precomputed view defined by a SQL query. * `SNAPSHOT`: An immutable
   * BigQuery table that preserves the contents of a base table at a particular
   * time. See additional information on [table
   * snapshots](https://cloud.google.com/bigquery/docs/table-snapshots-intro).
   * The default value is `TABLE`.
   *
   * @var string
   */
  public $type;
  protected $viewType = ViewDefinition::class;
  protected $viewDataType = '';

  /**
   * Optional. Specifies the configuration of a BigQuery table for Apache
   * Iceberg.
   *
   * @param BigLakeConfiguration $biglakeConfiguration
   */
  public function setBiglakeConfiguration(BigLakeConfiguration $biglakeConfiguration)
  {
    $this->biglakeConfiguration = $biglakeConfiguration;
  }
  /**
   * @return BigLakeConfiguration
   */
  public function getBiglakeConfiguration()
  {
    return $this->biglakeConfiguration;
  }
  /**
   * Output only. Contains information about the clone. This value is set via
   * the clone operation.
   *
   * @param CloneDefinition $cloneDefinition
   */
  public function setCloneDefinition(CloneDefinition $cloneDefinition)
  {
    $this->cloneDefinition = $cloneDefinition;
  }
  /**
   * @return CloneDefinition
   */
  public function getCloneDefinition()
  {
    return $this->cloneDefinition;
  }
  /**
   * Clustering specification for the table. Must be specified with time-based
   * partitioning, data in the table will be first partitioned and subsequently
   * clustered.
   *
   * @param Clustering $clustering
   */
  public function setClustering(Clustering $clustering)
  {
    $this->clustering = $clustering;
  }
  /**
   * @return Clustering
   */
  public function getClustering()
  {
    return $this->clustering;
  }
  /**
   * Output only. The time when this table was created, in milliseconds since
   * the epoch.
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
   * Optional. Defines the default collation specification of new STRING fields
   * in the table. During table creation or update, if a STRING field is added
   * to this table without explicit collation specified, then the table inherits
   * the table default collation. A change to this field affects only fields
   * added afterwards, and does not alter the existing fields. The following
   * values are supported: * 'und:ci': undetermined locale, case insensitive. *
   * '': empty string. Default to case-sensitive behavior.
   *
   * @param string $defaultCollation
   */
  public function setDefaultCollation($defaultCollation)
  {
    $this->defaultCollation = $defaultCollation;
  }
  /**
   * @return string
   */
  public function getDefaultCollation()
  {
    return $this->defaultCollation;
  }
  /**
   * Optional. Defines the default rounding mode specification of new decimal
   * fields (NUMERIC OR BIGNUMERIC) in the table. During table creation or
   * update, if a decimal field is added to this table without an explicit
   * rounding mode specified, then the field inherits the table default rounding
   * mode. Changing this field doesn't affect existing fields.
   *
   * Accepted values: ROUNDING_MODE_UNSPECIFIED, ROUND_HALF_AWAY_FROM_ZERO,
   * ROUND_HALF_EVEN
   *
   * @param self::DEFAULT_ROUNDING_MODE_* $defaultRoundingMode
   */
  public function setDefaultRoundingMode($defaultRoundingMode)
  {
    $this->defaultRoundingMode = $defaultRoundingMode;
  }
  /**
   * @return self::DEFAULT_ROUNDING_MODE_*
   */
  public function getDefaultRoundingMode()
  {
    return $this->defaultRoundingMode;
  }
  /**
   * Optional. A user-friendly description of this table.
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
   * Custom encryption configuration (e.g., Cloud KMS keys).
   *
   * @param EncryptionConfiguration $encryptionConfiguration
   */
  public function setEncryptionConfiguration(EncryptionConfiguration $encryptionConfiguration)
  {
    $this->encryptionConfiguration = $encryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getEncryptionConfiguration()
  {
    return $this->encryptionConfiguration;
  }
  /**
   * Output only. A hash of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The time when this table expires, in milliseconds since the
   * epoch. If not present, the table will persist indefinitely. Expired tables
   * will be deleted and their storage reclaimed. The defaultTableExpirationMs
   * property of the encapsulating dataset can be used to set a default
   * expirationTime on newly created tables.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Optional. Options defining open source compatible table.
   *
   * @param ExternalCatalogTableOptions $externalCatalogTableOptions
   */
  public function setExternalCatalogTableOptions(ExternalCatalogTableOptions $externalCatalogTableOptions)
  {
    $this->externalCatalogTableOptions = $externalCatalogTableOptions;
  }
  /**
   * @return ExternalCatalogTableOptions
   */
  public function getExternalCatalogTableOptions()
  {
    return $this->externalCatalogTableOptions;
  }
  /**
   * Optional. Describes the data format, location, and other properties of a
   * table stored outside of BigQuery. By defining these properties, the data
   * source can then be queried as if it were a standard BigQuery table.
   *
   * @param ExternalDataConfiguration $externalDataConfiguration
   */
  public function setExternalDataConfiguration(ExternalDataConfiguration $externalDataConfiguration)
  {
    $this->externalDataConfiguration = $externalDataConfiguration;
  }
  /**
   * @return ExternalDataConfiguration
   */
  public function getExternalDataConfiguration()
  {
    return $this->externalDataConfiguration;
  }
  /**
   * Optional. A descriptive name for this table.
   *
   * @param string $friendlyName
   */
  public function setFriendlyName($friendlyName)
  {
    $this->friendlyName = $friendlyName;
  }
  /**
   * @return string
   */
  public function getFriendlyName()
  {
    return $this->friendlyName;
  }
  /**
   * Output only. An opaque ID uniquely identifying the table.
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
   * The type of resource ID.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The labels associated with this table. You can use these to organize and
   * group your tables. Label keys and values can be no longer than 63
   * characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label values
   * are optional. Label keys must start with a letter and each label in the
   * list must have a different key.
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
   * Output only. The time when this table was last modified, in milliseconds
   * since the epoch.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * Output only. The geographic location where the table resides. This value is
   * inherited from the dataset.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Optional. If set, overrides the default managed table type configured in
   * the dataset.
   *
   * Accepted values: MANAGED_TABLE_TYPE_UNSPECIFIED, NATIVE, BIGLAKE
   *
   * @param self::MANAGED_TABLE_TYPE_* $managedTableType
   */
  public function setManagedTableType($managedTableType)
  {
    $this->managedTableType = $managedTableType;
  }
  /**
   * @return self::MANAGED_TABLE_TYPE_*
   */
  public function getManagedTableType()
  {
    return $this->managedTableType;
  }
  /**
   * Optional. The materialized view definition.
   *
   * @param MaterializedViewDefinition $materializedView
   */
  public function setMaterializedView(MaterializedViewDefinition $materializedView)
  {
    $this->materializedView = $materializedView;
  }
  /**
   * @return MaterializedViewDefinition
   */
  public function getMaterializedView()
  {
    return $this->materializedView;
  }
  /**
   * Output only. The materialized view status.
   *
   * @param MaterializedViewStatus $materializedViewStatus
   */
  public function setMaterializedViewStatus(MaterializedViewStatus $materializedViewStatus)
  {
    $this->materializedViewStatus = $materializedViewStatus;
  }
  /**
   * @return MaterializedViewStatus
   */
  public function getMaterializedViewStatus()
  {
    return $this->materializedViewStatus;
  }
  /**
   * Optional. The maximum staleness of data that could be returned when the
   * table (or stale MV) is queried. Staleness encoded as a string encoding of
   * sql IntervalValue type.
   *
   * @param string $maxStaleness
   */
  public function setMaxStaleness($maxStaleness)
  {
    $this->maxStaleness = $maxStaleness;
  }
  /**
   * @return string
   */
  public function getMaxStaleness()
  {
    return $this->maxStaleness;
  }
  /**
   * Deprecated.
   *
   * @param ModelDefinition $model
   */
  public function setModel(ModelDefinition $model)
  {
    $this->model = $model;
  }
  /**
   * @return ModelDefinition
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output only. Number of logical bytes that are less than 90 days old.
   *
   * @param string $numActiveLogicalBytes
   */
  public function setNumActiveLogicalBytes($numActiveLogicalBytes)
  {
    $this->numActiveLogicalBytes = $numActiveLogicalBytes;
  }
  /**
   * @return string
   */
  public function getNumActiveLogicalBytes()
  {
    return $this->numActiveLogicalBytes;
  }
  /**
   * Output only. Number of physical bytes less than 90 days old. This data is
   * not kept in real time, and might be delayed by a few seconds to a few
   * minutes.
   *
   * @param string $numActivePhysicalBytes
   */
  public function setNumActivePhysicalBytes($numActivePhysicalBytes)
  {
    $this->numActivePhysicalBytes = $numActivePhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumActivePhysicalBytes()
  {
    return $this->numActivePhysicalBytes;
  }
  /**
   * Output only. The size of this table in logical bytes, excluding any data in
   * the streaming buffer.
   *
   * @param string $numBytes
   */
  public function setNumBytes($numBytes)
  {
    $this->numBytes = $numBytes;
  }
  /**
   * @return string
   */
  public function getNumBytes()
  {
    return $this->numBytes;
  }
  /**
   * Output only. Number of physical bytes used by current live data storage.
   * This data is not kept in real time, and might be delayed by a few seconds
   * to a few minutes.
   *
   * @param string $numCurrentPhysicalBytes
   */
  public function setNumCurrentPhysicalBytes($numCurrentPhysicalBytes)
  {
    $this->numCurrentPhysicalBytes = $numCurrentPhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumCurrentPhysicalBytes()
  {
    return $this->numCurrentPhysicalBytes;
  }
  /**
   * Output only. The number of logical bytes in the table that are considered
   * "long-term storage".
   *
   * @param string $numLongTermBytes
   */
  public function setNumLongTermBytes($numLongTermBytes)
  {
    $this->numLongTermBytes = $numLongTermBytes;
  }
  /**
   * @return string
   */
  public function getNumLongTermBytes()
  {
    return $this->numLongTermBytes;
  }
  /**
   * Output only. Number of logical bytes that are more than 90 days old.
   *
   * @param string $numLongTermLogicalBytes
   */
  public function setNumLongTermLogicalBytes($numLongTermLogicalBytes)
  {
    $this->numLongTermLogicalBytes = $numLongTermLogicalBytes;
  }
  /**
   * @return string
   */
  public function getNumLongTermLogicalBytes()
  {
    return $this->numLongTermLogicalBytes;
  }
  /**
   * Output only. Number of physical bytes more than 90 days old. This data is
   * not kept in real time, and might be delayed by a few seconds to a few
   * minutes.
   *
   * @param string $numLongTermPhysicalBytes
   */
  public function setNumLongTermPhysicalBytes($numLongTermPhysicalBytes)
  {
    $this->numLongTermPhysicalBytes = $numLongTermPhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumLongTermPhysicalBytes()
  {
    return $this->numLongTermPhysicalBytes;
  }
  /**
   * Output only. The number of partitions present in the table or materialized
   * view. This data is not kept in real time, and might be delayed by a few
   * seconds to a few minutes.
   *
   * @param string $numPartitions
   */
  public function setNumPartitions($numPartitions)
  {
    $this->numPartitions = $numPartitions;
  }
  /**
   * @return string
   */
  public function getNumPartitions()
  {
    return $this->numPartitions;
  }
  /**
   * Output only. The physical size of this table in bytes. This includes
   * storage used for time travel.
   *
   * @param string $numPhysicalBytes
   */
  public function setNumPhysicalBytes($numPhysicalBytes)
  {
    $this->numPhysicalBytes = $numPhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumPhysicalBytes()
  {
    return $this->numPhysicalBytes;
  }
  /**
   * Output only. The number of rows of data in this table, excluding any data
   * in the streaming buffer.
   *
   * @param string $numRows
   */
  public function setNumRows($numRows)
  {
    $this->numRows = $numRows;
  }
  /**
   * @return string
   */
  public function getNumRows()
  {
    return $this->numRows;
  }
  /**
   * Output only. Number of physical bytes used by time travel storage (deleted
   * or changed data). This data is not kept in real time, and might be delayed
   * by a few seconds to a few minutes.
   *
   * @param string $numTimeTravelPhysicalBytes
   */
  public function setNumTimeTravelPhysicalBytes($numTimeTravelPhysicalBytes)
  {
    $this->numTimeTravelPhysicalBytes = $numTimeTravelPhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumTimeTravelPhysicalBytes()
  {
    return $this->numTimeTravelPhysicalBytes;
  }
  /**
   * Output only. Total number of logical bytes in the table or materialized
   * view.
   *
   * @param string $numTotalLogicalBytes
   */
  public function setNumTotalLogicalBytes($numTotalLogicalBytes)
  {
    $this->numTotalLogicalBytes = $numTotalLogicalBytes;
  }
  /**
   * @return string
   */
  public function getNumTotalLogicalBytes()
  {
    return $this->numTotalLogicalBytes;
  }
  /**
   * Output only. The physical size of this table in bytes. This also includes
   * storage used for time travel. This data is not kept in real time, and might
   * be delayed by a few seconds to a few minutes.
   *
   * @param string $numTotalPhysicalBytes
   */
  public function setNumTotalPhysicalBytes($numTotalPhysicalBytes)
  {
    $this->numTotalPhysicalBytes = $numTotalPhysicalBytes;
  }
  /**
   * @return string
   */
  public function getNumTotalPhysicalBytes()
  {
    return $this->numTotalPhysicalBytes;
  }
  /**
   * Optional. The partition information for all table formats, including
   * managed partitioned tables, hive partitioned tables, iceberg partitioned,
   * and metastore partitioned tables. This field is only populated for
   * metastore partitioned tables. For other table formats, this is an output
   * only field.
   *
   * @param PartitioningDefinition $partitionDefinition
   */
  public function setPartitionDefinition(PartitioningDefinition $partitionDefinition)
  {
    $this->partitionDefinition = $partitionDefinition;
  }
  /**
   * @return PartitioningDefinition
   */
  public function getPartitionDefinition()
  {
    return $this->partitionDefinition;
  }
  /**
   * If specified, configures range partitioning for this table.
   *
   * @param RangePartitioning $rangePartitioning
   */
  public function setRangePartitioning(RangePartitioning $rangePartitioning)
  {
    $this->rangePartitioning = $rangePartitioning;
  }
  /**
   * @return RangePartitioning
   */
  public function getRangePartitioning()
  {
    return $this->rangePartitioning;
  }
  /**
   * Optional. Output only. Table references of all replicas currently active on
   * the table.
   *
   * @param TableReference[] $replicas
   */
  public function setReplicas($replicas)
  {
    $this->replicas = $replicas;
  }
  /**
   * @return TableReference[]
   */
  public function getReplicas()
  {
    return $this->replicas;
  }
  /**
   * Optional. If set to true, queries over this table require a partition
   * filter that can be used for partition elimination to be specified.
   *
   * @param bool $requirePartitionFilter
   */
  public function setRequirePartitionFilter($requirePartitionFilter)
  {
    $this->requirePartitionFilter = $requirePartitionFilter;
  }
  /**
   * @return bool
   */
  public function getRequirePartitionFilter()
  {
    return $this->requirePartitionFilter;
  }
  /**
   * [Optional] The tags associated with this table. Tag keys are globally
   * unique. See additional information on
   * [tags](https://cloud.google.com/iam/docs/tags-access-control#definitions).
   * An object containing a list of "key": value pairs. The key is the
   * namespaced friendly name of the tag key, e.g. "12345/environment" where
   * 12345 is parent id. The value is the friendly short name of the tag value,
   * e.g. "production".
   *
   * @param string[] $resourceTags
   */
  public function setResourceTags($resourceTags)
  {
    $this->resourceTags = $resourceTags;
  }
  /**
   * @return string[]
   */
  public function getResourceTags()
  {
    return $this->resourceTags;
  }
  /**
   * Optional. Output only. Restriction config for table. If set, restrict
   * certain accesses on the table based on the config. See [Data
   * egress](https://cloud.google.com/bigquery/docs/analytics-hub-
   * introduction#data_egress) for more details.
   *
   * @param RestrictionConfig $restrictions
   */
  public function setRestrictions(RestrictionConfig $restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return RestrictionConfig
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
  /**
   * Optional. Describes the schema of this table.
   *
   * @param TableSchema $schema
   */
  public function setSchema(TableSchema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return TableSchema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Output only. A URL that can be used to access this resource again.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. Contains information about the snapshot. This value is set via
   * snapshot creation.
   *
   * @param SnapshotDefinition $snapshotDefinition
   */
  public function setSnapshotDefinition(SnapshotDefinition $snapshotDefinition)
  {
    $this->snapshotDefinition = $snapshotDefinition;
  }
  /**
   * @return SnapshotDefinition
   */
  public function getSnapshotDefinition()
  {
    return $this->snapshotDefinition;
  }
  /**
   * Output only. Contains information regarding this table's streaming buffer,
   * if one is present. This field will be absent if the table is not being
   * streamed to or if there is no data in the streaming buffer.
   *
   * @param Streamingbuffer $streamingBuffer
   */
  public function setStreamingBuffer(Streamingbuffer $streamingBuffer)
  {
    $this->streamingBuffer = $streamingBuffer;
  }
  /**
   * @return Streamingbuffer
   */
  public function getStreamingBuffer()
  {
    return $this->streamingBuffer;
  }
  /**
   * Optional. Tables Primary Key and Foreign Key information
   *
   * @param TableConstraints $tableConstraints
   */
  public function setTableConstraints(TableConstraints $tableConstraints)
  {
    $this->tableConstraints = $tableConstraints;
  }
  /**
   * @return TableConstraints
   */
  public function getTableConstraints()
  {
    return $this->tableConstraints;
  }
  /**
   * Required. Reference describing the ID of this table.
   *
   * @param TableReference $tableReference
   */
  public function setTableReference(TableReference $tableReference)
  {
    $this->tableReference = $tableReference;
  }
  /**
   * @return TableReference
   */
  public function getTableReference()
  {
    return $this->tableReference;
  }
  /**
   * Optional. Table replication info for table created `AS REPLICA` DDL like:
   * `CREATE MATERIALIZED VIEW mv1 AS REPLICA OF src_mv`
   *
   * @param TableReplicationInfo $tableReplicationInfo
   */
  public function setTableReplicationInfo(TableReplicationInfo $tableReplicationInfo)
  {
    $this->tableReplicationInfo = $tableReplicationInfo;
  }
  /**
   * @return TableReplicationInfo
   */
  public function getTableReplicationInfo()
  {
    return $this->tableReplicationInfo;
  }
  /**
   * If specified, configures time-based partitioning for this table.
   *
   * @param TimePartitioning $timePartitioning
   */
  public function setTimePartitioning(TimePartitioning $timePartitioning)
  {
    $this->timePartitioning = $timePartitioning;
  }
  /**
   * @return TimePartitioning
   */
  public function getTimePartitioning()
  {
    return $this->timePartitioning;
  }
  /**
   * Output only. Describes the table type. The following values are supported:
   * * `TABLE`: A normal BigQuery table. * `VIEW`: A virtual table defined by a
   * SQL query. * `EXTERNAL`: A table that references data stored in an external
   * storage system, such as Google Cloud Storage. * `MATERIALIZED_VIEW`: A
   * precomputed view defined by a SQL query. * `SNAPSHOT`: An immutable
   * BigQuery table that preserves the contents of a base table at a particular
   * time. See additional information on [table
   * snapshots](https://cloud.google.com/bigquery/docs/table-snapshots-intro).
   * The default value is `TABLE`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Optional. The view definition.
   *
   * @param ViewDefinition $view
   */
  public function setView(ViewDefinition $view)
  {
    $this->view = $view;
  }
  /**
   * @return ViewDefinition
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Table::class, 'Google_Service_Bigquery_Table');
