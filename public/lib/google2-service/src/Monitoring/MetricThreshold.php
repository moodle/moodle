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

namespace Google\Service\Monitoring;

class MetricThreshold extends \Google\Collection
{
  /**
   * No ordering relationship is specified.
   */
  public const COMPARISON_COMPARISON_UNSPECIFIED = 'COMPARISON_UNSPECIFIED';
  /**
   * True if the left argument is greater than the right argument.
   */
  public const COMPARISON_COMPARISON_GT = 'COMPARISON_GT';
  /**
   * True if the left argument is greater than or equal to the right argument.
   */
  public const COMPARISON_COMPARISON_GE = 'COMPARISON_GE';
  /**
   * True if the left argument is less than the right argument.
   */
  public const COMPARISON_COMPARISON_LT = 'COMPARISON_LT';
  /**
   * True if the left argument is less than or equal to the right argument.
   */
  public const COMPARISON_COMPARISON_LE = 'COMPARISON_LE';
  /**
   * True if the left argument is equal to the right argument.
   */
  public const COMPARISON_COMPARISON_EQ = 'COMPARISON_EQ';
  /**
   * True if the left argument is not equal to the right argument.
   */
  public const COMPARISON_COMPARISON_NE = 'COMPARISON_NE';
  /**
   * An unspecified evaluation missing data option. Equivalent to
   * EVALUATION_MISSING_DATA_NO_OP.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_UNSPECIFIED = 'EVALUATION_MISSING_DATA_UNSPECIFIED';
  /**
   * If there is no data to evaluate the condition, then evaluate the condition
   * as false.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_INACTIVE = 'EVALUATION_MISSING_DATA_INACTIVE';
  /**
   * If there is no data to evaluate the condition, then evaluate the condition
   * as true.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_ACTIVE = 'EVALUATION_MISSING_DATA_ACTIVE';
  /**
   * Do not evaluate the condition to any value if there is no data.
   */
  public const EVALUATION_MISSING_DATA_EVALUATION_MISSING_DATA_NO_OP = 'EVALUATION_MISSING_DATA_NO_OP';
  protected $collection_key = 'denominatorAggregations';
  protected $aggregationsType = Aggregation::class;
  protected $aggregationsDataType = 'array';
  /**
   * The comparison to apply between the time series (indicated by filter and
   * aggregation) and the threshold (indicated by threshold_value). The
   * comparison is applied on each time series, with the time series on the
   * left-hand side and the threshold on the right-hand side.Only COMPARISON_LT
   * and COMPARISON_GT are supported currently.
   *
   * @var string
   */
  public $comparison;
  protected $denominatorAggregationsType = Aggregation::class;
  protected $denominatorAggregationsDataType = 'array';
  /**
   * A filter (https://cloud.google.com/monitoring/api/v3/filters) that
   * identifies a time series that should be used as the denominator of a ratio
   * that will be compared with the threshold. If a denominator_filter is
   * specified, the time series specified by the filter field will be used as
   * the numerator.The filter must specify the metric type and optionally may
   * contain restrictions on resource type, resource labels, and metric labels.
   * This field may not exceed 2048 Unicode characters in length.
   *
   * @var string
   */
  public $denominatorFilter;
  /**
   * Required. The amount of time that a time series must violate the threshold
   * to be considered failing. Currently, only values that are a multiple of a
   * minute--e.g., 0, 60, 120, or 300 seconds--are supported. If an invalid
   * value is given, an error will be returned. When choosing a duration, it is
   * useful to keep in mind the frequency of the underlying time series data
   * (which may also be affected by any alignments specified in the aggregations
   * field); a good duration is long enough so that a single outlier does not
   * generate spurious alerts, but short enough that unhealthy states are
   * detected and alerted on quickly.
   *
   * @var string
   */
  public $duration;
  /**
   * A condition control that determines how metric-threshold conditions are
   * evaluated when data stops arriving. To use this control, the value of the
   * duration field must be greater than or equal to 60 seconds.
   *
   * @var string
   */
  public $evaluationMissingData;
  /**
   * Required. A filter (https://cloud.google.com/monitoring/api/v3/filters)
   * that identifies which time series should be compared with the threshold.The
   * filter is similar to the one that is specified in the ListTimeSeries
   * request (https://cloud.google.com/monitoring/api/ref_v3/rest/v3/projects.ti
   * meSeries/list) (that call is useful to verify the time series that will be
   * retrieved / processed). The filter must specify the metric type and the
   * resource type. Optionally, it can specify resource labels and metric
   * labels. This field must not exceed 2048 Unicode characters in length.
   *
   * @var string
   */
  public $filter;
  protected $forecastOptionsType = ForecastOptions::class;
  protected $forecastOptionsDataType = '';
  /**
   * A value against which to compare the time series.
   *
   * @var 
   */
  public $thresholdValue;
  protected $triggerType = Trigger::class;
  protected $triggerDataType = '';

  /**
   * Specifies the alignment of data points in individual time series as well as
   * how to combine the retrieved time series together (such as when aggregating
   * multiple streams on each resource to a single stream for each resource or
   * when aggregating streams across all members of a group of resources).
   * Multiple aggregations are applied in the order specified.This field is
   * similar to the one in the ListTimeSeries request (https://cloud.google.com/
   * monitoring/api/ref_v3/rest/v3/projects.timeSeries/list). It is advisable to
   * use the ListTimeSeries method when debugging this field.
   *
   * @param Aggregation[] $aggregations
   */
  public function setAggregations($aggregations)
  {
    $this->aggregations = $aggregations;
  }
  /**
   * @return Aggregation[]
   */
  public function getAggregations()
  {
    return $this->aggregations;
  }
  /**
   * The comparison to apply between the time series (indicated by filter and
   * aggregation) and the threshold (indicated by threshold_value). The
   * comparison is applied on each time series, with the time series on the
   * left-hand side and the threshold on the right-hand side.Only COMPARISON_LT
   * and COMPARISON_GT are supported currently.
   *
   * Accepted values: COMPARISON_UNSPECIFIED, COMPARISON_GT, COMPARISON_GE,
   * COMPARISON_LT, COMPARISON_LE, COMPARISON_EQ, COMPARISON_NE
   *
   * @param self::COMPARISON_* $comparison
   */
  public function setComparison($comparison)
  {
    $this->comparison = $comparison;
  }
  /**
   * @return self::COMPARISON_*
   */
  public function getComparison()
  {
    return $this->comparison;
  }
  /**
   * Specifies the alignment of data points in individual time series selected
   * by denominatorFilter as well as how to combine the retrieved time series
   * together (such as when aggregating multiple streams on each resource to a
   * single stream for each resource or when aggregating streams across all
   * members of a group of resources).When computing ratios, the aggregations
   * and denominator_aggregations fields must use the same alignment period and
   * produce time series that have the same periodicity and labels.
   *
   * @param Aggregation[] $denominatorAggregations
   */
  public function setDenominatorAggregations($denominatorAggregations)
  {
    $this->denominatorAggregations = $denominatorAggregations;
  }
  /**
   * @return Aggregation[]
   */
  public function getDenominatorAggregations()
  {
    return $this->denominatorAggregations;
  }
  /**
   * A filter (https://cloud.google.com/monitoring/api/v3/filters) that
   * identifies a time series that should be used as the denominator of a ratio
   * that will be compared with the threshold. If a denominator_filter is
   * specified, the time series specified by the filter field will be used as
   * the numerator.The filter must specify the metric type and optionally may
   * contain restrictions on resource type, resource labels, and metric labels.
   * This field may not exceed 2048 Unicode characters in length.
   *
   * @param string $denominatorFilter
   */
  public function setDenominatorFilter($denominatorFilter)
  {
    $this->denominatorFilter = $denominatorFilter;
  }
  /**
   * @return string
   */
  public function getDenominatorFilter()
  {
    return $this->denominatorFilter;
  }
  /**
   * Required. The amount of time that a time series must violate the threshold
   * to be considered failing. Currently, only values that are a multiple of a
   * minute--e.g., 0, 60, 120, or 300 seconds--are supported. If an invalid
   * value is given, an error will be returned. When choosing a duration, it is
   * useful to keep in mind the frequency of the underlying time series data
   * (which may also be affected by any alignments specified in the aggregations
   * field); a good duration is long enough so that a single outlier does not
   * generate spurious alerts, but short enough that unhealthy states are
   * detected and alerted on quickly.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * A condition control that determines how metric-threshold conditions are
   * evaluated when data stops arriving. To use this control, the value of the
   * duration field must be greater than or equal to 60 seconds.
   *
   * Accepted values: EVALUATION_MISSING_DATA_UNSPECIFIED,
   * EVALUATION_MISSING_DATA_INACTIVE, EVALUATION_MISSING_DATA_ACTIVE,
   * EVALUATION_MISSING_DATA_NO_OP
   *
   * @param self::EVALUATION_MISSING_DATA_* $evaluationMissingData
   */
  public function setEvaluationMissingData($evaluationMissingData)
  {
    $this->evaluationMissingData = $evaluationMissingData;
  }
  /**
   * @return self::EVALUATION_MISSING_DATA_*
   */
  public function getEvaluationMissingData()
  {
    return $this->evaluationMissingData;
  }
  /**
   * Required. A filter (https://cloud.google.com/monitoring/api/v3/filters)
   * that identifies which time series should be compared with the threshold.The
   * filter is similar to the one that is specified in the ListTimeSeries
   * request (https://cloud.google.com/monitoring/api/ref_v3/rest/v3/projects.ti
   * meSeries/list) (that call is useful to verify the time series that will be
   * retrieved / processed). The filter must specify the metric type and the
   * resource type. Optionally, it can specify resource labels and metric
   * labels. This field must not exceed 2048 Unicode characters in length.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * When this field is present, the MetricThreshold condition forecasts whether
   * the time series is predicted to violate the threshold within the
   * forecast_horizon. When this field is not set, the MetricThreshold tests the
   * current value of the timeseries against the threshold.
   *
   * @param ForecastOptions $forecastOptions
   */
  public function setForecastOptions(ForecastOptions $forecastOptions)
  {
    $this->forecastOptions = $forecastOptions;
  }
  /**
   * @return ForecastOptions
   */
  public function getForecastOptions()
  {
    return $this->forecastOptions;
  }
  public function setThresholdValue($thresholdValue)
  {
    $this->thresholdValue = $thresholdValue;
  }
  public function getThresholdValue()
  {
    return $this->thresholdValue;
  }
  /**
   * The number/percent of time series for which the comparison must hold in
   * order for the condition to trigger. If unspecified, then the condition will
   * trigger if the comparison is true for any of the time series that have been
   * identified by filter and aggregations, or by the ratio, if
   * denominator_filter and denominator_aggregations are specified.
   *
   * @param Trigger $trigger
   */
  public function setTrigger(Trigger $trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return Trigger
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricThreshold::class, 'Google_Service_Monitoring_MetricThreshold');
