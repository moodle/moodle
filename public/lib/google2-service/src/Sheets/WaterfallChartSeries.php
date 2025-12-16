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

class WaterfallChartSeries extends \Google\Collection
{
  protected $collection_key = 'customSubtotals';
  protected $customSubtotalsType = WaterfallChartCustomSubtotal::class;
  protected $customSubtotalsDataType = 'array';
  protected $dataType = ChartData::class;
  protected $dataDataType = '';
  protected $dataLabelType = DataLabel::class;
  protected $dataLabelDataType = '';
  /**
   * True to hide the subtotal column from the end of the series. By default, a
   * subtotal column will appear at the end of each series. Setting this field
   * to true will hide that subtotal column for this series.
   *
   * @var bool
   */
  public $hideTrailingSubtotal;
  protected $negativeColumnsStyleType = WaterfallChartColumnStyle::class;
  protected $negativeColumnsStyleDataType = '';
  protected $positiveColumnsStyleType = WaterfallChartColumnStyle::class;
  protected $positiveColumnsStyleDataType = '';
  protected $subtotalColumnsStyleType = WaterfallChartColumnStyle::class;
  protected $subtotalColumnsStyleDataType = '';

  /**
   * Custom subtotal columns appearing in this series. The order in which
   * subtotals are defined is not significant. Only one subtotal may be defined
   * for each data point.
   *
   * @param WaterfallChartCustomSubtotal[] $customSubtotals
   */
  public function setCustomSubtotals($customSubtotals)
  {
    $this->customSubtotals = $customSubtotals;
  }
  /**
   * @return WaterfallChartCustomSubtotal[]
   */
  public function getCustomSubtotals()
  {
    return $this->customSubtotals;
  }
  /**
   * The data being visualized in this series.
   *
   * @param ChartData $data
   */
  public function setData(ChartData $data)
  {
    $this->data = $data;
  }
  /**
   * @return ChartData
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * Information about the data labels for this series.
   *
   * @param DataLabel $dataLabel
   */
  public function setDataLabel(DataLabel $dataLabel)
  {
    $this->dataLabel = $dataLabel;
  }
  /**
   * @return DataLabel
   */
  public function getDataLabel()
  {
    return $this->dataLabel;
  }
  /**
   * True to hide the subtotal column from the end of the series. By default, a
   * subtotal column will appear at the end of each series. Setting this field
   * to true will hide that subtotal column for this series.
   *
   * @param bool $hideTrailingSubtotal
   */
  public function setHideTrailingSubtotal($hideTrailingSubtotal)
  {
    $this->hideTrailingSubtotal = $hideTrailingSubtotal;
  }
  /**
   * @return bool
   */
  public function getHideTrailingSubtotal()
  {
    return $this->hideTrailingSubtotal;
  }
  /**
   * Styles for all columns in this series with negative values.
   *
   * @param WaterfallChartColumnStyle $negativeColumnsStyle
   */
  public function setNegativeColumnsStyle(WaterfallChartColumnStyle $negativeColumnsStyle)
  {
    $this->negativeColumnsStyle = $negativeColumnsStyle;
  }
  /**
   * @return WaterfallChartColumnStyle
   */
  public function getNegativeColumnsStyle()
  {
    return $this->negativeColumnsStyle;
  }
  /**
   * Styles for all columns in this series with positive values.
   *
   * @param WaterfallChartColumnStyle $positiveColumnsStyle
   */
  public function setPositiveColumnsStyle(WaterfallChartColumnStyle $positiveColumnsStyle)
  {
    $this->positiveColumnsStyle = $positiveColumnsStyle;
  }
  /**
   * @return WaterfallChartColumnStyle
   */
  public function getPositiveColumnsStyle()
  {
    return $this->positiveColumnsStyle;
  }
  /**
   * Styles for all subtotal columns in this series.
   *
   * @param WaterfallChartColumnStyle $subtotalColumnsStyle
   */
  public function setSubtotalColumnsStyle(WaterfallChartColumnStyle $subtotalColumnsStyle)
  {
    $this->subtotalColumnsStyle = $subtotalColumnsStyle;
  }
  /**
   * @return WaterfallChartColumnStyle
   */
  public function getSubtotalColumnsStyle()
  {
    return $this->subtotalColumnsStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WaterfallChartSeries::class, 'Google_Service_Sheets_WaterfallChartSeries');
