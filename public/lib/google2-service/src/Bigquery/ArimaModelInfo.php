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

class ArimaModelInfo extends \Google\Collection
{
  protected $collection_key = 'timeSeriesIds';
  protected $arimaCoefficientsType = ArimaCoefficients::class;
  protected $arimaCoefficientsDataType = '';
  protected $arimaFittingMetricsType = ArimaFittingMetrics::class;
  protected $arimaFittingMetricsDataType = '';
  /**
   * Whether Arima model fitted with drift or not. It is always false when d is
   * not 1.
   *
   * @var bool
   */
  public $hasDrift;
  /**
   * If true, holiday_effect is a part of time series decomposition result.
   *
   * @var bool
   */
  public $hasHolidayEffect;
  /**
   * If true, spikes_and_dips is a part of time series decomposition result.
   *
   * @var bool
   */
  public $hasSpikesAndDips;
  /**
   * If true, step_changes is a part of time series decomposition result.
   *
   * @var bool
   */
  public $hasStepChanges;
  protected $nonSeasonalOrderType = ArimaOrder::class;
  protected $nonSeasonalOrderDataType = '';
  /**
   * Seasonal periods. Repeated because multiple periods are supported for one
   * time series.
   *
   * @var string[]
   */
  public $seasonalPeriods;
  /**
   * The time_series_id value for this time series. It will be one of the unique
   * values from the time_series_id_column specified during ARIMA model
   * training. Only present when time_series_id_column training option was used.
   *
   * @var string
   */
  public $timeSeriesId;
  /**
   * The tuple of time_series_ids identifying this time series. It will be one
   * of the unique tuples of values present in the time_series_id_columns
   * specified during ARIMA model training. Only present when
   * time_series_id_columns training option was used and the order of values
   * here are same as the order of time_series_id_columns.
   *
   * @var string[]
   */
  public $timeSeriesIds;

  /**
   * Arima coefficients.
   *
   * @param ArimaCoefficients $arimaCoefficients
   */
  public function setArimaCoefficients(ArimaCoefficients $arimaCoefficients)
  {
    $this->arimaCoefficients = $arimaCoefficients;
  }
  /**
   * @return ArimaCoefficients
   */
  public function getArimaCoefficients()
  {
    return $this->arimaCoefficients;
  }
  /**
   * Arima fitting metrics.
   *
   * @param ArimaFittingMetrics $arimaFittingMetrics
   */
  public function setArimaFittingMetrics(ArimaFittingMetrics $arimaFittingMetrics)
  {
    $this->arimaFittingMetrics = $arimaFittingMetrics;
  }
  /**
   * @return ArimaFittingMetrics
   */
  public function getArimaFittingMetrics()
  {
    return $this->arimaFittingMetrics;
  }
  /**
   * Whether Arima model fitted with drift or not. It is always false when d is
   * not 1.
   *
   * @param bool $hasDrift
   */
  public function setHasDrift($hasDrift)
  {
    $this->hasDrift = $hasDrift;
  }
  /**
   * @return bool
   */
  public function getHasDrift()
  {
    return $this->hasDrift;
  }
  /**
   * If true, holiday_effect is a part of time series decomposition result.
   *
   * @param bool $hasHolidayEffect
   */
  public function setHasHolidayEffect($hasHolidayEffect)
  {
    $this->hasHolidayEffect = $hasHolidayEffect;
  }
  /**
   * @return bool
   */
  public function getHasHolidayEffect()
  {
    return $this->hasHolidayEffect;
  }
  /**
   * If true, spikes_and_dips is a part of time series decomposition result.
   *
   * @param bool $hasSpikesAndDips
   */
  public function setHasSpikesAndDips($hasSpikesAndDips)
  {
    $this->hasSpikesAndDips = $hasSpikesAndDips;
  }
  /**
   * @return bool
   */
  public function getHasSpikesAndDips()
  {
    return $this->hasSpikesAndDips;
  }
  /**
   * If true, step_changes is a part of time series decomposition result.
   *
   * @param bool $hasStepChanges
   */
  public function setHasStepChanges($hasStepChanges)
  {
    $this->hasStepChanges = $hasStepChanges;
  }
  /**
   * @return bool
   */
  public function getHasStepChanges()
  {
    return $this->hasStepChanges;
  }
  /**
   * Non-seasonal order.
   *
   * @param ArimaOrder $nonSeasonalOrder
   */
  public function setNonSeasonalOrder(ArimaOrder $nonSeasonalOrder)
  {
    $this->nonSeasonalOrder = $nonSeasonalOrder;
  }
  /**
   * @return ArimaOrder
   */
  public function getNonSeasonalOrder()
  {
    return $this->nonSeasonalOrder;
  }
  /**
   * Seasonal periods. Repeated because multiple periods are supported for one
   * time series.
   *
   * @param string[] $seasonalPeriods
   */
  public function setSeasonalPeriods($seasonalPeriods)
  {
    $this->seasonalPeriods = $seasonalPeriods;
  }
  /**
   * @return string[]
   */
  public function getSeasonalPeriods()
  {
    return $this->seasonalPeriods;
  }
  /**
   * The time_series_id value for this time series. It will be one of the unique
   * values from the time_series_id_column specified during ARIMA model
   * training. Only present when time_series_id_column training option was used.
   *
   * @param string $timeSeriesId
   */
  public function setTimeSeriesId($timeSeriesId)
  {
    $this->timeSeriesId = $timeSeriesId;
  }
  /**
   * @return string
   */
  public function getTimeSeriesId()
  {
    return $this->timeSeriesId;
  }
  /**
   * The tuple of time_series_ids identifying this time series. It will be one
   * of the unique tuples of values present in the time_series_id_columns
   * specified during ARIMA model training. Only present when
   * time_series_id_columns training option was used and the order of values
   * here are same as the order of time_series_id_columns.
   *
   * @param string[] $timeSeriesIds
   */
  public function setTimeSeriesIds($timeSeriesIds)
  {
    $this->timeSeriesIds = $timeSeriesIds;
  }
  /**
   * @return string[]
   */
  public function getTimeSeriesIds()
  {
    return $this->timeSeriesIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArimaModelInfo::class, 'Google_Service_Bigquery_ArimaModelInfo');
