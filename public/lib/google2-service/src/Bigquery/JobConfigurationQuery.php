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

class JobConfigurationQuery extends \Google\Collection
{
  protected $collection_key = 'userDefinedFunctionResources';
  /**
   * Optional. If true and query uses legacy SQL dialect, allows the query to
   * produce arbitrarily large result tables at a slight cost in performance.
   * Requires destinationTable to be set. For GoogleSQL queries, this flag is
   * ignored and large results are always allowed. However, you must still set
   * destinationTable when result size exceeds the allowed maximum response
   * size.
   *
   * @var bool
   */
  public $allowLargeResults;
  protected $clusteringType = Clustering::class;
  protected $clusteringDataType = '';
  protected $connectionPropertiesType = ConnectionProperty::class;
  protected $connectionPropertiesDataType = 'array';
  /**
   * [Optional] Specifies whether the query should be executed as a continuous
   * query. The default value is false.
   *
   * @var bool
   */
  public $continuous;
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
  /**
   * If this property is true, the job creates a new session using a randomly
   * generated session_id. To continue using a created session with subsequent
   * queries, pass the existing session identifier as a `ConnectionProperty`
   * value. The session identifier is returned as part of the `SessionInfo`
   * message within the query statistics. The new session's location will be set
   * to `Job.JobReference.location` if it is present, otherwise it's set to the
   * default location based on existing routing logic.
   *
   * @var bool
   */
  public $createSession;
  protected $defaultDatasetType = DatasetReference::class;
  protected $defaultDatasetDataType = '';
  protected $destinationEncryptionConfigurationType = EncryptionConfiguration::class;
  protected $destinationEncryptionConfigurationDataType = '';
  protected $destinationTableType = TableReference::class;
  protected $destinationTableDataType = '';
  /**
   * Optional. If true and query uses legacy SQL dialect, flattens all nested
   * and repeated fields in the query results. allowLargeResults must be true if
   * this is set to false. For GoogleSQL queries, this flag is ignored and
   * results are never flattened.
   *
   * @var bool
   */
  public $flattenResults;
  /**
   * Optional. [Deprecated] Maximum billing tier allowed for this query. The
   * billing tier controls the amount of compute resources allotted to the
   * query, and multiplies the on-demand cost of the query accordingly. A query
   * that runs within its allotted resources will succeed and indicate its
   * billing tier in statistics.query.billingTier, but if the query exceeds its
   * allotted resources, it will fail with billingTierLimitExceeded. WARNING:
   * The billed byte amount can be multiplied by an amount up to this number!
   * Most users should not need to alter this setting, and we recommend that you
   * avoid introducing new uses of it.
   *
   * @var int
   */
  public $maximumBillingTier;
  /**
   * Limits the bytes billed for this job. Queries that will have bytes billed
   * beyond this limit will fail (without incurring a charge). If unspecified,
   * this will be set to your project default.
   *
   * @var string
   */
  public $maximumBytesBilled;
  /**
   * GoogleSQL only. Set to POSITIONAL to use positional (?) query parameters or
   * to NAMED to use named (@myparam) query parameters in this query.
   *
   * @var string
   */
  public $parameterMode;
  /**
   * [Deprecated] This property is deprecated.
   *
   * @var bool
   */
  public $preserveNulls;
  /**
   * Optional. Specifies a priority for the query. Possible values include
   * INTERACTIVE and BATCH. The default value is INTERACTIVE.
   *
   * @var string
   */
  public $priority;
  /**
   * [Required] SQL query text to execute. The useLegacySql field can be used to
   * indicate whether the query uses legacy SQL or GoogleSQL.
   *
   * @var string
   */
  public $query;
  protected $queryParametersType = QueryParameter::class;
  protected $queryParametersDataType = 'array';
  protected $rangePartitioningType = RangePartitioning::class;
  protected $rangePartitioningDataType = '';
  /**
   * Allows the schema of the destination table to be updated as a side effect
   * of the query job. Schema update options are supported in three cases: when
   * writeDisposition is WRITE_APPEND; when writeDisposition is
   * WRITE_TRUNCATE_DATA; when writeDisposition is WRITE_TRUNCATE and the
   * destination table is a partition of a table, specified by partition
   * decorators. For normal tables, WRITE_TRUNCATE will always overwrite the
   * schema. One or more of the following values are specified: *
   * ALLOW_FIELD_ADDITION: allow adding a nullable field to the schema. *
   * ALLOW_FIELD_RELAXATION: allow relaxing a required field in the original
   * schema to nullable.
   *
   * @var string[]
   */
  public $schemaUpdateOptions;
  protected $scriptOptionsType = ScriptOptions::class;
  protected $scriptOptionsDataType = '';
  protected $systemVariablesType = SystemVariables::class;
  protected $systemVariablesDataType = '';
  protected $tableDefinitionsType = ExternalDataConfiguration::class;
  protected $tableDefinitionsDataType = 'map';
  protected $timePartitioningType = TimePartitioning::class;
  protected $timePartitioningDataType = '';
  /**
   * Optional. Specifies whether to use BigQuery's legacy SQL dialect for this
   * query. The default value is true. If set to false, the query will use
   * BigQuery's GoogleSQL: https://cloud.google.com/bigquery/sql-reference/ When
   * useLegacySql is set to false, the value of flattenResults is ignored; query
   * will be run as if flattenResults is false.
   *
   * @var bool
   */
  public $useLegacySql;
  /**
   * Optional. Whether to look for the result in the query cache. The query
   * cache is a best-effort cache that will be flushed whenever tables in the
   * query are modified. Moreover, the query cache is only available when a
   * query does not have a destination table specified. The default value is
   * true.
   *
   * @var bool
   */
  public $useQueryCache;
  protected $userDefinedFunctionResourcesType = UserDefinedFunctionResource::class;
  protected $userDefinedFunctionResourcesDataType = 'array';
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the data, removes the constraints, and
   * uses the schema from the query result. * WRITE_TRUNCATE_DATA: If the table
   * already exists, BigQuery overwrites the data, but keeps the constraints and
   * schema of the existing table. * WRITE_APPEND: If the table already exists,
   * BigQuery appends the data to the table. * WRITE_EMPTY: If the table already
   * exists and contains data, a 'duplicate' error is returned in the job
   * result. The default value is WRITE_EMPTY. Each action is atomic and only
   * occurs if BigQuery is able to complete the job successfully. Creation,
   * truncation and append actions occur as one atomic update upon job
   * completion.
   *
   * @var string
   */
  public $writeDisposition;
  /**
   * Optional. This is only supported for a SELECT query using a temporary
   * table. If set, the query is allowed to write results incrementally to the
   * temporary result table. This may incur a performance penalty. This option
   * cannot be used with Legacy SQL. This feature is not yet available.
   *
   * @var bool
   */
  public $writeIncrementalResults;

  /**
   * Optional. If true and query uses legacy SQL dialect, allows the query to
   * produce arbitrarily large result tables at a slight cost in performance.
   * Requires destinationTable to be set. For GoogleSQL queries, this flag is
   * ignored and large results are always allowed. However, you must still set
   * destinationTable when result size exceeds the allowed maximum response
   * size.
   *
   * @param bool $allowLargeResults
   */
  public function setAllowLargeResults($allowLargeResults)
  {
    $this->allowLargeResults = $allowLargeResults;
  }
  /**
   * @return bool
   */
  public function getAllowLargeResults()
  {
    return $this->allowLargeResults;
  }
  /**
   * Clustering specification for the destination table.
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
   * Connection properties which can modify the query behavior.
   *
   * @param ConnectionProperty[] $connectionProperties
   */
  public function setConnectionProperties($connectionProperties)
  {
    $this->connectionProperties = $connectionProperties;
  }
  /**
   * @return ConnectionProperty[]
   */
  public function getConnectionProperties()
  {
    return $this->connectionProperties;
  }
  /**
   * [Optional] Specifies whether the query should be executed as a continuous
   * query. The default value is false.
   *
   * @param bool $continuous
   */
  public function setContinuous($continuous)
  {
    $this->continuous = $continuous;
  }
  /**
   * @return bool
   */
  public function getContinuous()
  {
    return $this->continuous;
  }
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
   * If this property is true, the job creates a new session using a randomly
   * generated session_id. To continue using a created session with subsequent
   * queries, pass the existing session identifier as a `ConnectionProperty`
   * value. The session identifier is returned as part of the `SessionInfo`
   * message within the query statistics. The new session's location will be set
   * to `Job.JobReference.location` if it is present, otherwise it's set to the
   * default location based on existing routing logic.
   *
   * @param bool $createSession
   */
  public function setCreateSession($createSession)
  {
    $this->createSession = $createSession;
  }
  /**
   * @return bool
   */
  public function getCreateSession()
  {
    return $this->createSession;
  }
  /**
   * Optional. Specifies the default dataset to use for unqualified table names
   * in the query. This setting does not alter behavior of unqualified dataset
   * names. Setting the system variable `@@dataset_id` achieves the same
   * behavior. See https://cloud.google.com/bigquery/docs/reference/system-
   * variables for more information on system variables.
   *
   * @param DatasetReference $defaultDataset
   */
  public function setDefaultDataset(DatasetReference $defaultDataset)
  {
    $this->defaultDataset = $defaultDataset;
  }
  /**
   * @return DatasetReference
   */
  public function getDefaultDataset()
  {
    return $this->defaultDataset;
  }
  /**
   * Custom encryption configuration (e.g., Cloud KMS keys)
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
   * Optional. Describes the table where the query results should be stored.
   * This property must be set for large results that exceed the maximum
   * response size. For queries that produce anonymous (cached) results, this
   * field will be populated by BigQuery.
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
   * Optional. If true and query uses legacy SQL dialect, flattens all nested
   * and repeated fields in the query results. allowLargeResults must be true if
   * this is set to false. For GoogleSQL queries, this flag is ignored and
   * results are never flattened.
   *
   * @param bool $flattenResults
   */
  public function setFlattenResults($flattenResults)
  {
    $this->flattenResults = $flattenResults;
  }
  /**
   * @return bool
   */
  public function getFlattenResults()
  {
    return $this->flattenResults;
  }
  /**
   * Optional. [Deprecated] Maximum billing tier allowed for this query. The
   * billing tier controls the amount of compute resources allotted to the
   * query, and multiplies the on-demand cost of the query accordingly. A query
   * that runs within its allotted resources will succeed and indicate its
   * billing tier in statistics.query.billingTier, but if the query exceeds its
   * allotted resources, it will fail with billingTierLimitExceeded. WARNING:
   * The billed byte amount can be multiplied by an amount up to this number!
   * Most users should not need to alter this setting, and we recommend that you
   * avoid introducing new uses of it.
   *
   * @param int $maximumBillingTier
   */
  public function setMaximumBillingTier($maximumBillingTier)
  {
    $this->maximumBillingTier = $maximumBillingTier;
  }
  /**
   * @return int
   */
  public function getMaximumBillingTier()
  {
    return $this->maximumBillingTier;
  }
  /**
   * Limits the bytes billed for this job. Queries that will have bytes billed
   * beyond this limit will fail (without incurring a charge). If unspecified,
   * this will be set to your project default.
   *
   * @param string $maximumBytesBilled
   */
  public function setMaximumBytesBilled($maximumBytesBilled)
  {
    $this->maximumBytesBilled = $maximumBytesBilled;
  }
  /**
   * @return string
   */
  public function getMaximumBytesBilled()
  {
    return $this->maximumBytesBilled;
  }
  /**
   * GoogleSQL only. Set to POSITIONAL to use positional (?) query parameters or
   * to NAMED to use named (@myparam) query parameters in this query.
   *
   * @param string $parameterMode
   */
  public function setParameterMode($parameterMode)
  {
    $this->parameterMode = $parameterMode;
  }
  /**
   * @return string
   */
  public function getParameterMode()
  {
    return $this->parameterMode;
  }
  /**
   * [Deprecated] This property is deprecated.
   *
   * @param bool $preserveNulls
   */
  public function setPreserveNulls($preserveNulls)
  {
    $this->preserveNulls = $preserveNulls;
  }
  /**
   * @return bool
   */
  public function getPreserveNulls()
  {
    return $this->preserveNulls;
  }
  /**
   * Optional. Specifies a priority for the query. Possible values include
   * INTERACTIVE and BATCH. The default value is INTERACTIVE.
   *
   * @param string $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }
  /**
   * @return string
   */
  public function getPriority()
  {
    return $this->priority;
  }
  /**
   * [Required] SQL query text to execute. The useLegacySql field can be used to
   * indicate whether the query uses legacy SQL or GoogleSQL.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Query parameters for GoogleSQL queries.
   *
   * @param QueryParameter[] $queryParameters
   */
  public function setQueryParameters($queryParameters)
  {
    $this->queryParameters = $queryParameters;
  }
  /**
   * @return QueryParameter[]
   */
  public function getQueryParameters()
  {
    return $this->queryParameters;
  }
  /**
   * Range partitioning specification for the destination table. Only one of
   * timePartitioning and rangePartitioning should be specified.
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
   * Allows the schema of the destination table to be updated as a side effect
   * of the query job. Schema update options are supported in three cases: when
   * writeDisposition is WRITE_APPEND; when writeDisposition is
   * WRITE_TRUNCATE_DATA; when writeDisposition is WRITE_TRUNCATE and the
   * destination table is a partition of a table, specified by partition
   * decorators. For normal tables, WRITE_TRUNCATE will always overwrite the
   * schema. One or more of the following values are specified: *
   * ALLOW_FIELD_ADDITION: allow adding a nullable field to the schema. *
   * ALLOW_FIELD_RELAXATION: allow relaxing a required field in the original
   * schema to nullable.
   *
   * @param string[] $schemaUpdateOptions
   */
  public function setSchemaUpdateOptions($schemaUpdateOptions)
  {
    $this->schemaUpdateOptions = $schemaUpdateOptions;
  }
  /**
   * @return string[]
   */
  public function getSchemaUpdateOptions()
  {
    return $this->schemaUpdateOptions;
  }
  /**
   * Options controlling the execution of scripts.
   *
   * @param ScriptOptions $scriptOptions
   */
  public function setScriptOptions(ScriptOptions $scriptOptions)
  {
    $this->scriptOptions = $scriptOptions;
  }
  /**
   * @return ScriptOptions
   */
  public function getScriptOptions()
  {
    return $this->scriptOptions;
  }
  /**
   * Output only. System variables for GoogleSQL queries. A system variable is
   * output if the variable is settable and its value differs from the system
   * default. "@@" prefix is not included in the name of the System variables.
   *
   * @param SystemVariables $systemVariables
   */
  public function setSystemVariables(SystemVariables $systemVariables)
  {
    $this->systemVariables = $systemVariables;
  }
  /**
   * @return SystemVariables
   */
  public function getSystemVariables()
  {
    return $this->systemVariables;
  }
  /**
   * Optional. You can specify external table definitions, which operate as
   * ephemeral tables that can be queried. These definitions are configured
   * using a JSON map, where the string key represents the table identifier, and
   * the value is the corresponding external data configuration object.
   *
   * @param ExternalDataConfiguration[] $tableDefinitions
   */
  public function setTableDefinitions($tableDefinitions)
  {
    $this->tableDefinitions = $tableDefinitions;
  }
  /**
   * @return ExternalDataConfiguration[]
   */
  public function getTableDefinitions()
  {
    return $this->tableDefinitions;
  }
  /**
   * Time-based partitioning specification for the destination table. Only one
   * of timePartitioning and rangePartitioning should be specified.
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
   * Optional. Specifies whether to use BigQuery's legacy SQL dialect for this
   * query. The default value is true. If set to false, the query will use
   * BigQuery's GoogleSQL: https://cloud.google.com/bigquery/sql-reference/ When
   * useLegacySql is set to false, the value of flattenResults is ignored; query
   * will be run as if flattenResults is false.
   *
   * @param bool $useLegacySql
   */
  public function setUseLegacySql($useLegacySql)
  {
    $this->useLegacySql = $useLegacySql;
  }
  /**
   * @return bool
   */
  public function getUseLegacySql()
  {
    return $this->useLegacySql;
  }
  /**
   * Optional. Whether to look for the result in the query cache. The query
   * cache is a best-effort cache that will be flushed whenever tables in the
   * query are modified. Moreover, the query cache is only available when a
   * query does not have a destination table specified. The default value is
   * true.
   *
   * @param bool $useQueryCache
   */
  public function setUseQueryCache($useQueryCache)
  {
    $this->useQueryCache = $useQueryCache;
  }
  /**
   * @return bool
   */
  public function getUseQueryCache()
  {
    return $this->useQueryCache;
  }
  /**
   * Describes user-defined function resources used in the query.
   *
   * @param UserDefinedFunctionResource[] $userDefinedFunctionResources
   */
  public function setUserDefinedFunctionResources($userDefinedFunctionResources)
  {
    $this->userDefinedFunctionResources = $userDefinedFunctionResources;
  }
  /**
   * @return UserDefinedFunctionResource[]
   */
  public function getUserDefinedFunctionResources()
  {
    return $this->userDefinedFunctionResources;
  }
  /**
   * Optional. Specifies the action that occurs if the destination table already
   * exists. The following values are supported: * WRITE_TRUNCATE: If the table
   * already exists, BigQuery overwrites the data, removes the constraints, and
   * uses the schema from the query result. * WRITE_TRUNCATE_DATA: If the table
   * already exists, BigQuery overwrites the data, but keeps the constraints and
   * schema of the existing table. * WRITE_APPEND: If the table already exists,
   * BigQuery appends the data to the table. * WRITE_EMPTY: If the table already
   * exists and contains data, a 'duplicate' error is returned in the job
   * result. The default value is WRITE_EMPTY. Each action is atomic and only
   * occurs if BigQuery is able to complete the job successfully. Creation,
   * truncation and append actions occur as one atomic update upon job
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
  /**
   * Optional. This is only supported for a SELECT query using a temporary
   * table. If set, the query is allowed to write results incrementally to the
   * temporary result table. This may incur a performance penalty. This option
   * cannot be used with Legacy SQL. This feature is not yet available.
   *
   * @param bool $writeIncrementalResults
   */
  public function setWriteIncrementalResults($writeIncrementalResults)
  {
    $this->writeIncrementalResults = $writeIncrementalResults;
  }
  /**
   * @return bool
   */
  public function getWriteIncrementalResults()
  {
    return $this->writeIncrementalResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobConfigurationQuery::class, 'Google_Service_Bigquery_JobConfigurationQuery');
