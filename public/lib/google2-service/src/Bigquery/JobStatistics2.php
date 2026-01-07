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

class JobStatistics2 extends \Google\Collection
{
  protected $collection_key = 'undeclaredQueryParameters';
  protected $biEngineStatisticsType = BiEngineStatistics::class;
  protected $biEngineStatisticsDataType = '';
  /**
   * Output only. Billing tier for the job. This is a BigQuery-specific concept
   * which is not related to the Google Cloud notion of "free tier". The value
   * here is a measure of the query's resource consumption relative to the
   * amount of data scanned. For on-demand queries, the limit is 100, and all
   * queries within this limit are billed at the standard on-demand rates. On-
   * demand queries that exceed this limit will fail with a
   * billingTierLimitExceeded error.
   *
   * @var int
   */
  public $billingTier;
  /**
   * Output only. Whether the query result was fetched from the query cache.
   *
   * @var bool
   */
  public $cacheHit;
  protected $dclTargetDatasetType = DatasetReference::class;
  protected $dclTargetDatasetDataType = '';
  protected $dclTargetTableType = TableReference::class;
  protected $dclTargetTableDataType = '';
  protected $dclTargetViewType = TableReference::class;
  protected $dclTargetViewDataType = '';
  /**
   * Output only. The number of row access policies affected by a DDL statement.
   * Present only for DROP ALL ROW ACCESS POLICIES queries.
   *
   * @var string
   */
  public $ddlAffectedRowAccessPolicyCount;
  protected $ddlDestinationTableType = TableReference::class;
  protected $ddlDestinationTableDataType = '';
  /**
   * Output only. The DDL operation performed, possibly dependent on the pre-
   * existence of the DDL target.
   *
   * @var string
   */
  public $ddlOperationPerformed;
  protected $ddlTargetDatasetType = DatasetReference::class;
  protected $ddlTargetDatasetDataType = '';
  protected $ddlTargetRoutineType = RoutineReference::class;
  protected $ddlTargetRoutineDataType = '';
  protected $ddlTargetRowAccessPolicyType = RowAccessPolicyReference::class;
  protected $ddlTargetRowAccessPolicyDataType = '';
  protected $ddlTargetTableType = TableReference::class;
  protected $ddlTargetTableDataType = '';
  protected $dmlStatsType = DmlStatistics::class;
  protected $dmlStatsDataType = '';
  /**
   * Output only. The original estimate of bytes processed for the job.
   *
   * @var string
   */
  public $estimatedBytesProcessed;
  protected $exportDataStatisticsType = ExportDataStatistics::class;
  protected $exportDataStatisticsDataType = '';
  protected $externalServiceCostsType = ExternalServiceCost::class;
  protected $externalServiceCostsDataType = 'array';
  protected $incrementalResultStatsType = IncrementalResultStats::class;
  protected $incrementalResultStatsDataType = '';
  protected $loadQueryStatisticsType = LoadQueryStatistics::class;
  protected $loadQueryStatisticsDataType = '';
  protected $materializedViewStatisticsType = MaterializedViewStatistics::class;
  protected $materializedViewStatisticsDataType = '';
  protected $metadataCacheStatisticsType = MetadataCacheStatistics::class;
  protected $metadataCacheStatisticsDataType = '';
  protected $mlStatisticsType = MlStatistics::class;
  protected $mlStatisticsDataType = '';
  protected $modelTrainingType = BigQueryModelTraining::class;
  protected $modelTrainingDataType = '';
  /**
   * Deprecated.
   *
   * @var int
   */
  public $modelTrainingCurrentIteration;
  /**
   * Deprecated.
   *
   * @var string
   */
  public $modelTrainingExpectedTotalIteration;
  /**
   * Output only. The number of rows affected by a DML statement. Present only
   * for DML statements INSERT, UPDATE or DELETE.
   *
   * @var string
   */
  public $numDmlAffectedRows;
  protected $performanceInsightsType = PerformanceInsights::class;
  protected $performanceInsightsDataType = '';
  protected $queryInfoType = QueryInfo::class;
  protected $queryInfoDataType = '';
  protected $queryPlanType = ExplainQueryStage::class;
  protected $queryPlanDataType = 'array';
  protected $referencedRoutinesType = RoutineReference::class;
  protected $referencedRoutinesDataType = 'array';
  protected $referencedTablesType = TableReference::class;
  protected $referencedTablesDataType = 'array';
  protected $reservationUsageType = JobStatistics2ReservationUsage::class;
  protected $reservationUsageDataType = 'array';
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  protected $searchStatisticsType = SearchStatistics::class;
  protected $searchStatisticsDataType = '';
  protected $sparkStatisticsType = SparkStatistics::class;
  protected $sparkStatisticsDataType = '';
  /**
   * Output only. The type of query statement, if valid. Possible values: *
   * `SELECT`:
   * [`SELECT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/query-syntax#select_list) statement. * `ASSERT`:
   * [`ASSERT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/debugging-statements#assert) statement. * `INSERT`:
   * [`INSERT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/dml-syntax#insert_statement) statement. * `UPDATE`:
   * [`UPDATE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/dml-syntax#update_statement) statement. * `DELETE`:
   * [`DELETE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-manipulation-language) statement. * `MERGE`:
   * [`MERGE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-manipulation-language) statement. * `CREATE_TABLE`: [`CREATE
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_table_statement) statement, without `AS SELECT`.
   * * `CREATE_TABLE_AS_SELECT`: [`CREATE TABLE AS
   * SELECT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_table_statement) statement. *
   * `CREATE_VIEW`: [`CREATE
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_view_statement) statement. * `CREATE_MODEL`:
   * [`CREATE MODEL`](https://cloud.google.com/bigquery-
   * ml/docs/reference/standard-sql/bigqueryml-syntax-
   * create#create_model_statement) statement. * `CREATE_MATERIALIZED_VIEW`:
   * [`CREATE MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_materialized_view_statement) statement. *
   * `CREATE_FUNCTION`: [`CREATE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_function_statement) statement. *
   * `CREATE_TABLE_FUNCTION`: [`CREATE TABLE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_table_function_statement) statement. *
   * `CREATE_PROCEDURE`: [`CREATE
   * PROCEDURE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_procedure) statement. *
   * `CREATE_ROW_ACCESS_POLICY`: [`CREATE ROW ACCESS
   * POLICY`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_row_access_policy_statement) statement.
   * * `CREATE_SCHEMA`: [`CREATE
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_schema_statement) statement. *
   * `CREATE_SNAPSHOT_TABLE`: [`CREATE SNAPSHOT
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_snapshot_table_statement) statement. *
   * `CREATE_SEARCH_INDEX`: [`CREATE SEARCH
   * INDEX`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_search_index_statement) statement. *
   * `DROP_TABLE`: [`DROP
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_table_statement) statement. *
   * `DROP_EXTERNAL_TABLE`: [`DROP EXTERNAL
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_external_table_statement) statement. *
   * `DROP_VIEW`: [`DROP
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_view_statement) statement. * `DROP_MODEL`: [`DROP
   * MODEL`](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-drop-model) statement. * `DROP_MATERIALIZED_VIEW`:
   * [`DROP MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_materialized_view_statement) statement. *
   * `DROP_FUNCTION` : [`DROP
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_function_statement) statement. *
   * `DROP_TABLE_FUNCTION` : [`DROP TABLE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_table_function) statement. *
   * `DROP_PROCEDURE`: [`DROP
   * PROCEDURE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_procedure_statement) statement. *
   * `DROP_SEARCH_INDEX`: [`DROP SEARCH
   * INDEX`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_search_index) statement. * `DROP_SCHEMA`: [`DROP
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_schema_statement) statement. *
   * `DROP_SNAPSHOT_TABLE`: [`DROP SNAPSHOT
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_snapshot_table_statement) statement. *
   * `DROP_ROW_ACCESS_POLICY`: [`DROP [ALL] ROW ACCESS POLICY|POLICIES`](https:/
   * /cloud.google.com/bigquery/docs/reference/standard-sql/data-definition-
   * language#drop_row_access_policy_statement) statement. * `ALTER_TABLE`:
   * [`ALTER TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#alter_table_set_options_statement) statement.
   * * `ALTER_VIEW`: [`ALTER
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#alter_view_set_options_statement) statement. *
   * `ALTER_MATERIALIZED_VIEW`: [`ALTER MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#alter_materialized_view_set_options_statement)
   * statement. * `ALTER_SCHEMA`: [`ALTER
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#alter_schema_set_options_statement) statement.
   * * `SCRIPT`:
   * [`SCRIPT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/procedural-language). * `TRUNCATE_TABLE`: [`TRUNCATE
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/dml-
   * syntax#truncate_table_statement) statement. * `CREATE_EXTERNAL_TABLE`:
   * [`CREATE EXTERNAL
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_external_table_statement) statement. *
   * `EXPORT_DATA`: [`EXPORT
   * DATA`](https://cloud.google.com/bigquery/docs/reference/standard-sql/other-
   * statements#export_data_statement) statement. * `EXPORT_MODEL`: [`EXPORT
   * MODEL`](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-export-model) statement. * `LOAD_DATA`: [`LOAD
   * DATA`](https://cloud.google.com/bigquery/docs/reference/standard-sql/other-
   * statements#load_data_statement) statement. * `CALL`:
   * [`CALL`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/procedural-language#call) statement.
   *
   * @var string
   */
  public $statementType;
  protected $timelineType = QueryTimelineSample::class;
  protected $timelineDataType = 'array';
  /**
   * Output only. If the project is configured to use on-demand pricing, then
   * this field contains the total bytes billed for the job. If the project is
   * configured to use flat-rate pricing, then you are not billed for bytes and
   * this field is informational only.
   *
   * @var string
   */
  public $totalBytesBilled;
  /**
   * Output only. Total bytes processed for the job.
   *
   * @var string
   */
  public $totalBytesProcessed;
  /**
   * Output only. For dry-run jobs, totalBytesProcessed is an estimate and this
   * field specifies the accuracy of the estimate. Possible values can be:
   * UNKNOWN: accuracy of the estimate is unknown. PRECISE: estimate is precise.
   * LOWER_BOUND: estimate is lower bound of what the query would cost.
   * UPPER_BOUND: estimate is upper bound of what the query would cost.
   *
   * @var string
   */
  public $totalBytesProcessedAccuracy;
  /**
   * Output only. Total number of partitions processed from all partitioned
   * tables referenced in the job.
   *
   * @var string
   */
  public $totalPartitionsProcessed;
  /**
   * Output only. Total slot milliseconds for the job that ran on external
   * services and billed on the services SKU. This field is only populated for
   * jobs that have external service costs, and is the total of the usage for
   * costs whose billing method is `"SERVICES_SKU"`.
   *
   * @var string
   */
  public $totalServicesSkuSlotMs;
  /**
   * Output only. Slot-milliseconds for the job.
   *
   * @var string
   */
  public $totalSlotMs;
  /**
   * Output only. Total bytes transferred for cross-cloud queries such as Cross
   * Cloud Transfer and CREATE TABLE AS SELECT (CTAS).
   *
   * @var string
   */
  public $transferredBytes;
  protected $undeclaredQueryParametersType = QueryParameter::class;
  protected $undeclaredQueryParametersDataType = 'array';
  protected $vectorSearchStatisticsType = VectorSearchStatistics::class;
  protected $vectorSearchStatisticsDataType = '';

  /**
   * Output only. BI Engine specific Statistics.
   *
   * @param BiEngineStatistics $biEngineStatistics
   */
  public function setBiEngineStatistics(BiEngineStatistics $biEngineStatistics)
  {
    $this->biEngineStatistics = $biEngineStatistics;
  }
  /**
   * @return BiEngineStatistics
   */
  public function getBiEngineStatistics()
  {
    return $this->biEngineStatistics;
  }
  /**
   * Output only. Billing tier for the job. This is a BigQuery-specific concept
   * which is not related to the Google Cloud notion of "free tier". The value
   * here is a measure of the query's resource consumption relative to the
   * amount of data scanned. For on-demand queries, the limit is 100, and all
   * queries within this limit are billed at the standard on-demand rates. On-
   * demand queries that exceed this limit will fail with a
   * billingTierLimitExceeded error.
   *
   * @param int $billingTier
   */
  public function setBillingTier($billingTier)
  {
    $this->billingTier = $billingTier;
  }
  /**
   * @return int
   */
  public function getBillingTier()
  {
    return $this->billingTier;
  }
  /**
   * Output only. Whether the query result was fetched from the query cache.
   *
   * @param bool $cacheHit
   */
  public function setCacheHit($cacheHit)
  {
    $this->cacheHit = $cacheHit;
  }
  /**
   * @return bool
   */
  public function getCacheHit()
  {
    return $this->cacheHit;
  }
  /**
   * Output only. Referenced dataset for DCL statement.
   *
   * @param DatasetReference $dclTargetDataset
   */
  public function setDclTargetDataset(DatasetReference $dclTargetDataset)
  {
    $this->dclTargetDataset = $dclTargetDataset;
  }
  /**
   * @return DatasetReference
   */
  public function getDclTargetDataset()
  {
    return $this->dclTargetDataset;
  }
  /**
   * Output only. Referenced table for DCL statement.
   *
   * @param TableReference $dclTargetTable
   */
  public function setDclTargetTable(TableReference $dclTargetTable)
  {
    $this->dclTargetTable = $dclTargetTable;
  }
  /**
   * @return TableReference
   */
  public function getDclTargetTable()
  {
    return $this->dclTargetTable;
  }
  /**
   * Output only. Referenced view for DCL statement.
   *
   * @param TableReference $dclTargetView
   */
  public function setDclTargetView(TableReference $dclTargetView)
  {
    $this->dclTargetView = $dclTargetView;
  }
  /**
   * @return TableReference
   */
  public function getDclTargetView()
  {
    return $this->dclTargetView;
  }
  /**
   * Output only. The number of row access policies affected by a DDL statement.
   * Present only for DROP ALL ROW ACCESS POLICIES queries.
   *
   * @param string $ddlAffectedRowAccessPolicyCount
   */
  public function setDdlAffectedRowAccessPolicyCount($ddlAffectedRowAccessPolicyCount)
  {
    $this->ddlAffectedRowAccessPolicyCount = $ddlAffectedRowAccessPolicyCount;
  }
  /**
   * @return string
   */
  public function getDdlAffectedRowAccessPolicyCount()
  {
    return $this->ddlAffectedRowAccessPolicyCount;
  }
  /**
   * Output only. The table after rename. Present only for ALTER TABLE RENAME TO
   * query.
   *
   * @param TableReference $ddlDestinationTable
   */
  public function setDdlDestinationTable(TableReference $ddlDestinationTable)
  {
    $this->ddlDestinationTable = $ddlDestinationTable;
  }
  /**
   * @return TableReference
   */
  public function getDdlDestinationTable()
  {
    return $this->ddlDestinationTable;
  }
  /**
   * Output only. The DDL operation performed, possibly dependent on the pre-
   * existence of the DDL target.
   *
   * @param string $ddlOperationPerformed
   */
  public function setDdlOperationPerformed($ddlOperationPerformed)
  {
    $this->ddlOperationPerformed = $ddlOperationPerformed;
  }
  /**
   * @return string
   */
  public function getDdlOperationPerformed()
  {
    return $this->ddlOperationPerformed;
  }
  /**
   * Output only. The DDL target dataset. Present only for CREATE/ALTER/DROP
   * SCHEMA(dataset) queries.
   *
   * @param DatasetReference $ddlTargetDataset
   */
  public function setDdlTargetDataset(DatasetReference $ddlTargetDataset)
  {
    $this->ddlTargetDataset = $ddlTargetDataset;
  }
  /**
   * @return DatasetReference
   */
  public function getDdlTargetDataset()
  {
    return $this->ddlTargetDataset;
  }
  /**
   * Output only. [Beta] The DDL target routine. Present only for CREATE/DROP
   * FUNCTION/PROCEDURE queries.
   *
   * @param RoutineReference $ddlTargetRoutine
   */
  public function setDdlTargetRoutine(RoutineReference $ddlTargetRoutine)
  {
    $this->ddlTargetRoutine = $ddlTargetRoutine;
  }
  /**
   * @return RoutineReference
   */
  public function getDdlTargetRoutine()
  {
    return $this->ddlTargetRoutine;
  }
  /**
   * Output only. The DDL target row access policy. Present only for CREATE/DROP
   * ROW ACCESS POLICY queries.
   *
   * @param RowAccessPolicyReference $ddlTargetRowAccessPolicy
   */
  public function setDdlTargetRowAccessPolicy(RowAccessPolicyReference $ddlTargetRowAccessPolicy)
  {
    $this->ddlTargetRowAccessPolicy = $ddlTargetRowAccessPolicy;
  }
  /**
   * @return RowAccessPolicyReference
   */
  public function getDdlTargetRowAccessPolicy()
  {
    return $this->ddlTargetRowAccessPolicy;
  }
  /**
   * Output only. The DDL target table. Present only for CREATE/DROP TABLE/VIEW
   * and DROP ALL ROW ACCESS POLICIES queries.
   *
   * @param TableReference $ddlTargetTable
   */
  public function setDdlTargetTable(TableReference $ddlTargetTable)
  {
    $this->ddlTargetTable = $ddlTargetTable;
  }
  /**
   * @return TableReference
   */
  public function getDdlTargetTable()
  {
    return $this->ddlTargetTable;
  }
  /**
   * Output only. Detailed statistics for DML statements INSERT, UPDATE, DELETE,
   * MERGE or TRUNCATE.
   *
   * @param DmlStatistics $dmlStats
   */
  public function setDmlStats(DmlStatistics $dmlStats)
  {
    $this->dmlStats = $dmlStats;
  }
  /**
   * @return DmlStatistics
   */
  public function getDmlStats()
  {
    return $this->dmlStats;
  }
  /**
   * Output only. The original estimate of bytes processed for the job.
   *
   * @param string $estimatedBytesProcessed
   */
  public function setEstimatedBytesProcessed($estimatedBytesProcessed)
  {
    $this->estimatedBytesProcessed = $estimatedBytesProcessed;
  }
  /**
   * @return string
   */
  public function getEstimatedBytesProcessed()
  {
    return $this->estimatedBytesProcessed;
  }
  /**
   * Output only. Stats for EXPORT DATA statement.
   *
   * @param ExportDataStatistics $exportDataStatistics
   */
  public function setExportDataStatistics(ExportDataStatistics $exportDataStatistics)
  {
    $this->exportDataStatistics = $exportDataStatistics;
  }
  /**
   * @return ExportDataStatistics
   */
  public function getExportDataStatistics()
  {
    return $this->exportDataStatistics;
  }
  /**
   * Output only. Job cost breakdown as bigquery internal cost and external
   * service costs.
   *
   * @param ExternalServiceCost[] $externalServiceCosts
   */
  public function setExternalServiceCosts($externalServiceCosts)
  {
    $this->externalServiceCosts = $externalServiceCosts;
  }
  /**
   * @return ExternalServiceCost[]
   */
  public function getExternalServiceCosts()
  {
    return $this->externalServiceCosts;
  }
  /**
   * Output only. Statistics related to incremental query results, if enabled
   * for the query. This feature is not yet available.
   *
   * @param IncrementalResultStats $incrementalResultStats
   */
  public function setIncrementalResultStats(IncrementalResultStats $incrementalResultStats)
  {
    $this->incrementalResultStats = $incrementalResultStats;
  }
  /**
   * @return IncrementalResultStats
   */
  public function getIncrementalResultStats()
  {
    return $this->incrementalResultStats;
  }
  /**
   * Output only. Statistics for a LOAD query.
   *
   * @param LoadQueryStatistics $loadQueryStatistics
   */
  public function setLoadQueryStatistics(LoadQueryStatistics $loadQueryStatistics)
  {
    $this->loadQueryStatistics = $loadQueryStatistics;
  }
  /**
   * @return LoadQueryStatistics
   */
  public function getLoadQueryStatistics()
  {
    return $this->loadQueryStatistics;
  }
  /**
   * Output only. Statistics of materialized views of a query job.
   *
   * @param MaterializedViewStatistics $materializedViewStatistics
   */
  public function setMaterializedViewStatistics(MaterializedViewStatistics $materializedViewStatistics)
  {
    $this->materializedViewStatistics = $materializedViewStatistics;
  }
  /**
   * @return MaterializedViewStatistics
   */
  public function getMaterializedViewStatistics()
  {
    return $this->materializedViewStatistics;
  }
  /**
   * Output only. Statistics of metadata cache usage in a query for BigLake
   * tables.
   *
   * @param MetadataCacheStatistics $metadataCacheStatistics
   */
  public function setMetadataCacheStatistics(MetadataCacheStatistics $metadataCacheStatistics)
  {
    $this->metadataCacheStatistics = $metadataCacheStatistics;
  }
  /**
   * @return MetadataCacheStatistics
   */
  public function getMetadataCacheStatistics()
  {
    return $this->metadataCacheStatistics;
  }
  /**
   * Output only. Statistics of a BigQuery ML training job.
   *
   * @param MlStatistics $mlStatistics
   */
  public function setMlStatistics(MlStatistics $mlStatistics)
  {
    $this->mlStatistics = $mlStatistics;
  }
  /**
   * @return MlStatistics
   */
  public function getMlStatistics()
  {
    return $this->mlStatistics;
  }
  /**
   * Deprecated.
   *
   * @param BigQueryModelTraining $modelTraining
   */
  public function setModelTraining(BigQueryModelTraining $modelTraining)
  {
    $this->modelTraining = $modelTraining;
  }
  /**
   * @return BigQueryModelTraining
   */
  public function getModelTraining()
  {
    return $this->modelTraining;
  }
  /**
   * Deprecated.
   *
   * @param int $modelTrainingCurrentIteration
   */
  public function setModelTrainingCurrentIteration($modelTrainingCurrentIteration)
  {
    $this->modelTrainingCurrentIteration = $modelTrainingCurrentIteration;
  }
  /**
   * @return int
   */
  public function getModelTrainingCurrentIteration()
  {
    return $this->modelTrainingCurrentIteration;
  }
  /**
   * Deprecated.
   *
   * @param string $modelTrainingExpectedTotalIteration
   */
  public function setModelTrainingExpectedTotalIteration($modelTrainingExpectedTotalIteration)
  {
    $this->modelTrainingExpectedTotalIteration = $modelTrainingExpectedTotalIteration;
  }
  /**
   * @return string
   */
  public function getModelTrainingExpectedTotalIteration()
  {
    return $this->modelTrainingExpectedTotalIteration;
  }
  /**
   * Output only. The number of rows affected by a DML statement. Present only
   * for DML statements INSERT, UPDATE or DELETE.
   *
   * @param string $numDmlAffectedRows
   */
  public function setNumDmlAffectedRows($numDmlAffectedRows)
  {
    $this->numDmlAffectedRows = $numDmlAffectedRows;
  }
  /**
   * @return string
   */
  public function getNumDmlAffectedRows()
  {
    return $this->numDmlAffectedRows;
  }
  /**
   * Output only. Performance insights.
   *
   * @param PerformanceInsights $performanceInsights
   */
  public function setPerformanceInsights(PerformanceInsights $performanceInsights)
  {
    $this->performanceInsights = $performanceInsights;
  }
  /**
   * @return PerformanceInsights
   */
  public function getPerformanceInsights()
  {
    return $this->performanceInsights;
  }
  /**
   * Output only. Query optimization information for a QUERY job.
   *
   * @param QueryInfo $queryInfo
   */
  public function setQueryInfo(QueryInfo $queryInfo)
  {
    $this->queryInfo = $queryInfo;
  }
  /**
   * @return QueryInfo
   */
  public function getQueryInfo()
  {
    return $this->queryInfo;
  }
  /**
   * Output only. Describes execution plan for the query.
   *
   * @param ExplainQueryStage[] $queryPlan
   */
  public function setQueryPlan($queryPlan)
  {
    $this->queryPlan = $queryPlan;
  }
  /**
   * @return ExplainQueryStage[]
   */
  public function getQueryPlan()
  {
    return $this->queryPlan;
  }
  /**
   * Output only. Referenced routines for the job.
   *
   * @param RoutineReference[] $referencedRoutines
   */
  public function setReferencedRoutines($referencedRoutines)
  {
    $this->referencedRoutines = $referencedRoutines;
  }
  /**
   * @return RoutineReference[]
   */
  public function getReferencedRoutines()
  {
    return $this->referencedRoutines;
  }
  /**
   * Output only. Referenced tables for the job.
   *
   * @param TableReference[] $referencedTables
   */
  public function setReferencedTables($referencedTables)
  {
    $this->referencedTables = $referencedTables;
  }
  /**
   * @return TableReference[]
   */
  public function getReferencedTables()
  {
    return $this->referencedTables;
  }
  /**
   * Output only. Job resource usage breakdown by reservation. This field
   * reported misleading information and will no longer be populated.
   *
   * @deprecated
   * @param JobStatistics2ReservationUsage[] $reservationUsage
   */
  public function setReservationUsage($reservationUsage)
  {
    $this->reservationUsage = $reservationUsage;
  }
  /**
   * @deprecated
   * @return JobStatistics2ReservationUsage[]
   */
  public function getReservationUsage()
  {
    return $this->reservationUsage;
  }
  /**
   * Output only. The schema of the results. Present only for successful dry run
   * of non-legacy SQL queries.
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
   * Output only. Search query specific statistics.
   *
   * @param SearchStatistics $searchStatistics
   */
  public function setSearchStatistics(SearchStatistics $searchStatistics)
  {
    $this->searchStatistics = $searchStatistics;
  }
  /**
   * @return SearchStatistics
   */
  public function getSearchStatistics()
  {
    return $this->searchStatistics;
  }
  /**
   * Output only. Statistics of a Spark procedure job.
   *
   * @param SparkStatistics $sparkStatistics
   */
  public function setSparkStatistics(SparkStatistics $sparkStatistics)
  {
    $this->sparkStatistics = $sparkStatistics;
  }
  /**
   * @return SparkStatistics
   */
  public function getSparkStatistics()
  {
    return $this->sparkStatistics;
  }
  /**
   * Output only. The type of query statement, if valid. Possible values: *
   * `SELECT`:
   * [`SELECT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/query-syntax#select_list) statement. * `ASSERT`:
   * [`ASSERT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/debugging-statements#assert) statement. * `INSERT`:
   * [`INSERT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/dml-syntax#insert_statement) statement. * `UPDATE`:
   * [`UPDATE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/dml-syntax#update_statement) statement. * `DELETE`:
   * [`DELETE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-manipulation-language) statement. * `MERGE`:
   * [`MERGE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-manipulation-language) statement. * `CREATE_TABLE`: [`CREATE
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_table_statement) statement, without `AS SELECT`.
   * * `CREATE_TABLE_AS_SELECT`: [`CREATE TABLE AS
   * SELECT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_table_statement) statement. *
   * `CREATE_VIEW`: [`CREATE
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_view_statement) statement. * `CREATE_MODEL`:
   * [`CREATE MODEL`](https://cloud.google.com/bigquery-
   * ml/docs/reference/standard-sql/bigqueryml-syntax-
   * create#create_model_statement) statement. * `CREATE_MATERIALIZED_VIEW`:
   * [`CREATE MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_materialized_view_statement) statement. *
   * `CREATE_FUNCTION`: [`CREATE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_function_statement) statement. *
   * `CREATE_TABLE_FUNCTION`: [`CREATE TABLE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_table_function_statement) statement. *
   * `CREATE_PROCEDURE`: [`CREATE
   * PROCEDURE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_procedure) statement. *
   * `CREATE_ROW_ACCESS_POLICY`: [`CREATE ROW ACCESS
   * POLICY`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_row_access_policy_statement) statement.
   * * `CREATE_SCHEMA`: [`CREATE
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#create_schema_statement) statement. *
   * `CREATE_SNAPSHOT_TABLE`: [`CREATE SNAPSHOT
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_snapshot_table_statement) statement. *
   * `CREATE_SEARCH_INDEX`: [`CREATE SEARCH
   * INDEX`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_search_index_statement) statement. *
   * `DROP_TABLE`: [`DROP
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_table_statement) statement. *
   * `DROP_EXTERNAL_TABLE`: [`DROP EXTERNAL
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_external_table_statement) statement. *
   * `DROP_VIEW`: [`DROP
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_view_statement) statement. * `DROP_MODEL`: [`DROP
   * MODEL`](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-drop-model) statement. * `DROP_MATERIALIZED_VIEW`:
   * [`DROP MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_materialized_view_statement) statement. *
   * `DROP_FUNCTION` : [`DROP
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_function_statement) statement. *
   * `DROP_TABLE_FUNCTION` : [`DROP TABLE
   * FUNCTION`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_table_function) statement. *
   * `DROP_PROCEDURE`: [`DROP
   * PROCEDURE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_procedure_statement) statement. *
   * `DROP_SEARCH_INDEX`: [`DROP SEARCH
   * INDEX`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_search_index) statement. * `DROP_SCHEMA`: [`DROP
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#drop_schema_statement) statement. *
   * `DROP_SNAPSHOT_TABLE`: [`DROP SNAPSHOT
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#drop_snapshot_table_statement) statement. *
   * `DROP_ROW_ACCESS_POLICY`: [`DROP [ALL] ROW ACCESS POLICY|POLICIES`](https:/
   * /cloud.google.com/bigquery/docs/reference/standard-sql/data-definition-
   * language#drop_row_access_policy_statement) statement. * `ALTER_TABLE`:
   * [`ALTER TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#alter_table_set_options_statement) statement.
   * * `ALTER_VIEW`: [`ALTER
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#alter_view_set_options_statement) statement. *
   * `ALTER_MATERIALIZED_VIEW`: [`ALTER MATERIALIZED
   * VIEW`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#alter_materialized_view_set_options_statement)
   * statement. * `ALTER_SCHEMA`: [`ALTER
   * SCHEMA`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/data-definition-language#alter_schema_set_options_statement) statement.
   * * `SCRIPT`:
   * [`SCRIPT`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/procedural-language). * `TRUNCATE_TABLE`: [`TRUNCATE
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/dml-
   * syntax#truncate_table_statement) statement. * `CREATE_EXTERNAL_TABLE`:
   * [`CREATE EXTERNAL
   * TABLE`](https://cloud.google.com/bigquery/docs/reference/standard-sql/data-
   * definition-language#create_external_table_statement) statement. *
   * `EXPORT_DATA`: [`EXPORT
   * DATA`](https://cloud.google.com/bigquery/docs/reference/standard-sql/other-
   * statements#export_data_statement) statement. * `EXPORT_MODEL`: [`EXPORT
   * MODEL`](https://cloud.google.com/bigquery-ml/docs/reference/standard-
   * sql/bigqueryml-syntax-export-model) statement. * `LOAD_DATA`: [`LOAD
   * DATA`](https://cloud.google.com/bigquery/docs/reference/standard-sql/other-
   * statements#load_data_statement) statement. * `CALL`:
   * [`CALL`](https://cloud.google.com/bigquery/docs/reference/standard-
   * sql/procedural-language#call) statement.
   *
   * @param string $statementType
   */
  public function setStatementType($statementType)
  {
    $this->statementType = $statementType;
  }
  /**
   * @return string
   */
  public function getStatementType()
  {
    return $this->statementType;
  }
  /**
   * Output only. Describes a timeline of job execution.
   *
   * @param QueryTimelineSample[] $timeline
   */
  public function setTimeline($timeline)
  {
    $this->timeline = $timeline;
  }
  /**
   * @return QueryTimelineSample[]
   */
  public function getTimeline()
  {
    return $this->timeline;
  }
  /**
   * Output only. If the project is configured to use on-demand pricing, then
   * this field contains the total bytes billed for the job. If the project is
   * configured to use flat-rate pricing, then you are not billed for bytes and
   * this field is informational only.
   *
   * @param string $totalBytesBilled
   */
  public function setTotalBytesBilled($totalBytesBilled)
  {
    $this->totalBytesBilled = $totalBytesBilled;
  }
  /**
   * @return string
   */
  public function getTotalBytesBilled()
  {
    return $this->totalBytesBilled;
  }
  /**
   * Output only. Total bytes processed for the job.
   *
   * @param string $totalBytesProcessed
   */
  public function setTotalBytesProcessed($totalBytesProcessed)
  {
    $this->totalBytesProcessed = $totalBytesProcessed;
  }
  /**
   * @return string
   */
  public function getTotalBytesProcessed()
  {
    return $this->totalBytesProcessed;
  }
  /**
   * Output only. For dry-run jobs, totalBytesProcessed is an estimate and this
   * field specifies the accuracy of the estimate. Possible values can be:
   * UNKNOWN: accuracy of the estimate is unknown. PRECISE: estimate is precise.
   * LOWER_BOUND: estimate is lower bound of what the query would cost.
   * UPPER_BOUND: estimate is upper bound of what the query would cost.
   *
   * @param string $totalBytesProcessedAccuracy
   */
  public function setTotalBytesProcessedAccuracy($totalBytesProcessedAccuracy)
  {
    $this->totalBytesProcessedAccuracy = $totalBytesProcessedAccuracy;
  }
  /**
   * @return string
   */
  public function getTotalBytesProcessedAccuracy()
  {
    return $this->totalBytesProcessedAccuracy;
  }
  /**
   * Output only. Total number of partitions processed from all partitioned
   * tables referenced in the job.
   *
   * @param string $totalPartitionsProcessed
   */
  public function setTotalPartitionsProcessed($totalPartitionsProcessed)
  {
    $this->totalPartitionsProcessed = $totalPartitionsProcessed;
  }
  /**
   * @return string
   */
  public function getTotalPartitionsProcessed()
  {
    return $this->totalPartitionsProcessed;
  }
  /**
   * Output only. Total slot milliseconds for the job that ran on external
   * services and billed on the services SKU. This field is only populated for
   * jobs that have external service costs, and is the total of the usage for
   * costs whose billing method is `"SERVICES_SKU"`.
   *
   * @param string $totalServicesSkuSlotMs
   */
  public function setTotalServicesSkuSlotMs($totalServicesSkuSlotMs)
  {
    $this->totalServicesSkuSlotMs = $totalServicesSkuSlotMs;
  }
  /**
   * @return string
   */
  public function getTotalServicesSkuSlotMs()
  {
    return $this->totalServicesSkuSlotMs;
  }
  /**
   * Output only. Slot-milliseconds for the job.
   *
   * @param string $totalSlotMs
   */
  public function setTotalSlotMs($totalSlotMs)
  {
    $this->totalSlotMs = $totalSlotMs;
  }
  /**
   * @return string
   */
  public function getTotalSlotMs()
  {
    return $this->totalSlotMs;
  }
  /**
   * Output only. Total bytes transferred for cross-cloud queries such as Cross
   * Cloud Transfer and CREATE TABLE AS SELECT (CTAS).
   *
   * @param string $transferredBytes
   */
  public function setTransferredBytes($transferredBytes)
  {
    $this->transferredBytes = $transferredBytes;
  }
  /**
   * @return string
   */
  public function getTransferredBytes()
  {
    return $this->transferredBytes;
  }
  /**
   * Output only. GoogleSQL only: list of undeclared query parameters detected
   * during a dry run validation.
   *
   * @param QueryParameter[] $undeclaredQueryParameters
   */
  public function setUndeclaredQueryParameters($undeclaredQueryParameters)
  {
    $this->undeclaredQueryParameters = $undeclaredQueryParameters;
  }
  /**
   * @return QueryParameter[]
   */
  public function getUndeclaredQueryParameters()
  {
    return $this->undeclaredQueryParameters;
  }
  /**
   * Output only. Vector Search query specific statistics.
   *
   * @param VectorSearchStatistics $vectorSearchStatistics
   */
  public function setVectorSearchStatistics(VectorSearchStatistics $vectorSearchStatistics)
  {
    $this->vectorSearchStatistics = $vectorSearchStatistics;
  }
  /**
   * @return VectorSearchStatistics
   */
  public function getVectorSearchStatistics()
  {
    return $this->vectorSearchStatistics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JobStatistics2::class, 'Google_Service_Bigquery_JobStatistics2');
