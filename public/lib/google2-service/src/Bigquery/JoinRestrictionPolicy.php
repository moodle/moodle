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

class JoinRestrictionPolicy extends \Google\Collection
{
  /**
   * A join is neither required nor restricted on any column. Default value.
   */
  public const JOIN_CONDITION_JOIN_CONDITION_UNSPECIFIED = 'JOIN_CONDITION_UNSPECIFIED';
  /**
   * A join is required on at least one of the specified columns.
   */
  public const JOIN_CONDITION_JOIN_ANY = 'JOIN_ANY';
  /**
   * A join is required on all specified columns.
   */
  public const JOIN_CONDITION_JOIN_ALL = 'JOIN_ALL';
  /**
   * A join is not required, but if present it is only permitted on
   * 'join_allowed_columns'
   */
  public const JOIN_CONDITION_JOIN_NOT_REQUIRED = 'JOIN_NOT_REQUIRED';
  /**
   * Joins are blocked for all queries.
   */
  public const JOIN_CONDITION_JOIN_BLOCKED = 'JOIN_BLOCKED';
  protected $collection_key = 'joinAllowedColumns';
  /**
   * Optional. The only columns that joins are allowed on. This field is must be
   * specified for join_conditions JOIN_ANY and JOIN_ALL and it cannot be set
   * for JOIN_BLOCKED.
   *
   * @var string[]
   */
  public $joinAllowedColumns;
  /**
   * Optional. Specifies if a join is required or not on queries for the view.
   * Default is JOIN_CONDITION_UNSPECIFIED.
   *
   * @var string
   */
  public $joinCondition;

  /**
   * Optional. The only columns that joins are allowed on. This field is must be
   * specified for join_conditions JOIN_ANY and JOIN_ALL and it cannot be set
   * for JOIN_BLOCKED.
   *
   * @param string[] $joinAllowedColumns
   */
  public function setJoinAllowedColumns($joinAllowedColumns)
  {
    $this->joinAllowedColumns = $joinAllowedColumns;
  }
  /**
   * @return string[]
   */
  public function getJoinAllowedColumns()
  {
    return $this->joinAllowedColumns;
  }
  /**
   * Optional. Specifies if a join is required or not on queries for the view.
   * Default is JOIN_CONDITION_UNSPECIFIED.
   *
   * Accepted values: JOIN_CONDITION_UNSPECIFIED, JOIN_ANY, JOIN_ALL,
   * JOIN_NOT_REQUIRED, JOIN_BLOCKED
   *
   * @param self::JOIN_CONDITION_* $joinCondition
   */
  public function setJoinCondition($joinCondition)
  {
    $this->joinCondition = $joinCondition;
  }
  /**
   * @return self::JOIN_CONDITION_*
   */
  public function getJoinCondition()
  {
    return $this->joinCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JoinRestrictionPolicy::class, 'Google_Service_Bigquery_JoinRestrictionPolicy');
