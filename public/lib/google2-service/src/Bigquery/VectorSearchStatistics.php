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

class VectorSearchStatistics extends \Google\Collection
{
  /**
   * Index usage mode not specified.
   */
  public const INDEX_USAGE_MODE_INDEX_USAGE_MODE_UNSPECIFIED = 'INDEX_USAGE_MODE_UNSPECIFIED';
  /**
   * No vector indexes were used in the vector search query. See
   * [`indexUnusedReasons`]
   * (/bigquery/docs/reference/rest/v2/Job#IndexUnusedReason) for detailed
   * reasons.
   */
  public const INDEX_USAGE_MODE_UNUSED = 'UNUSED';
  /**
   * Part of the vector search query used vector indexes. See
   * [`indexUnusedReasons`]
   * (/bigquery/docs/reference/rest/v2/Job#IndexUnusedReason) for why other
   * parts of the query did not use vector indexes.
   */
  public const INDEX_USAGE_MODE_PARTIALLY_USED = 'PARTIALLY_USED';
  /**
   * The entire vector search query used vector indexes.
   */
  public const INDEX_USAGE_MODE_FULLY_USED = 'FULLY_USED';
  protected $collection_key = 'storedColumnsUsages';
  protected $indexUnusedReasonsType = IndexUnusedReason::class;
  protected $indexUnusedReasonsDataType = 'array';
  /**
   * Specifies the index usage mode for the query.
   *
   * @var string
   */
  public $indexUsageMode;
  protected $storedColumnsUsagesType = StoredColumnsUsage::class;
  protected $storedColumnsUsagesDataType = 'array';

  /**
   * When `indexUsageMode` is `UNUSED` or `PARTIALLY_USED`, this field explains
   * why indexes were not used in all or part of the vector search query. If
   * `indexUsageMode` is `FULLY_USED`, this field is not populated.
   *
   * @param IndexUnusedReason[] $indexUnusedReasons
   */
  public function setIndexUnusedReasons($indexUnusedReasons)
  {
    $this->indexUnusedReasons = $indexUnusedReasons;
  }
  /**
   * @return IndexUnusedReason[]
   */
  public function getIndexUnusedReasons()
  {
    return $this->indexUnusedReasons;
  }
  /**
   * Specifies the index usage mode for the query.
   *
   * Accepted values: INDEX_USAGE_MODE_UNSPECIFIED, UNUSED, PARTIALLY_USED,
   * FULLY_USED
   *
   * @param self::INDEX_USAGE_MODE_* $indexUsageMode
   */
  public function setIndexUsageMode($indexUsageMode)
  {
    $this->indexUsageMode = $indexUsageMode;
  }
  /**
   * @return self::INDEX_USAGE_MODE_*
   */
  public function getIndexUsageMode()
  {
    return $this->indexUsageMode;
  }
  /**
   * Specifies the usage of stored columns in the query when stored columns are
   * used in the query.
   *
   * @param StoredColumnsUsage[] $storedColumnsUsages
   */
  public function setStoredColumnsUsages($storedColumnsUsages)
  {
    $this->storedColumnsUsages = $storedColumnsUsages;
  }
  /**
   * @return StoredColumnsUsage[]
   */
  public function getStoredColumnsUsages()
  {
    return $this->storedColumnsUsages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VectorSearchStatistics::class, 'Google_Service_Bigquery_VectorSearchStatistics');
