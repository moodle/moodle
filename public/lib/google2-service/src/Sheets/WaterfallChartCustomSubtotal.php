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

class WaterfallChartCustomSubtotal extends \Google\Model
{
  /**
   * True if the data point at subtotal_index is the subtotal. If false, the
   * subtotal will be computed and appear after the data point.
   *
   * @var bool
   */
  public $dataIsSubtotal;
  /**
   * A label for the subtotal column.
   *
   * @var string
   */
  public $label;
  /**
   * The zero-based index of a data point within the series. If data_is_subtotal
   * is true, the data point at this index is the subtotal. Otherwise, the
   * subtotal appears after the data point with this index. A series can have
   * multiple subtotals at arbitrary indices, but subtotals do not affect the
   * indices of the data points. For example, if a series has three data points,
   * their indices will always be 0, 1, and 2, regardless of how many subtotals
   * exist on the series or what data points they are associated with.
   *
   * @var int
   */
  public $subtotalIndex;

  /**
   * True if the data point at subtotal_index is the subtotal. If false, the
   * subtotal will be computed and appear after the data point.
   *
   * @param bool $dataIsSubtotal
   */
  public function setDataIsSubtotal($dataIsSubtotal)
  {
    $this->dataIsSubtotal = $dataIsSubtotal;
  }
  /**
   * @return bool
   */
  public function getDataIsSubtotal()
  {
    return $this->dataIsSubtotal;
  }
  /**
   * A label for the subtotal column.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The zero-based index of a data point within the series. If data_is_subtotal
   * is true, the data point at this index is the subtotal. Otherwise, the
   * subtotal appears after the data point with this index. A series can have
   * multiple subtotals at arbitrary indices, but subtotals do not affect the
   * indices of the data points. For example, if a series has three data points,
   * their indices will always be 0, 1, and 2, regardless of how many subtotals
   * exist on the series or what data points they are associated with.
   *
   * @param int $subtotalIndex
   */
  public function setSubtotalIndex($subtotalIndex)
  {
    $this->subtotalIndex = $subtotalIndex;
  }
  /**
   * @return int
   */
  public function getSubtotalIndex()
  {
    return $this->subtotalIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WaterfallChartCustomSubtotal::class, 'Google_Service_Sheets_WaterfallChartCustomSubtotal');
