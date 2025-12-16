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

namespace Google\Service\CloudSearch;

class SortOptions extends \Google\Model
{
  public const SORT_ORDER_ASCENDING = 'ASCENDING';
  public const SORT_ORDER_DESCENDING = 'DESCENDING';
  /**
   * The name of the operator corresponding to the field to sort on. The
   * corresponding property must be marked as sortable.
   *
   * @var string
   */
  public $operatorName;
  /**
   * Ascending is the default sort order
   *
   * @var string
   */
  public $sortOrder;

  /**
   * The name of the operator corresponding to the field to sort on. The
   * corresponding property must be marked as sortable.
   *
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
  /**
   * Ascending is the default sort order
   *
   * Accepted values: ASCENDING, DESCENDING
   *
   * @param self::SORT_ORDER_* $sortOrder
   */
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }
  /**
   * @return self::SORT_ORDER_*
   */
  public function getSortOrder()
  {
    return $this->sortOrder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SortOptions::class, 'Google_Service_CloudSearch_SortOptions');
