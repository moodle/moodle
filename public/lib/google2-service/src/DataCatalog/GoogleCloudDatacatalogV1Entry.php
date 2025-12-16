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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1Entry extends \Google\Model
{
  /**
   * Default unknown system.
   */
  public const INTEGRATED_SYSTEM_INTEGRATED_SYSTEM_UNSPECIFIED = 'INTEGRATED_SYSTEM_UNSPECIFIED';
  /**
   * BigQuery.
   */
  public const INTEGRATED_SYSTEM_BIGQUERY = 'BIGQUERY';
  /**
   * Cloud Pub/Sub.
   */
  public const INTEGRATED_SYSTEM_CLOUD_PUBSUB = 'CLOUD_PUBSUB';
  /**
   * Dataproc Metastore.
   */
  public const INTEGRATED_SYSTEM_DATAPROC_METASTORE = 'DATAPROC_METASTORE';
  /**
   * Dataplex Universal Catalog.
   */
  public const INTEGRATED_SYSTEM_DATAPLEX = 'DATAPLEX';
  /**
   * Cloud Spanner
   */
  public const INTEGRATED_SYSTEM_CLOUD_SPANNER = 'CLOUD_SPANNER';
  /**
   * Cloud Bigtable
   */
  public const INTEGRATED_SYSTEM_CLOUD_BIGTABLE = 'CLOUD_BIGTABLE';
  /**
   * Cloud Sql
   */
  public const INTEGRATED_SYSTEM_CLOUD_SQL = 'CLOUD_SQL';
  /**
   * Looker
   */
  public const INTEGRATED_SYSTEM_LOOKER = 'LOOKER';
  /**
   * Vertex AI
   */
  public const INTEGRATED_SYSTEM_VERTEX_AI = 'VERTEX_AI';
  /**
   * Default unknown type.
   */
  public const TYPE_ENTRY_TYPE_UNSPECIFIED = 'ENTRY_TYPE_UNSPECIFIED';
  /**
   * The entry type that has a GoogleSQL schema, including logical views.
   */
  public const TYPE_TABLE = 'TABLE';
  /**
   * The type of models. For more information, see [Supported models in BigQuery
   * ML](/bigquery/docs/bqml-introduction#supported_models).
   */
  public const TYPE_MODEL = 'MODEL';
  /**
   * An entry type for streaming entries. For example, a Pub/Sub topic.
   */
  public const TYPE_DATA_STREAM = 'DATA_STREAM';
  /**
   * An entry type for a set of files or objects. For example, a Cloud Storage
   * fileset.
   */
  public const TYPE_FILESET = 'FILESET';
  /**
   * A group of servers that work together. For example, a Kafka cluster.
   */
  public const TYPE_CLUSTER = 'CLUSTER';
  /**
   * A database.
   */
  public const TYPE_DATABASE = 'DATABASE';
  /**
   * Connection to a data source. For example, a BigQuery connection.
   */
  public const TYPE_DATA_SOURCE_CONNECTION = 'DATA_SOURCE_CONNECTION';
  /**
   * Routine, for example, a BigQuery routine.
   */
  public const TYPE_ROUTINE = 'ROUTINE';
  /**
   * A Dataplex Universal Catalog lake.
   */
  public const TYPE_LAKE = 'LAKE';
  /**
   * A Dataplex Universal Catalog zone.
   */
  public const TYPE_ZONE = 'ZONE';
  /**
   * A service, for example, a Dataproc Metastore service.
   */
  public const TYPE_SERVICE = 'SERVICE';
  /**
   * Schema within a relational database.
   */
  public const TYPE_DATABASE_SCHEMA = 'DATABASE_SCHEMA';
  /**
   * A Dashboard, for example from Looker.
   */
  public const TYPE_DASHBOARD = 'DASHBOARD';
  /**
   * A Looker Explore. For more information, see [Looker Explore API] (https://d
   * evelopers.looker.com/api/explorer/4.0/methods/LookmlModel/lookml_model_expl
   * ore).
   */
  public const TYPE_EXPLORE = 'EXPLORE';
  /**
   * A Looker Look. For more information, see [Looker Look API]
   * (https://developers.looker.com/api/explorer/4.0/methods/Look).
   */
  public const TYPE_LOOK = 'LOOK';
  /**
   * Feature Online Store resource in Vertex AI Feature Store.
   */
  public const TYPE_FEATURE_ONLINE_STORE = 'FEATURE_ONLINE_STORE';
  /**
   * Feature View resource in Vertex AI Feature Store.
   */
  public const TYPE_FEATURE_VIEW = 'FEATURE_VIEW';
  /**
   * Feature Group resource in Vertex AI Feature Store.
   */
  public const TYPE_FEATURE_GROUP = 'FEATURE_GROUP';
  /**
   * An entry type for a graph.
   */
  public const TYPE_GRAPH = 'GRAPH';
  protected $bigqueryDateShardedSpecType = GoogleCloudDatacatalogV1BigQueryDateShardedSpec::class;
  protected $bigqueryDateShardedSpecDataType = '';
  protected $bigqueryTableSpecType = GoogleCloudDatacatalogV1BigQueryTableSpec::class;
  protected $bigqueryTableSpecDataType = '';
  protected $businessContextType = GoogleCloudDatacatalogV1BusinessContext::class;
  protected $businessContextDataType = '';
  protected $cloudBigtableSystemSpecType = GoogleCloudDatacatalogV1CloudBigtableSystemSpec::class;
  protected $cloudBigtableSystemSpecDataType = '';
  protected $dataSourceType = GoogleCloudDatacatalogV1DataSource::class;
  protected $dataSourceDataType = '';
  protected $dataSourceConnectionSpecType = GoogleCloudDatacatalogV1DataSourceConnectionSpec::class;
  protected $dataSourceConnectionSpecDataType = '';
  protected $databaseTableSpecType = GoogleCloudDatacatalogV1DatabaseTableSpec::class;
  protected $databaseTableSpecDataType = '';
  protected $datasetSpecType = GoogleCloudDatacatalogV1DatasetSpec::class;
  protected $datasetSpecDataType = '';
  /**
   * Entry description that can consist of several sentences or paragraphs that
   * describe entry contents. The description must not contain Unicode non-
   * characters as well as C0 and C1 control codes except tabs (HT), new lines
   * (LF), carriage returns (CR), and page breaks (FF). The maximum size is 2000
   * bytes when encoded in UTF-8. Default value is an empty string.
   *
   * @var string
   */
  public $description;
  /**
   * Display name of an entry. The maximum size is 500 bytes when encoded in
   * UTF-8. Default value is an empty string.
   *
   * @var string
   */
  public $displayName;
  protected $featureOnlineStoreSpecType = GoogleCloudDatacatalogV1FeatureOnlineStoreSpec::class;
  protected $featureOnlineStoreSpecDataType = '';
  protected $filesetSpecType = GoogleCloudDatacatalogV1FilesetSpec::class;
  protected $filesetSpecDataType = '';
  /**
   * [Fully Qualified Name (FQN)](https://cloud.google.com//data-
   * catalog/docs/fully-qualified-names) of the resource. Set automatically for
   * entries representing resources from synced systems. Settable only during
   * creation, and read-only later. Can be used for search and lookup of the
   * entries.
   *
   * @var string
   */
  public $fullyQualifiedName;
  protected $gcsFilesetSpecType = GoogleCloudDatacatalogV1GcsFilesetSpec::class;
  protected $gcsFilesetSpecDataType = '';
  protected $graphSpecType = GoogleCloudDatacatalogV1GraphSpec::class;
  protected $graphSpecDataType = '';
  /**
   * Output only. Indicates the entry's source system that Data Catalog
   * integrates with, such as BigQuery, Pub/Sub, or Dataproc Metastore.
   *
   * @var string
   */
  public $integratedSystem;
  /**
   * Cloud labels attached to the entry. In Data Catalog, you can create and
   * modify labels attached only to custom entries. Synced entries have
   * unmodifiable labels that come from the source system.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The resource this metadata entry refers to. For Google Cloud Platform
   * resources, `linked_resource` is the [Full Resource Name]
   * (https://cloud.google.com/apis/design/resource_names#full_resource_name).
   * For example, the `linked_resource` for a table resource from BigQuery is: `
   * //bigquery.googleapis.com/projects/{PROJECT_ID}/datasets/{DATASET_ID}/table
   * s/{TABLE_ID}` Output only when the entry is one of the types in the
   * `EntryType` enum. For entries with a `user_specified_type`, this field is
   * optional and defaults to an empty string. The resource string must contain
   * only letters (a-z, A-Z), numbers (0-9), underscores (_), periods (.),
   * colons (:), slashes (/), dashes (-), and hashes (#). The maximum size is
   * 200 bytes when encoded in UTF-8.
   *
   * @var string
   */
  public $linkedResource;
  protected $lookerSystemSpecType = GoogleCloudDatacatalogV1LookerSystemSpec::class;
  protected $lookerSystemSpecDataType = '';
  protected $modelSpecType = GoogleCloudDatacatalogV1ModelSpec::class;
  protected $modelSpecDataType = '';
  /**
   * Output only. Identifier. The resource name of an entry in URL format. Note:
   * The entry itself and its child resources might not be stored in the
   * location specified in its name.
   *
   * @var string
   */
  public $name;
  protected $personalDetailsType = GoogleCloudDatacatalogV1PersonalDetails::class;
  protected $personalDetailsDataType = '';
  protected $routineSpecType = GoogleCloudDatacatalogV1RoutineSpec::class;
  protected $routineSpecDataType = '';
  protected $schemaType = GoogleCloudDatacatalogV1Schema::class;
  protected $schemaDataType = '';
  protected $serviceSpecType = GoogleCloudDatacatalogV1ServiceSpec::class;
  protected $serviceSpecDataType = '';
  protected $sourceSystemTimestampsType = GoogleCloudDatacatalogV1SystemTimestamps::class;
  protected $sourceSystemTimestampsDataType = '';
  protected $sqlDatabaseSystemSpecType = GoogleCloudDatacatalogV1SqlDatabaseSystemSpec::class;
  protected $sqlDatabaseSystemSpecDataType = '';
  /**
   * The type of the entry. For details, see [`EntryType`](#entrytype).
   *
   * @var string
   */
  public $type;
  protected $usageSignalType = GoogleCloudDatacatalogV1UsageSignal::class;
  protected $usageSignalDataType = '';
  /**
   * Indicates the entry's source system that Data Catalog doesn't automatically
   * integrate with. The `user_specified_system` string has the following
   * limitations: * Is case insensitive. * Must begin with a letter or
   * underscore. * Can only contain letters, numbers, and underscores. * Must be
   * at least 1 character and at most 64 characters long.
   *
   * @var string
   */
  public $userSpecifiedSystem;
  /**
   * Custom entry type that doesn't match any of the values allowed for input
   * and listed in the `EntryType` enum. When creating an entry, first check the
   * type values in the enum. If there are no appropriate types for the new
   * entry, provide a custom value, for example, `my_special_type`. The
   * `user_specified_type` string has the following limitations: * Is case
   * insensitive. * Must begin with a letter or underscore. * Can only contain
   * letters, numbers, and underscores. * Must be at least 1 character and at
   * most 64 characters long.
   *
   * @var string
   */
  public $userSpecifiedType;

  /**
   * Output only. Specification for a group of BigQuery tables with the
   * `[prefix]YYYYMMDD` name pattern. For more information, see [Introduction to
   * partitioned tables] (https://cloud.google.com/bigquery/docs/partitioned-
   * tables#partitioning_versus_sharding).
   *
   * @param GoogleCloudDatacatalogV1BigQueryDateShardedSpec $bigqueryDateShardedSpec
   */
  public function setBigqueryDateShardedSpec(GoogleCloudDatacatalogV1BigQueryDateShardedSpec $bigqueryDateShardedSpec)
  {
    $this->bigqueryDateShardedSpec = $bigqueryDateShardedSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1BigQueryDateShardedSpec
   */
  public function getBigqueryDateShardedSpec()
  {
    return $this->bigqueryDateShardedSpec;
  }
  /**
   * Output only. Specification that applies to a BigQuery table. Valid only for
   * entries with the `TABLE` type.
   *
   * @param GoogleCloudDatacatalogV1BigQueryTableSpec $bigqueryTableSpec
   */
  public function setBigqueryTableSpec(GoogleCloudDatacatalogV1BigQueryTableSpec $bigqueryTableSpec)
  {
    $this->bigqueryTableSpec = $bigqueryTableSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1BigQueryTableSpec
   */
  public function getBigqueryTableSpec()
  {
    return $this->bigqueryTableSpec;
  }
  /**
   * Business Context of the entry. Not supported for BigQuery datasets
   *
   * @param GoogleCloudDatacatalogV1BusinessContext $businessContext
   */
  public function setBusinessContext(GoogleCloudDatacatalogV1BusinessContext $businessContext)
  {
    $this->businessContext = $businessContext;
  }
  /**
   * @return GoogleCloudDatacatalogV1BusinessContext
   */
  public function getBusinessContext()
  {
    return $this->businessContext;
  }
  /**
   * Specification that applies to Cloud Bigtable system. Only settable when
   * `integrated_system` is equal to `CLOUD_BIGTABLE`
   *
   * @param GoogleCloudDatacatalogV1CloudBigtableSystemSpec $cloudBigtableSystemSpec
   */
  public function setCloudBigtableSystemSpec(GoogleCloudDatacatalogV1CloudBigtableSystemSpec $cloudBigtableSystemSpec)
  {
    $this->cloudBigtableSystemSpec = $cloudBigtableSystemSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1CloudBigtableSystemSpec
   */
  public function getCloudBigtableSystemSpec()
  {
    return $this->cloudBigtableSystemSpec;
  }
  /**
   * Output only. Physical location of the entry.
   *
   * @param GoogleCloudDatacatalogV1DataSource $dataSource
   */
  public function setDataSource(GoogleCloudDatacatalogV1DataSource $dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return GoogleCloudDatacatalogV1DataSource
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Specification that applies to a data source connection. Valid only for
   * entries with the `DATA_SOURCE_CONNECTION` type.
   *
   * @param GoogleCloudDatacatalogV1DataSourceConnectionSpec $dataSourceConnectionSpec
   */
  public function setDataSourceConnectionSpec(GoogleCloudDatacatalogV1DataSourceConnectionSpec $dataSourceConnectionSpec)
  {
    $this->dataSourceConnectionSpec = $dataSourceConnectionSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1DataSourceConnectionSpec
   */
  public function getDataSourceConnectionSpec()
  {
    return $this->dataSourceConnectionSpec;
  }
  /**
   * Specification that applies to a table resource. Valid only for entries with
   * the `TABLE` or `EXPLORE` type.
   *
   * @param GoogleCloudDatacatalogV1DatabaseTableSpec $databaseTableSpec
   */
  public function setDatabaseTableSpec(GoogleCloudDatacatalogV1DatabaseTableSpec $databaseTableSpec)
  {
    $this->databaseTableSpec = $databaseTableSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1DatabaseTableSpec
   */
  public function getDatabaseTableSpec()
  {
    return $this->databaseTableSpec;
  }
  /**
   * Specification that applies to a dataset.
   *
   * @param GoogleCloudDatacatalogV1DatasetSpec $datasetSpec
   */
  public function setDatasetSpec(GoogleCloudDatacatalogV1DatasetSpec $datasetSpec)
  {
    $this->datasetSpec = $datasetSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1DatasetSpec
   */
  public function getDatasetSpec()
  {
    return $this->datasetSpec;
  }
  /**
   * Entry description that can consist of several sentences or paragraphs that
   * describe entry contents. The description must not contain Unicode non-
   * characters as well as C0 and C1 control codes except tabs (HT), new lines
   * (LF), carriage returns (CR), and page breaks (FF). The maximum size is 2000
   * bytes when encoded in UTF-8. Default value is an empty string.
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
   * Display name of an entry. The maximum size is 500 bytes when encoded in
   * UTF-8. Default value is an empty string.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * FeatureonlineStore spec for Vertex AI Feature Store.
   *
   * @param GoogleCloudDatacatalogV1FeatureOnlineStoreSpec $featureOnlineStoreSpec
   */
  public function setFeatureOnlineStoreSpec(GoogleCloudDatacatalogV1FeatureOnlineStoreSpec $featureOnlineStoreSpec)
  {
    $this->featureOnlineStoreSpec = $featureOnlineStoreSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1FeatureOnlineStoreSpec
   */
  public function getFeatureOnlineStoreSpec()
  {
    return $this->featureOnlineStoreSpec;
  }
  /**
   * Specification that applies to a fileset resource. Valid only for entries
   * with the `FILESET` type.
   *
   * @param GoogleCloudDatacatalogV1FilesetSpec $filesetSpec
   */
  public function setFilesetSpec(GoogleCloudDatacatalogV1FilesetSpec $filesetSpec)
  {
    $this->filesetSpec = $filesetSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1FilesetSpec
   */
  public function getFilesetSpec()
  {
    return $this->filesetSpec;
  }
  /**
   * [Fully Qualified Name (FQN)](https://cloud.google.com//data-
   * catalog/docs/fully-qualified-names) of the resource. Set automatically for
   * entries representing resources from synced systems. Settable only during
   * creation, and read-only later. Can be used for search and lookup of the
   * entries.
   *
   * @param string $fullyQualifiedName
   */
  public function setFullyQualifiedName($fullyQualifiedName)
  {
    $this->fullyQualifiedName = $fullyQualifiedName;
  }
  /**
   * @return string
   */
  public function getFullyQualifiedName()
  {
    return $this->fullyQualifiedName;
  }
  /**
   * Specification that applies to a Cloud Storage fileset. Valid only for
   * entries with the `FILESET` type.
   *
   * @param GoogleCloudDatacatalogV1GcsFilesetSpec $gcsFilesetSpec
   */
  public function setGcsFilesetSpec(GoogleCloudDatacatalogV1GcsFilesetSpec $gcsFilesetSpec)
  {
    $this->gcsFilesetSpec = $gcsFilesetSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1GcsFilesetSpec
   */
  public function getGcsFilesetSpec()
  {
    return $this->gcsFilesetSpec;
  }
  /**
   * Spec for graph.
   *
   * @param GoogleCloudDatacatalogV1GraphSpec $graphSpec
   */
  public function setGraphSpec(GoogleCloudDatacatalogV1GraphSpec $graphSpec)
  {
    $this->graphSpec = $graphSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1GraphSpec
   */
  public function getGraphSpec()
  {
    return $this->graphSpec;
  }
  /**
   * Output only. Indicates the entry's source system that Data Catalog
   * integrates with, such as BigQuery, Pub/Sub, or Dataproc Metastore.
   *
   * Accepted values: INTEGRATED_SYSTEM_UNSPECIFIED, BIGQUERY, CLOUD_PUBSUB,
   * DATAPROC_METASTORE, DATAPLEX, CLOUD_SPANNER, CLOUD_BIGTABLE, CLOUD_SQL,
   * LOOKER, VERTEX_AI
   *
   * @param self::INTEGRATED_SYSTEM_* $integratedSystem
   */
  public function setIntegratedSystem($integratedSystem)
  {
    $this->integratedSystem = $integratedSystem;
  }
  /**
   * @return self::INTEGRATED_SYSTEM_*
   */
  public function getIntegratedSystem()
  {
    return $this->integratedSystem;
  }
  /**
   * Cloud labels attached to the entry. In Data Catalog, you can create and
   * modify labels attached only to custom entries. Synced entries have
   * unmodifiable labels that come from the source system.
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
   * The resource this metadata entry refers to. For Google Cloud Platform
   * resources, `linked_resource` is the [Full Resource Name]
   * (https://cloud.google.com/apis/design/resource_names#full_resource_name).
   * For example, the `linked_resource` for a table resource from BigQuery is: `
   * //bigquery.googleapis.com/projects/{PROJECT_ID}/datasets/{DATASET_ID}/table
   * s/{TABLE_ID}` Output only when the entry is one of the types in the
   * `EntryType` enum. For entries with a `user_specified_type`, this field is
   * optional and defaults to an empty string. The resource string must contain
   * only letters (a-z, A-Z), numbers (0-9), underscores (_), periods (.),
   * colons (:), slashes (/), dashes (-), and hashes (#). The maximum size is
   * 200 bytes when encoded in UTF-8.
   *
   * @param string $linkedResource
   */
  public function setLinkedResource($linkedResource)
  {
    $this->linkedResource = $linkedResource;
  }
  /**
   * @return string
   */
  public function getLinkedResource()
  {
    return $this->linkedResource;
  }
  /**
   * Specification that applies to Looker sysstem. Only settable when
   * `user_specified_system` is equal to `LOOKER`
   *
   * @param GoogleCloudDatacatalogV1LookerSystemSpec $lookerSystemSpec
   */
  public function setLookerSystemSpec(GoogleCloudDatacatalogV1LookerSystemSpec $lookerSystemSpec)
  {
    $this->lookerSystemSpec = $lookerSystemSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1LookerSystemSpec
   */
  public function getLookerSystemSpec()
  {
    return $this->lookerSystemSpec;
  }
  /**
   * Model specification.
   *
   * @param GoogleCloudDatacatalogV1ModelSpec $modelSpec
   */
  public function setModelSpec(GoogleCloudDatacatalogV1ModelSpec $modelSpec)
  {
    $this->modelSpec = $modelSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1ModelSpec
   */
  public function getModelSpec()
  {
    return $this->modelSpec;
  }
  /**
   * Output only. Identifier. The resource name of an entry in URL format. Note:
   * The entry itself and its child resources might not be stored in the
   * location specified in its name.
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
   * Output only. Additional information related to the entry. Private to the
   * current user.
   *
   * @param GoogleCloudDatacatalogV1PersonalDetails $personalDetails
   */
  public function setPersonalDetails(GoogleCloudDatacatalogV1PersonalDetails $personalDetails)
  {
    $this->personalDetails = $personalDetails;
  }
  /**
   * @return GoogleCloudDatacatalogV1PersonalDetails
   */
  public function getPersonalDetails()
  {
    return $this->personalDetails;
  }
  /**
   * Specification that applies to a user-defined function or procedure. Valid
   * only for entries with the `ROUTINE` type.
   *
   * @param GoogleCloudDatacatalogV1RoutineSpec $routineSpec
   */
  public function setRoutineSpec(GoogleCloudDatacatalogV1RoutineSpec $routineSpec)
  {
    $this->routineSpec = $routineSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1RoutineSpec
   */
  public function getRoutineSpec()
  {
    return $this->routineSpec;
  }
  /**
   * Schema of the entry. An entry might not have any schema attached to it.
   *
   * @param GoogleCloudDatacatalogV1Schema $schema
   */
  public function setSchema(GoogleCloudDatacatalogV1Schema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return GoogleCloudDatacatalogV1Schema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Specification that applies to a Service resource.
   *
   * @param GoogleCloudDatacatalogV1ServiceSpec $serviceSpec
   */
  public function setServiceSpec(GoogleCloudDatacatalogV1ServiceSpec $serviceSpec)
  {
    $this->serviceSpec = $serviceSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1ServiceSpec
   */
  public function getServiceSpec()
  {
    return $this->serviceSpec;
  }
  /**
   * Timestamps from the underlying resource, not from the Data Catalog entry.
   * Output only when the entry has a system listed in the `IntegratedSystem`
   * enum. For entries with `user_specified_system`, this field is optional and
   * defaults to an empty timestamp.
   *
   * @param GoogleCloudDatacatalogV1SystemTimestamps $sourceSystemTimestamps
   */
  public function setSourceSystemTimestamps(GoogleCloudDatacatalogV1SystemTimestamps $sourceSystemTimestamps)
  {
    $this->sourceSystemTimestamps = $sourceSystemTimestamps;
  }
  /**
   * @return GoogleCloudDatacatalogV1SystemTimestamps
   */
  public function getSourceSystemTimestamps()
  {
    return $this->sourceSystemTimestamps;
  }
  /**
   * Specification that applies to a relational database system. Only settable
   * when `user_specified_system` is equal to `SQL_DATABASE`
   *
   * @param GoogleCloudDatacatalogV1SqlDatabaseSystemSpec $sqlDatabaseSystemSpec
   */
  public function setSqlDatabaseSystemSpec(GoogleCloudDatacatalogV1SqlDatabaseSystemSpec $sqlDatabaseSystemSpec)
  {
    $this->sqlDatabaseSystemSpec = $sqlDatabaseSystemSpec;
  }
  /**
   * @return GoogleCloudDatacatalogV1SqlDatabaseSystemSpec
   */
  public function getSqlDatabaseSystemSpec()
  {
    return $this->sqlDatabaseSystemSpec;
  }
  /**
   * The type of the entry. For details, see [`EntryType`](#entrytype).
   *
   * Accepted values: ENTRY_TYPE_UNSPECIFIED, TABLE, MODEL, DATA_STREAM,
   * FILESET, CLUSTER, DATABASE, DATA_SOURCE_CONNECTION, ROUTINE, LAKE, ZONE,
   * SERVICE, DATABASE_SCHEMA, DASHBOARD, EXPLORE, LOOK, FEATURE_ONLINE_STORE,
   * FEATURE_VIEW, FEATURE_GROUP, GRAPH
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Resource usage statistics.
   *
   * @param GoogleCloudDatacatalogV1UsageSignal $usageSignal
   */
  public function setUsageSignal(GoogleCloudDatacatalogV1UsageSignal $usageSignal)
  {
    $this->usageSignal = $usageSignal;
  }
  /**
   * @return GoogleCloudDatacatalogV1UsageSignal
   */
  public function getUsageSignal()
  {
    return $this->usageSignal;
  }
  /**
   * Indicates the entry's source system that Data Catalog doesn't automatically
   * integrate with. The `user_specified_system` string has the following
   * limitations: * Is case insensitive. * Must begin with a letter or
   * underscore. * Can only contain letters, numbers, and underscores. * Must be
   * at least 1 character and at most 64 characters long.
   *
   * @param string $userSpecifiedSystem
   */
  public function setUserSpecifiedSystem($userSpecifiedSystem)
  {
    $this->userSpecifiedSystem = $userSpecifiedSystem;
  }
  /**
   * @return string
   */
  public function getUserSpecifiedSystem()
  {
    return $this->userSpecifiedSystem;
  }
  /**
   * Custom entry type that doesn't match any of the values allowed for input
   * and listed in the `EntryType` enum. When creating an entry, first check the
   * type values in the enum. If there are no appropriate types for the new
   * entry, provide a custom value, for example, `my_special_type`. The
   * `user_specified_type` string has the following limitations: * Is case
   * insensitive. * Must begin with a letter or underscore. * Can only contain
   * letters, numbers, and underscores. * Must be at least 1 character and at
   * most 64 characters long.
   *
   * @param string $userSpecifiedType
   */
  public function setUserSpecifiedType($userSpecifiedType)
  {
    $this->userSpecifiedType = $userSpecifiedType;
  }
  /**
   * @return string
   */
  public function getUserSpecifiedType()
  {
    return $this->userSpecifiedType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1Entry::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1Entry');
