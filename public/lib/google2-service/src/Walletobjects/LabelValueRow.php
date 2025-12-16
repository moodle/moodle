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

namespace Google\Service\Walletobjects;

class LabelValueRow extends \Google\Collection
{
  protected $collection_key = 'columns';
  protected $columnsType = LabelValue::class;
  protected $columnsDataType = 'array';

  /**
   * A list of labels and values. These will be displayed in a singular column,
   * one after the other, not in multiple columns, despite the field name.
   *
   * @param LabelValue[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return LabelValue[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelValueRow::class, 'Google_Service_Walletobjects_LabelValueRow');
