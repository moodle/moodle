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

namespace Google\Service\Sheets;

class PivotGroupSortValueBucket extends \Google\Collection
{
  protected $collection_key = 'buckets';
  protected $bucketsType = ExtendedValue::class;
  protected $bucketsDataType = 'array';
  /**
   * The offset in the PivotTable.values list which the values in this grouping
   * should be sorted by.
   *
   * @var int
   */
  public $valuesIndex;

  /**
   * Determines the bucket from which values are chosen to sort. For example, in
   * a pivot table with one row group & two column groups, the row group can
   * list up to two values. The first value corresponds to a value within the
   * first column group, and the second value corresponds to a value in the
   * second column group. If no values are listed, this would indicate that the
   * row should be sorted according to the "Grand Total" over the column groups.
   * If a single value is listed, this would correspond to using the "Total" of
   * that bucket.
   *
   * @param ExtendedValue[] $buckets
   */
  public function setBuckets($buckets)
  {
    $this->buckets = $buckets;
  }
  /**
   * @return ExtendedValue[]
   */
  public function getBuckets()
  {
    return $this->buckets;
  }
  /**
   * The offset in the PivotTable.values list which the values in this grouping
   * should be sorted by.
   *
   * @param int $valuesIndex
   */
  public function setValuesIndex($valuesIndex)
  {
    $this->valuesIndex = $valuesIndex;
  }
  /**
   * @return int
   */
  public function getValuesIndex()
  {
    return $this->valuesIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotGroupSortValueBucket::class, 'Google_Service_Sheets_PivotGroupSortValueBucket');
