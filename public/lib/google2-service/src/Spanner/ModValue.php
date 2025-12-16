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

namespace Google\Service\Spanner;

class ModValue extends \Google\Model
{
  /**
   * Index within the repeated column_metadata field, to obtain the column
   * metadata for the column that was modified.
   *
   * @var int
   */
  public $columnMetadataIndex;
  /**
   * The value of the column.
   *
   * @var array
   */
  public $value;

  /**
   * Index within the repeated column_metadata field, to obtain the column
   * metadata for the column that was modified.
   *
   * @param int $columnMetadataIndex
   */
  public function setColumnMetadataIndex($columnMetadataIndex)
  {
    $this->columnMetadataIndex = $columnMetadataIndex;
  }
  /**
   * @return int
   */
  public function getColumnMetadataIndex()
  {
    return $this->columnMetadataIndex;
  }
  /**
   * The value of the column.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ModValue::class, 'Google_Service_Spanner_ModValue');
