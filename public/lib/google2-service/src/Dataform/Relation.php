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

namespace Google\Service\Dataform;

class Relation extends \Google\Collection
{
  /**
   * Default value.
   */
  public const FILE_FORMAT_FILE_FORMAT_UNSPECIFIED = 'FILE_FORMAT_UNSPECIFIED';
  /**
   * Apache Parquet format.
   */
  public const FILE_FORMAT_PARQUET = 'PARQUET';
  /**
   * Default value. This value is unused.
   */
  public const RELATION_TYPE_RELATION_TYPE_UNSPECIFIED = 'RELATION_TYPE_UNSPECIFIED';
  /**
   * The relation is a table.
   */
  public const RELATION_TYPE_TABLE = 'TABLE';
  /**
   * The relation is a view.
   */
  public const RELATION_TYPE_VIEW = 'VIEW';
  /**
   * The relation is an incrementalized table.
   */
  public const RELATION_TYPE_INCREMENTAL_TABLE = 'INCREMENTAL_TABLE';
  /**
   * The relation is a materialized view.
   */
  public const RELATION_TYPE_MATERIALIZED_VIEW = 'MATERIALIZED_VIEW';
  /**
   * Default value.
   */
  public const TABLE_FORMAT_TABLE_FORMAT_UNSPECIFIED = 'TABLE_FORMAT_UNSPECIFIED';
  /**
   * Apache Iceberg format.
   */
  public const TABLE_FORMAT_ICEBERG = 'ICEBERG';
  protected $collection_key = 'tags';
  /**
   * Additional options that will be provided as key/value pairs into the
   * options clause of a create table/view statement. See
   * https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language for more information on which options are supported.
   *
   * @var string[]
   */
  public $additionalOptions;
  /**
   * A list of columns or SQL expressions used to cluster the table.
   *
   * @var string[]
   */
  public $clusterExpressions;
  /**
   * Optional. The connection specifying the credentials to be used to read and
   * write to external storage, such as Cloud Storage. The connection can have
   * the form `{project}.{location}.{connection_id}` or
   * `projects/{project}/locations/{location}/connections/{connection_id}", or
   * be set to DEFAULT.
   *
   * @var string
   */
  public $connection;
  protected $dependencyTargetsType = Target::class;
  protected $dependencyTargetsDataType = 'array';
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @var bool
   */
  public $disabled;
  /**
   * Optional. The file format for the BigQuery table.
   *
   * @var string
   */
  public $fileFormat;
  protected $incrementalTableConfigType = IncrementalTableConfig::class;
  protected $incrementalTableConfigDataType = '';
  /**
   * Sets the partition expiration in days.
   *
   * @var int
   */
  public $partitionExpirationDays;
  /**
   * The SQL expression used to partition the relation.
   *
   * @var string
   */
  public $partitionExpression;
  /**
   * SQL statements to be executed after creating the relation.
   *
   * @var string[]
   */
  public $postOperations;
  /**
   * SQL statements to be executed before creating the relation.
   *
   * @var string[]
   */
  public $preOperations;
  protected $relationDescriptorType = RelationDescriptor::class;
  protected $relationDescriptorDataType = '';
  /**
   * The type of this relation.
   *
   * @var string
   */
  public $relationType;
  /**
   * Specifies whether queries on this table must include a predicate filter
   * that filters on the partitioning column.
   *
   * @var bool
   */
  public $requirePartitionFilter;
  /**
   * The SELECT query which returns rows which this relation should contain.
   *
   * @var string
   */
  public $selectQuery;
  /**
   * Optional. The fully qualified location prefix of the external folder where
   * table data is stored. The URI should be in the format
   * `gs://bucket/path_to_table/`.
   *
   * @var string
   */
  public $storageUri;
  /**
   * Optional. The table format for the BigQuery table.
   *
   * @var string
   */
  public $tableFormat;
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @var string[]
   */
  public $tags;

  /**
   * Additional options that will be provided as key/value pairs into the
   * options clause of a create table/view statement. See
   * https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language for more information on which options are supported.
   *
   * @param string[] $additionalOptions
   */
  public function setAdditionalOptions($additionalOptions)
  {
    $this->additionalOptions = $additionalOptions;
  }
  /**
   * @return string[]
   */
  public function getAdditionalOptions()
  {
    return $this->additionalOptions;
  }
  /**
   * A list of columns or SQL expressions used to cluster the table.
   *
   * @param string[] $clusterExpressions
   */
  public function setClusterExpressions($clusterExpressions)
  {
    $this->clusterExpressions = $clusterExpressions;
  }
  /**
   * @return string[]
   */
  public function getClusterExpressions()
  {
    return $this->clusterExpressions;
  }
  /**
   * Optional. The connection specifying the credentials to be used to read and
   * write to external storage, such as Cloud Storage. The connection can have
   * the form `{project}.{location}.{connection_id}` or
   * `projects/{project}/locations/{location}/connections/{connection_id}", or
   * be set to DEFAULT.
   *
   * @param string $connection
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
  }
  /**
   * @return string
   */
  public function getConnection()
  {
    return $this->connection;
  }
  /**
   * A list of actions that this action depends on.
   *
   * @param Target[] $dependencyTargets
   */
  public function setDependencyTargets($dependencyTargets)
  {
    $this->dependencyTargets = $dependencyTargets;
  }
  /**
   * @return Target[]
   */
  public function getDependencyTargets()
  {
    return $this->dependencyTargets;
  }
  /**
   * Whether this action is disabled (i.e. should not be run).
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Optional. The file format for the BigQuery table.
   *
   * Accepted values: FILE_FORMAT_UNSPECIFIED, PARQUET
   *
   * @param self::FILE_FORMAT_* $fileFormat
   */
  public function setFileFormat($fileFormat)
  {
    $this->fileFormat = $fileFormat;
  }
  /**
   * @return self::FILE_FORMAT_*
   */
  public function getFileFormat()
  {
    return $this->fileFormat;
  }
  /**
   * Configures `INCREMENTAL_TABLE` settings for this relation. Only set if
   * `relation_type` is `INCREMENTAL_TABLE`.
   *
   * @param IncrementalTableConfig $incrementalTableConfig
   */
  public function setIncrementalTableConfig(IncrementalTableConfig $incrementalTableConfig)
  {
    $this->incrementalTableConfig = $incrementalTableConfig;
  }
  /**
   * @return IncrementalTableConfig
   */
  public function getIncrementalTableConfig()
  {
    return $this->incrementalTableConfig;
  }
  /**
   * Sets the partition expiration in days.
   *
   * @param int $partitionExpirationDays
   */
  public function setPartitionExpirationDays($partitionExpirationDays)
  {
    $this->partitionExpirationDays = $partitionExpirationDays;
  }
  /**
   * @return int
   */
  public function getPartitionExpirationDays()
  {
    return $this->partitionExpirationDays;
  }
  /**
   * The SQL expression used to partition the relation.
   *
   * @param string $partitionExpression
   */
  public function setPartitionExpression($partitionExpression)
  {
    $this->partitionExpression = $partitionExpression;
  }
  /**
   * @return string
   */
  public function getPartitionExpression()
  {
    return $this->partitionExpression;
  }
  /**
   * SQL statements to be executed after creating the relation.
   *
   * @param string[] $postOperations
   */
  public function setPostOperations($postOperations)
  {
    $this->postOperations = $postOperations;
  }
  /**
   * @return string[]
   */
  public function getPostOperations()
  {
    return $this->postOperations;
  }
  /**
   * SQL statements to be executed before creating the relation.
   *
   * @param string[] $preOperations
   */
  public function setPreOperations($preOperations)
  {
    $this->preOperations = $preOperations;
  }
  /**
   * @return string[]
   */
  public function getPreOperations()
  {
    return $this->preOperations;
  }
  /**
   * Descriptor for the relation and its columns.
   *
   * @param RelationDescriptor $relationDescriptor
   */
  public function setRelationDescriptor(RelationDescriptor $relationDescriptor)
  {
    $this->relationDescriptor = $relationDescriptor;
  }
  /**
   * @return RelationDescriptor
   */
  public function getRelationDescriptor()
  {
    return $this->relationDescriptor;
  }
  /**
   * The type of this relation.
   *
   * Accepted values: RELATION_TYPE_UNSPECIFIED, TABLE, VIEW, INCREMENTAL_TABLE,
   * MATERIALIZED_VIEW
   *
   * @param self::RELATION_TYPE_* $relationType
   */
  public function setRelationType($relationType)
  {
    $this->relationType = $relationType;
  }
  /**
   * @return self::RELATION_TYPE_*
   */
  public function getRelationType()
  {
    return $this->relationType;
  }
  /**
   * Specifies whether queries on this table must include a predicate filter
   * that filters on the partitioning column.
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
   * The SELECT query which returns rows which this relation should contain.
   *
   * @param string $selectQuery
   */
  public function setSelectQuery($selectQuery)
  {
    $this->selectQuery = $selectQuery;
  }
  /**
   * @return string
   */
  public function getSelectQuery()
  {
    return $this->selectQuery;
  }
  /**
   * Optional. The fully qualified location prefix of the external folder where
   * table data is stored. The URI should be in the format
   * `gs://bucket/path_to_table/`.
   *
   * @param string $storageUri
   */
  public function setStorageUri($storageUri)
  {
    $this->storageUri = $storageUri;
  }
  /**
   * @return string
   */
  public function getStorageUri()
  {
    return $this->storageUri;
  }
  /**
   * Optional. The table format for the BigQuery table.
   *
   * Accepted values: TABLE_FORMAT_UNSPECIFIED, ICEBERG
   *
   * @param self::TABLE_FORMAT_* $tableFormat
   */
  public function setTableFormat($tableFormat)
  {
    $this->tableFormat = $tableFormat;
  }
  /**
   * @return self::TABLE_FORMAT_*
   */
  public function getTableFormat()
  {
    return $this->tableFormat;
  }
  /**
   * Arbitrary, user-defined tags on this action.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Relation::class, 'Google_Service_Dataform_Relation');
