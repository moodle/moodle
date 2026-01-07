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

class WaterfallChartSpec extends \Google\Collection
{
  /**
   * Default value, do not use.
   */
  public const STACKED_TYPE_WATERFALL_STACKED_TYPE_UNSPECIFIED = 'WATERFALL_STACKED_TYPE_UNSPECIFIED';
  /**
   * Values corresponding to the same domain (horizontal axis) value will be
   * stacked vertically.
   */
  public const STACKED_TYPE_STACKED = 'STACKED';
  /**
   * Series will spread out along the horizontal axis.
   */
  public const STACKED_TYPE_SEQUENTIAL = 'SEQUENTIAL';
  protected $collection_key = 'series';
  protected $connectorLineStyleType = LineStyle::class;
  protected $connectorLineStyleDataType = '';
  protected $domainType = WaterfallChartDomain::class;
  protected $domainDataType = '';
  /**
   * True to interpret the first value as a total.
   *
   * @var bool
   */
  public $firstValueIsTotal;
  /**
   * True to hide connector lines between columns.
   *
   * @var bool
   */
  public $hideConnectorLines;
  protected $seriesType = WaterfallChartSeries::class;
  protected $seriesDataType = 'array';
  /**
   * The stacked type.
   *
   * @var string
   */
  public $stackedType;
  protected $totalDataLabelType = DataLabel::class;
  protected $totalDataLabelDataType = '';

  /**
   * The line style for the connector lines.
   *
   * @param LineStyle $connectorLineStyle
   */
  public function setConnectorLineStyle(LineStyle $connectorLineStyle)
  {
    $this->connectorLineStyle = $connectorLineStyle;
  }
  /**
   * @return LineStyle
   */
  public function getConnectorLineStyle()
  {
    return $this->connectorLineStyle;
  }
  /**
   * The domain data (horizontal axis) for the waterfall chart.
   *
   * @param WaterfallChartDomain $domain
   */
  public function setDomain(WaterfallChartDomain $domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return WaterfallChartDomain
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * True to interpret the first value as a total.
   *
   * @param bool $firstValueIsTotal
   */
  public function setFirstValueIsTotal($firstValueIsTotal)
  {
    $this->firstValueIsTotal = $firstValueIsTotal;
  }
  /**
   * @return bool
   */
  public function getFirstValueIsTotal()
  {
    return $this->firstValueIsTotal;
  }
  /**
   * True to hide connector lines between columns.
   *
   * @param bool $hideConnectorLines
   */
  public function setHideConnectorLines($hideConnectorLines)
  {
    $this->hideConnectorLines = $hideConnectorLines;
  }
  /**
   * @return bool
   */
  public function getHideConnectorLines()
  {
    return $this->hideConnectorLines;
  }
  /**
   * The data this waterfall chart is visualizing.
   *
   * @param WaterfallChartSeries[] $series
   */
  public function setSeries($series)
  {
    $this->series = $series;
  }
  /**
   * @return WaterfallChartSeries[]
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * The stacked type.
   *
   * Accepted values: WATERFALL_STACKED_TYPE_UNSPECIFIED, STACKED, SEQUENTIAL
   *
   * @param self::STACKED_TYPE_* $stackedType
   */
  public function setStackedType($stackedType)
  {
    $this->stackedType = $stackedType;
  }
  /**
   * @return self::STACKED_TYPE_*
   */
  public function getStackedType()
  {
    return $this->stackedType;
  }
  /**
   * Controls whether to display additional data labels on stacked charts which
   * sum the total value of all stacked values at each value along the domain
   * axis. stacked_type must be STACKED and neither CUSTOM nor placement can be
   * set on the total_data_label.
   *
   * @param DataLabel $totalDataLabel
   */
  public function setTotalDataLabel(DataLabel $totalDataLabel)
  {
    $this->totalDataLabel = $totalDataLabel;
  }
  /**
   * @return DataLabel
   */
  public function getTotalDataLabel()
  {
    return $this->totalDataLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WaterfallChartSpec::class, 'Google_Service_Sheets_WaterfallChartSpec');
