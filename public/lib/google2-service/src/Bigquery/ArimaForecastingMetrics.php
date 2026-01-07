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

namespace Google\Service\Bigquery;

class ArimaForecastingMetrics extends \Google\Collection
{
  protected $collection_key = 'timeSeriesId';
  protected $arimaFittingMetricsType = ArimaFittingMetrics::class;
  protected $arimaFittingMetricsDataType = 'array';
  protected $arimaSingleModelForecastingMetricsType = ArimaSingleModelForecastingMetrics::class;
  protected $arimaSingleModelForecastingMetricsDataType = 'array';
  /**
   * Whether Arima model fitted with drift or not. It is always false when d is
   * not 1.
   *
   * @deprecated
   * @var bool[]
   */
  public $hasDrift;
  protected $nonSeasonalOrderType = ArimaOrder::class;
  protected $nonSeasonalOrderDataType = 'array';
  /**
   * Seasonal periods. Repeated because multiple periods are supported for one
   * time series.
   *
   * @deprecated
   * @var string[]
   */
  public $seasonalPeriods;
  /**
   * Id to differentiate different time series for the large-scale case.
   *
   * @deprecated
   * @var string[]
   */
  public $timeSeriesId;

  /**
   * Arima model fitting metrics.
   *
   * @deprecated
   * @param ArimaFittingMetrics[] $arimaFittingMetrics
   */
  public function setArimaFittingMetrics($arimaFittingMetrics)
  {
    $this->arimaFittingMetrics = $arimaFittingMetrics;
  }
  /**
   * @deprecated
   * @return ArimaFittingMetrics[]
   */
  public function getArimaFittingMetrics()
  {
    return $this->arimaFittingMetrics;
  }
  /**
   * Repeated as there can be many metric sets (one for each model) in auto-
   * arima and the large-scale case.
   *
   * @param ArimaSingleModelForecastingMetrics[] $arimaSingleModelForecastingMetrics
   */
  public function setArimaSingleModelForecastingMetrics($arimaSingleModelForecastingMetrics)
  {
    $this->arimaSingleModelForecastingMetrics = $arimaSingleModelForecastingMetrics;
  }
  /**
   * @return ArimaSingleModelForecastingMetrics[]
   */
  public function getArimaSingleModelForecastingMetrics()
  {
    return $this->arimaSingleModelForecastingMetrics;
  }
  /**
   * Whether Arima model fitted with drift or not. It is always false when d is
   * not 1.
   *
   * @deprecated
   * @param bool[] $hasDrift
   */
  public function setHasDrift($hasDrift)
  {
    $this->hasDrift = $hasDrift;
  }
  /**
   * @deprecated
   * @return bool[]
   */
  public function getHasDrift()
  {
    return $this->hasDrift;
  }
  /**
   * Non-seasonal order.
   *
   * @deprecated
   * @param ArimaOrder[] $nonSeasonalOrder
   */
  public function setNonSeasonalOrder($nonSeasonalOrder)
  {
    $this->nonSeasonalOrder = $nonSeasonalOrder;
  }
  /**
   * @deprecated
   * @return ArimaOrder[]
   */
  public function getNonSeasonalOrder()
  {
    return $this->nonSeasonalOrder;
  }
  /**
   * Seasonal periods. Repeated because multiple periods are supported for one
   * time series.
   *
   * @deprecated
   * @param string[] $seasonalPeriods
   */
  public function setSeasonalPeriods($seasonalPeriods)
  {
    $this->seasonalPeriods = $seasonalPeriods;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getSeasonalPeriods()
  {
    return $this->seasonalPeriods;
  }
  /**
   * Id to differentiate different time series for the large-scale case.
   *
   * @deprecated
   * @param string[] $timeSeriesId
   */
  public function setTimeSeriesId($timeSeriesId)
  {
    $this->timeSeriesId = $timeSeriesId;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTimeSeriesId()
  {
    return $this->timeSeriesId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArimaForecastingMetrics::class, 'Google_Service_Bigquery_ArimaForecastingMetrics');
