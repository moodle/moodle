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

namespace Google\Service\DatabaseMigrationService;

class ValueListFilter extends \Google\Collection
{
  /**
   * Value present in list unspecified
   */
  public const VALUE_PRESENT_LIST_VALUE_PRESENT_IN_LIST_UNSPECIFIED = 'VALUE_PRESENT_IN_LIST_UNSPECIFIED';
  /**
   * If the source value is in the supplied list at value_list
   */
  public const VALUE_PRESENT_LIST_VALUE_PRESENT_IN_LIST_IF_VALUE_LIST = 'VALUE_PRESENT_IN_LIST_IF_VALUE_LIST';
  /**
   * If the source value is not in the supplied list at value_list
   */
  public const VALUE_PRESENT_LIST_VALUE_PRESENT_IN_LIST_IF_VALUE_NOT_LIST = 'VALUE_PRESENT_IN_LIST_IF_VALUE_NOT_LIST';
  protected $collection_key = 'values';
  /**
   * Required. Whether to ignore case when filtering by values. Defaults to
   * false
   *
   * @var bool
   */
  public $ignoreCase;
  /**
   * Required. Indicates whether the filter matches rows with values that are
   * present in the list or those with values not present in it.
   *
   * @var string
   */
  public $valuePresentList;
  /**
   * Required. The list to be used to filter by
   *
   * @var string[]
   */
  public $values;

  /**
   * Required. Whether to ignore case when filtering by values. Defaults to
   * false
   *
   * @param bool $ignoreCase
   */
  public function setIgnoreCase($ignoreCase)
  {
    $this->ignoreCase = $ignoreCase;
  }
  /**
   * @return bool
   */
  public function getIgnoreCase()
  {
    return $this->ignoreCase;
  }
  /**
   * Required. Indicates whether the filter matches rows with values that are
   * present in the list or those with values not present in it.
   *
   * Accepted values: VALUE_PRESENT_IN_LIST_UNSPECIFIED,
   * VALUE_PRESENT_IN_LIST_IF_VALUE_LIST,
   * VALUE_PRESENT_IN_LIST_IF_VALUE_NOT_LIST
   *
   * @param self::VALUE_PRESENT_LIST_* $valuePresentList
   */
  public function setValuePresentList($valuePresentList)
  {
    $this->valuePresentList = $valuePresentList;
  }
  /**
   * @return self::VALUE_PRESENT_LIST_*
   */
  public function getValuePresentList()
  {
    return $this->valuePresentList;
  }
  /**
   * Required. The list to be used to filter by
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ValueListFilter::class, 'Google_Service_DatabaseMigrationService_ValueListFilter');
