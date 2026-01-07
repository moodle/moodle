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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList extends \Google\Collection
{
  public const FILTER_TYPE_DEFAULT_INCLUSIVE = 'DEFAULT_INCLUSIVE';
  public const FILTER_TYPE_EXCLUSIVE = 'EXCLUSIVE';
  protected $collection_key = 'enumStrings';
  /**
   * @var string[]
   */
  public $enumStrings;
  /**
   * @var string
   */
  public $filterType;

  /**
   * @param string[] $enumStrings
   */
  public function setEnumStrings($enumStrings)
  {
    $this->enumStrings = $enumStrings;
  }
  /**
   * @return string[]
   */
  public function getEnumStrings()
  {
    return $this->enumStrings;
  }
  /**
   * @param self::FILTER_TYPE_* $filterType
   */
  public function setFilterType($filterType)
  {
    $this->filterType = $filterType;
  }
  /**
   * @return self::FILTER_TYPE_*
   */
  public function getFilterType()
  {
    return $this->filterType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoBaseAlertConfigErrorEnumList');
