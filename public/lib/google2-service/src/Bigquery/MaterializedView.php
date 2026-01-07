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

class MaterializedView extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const REJECTED_REASON_REJECTED_REASON_UNSPECIFIED = 'REJECTED_REASON_UNSPECIFIED';
  /**
   * View has no cached data because it has not refreshed yet.
   */
  public const REJECTED_REASON_NO_DATA = 'NO_DATA';
  /**
   * The estimated cost of the view is more expensive than another view or the
   * base table. Note: The estimate cost might not match the billed cost.
   */
  public const REJECTED_REASON_COST = 'COST';
  /**
   * View has no cached data because a base table is truncated.
   */
  public const REJECTED_REASON_BASE_TABLE_TRUNCATED = 'BASE_TABLE_TRUNCATED';
  /**
   * View is invalidated because of a data change in one or more base tables. It
   * could be any recent change if the [`maxStaleness`](https://cloud.google.com
   * /bigquery/docs/reference/rest/v2/tables#Table.FIELDS.max_staleness) option
   * is not set for the view, or otherwise any change outside of the staleness
   * window.
   */
  public const REJECTED_REASON_BASE_TABLE_DATA_CHANGE = 'BASE_TABLE_DATA_CHANGE';
  /**
   * View is invalidated because a base table's partition expiration has
   * changed.
   */
  public const REJECTED_REASON_BASE_TABLE_PARTITION_EXPIRATION_CHANGE = 'BASE_TABLE_PARTITION_EXPIRATION_CHANGE';
  /**
   * View is invalidated because a base table's partition has expired.
   */
  public const REJECTED_REASON_BASE_TABLE_EXPIRED_PARTITION = 'BASE_TABLE_EXPIRED_PARTITION';
  /**
   * View is invalidated because a base table has an incompatible metadata
   * change.
   */
  public const REJECTED_REASON_BASE_TABLE_INCOMPATIBLE_METADATA_CHANGE = 'BASE_TABLE_INCOMPATIBLE_METADATA_CHANGE';
  /**
   * View is invalidated because it was refreshed with a time zone other than
   * that of the current job.
   */
  public const REJECTED_REASON_TIME_ZONE = 'TIME_ZONE';
  /**
   * View is outside the time travel window.
   */
  public const REJECTED_REASON_OUT_OF_TIME_TRAVEL_WINDOW = 'OUT_OF_TIME_TRAVEL_WINDOW';
  /**
   * View is inaccessible to the user because of a fine-grained security policy
   * on one of its base tables.
   */
  public const REJECTED_REASON_BASE_TABLE_FINE_GRAINED_SECURITY_POLICY = 'BASE_TABLE_FINE_GRAINED_SECURITY_POLICY';
  /**
   * One of the view's base tables is too stale. For example, the cached
   * metadata of a BigLake external table needs to be updated.
   */
  public const REJECTED_REASON_BASE_TABLE_TOO_STALE = 'BASE_TABLE_TOO_STALE';
  /**
   * Whether the materialized view is chosen for the query. A materialized view
   * can be chosen to rewrite multiple parts of the same query. If a
   * materialized view is chosen to rewrite any part of the query, then this
   * field is true, even if the materialized view was not chosen to rewrite
   * others parts.
   *
   * @var bool
   */
  public $chosen;
  /**
   * If present, specifies a best-effort estimation of the bytes saved by using
   * the materialized view rather than its base tables.
   *
   * @var string
   */
  public $estimatedBytesSaved;
  /**
   * If present, specifies the reason why the materialized view was not chosen
   * for the query.
   *
   * @var string
   */
  public $rejectedReason;
  protected $tableReferenceType = TableReference::class;
  protected $tableReferenceDataType = '';

  /**
   * Whether the materialized view is chosen for the query. A materialized view
   * can be chosen to rewrite multiple parts of the same query. If a
   * materialized view is chosen to rewrite any part of the query, then this
   * field is true, even if the materialized view was not chosen to rewrite
   * others parts.
   *
   * @param bool $chosen
   */
  public function setChosen($chosen)
  {
    $this->chosen = $chosen;
  }
  /**
   * @return bool
   */
  public function getChosen()
  {
    return $this->chosen;
  }
  /**
   * If present, specifies a best-effort estimation of the bytes saved by using
   * the materialized view rather than its base tables.
   *
   * @param string $estimatedBytesSaved
   */
  public function setEstimatedBytesSaved($estimatedBytesSaved)
  {
    $this->estimatedBytesSaved = $estimatedBytesSaved;
  }
  /**
   * @return string
   */
  public function getEstimatedBytesSaved()
  {
    return $this->estimatedBytesSaved;
  }
  /**
   * If present, specifies the reason why the materialized view was not chosen
   * for the query.
   *
   * Accepted values: REJECTED_REASON_UNSPECIFIED, NO_DATA, COST,
   * BASE_TABLE_TRUNCATED, BASE_TABLE_DATA_CHANGE,
   * BASE_TABLE_PARTITION_EXPIRATION_CHANGE, BASE_TABLE_EXPIRED_PARTITION,
   * BASE_TABLE_INCOMPATIBLE_METADATA_CHANGE, TIME_ZONE,
   * OUT_OF_TIME_TRAVEL_WINDOW, BASE_TABLE_FINE_GRAINED_SECURITY_POLICY,
   * BASE_TABLE_TOO_STALE
   *
   * @param self::REJECTED_REASON_* $rejectedReason
   */
  public function setRejectedReason($rejectedReason)
  {
    $this->rejectedReason = $rejectedReason;
  }
  /**
   * @return self::REJECTED_REASON_*
   */
  public function getRejectedReason()
  {
    return $this->rejectedReason;
  }
  /**
   * The candidate materialized view.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaterializedView::class, 'Google_Service_Bigquery_MaterializedView');
