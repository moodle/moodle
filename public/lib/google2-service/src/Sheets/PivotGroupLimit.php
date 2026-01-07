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

class PivotGroupLimit extends \Google\Model
{
  /**
   * The order in which the group limit is applied to the pivot table. Pivot
   * group limits are applied from lower to higher order number. Order numbers
   * are normalized to consecutive integers from 0. For write request, to fully
   * customize the applying orders, all pivot group limits should have this
   * field set with an unique number. Otherwise, the order is determined by the
   * index in the PivotTable.rows list and then the PivotTable.columns list.
   *
   * @var int
   */
  public $applyOrder;
  /**
   * The count limit.
   *
   * @var int
   */
  public $countLimit;

  /**
   * The order in which the group limit is applied to the pivot table. Pivot
   * group limits are applied from lower to higher order number. Order numbers
   * are normalized to consecutive integers from 0. For write request, to fully
   * customize the applying orders, all pivot group limits should have this
   * field set with an unique number. Otherwise, the order is determined by the
   * index in the PivotTable.rows list and then the PivotTable.columns list.
   *
   * @param int $applyOrder
   */
  public function setApplyOrder($applyOrder)
  {
    $this->applyOrder = $applyOrder;
  }
  /**
   * @return int
   */
  public function getApplyOrder()
  {
    return $this->applyOrder;
  }
  /**
   * The count limit.
   *
   * @param int $countLimit
   */
  public function setCountLimit($countLimit)
  {
    $this->countLimit = $countLimit;
  }
  /**
   * @return int
   */
  public function getCountLimit()
  {
    return $this->countLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PivotGroupLimit::class, 'Google_Service_Sheets_PivotGroupLimit');
