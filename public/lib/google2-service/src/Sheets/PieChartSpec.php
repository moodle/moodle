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

class PieChartSpec extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const LEGEND_POSITION_PIE_CHART_LEGEND_POSITION_UNSPECIFIED = 'PIE_CHART_LEGEND_POSITION_UNSPECIFIED';
  /**
   * The legend is rendered on the bottom of the chart.
   */
  public const LEGEND_POSITION_BOTTOM_LEGEND = 'BOTTOM_LEGEND';
  /**
   * The legend is rendered on the left of the chart.
   */
  public const LEGEND_POSITION_LEFT_LEGEND = 'LEFT_LEGEND';
  /**
   * The legend is rendered on the right of the chart.
   */
  public const LEGEND_POSITION_RIGHT_LEGEND = 'RIGHT_LEGEND';
  /**
   * The legend is rendered on the top of the chart.
   */
  public const LEGEND_POSITION_TOP_LEGEND = 'TOP_LEGEND';
  /**
   * No legend is rendered.
   */
  public const LEGEND_POSITION_NO_LEGEND = 'NO_LEGEND';
  /**
   * Each pie slice has a label attached to it.
   */
  public const LEGEND_POSITION_LABELED_LEGEND = 'LABELED_LEGEND';
  protected $domainType = ChartData::class;
  protected $domainDataType = '';
  /**
   * Where the legend of the pie chart should be drawn.
   *
   * @var string
   */
  public $legendPosition;
  /**
   * The size of the hole in the pie chart.
   *
   * @var 
   */
  public $pieHole;
  protected $seriesType = ChartData::class;
  protected $seriesDataType = '';
  /**
   * True if the pie is three dimensional.
   *
   * @var bool
   */
  public $threeDimensional;

  /**
   * The data that covers the domain of the pie chart.
   *
   * @param ChartData $domain
   */
  public function setDomain(ChartData $domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return ChartData
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Where the legend of the pie chart should be drawn.
   *
   * Accepted values: PIE_CHART_LEGEND_POSITION_UNSPECIFIED, BOTTOM_LEGEND,
   * LEFT_LEGEND, RIGHT_LEGEND, TOP_LEGEND, NO_LEGEND, LABELED_LEGEND
   *
   * @param self::LEGEND_POSITION_* $legendPosition
   */
  public function setLegendPosition($legendPosition)
  {
    $this->legendPosition = $legendPosition;
  }
  /**
   * @return self::LEGEND_POSITION_*
   */
  public function getLegendPosition()
  {
    return $this->legendPosition;
  }
  public function setPieHole($pieHole)
  {
    $this->pieHole = $pieHole;
  }
  public function getPieHole()
  {
    return $this->pieHole;
  }
  /**
   * The data that covers the one and only series of the pie chart.
   *
   * @param ChartData $series
   */
  public function setSeries(ChartData $series)
  {
    $this->series = $series;
  }
  /**
   * @return ChartData
   */
  public function getSeries()
  {
    return $this->series;
  }
  /**
   * True if the pie is three dimensional.
   *
   * @param bool $threeDimensional
   */
  public function setThreeDimensional($threeDimensional)
  {
    $this->threeDimensional = $threeDimensional;
  }
  /**
   * @return bool
   */
  public function getThreeDimensional()
  {
    return $this->threeDimensional;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PieChartSpec::class, 'Google_Service_Sheets_PieChartSpec');
