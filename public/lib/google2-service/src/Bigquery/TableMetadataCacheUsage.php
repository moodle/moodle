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

class TableMetadataCacheUsage extends \Google\Model
{
  /**
   * Unused reasons not specified.
   */
  public const UNUSED_REASON_UNUSED_REASON_UNSPECIFIED = 'UNUSED_REASON_UNSPECIFIED';
  /**
   * Metadata cache was outside the table's maxStaleness.
   */
  public const UNUSED_REASON_EXCEEDED_MAX_STALENESS = 'EXCEEDED_MAX_STALENESS';
  /**
   * Metadata caching feature is not enabled. [Update BigLake tables]
   * (/bigquery/docs/create-cloud-storage-table-biglake#update-biglake-tables)
   * to enable the metadata caching.
   */
  public const UNUSED_REASON_METADATA_CACHING_NOT_ENABLED = 'METADATA_CACHING_NOT_ENABLED';
  /**
   * Other unknown reason.
   */
  public const UNUSED_REASON_OTHER_REASON = 'OTHER_REASON';
  /**
   * Free form human-readable reason metadata caching was unused for the job.
   *
   * @var string
   */
  public $explanation;
  protected $pruningStatsType = PruningStats::class;
  protected $pruningStatsDataType = '';
  /**
   * Duration since last refresh as of this job for managed tables (indicates
   * metadata cache staleness as seen by this job).
   *
   * @var string
   */
  public $staleness;
  protected $tableReferenceType = TableReference::class;
  protected $tableReferenceDataType = '';
  /**
   * [Table type](https://cloud.google.com/bigquery/docs/reference/rest/v2/table
   * s#Table.FIELDS.type).
   *
   * @var string
   */
  public $tableType;
  /**
   * Reason for not using metadata caching for the table.
   *
   * @var string
   */
  public $unusedReason;

  /**
   * Free form human-readable reason metadata caching was unused for the job.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * The column metadata index pruning statistics.
   *
   * @param PruningStats $pruningStats
   */
  public function setPruningStats(PruningStats $pruningStats)
  {
    $this->pruningStats = $pruningStats;
  }
  /**
   * @return PruningStats
   */
  public function getPruningStats()
  {
    return $this->pruningStats;
  }
  /**
   * Duration since last refresh as of this job for managed tables (indicates
   * metadata cache staleness as seen by this job).
   *
   * @param string $staleness
   */
  public function setStaleness($staleness)
  {
    $this->staleness = $staleness;
  }
  /**
   * @return string
   */
  public function getStaleness()
  {
    return $this->staleness;
  }
  /**
   * Metadata caching eligible table referenced in the query.
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
   * [Table type](https://cloud.google.com/bigquery/docs/reference/rest/v2/table
   * s#Table.FIELDS.type).
   *
   * @param string $tableType
   */
  public function setTableType($tableType)
  {
    $this->tableType = $tableType;
  }
  /**
   * @return string
   */
  public function getTableType()
  {
    return $this->tableType;
  }
  /**
   * Reason for not using metadata caching for the table.
   *
   * Accepted values: UNUSED_REASON_UNSPECIFIED, EXCEEDED_MAX_STALENESS,
   * METADATA_CACHING_NOT_ENABLED, OTHER_REASON
   *
   * @param self::UNUSED_REASON_* $unusedReason
   */
  public function setUnusedReason($unusedReason)
  {
    $this->unusedReason = $unusedReason;
  }
  /**
   * @return self::UNUSED_REASON_*
   */
  public function getUnusedReason()
  {
    return $this->unusedReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableMetadataCacheUsage::class, 'Google_Service_Bigquery_TableMetadataCacheUsage');
