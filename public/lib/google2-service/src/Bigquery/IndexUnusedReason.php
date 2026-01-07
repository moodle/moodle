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

class IndexUnusedReason extends \Google\Model
{
  /**
   * Code not specified.
   */
  public const CODE_CODE_UNSPECIFIED = 'CODE_UNSPECIFIED';
  /**
   * Indicates the search index configuration has not been created.
   */
  public const CODE_INDEX_CONFIG_NOT_AVAILABLE = 'INDEX_CONFIG_NOT_AVAILABLE';
  /**
   * Indicates the search index creation has not been completed.
   */
  public const CODE_PENDING_INDEX_CREATION = 'PENDING_INDEX_CREATION';
  /**
   * Indicates the base table has been truncated (rows have been removed from
   * table with TRUNCATE TABLE statement) since the last time the search index
   * was refreshed.
   */
  public const CODE_BASE_TABLE_TRUNCATED = 'BASE_TABLE_TRUNCATED';
  /**
   * Indicates the search index configuration has been changed since the last
   * time the search index was refreshed.
   */
  public const CODE_INDEX_CONFIG_MODIFIED = 'INDEX_CONFIG_MODIFIED';
  /**
   * Indicates the search query accesses data at a timestamp before the last
   * time the search index was refreshed.
   */
  public const CODE_TIME_TRAVEL_QUERY = 'TIME_TRAVEL_QUERY';
  /**
   * Indicates the usage of search index will not contribute to any pruning
   * improvement for the search function, e.g. when the search predicate is in a
   * disjunction with other non-search predicates.
   */
  public const CODE_NO_PRUNING_POWER = 'NO_PRUNING_POWER';
  /**
   * Indicates the search index does not cover all fields in the search
   * function.
   */
  public const CODE_UNINDEXED_SEARCH_FIELDS = 'UNINDEXED_SEARCH_FIELDS';
  /**
   * Indicates the search index does not support the given search query pattern.
   */
  public const CODE_UNSUPPORTED_SEARCH_PATTERN = 'UNSUPPORTED_SEARCH_PATTERN';
  /**
   * Indicates the query has been optimized by using a materialized view.
   */
  public const CODE_OPTIMIZED_WITH_MATERIALIZED_VIEW = 'OPTIMIZED_WITH_MATERIALIZED_VIEW';
  /**
   * Indicates the query has been secured by data masking, and thus search
   * indexes are not applicable.
   */
  public const CODE_SECURED_BY_DATA_MASKING = 'SECURED_BY_DATA_MASKING';
  /**
   * Indicates that the search index and the search function call do not have
   * the same text analyzer.
   */
  public const CODE_MISMATCHED_TEXT_ANALYZER = 'MISMATCHED_TEXT_ANALYZER';
  /**
   * Indicates the base table is too small (below a certain threshold). The
   * index does not provide noticeable search performance gains when the base
   * table is too small.
   */
  public const CODE_BASE_TABLE_TOO_SMALL = 'BASE_TABLE_TOO_SMALL';
  /**
   * Indicates that the total size of indexed base tables in your organization
   * exceeds your region's limit and the index is not used in the query. To
   * index larger base tables, you can use your own reservation for index-
   * management jobs.
   */
  public const CODE_BASE_TABLE_TOO_LARGE = 'BASE_TABLE_TOO_LARGE';
  /**
   * Indicates that the estimated performance gain from using the search index
   * is too low for the given search query.
   */
  public const CODE_ESTIMATED_PERFORMANCE_GAIN_TOO_LOW = 'ESTIMATED_PERFORMANCE_GAIN_TOO_LOW';
  /**
   * Indicates that the column metadata index (which the search index depends
   * on) is not used. User can refer to the [column metadata index
   * usage](https://cloud.google.com/bigquery/docs/metadata-indexing-managed-
   * tables#view_column_metadata_index_usage) for more details on why it was not
   * used.
   */
  public const CODE_COLUMN_METADATA_INDEX_NOT_USED = 'COLUMN_METADATA_INDEX_NOT_USED';
  /**
   * Indicates that search indexes can not be used for search query with
   * STANDARD edition.
   */
  public const CODE_NOT_SUPPORTED_IN_STANDARD_EDITION = 'NOT_SUPPORTED_IN_STANDARD_EDITION';
  /**
   * Indicates that an option in the search function that cannot make use of the
   * index has been selected.
   */
  public const CODE_INDEX_SUPPRESSED_BY_FUNCTION_OPTION = 'INDEX_SUPPRESSED_BY_FUNCTION_OPTION';
  /**
   * Indicates that the query was cached, and thus the search index was not
   * used.
   */
  public const CODE_QUERY_CACHE_HIT = 'QUERY_CACHE_HIT';
  /**
   * The index cannot be used in the search query because it is stale.
   */
  public const CODE_STALE_INDEX = 'STALE_INDEX';
  /**
   * Indicates an internal error that causes the search index to be unused.
   */
  public const CODE_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * Indicates that the reason search indexes cannot be used in the query is not
   * covered by any of the other IndexUnusedReason options.
   */
  public const CODE_OTHER_REASON = 'OTHER_REASON';
  protected $baseTableType = TableReference::class;
  protected $baseTableDataType = '';
  /**
   * Specifies the high-level reason for the scenario when no search index was
   * used.
   *
   * @var string
   */
  public $code;
  /**
   * Specifies the name of the unused search index, if available.
   *
   * @var string
   */
  public $indexName;
  /**
   * Free form human-readable reason for the scenario when no search index was
   * used.
   *
   * @var string
   */
  public $message;

  /**
   * Specifies the base table involved in the reason that no search index was
   * used.
   *
   * @param TableReference $baseTable
   */
  public function setBaseTable(TableReference $baseTable)
  {
    $this->baseTable = $baseTable;
  }
  /**
   * @return TableReference
   */
  public function getBaseTable()
  {
    return $this->baseTable;
  }
  /**
   * Specifies the high-level reason for the scenario when no search index was
   * used.
   *
   * Accepted values: CODE_UNSPECIFIED, INDEX_CONFIG_NOT_AVAILABLE,
   * PENDING_INDEX_CREATION, BASE_TABLE_TRUNCATED, INDEX_CONFIG_MODIFIED,
   * TIME_TRAVEL_QUERY, NO_PRUNING_POWER, UNINDEXED_SEARCH_FIELDS,
   * UNSUPPORTED_SEARCH_PATTERN, OPTIMIZED_WITH_MATERIALIZED_VIEW,
   * SECURED_BY_DATA_MASKING, MISMATCHED_TEXT_ANALYZER, BASE_TABLE_TOO_SMALL,
   * BASE_TABLE_TOO_LARGE, ESTIMATED_PERFORMANCE_GAIN_TOO_LOW,
   * COLUMN_METADATA_INDEX_NOT_USED, NOT_SUPPORTED_IN_STANDARD_EDITION,
   * INDEX_SUPPRESSED_BY_FUNCTION_OPTION, QUERY_CACHE_HIT, STALE_INDEX,
   * INTERNAL_ERROR, OTHER_REASON
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Specifies the name of the unused search index, if available.
   *
   * @param string $indexName
   */
  public function setIndexName($indexName)
  {
    $this->indexName = $indexName;
  }
  /**
   * @return string
   */
  public function getIndexName()
  {
    return $this->indexName;
  }
  /**
   * Free form human-readable reason for the scenario when no search index was
   * used.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IndexUnusedReason::class, 'Google_Service_Bigquery_IndexUnusedReason');
