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

namespace Google\Service\Analytics;

class FilterLowercaseDetails extends \Google\Model
{
  /**
   * Field to use in the filter.
   *
   * @var string
   */
  public $field;
  /**
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @var int
   */
  public $fieldIndex;

  /**
   * Field to use in the filter.
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
   * The Index of the custom dimension. Required if field is a CUSTOM_DIMENSION.
   *
   * @param int $fieldIndex
   */
  public function setFieldIndex($fieldIndex)
  {
    $this->fieldIndex = $fieldIndex;
  }
  /**
   * @return int
   */
  public function getFieldIndex()
  {
    return $this->fieldIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterLowercaseDetails::class, 'Google_Service_Analytics_FilterLowercaseDetails');
