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

namespace Google\Service\CloudNaturalLanguage;

class XPSRow extends \Google\Collection
{
  protected $collection_key = 'values';
  /**
   * The ids of the columns. Note: The below `values` field must match order of
   * this field, if this field is set.
   *
   * @var int[]
   */
  public $columnIds;
  /**
   * The values of the row cells, given in the same order as the column_ids. If
   * column_ids is not set, then in the same order as the
   * input_feature_column_ids in TablesModelMetadata.
   *
   * @var array[]
   */
  public $values;

  /**
   * The ids of the columns. Note: The below `values` field must match order of
   * this field, if this field is set.
   *
   * @param int[] $columnIds
   */
  public function setColumnIds($columnIds)
  {
    $this->columnIds = $columnIds;
  }
  /**
   * @return int[]
   */
  public function getColumnIds()
  {
    return $this->columnIds;
  }
  /**
   * The values of the row cells, given in the same order as the column_ids. If
   * column_ids is not set, then in the same order as the
   * input_feature_column_ids in TablesModelMetadata.
   *
   * @param array[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return array[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSRow::class, 'Google_Service_CloudNaturalLanguage_XPSRow');
