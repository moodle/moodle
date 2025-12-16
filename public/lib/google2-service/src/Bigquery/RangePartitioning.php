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

class RangePartitioning extends \Google\Model
{
  /**
   * Required. The name of the column to partition the table on. It must be a
   * top-level, INT64 column whose mode is NULLABLE or REQUIRED.
   *
   * @var string
   */
  public $field;
  protected $rangeType = RangePartitioningRange::class;
  protected $rangeDataType = '';

  /**
   * Required. The name of the column to partition the table on. It must be a
   * top-level, INT64 column whose mode is NULLABLE or REQUIRED.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * [Experimental] Defines the ranges for range partitioning.
   *
   * @param RangePartitioningRange $range
   */
  public function setRange(RangePartitioningRange $range)
  {
    $this->range = $range;
  }
  /**
   * @return RangePartitioningRange
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RangePartitioning::class, 'Google_Service_Bigquery_RangePartitioning');
