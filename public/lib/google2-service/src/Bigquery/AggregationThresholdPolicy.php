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

class AggregationThresholdPolicy extends \Google\Collection
{
  protected $collection_key = 'privacyUnitColumns';
  /**
   * Optional. The privacy unit column(s) associated with this policy. For now,
   * only one column per data source object (table, view) is allowed as a
   * privacy unit column. Representing as a repeated field in metadata for
   * extensibility to multiple columns in future. Duplicates and Repeated struct
   * fields are not allowed. For nested fields, use dot notation ("outer.inner")
   *
   * @var string[]
   */
  public $privacyUnitColumns;
  /**
   * Optional. The threshold for the "aggregation threshold" policy.
   *
   * @var string
   */
  public $threshold;

  /**
   * Optional. The privacy unit column(s) associated with this policy. For now,
   * only one column per data source object (table, view) is allowed as a
   * privacy unit column. Representing as a repeated field in metadata for
   * extensibility to multiple columns in future. Duplicates and Repeated struct
   * fields are not allowed. For nested fields, use dot notation ("outer.inner")
   *
   * @param string[] $privacyUnitColumns
   */
  public function setPrivacyUnitColumns($privacyUnitColumns)
  {
    $this->privacyUnitColumns = $privacyUnitColumns;
  }
  /**
   * @return string[]
   */
  public function getPrivacyUnitColumns()
  {
    return $this->privacyUnitColumns;
  }
  /**
   * Optional. The threshold for the "aggregation threshold" policy.
   *
   * @param string $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return string
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationThresholdPolicy::class, 'Google_Service_Bigquery_AggregationThresholdPolicy');
