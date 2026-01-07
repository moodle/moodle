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

class QueryRequest extends \Google\Collection
{
  /**
   * If unspecified JOB_CREATION_REQUIRED is the default.
   */
  public const JOB_CREATION_MODE_JOB_CREATION_MODE_UNSPECIFIED = 'JOB_CREATION_MODE_UNSPECIFIED';
  /**
   * Default. Job creation is always required.
   */
  public const JOB_CREATION_MODE_JOB_CREATION_REQUIRED = 'JOB_CREATION_REQUIRED';
  /**
   * Job creation is optional. Returning immediate results is prioritized.
   * BigQuery will automatically determine if a Job needs to be created. The
   * conditions under which BigQuery can decide to not create a Job are subject
   * to change. If Job creation is required, JOB_CREATION_REQUIRED mode should
   * be used, which is the default.
   */
  public const JOB_CREATION_MODE_JOB_CREATION_OPTIONAL = 'JOB_CREATION_OPTIONAL';
  protected $collection_key = 'queryParameters';
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
   * Optional. If true, creates a new session using a randomly generated
   * session_id. If false, runs query with an existing session_id passed in
   * ConnectionProperty, otherwise runs query in non-session mode. The session
   * location will be set to QueryRequest.location if it is present, otherwise
   * it's set to the default location based on existing routing logic.
   *
   * @var bool
   */
  public $createSession;
  protected $defaultDatasetType = DatasetReference::class;
  protected $defaultDatasetDataType = '';
  protected $destinationEncryptionConfigurationType = EncryptionConfiguration::class;
  protected $destinationEncryptionConfigurationDataType = '';
  /**
   * Optional. If set to true, BigQuery doesn't run the job. Instead, if the
   * query is valid, BigQuery returns statistics about the job such as how many
   * bytes would be processed. If the query is invalid, an error returns. The
   * default value is false.
   *
   * @var bool
   */
  public $dryRun;
  protected $formatOptionsType = DataFormatOptions::class;
  protected $formatOptionsDataType = '';
  /**
   * Optional. If not set, jobs are always required. If set, the query request
   * will follow the behavior described JobCreationMode.
   *
   * @var string
   */
  public $jobCreationMode;
  /**
   * Optional. Job timeout in milliseconds. If this time limit is exceeded,
   * BigQuery will attempt to stop a longer job, but may not always succeed in
   * canceling it before the job completes. For example, a job that takes more
   * than 60 seconds to complete has a better chance of being stopped than a job
   * that takes 10 seconds to complete. This timeout applies to the query even
   * if a job does not need to be created.
   *
   * @var string
   */
  public $jobTimeoutMs;
  /**
   * The resource type of the request.
   *
   * @var string
   */
  public $kind;
  /**
   * Optional. The labels associated with this query. Labels can be used to
   * organize and group query jobs. Label keys and values can be no longer than
   * 63 characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label keys
   * must start with a letter and each label in the list must have a different
   * key.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The geographic location where the job should run. For more information, see
   * how to [specify locations](https://cloud.google.com/bigquery/docs/locations
   * #specify_locations).
   *
   * @var string
   */
  public $location;
  /**
   * Optional. The maximum number of rows of data to return per page of results.
   * Setting this flag to a small value such as 1000 and then paging through
   * results might improve reliability when the query result set is large. In
   * addition to this limit, responses are also limited to 10 MB. By default,
   * there is no maximum row count, and only the byte limit applies.
   *
   * @var string
   */
  public $maxResults;
  /**
   * Optional. A target limit on the rate of slot consumption by this query. If
   * set to a value > 0, BigQuery will attempt to limit the rate of slot
   * consumption by this query to keep it below the configured limit, even if
   * the query is eligible for more slots based on fair scheduling. The unused
   * slots will be available for other jobs and queries to use. Note: This
   * feature is not yet generally available.
   *
   * @var int
   */
  public $maxSlots;
  /**
   * Optional. Limits the bytes billed for this query. Queries with bytes billed
   * above this limit will fail (without incurring a charge). If unspecified,
   * the project default is used.
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
   * This property is deprecated.
   *
   * @deprecated
   * @var bool
   */
  public $preserveNulls;
  /**
   * Required. A query string to execute, using Google Standard SQL or legacy
   * SQL syntax. Example: "SELECT COUNT(f1) FROM
   * myProjectId.myDatasetId.myTableId".
   *
   * @var string
   */
  public $query;
  protected $queryParametersType = QueryParameter::class;
  protected $queryParametersDataType = 'array';
  /**
   * Optional. A unique user provided identifier to ensure idempotent behavior
   * for queries. Note that this is different from the job_id. It has the
   * following properties: 1. It is case-sensitive, limited to up to 36 ASCII
   * characters. A UUID is recommended. 2. Read only queries can ignore this
   * token since they are nullipotent by definition. 3. For the purposes of
   * idempotency ensured by the request_id, a request is considered duplicate of
   * another only if they have the same request_id and are actually duplicates.
   * When determining whether a request is a duplicate of another request, all
   * parameters in the request that may affect the result are considered. For
   * example, query, connection_properties, query_parameters, use_legacy_sql are
   * parameters that affect the result and are considered when determining
   * whether a request is a duplicate, but properties like timeout_ms don't
   * affect the result and are thus not considered. Dry run query requests are
   * never considered duplicate of another request. 4. When a duplicate mutating
   * query request is detected, it returns: a. the results of the mutation if it
   * completes successfully within the timeout. b. the running operation if it
   * is still in progress at the end of the timeout. 5. Its lifetime is limited
   * to 15 minutes. In other words, if two requests are sent with the same
   * request_id, but more than 15 minutes apart, idempotency is not guaranteed.
   *
   * @var string
   */
  public $requestId;
  /**
   * Optional. The reservation that jobs.query request would use. User can
   * specify a reservation to execute the job.query. The expected format is
   * `projects/{project}/locations/{location}/reservations/{reservation}`.
   *
   * @var string
   */
  public $reservation;
  /**
   * Optional. Optional: Specifies the maximum amount of time, in milliseconds,
   * that the client is willing to wait for the query to complete. By default,
   * this limit is 10 seconds (10,000 milliseconds). If the query is complete,
   * the jobComplete field in the response is true. If the query has not yet
   * completed, jobComplete is false. You can request a longer timeout period in
   * the timeoutMs field. However, the call is not guaranteed to wait for the
   * specified timeout; it typically returns after around 200 seconds (200,000
   * milliseconds), even if the query is not complete. If jobComplete is false,
   * you can continue to wait for the query to complete by calling the
   * getQueryResults method until the jobComplete field in the getQueryResults
   * response is true.
   *
   * @var string
   */
  public $timeoutMs;
  /**
   * Specifies whether to use BigQuery's legacy SQL dialect for this query. The
   * default value is true. If set to false, the query will use BigQuery's
   * GoogleSQL: https://cloud.google.com/bigquery/sql-reference/ When
   * useLegacySql is set to false, the value of flattenResults is ignored; query
   * will be run as if flattenResults is false.
   *
   * @var bool
   */
  public $useLegacySql;
  /**
   * Optional. Whether to look for the result in the query cache. The query
   * cache is a best-effort cache that will be flushed whenever tables in the
   * query are modified. The default value is true.
   *
   * @var bool
   */
  public $useQueryCache;
  /**
   * Optional. This is only supported for SELECT query. If set, the query is
   * allowed to write results incrementally to the temporary result table. This
   * may incur a performance penalty. This option cannot be used with Legacy
   * SQL. This feature is not yet available.
   *
   * @var bool
   */
  public $writeIncrementalResults;

  /**
   * Optional. Connection properties which can modify the query behavior.
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
   * Optional. If true, creates a new session using a randomly generated
   * session_id. If false, runs query with an existing session_id passed in
   * ConnectionProperty, otherwise runs query in non-session mode. The session
   * location will be set to QueryRequest.location if it is present, otherwise
   * it's set to the default location based on existing routing logic.
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
   * Optional. Specifies the default datasetId and projectId to assume for any
   * unqualified table names in the query. If not set, all table names in the
   * query string must be qualified in the format 'datasetId.tableId'.
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
   * Optional. Custom encryption configuration (e.g., Cloud KMS keys)
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
   * Optional. If set to true, BigQuery doesn't run the job. Instead, if the
   * query is valid, BigQuery returns statistics about the job such as how many
   * bytes would be processed. If the query is invalid, an error returns. The
   * default value is false.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * Optional. Output format adjustments.
   *
   * @param DataFormatOptions $formatOptions
   */
  public function setFormatOptions(DataFormatOptions $formatOptions)
  {
    $this->formatOptions = $formatOptions;
  }
  /**
   * @return DataFormatOptions
   */
  public function getFormatOptions()
  {
    return $this->formatOptions;
  }
  /**
   * Optional. If not set, jobs are always required. If set, the query request
   * will follow the behavior described JobCreationMode.
   *
   * Accepted values: JOB_CREATION_MODE_UNSPECIFIED, JOB_CREATION_REQUIRED,
   * JOB_CREATION_OPTIONAL
   *
   * @param self::JOB_CREATION_MODE_* $jobCreationMode
   */
  public function setJobCreationMode($jobCreationMode)
  {
    $this->jobCreationMode = $jobCreationMode;
  }
  /**
   * @return self::JOB_CREATION_MODE_*
   */
  public function getJobCreationMode()
  {
    return $this->jobCreationMode;
  }
  /**
   * Optional. Job timeout in milliseconds. If this time limit is exceeded,
   * BigQuery will attempt to stop a longer job, but may not always succeed in
   * canceling it before the job completes. For example, a job that takes more
   * than 60 seconds to complete has a better chance of being stopped than a job
   * that takes 10 seconds to complete. This timeout applies to the query even
   * if a job does not need to be created.
   *
   * @param string $jobTimeoutMs
   */
  public function setJobTimeoutMs($jobTimeoutMs)
  {
    $this->jobTimeoutMs = $jobTimeoutMs;
  }
  /**
   * @return string
   */
  public function getJobTimeoutMs()
  {
    return $this->jobTimeoutMs;
  }
  /**
   * The resource type of the request.
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
   * Optional. The labels associated with this query. Labels can be used to
   * organize and group query jobs. Label keys and values can be no longer than
   * 63 characters, can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. Label keys
   * must start with a letter and each label in the list must have a different
   * key.
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
   * The geographic location where the job should run. For more information, see
   * how to [specify locations](https://cloud.google.com/bigquery/docs/locations
   * #specify_locations).
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
   * Optional. The maximum number of rows of data to return per page of results.
   * Setting this flag to a small value such as 1000 and then paging through
   * results might improve reliability when the query result set is large. In
   * addition to this limit, responses are also limited to 10 MB. By default,
   * there is no maximum row count, and only the byte limit applies.
   *
   * @param string $maxResults
   */
  public function setMaxResults($maxResults)
  {
    $this->maxResults = $maxResults;
  }
  /**
   * @return string
   */
  public function getMaxResults()
  {
    return $this->maxResults;
  }
  /**
   * Optional. A target limit on the rate of slot consumption by this query. If
   * set to a value > 0, BigQuery will attempt to limit the rate of slot
   * consumption by this query to keep it below the configured limit, even if
   * the query is eligible for more slots based on fair scheduling. The unused
   * slots will be available for other jobs and queries to use. Note: This
   * feature is not yet generally available.
   *
   * @param int $maxSlots
   */
  public function setMaxSlots($maxSlots)
  {
    $this->maxSlots = $maxSlots;
  }
  /**
   * @return int
   */
  public function getMaxSlots()
  {
    return $this->maxSlots;
  }
  /**
   * Optional. Limits the bytes billed for this query. Queries with bytes billed
   * above this limit will fail (without incurring a charge). If unspecified,
   * the project default is used.
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
   * This property is deprecated.
   *
   * @deprecated
   * @param bool $preserveNulls
   */
  public function setPreserveNulls($preserveNulls)
  {
    $this->preserveNulls = $preserveNulls;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPreserveNulls()
  {
    return $this->preserveNulls;
  }
  /**
   * Required. A query string to execute, using Google Standard SQL or legacy
   * SQL syntax. Example: "SELECT COUNT(f1) FROM
   * myProjectId.myDatasetId.myTableId".
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
   * Optional. A unique user provided identifier to ensure idempotent behavior
   * for queries. Note that this is different from the job_id. It has the
   * following properties: 1. It is case-sensitive, limited to up to 36 ASCII
   * characters. A UUID is recommended. 2. Read only queries can ignore this
   * token since they are nullipotent by definition. 3. For the purposes of
   * idempotency ensured by the request_id, a request is considered duplicate of
   * another only if they have the same request_id and are actually duplicates.
   * When determining whether a request is a duplicate of another request, all
   * parameters in the request that may affect the result are considered. For
   * example, query, connection_properties, query_parameters, use_legacy_sql are
   * parameters that affect the result and are considered when determining
   * whether a request is a duplicate, but properties like timeout_ms don't
   * affect the result and are thus not considered. Dry run query requests are
   * never considered duplicate of another request. 4. When a duplicate mutating
   * query request is detected, it returns: a. the results of the mutation if it
   * completes successfully within the timeout. b. the running operation if it
   * is still in progress at the end of the timeout. 5. Its lifetime is limited
   * to 15 minutes. In other words, if two requests are sent with the same
   * request_id, but more than 15 minutes apart, idempotency is not guaranteed.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * Optional. The reservation that jobs.query request would use. User can
   * specify a reservation to execute the job.query. The expected format is
   * `projects/{project}/locations/{location}/reservations/{reservation}`.
   *
   * @param string $reservation
   */
  public function setReservation($reservation)
  {
    $this->reservation = $reservation;
  }
  /**
   * @return string
   */
  public function getReservation()
  {
    return $this->reservation;
  }
  /**
   * Optional. Optional: Specifies the maximum amount of time, in milliseconds,
   * that the client is willing to wait for the query to complete. By default,
   * this limit is 10 seconds (10,000 milliseconds). If the query is complete,
   * the jobComplete field in the response is true. If the query has not yet
   * completed, jobComplete is false. You can request a longer timeout period in
   * the timeoutMs field. However, the call is not guaranteed to wait for the
   * specified timeout; it typically returns after around 200 seconds (200,000
   * milliseconds), even if the query is not complete. If jobComplete is false,
   * you can continue to wait for the query to complete by calling the
   * getQueryResults method until the jobComplete field in the getQueryResults
   * response is true.
   *
   * @param string $timeoutMs
   */
  public function setTimeoutMs($timeoutMs)
  {
    $this->timeoutMs = $timeoutMs;
  }
  /**
   * @return string
   */
  public function getTimeoutMs()
  {
    return $this->timeoutMs;
  }
  /**
   * Specifies whether to use BigQuery's legacy SQL dialect for this query. The
   * default value is true. If set to false, the query will use BigQuery's
   * GoogleSQL: https://cloud.google.com/bigquery/sql-reference/ When
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
   * query are modified. The default value is true.
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
   * Optional. This is only supported for SELECT query. If set, the query is
   * allowed to write results incrementally to the temporary result table. This
   * may incur a performance penalty. This option cannot be used with Legacy
   * SQL. This feature is not yet available.
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
class_alias(QueryRequest::class, 'Google_Service_Bigquery_QueryRequest');
