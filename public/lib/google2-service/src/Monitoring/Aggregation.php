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

class Aggregation extends \Google\Collection
{
  /**
   * No cross-time series reduction. The output of the Aligner is returned.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_NONE = 'REDUCE_NONE';
  /**
   * Reduce by computing the mean value across time series for each alignment
   * period. This reducer is valid for DELTA and GAUGE metrics with numeric or
   * distribution values. The value_type of the output is DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_MEAN = 'REDUCE_MEAN';
  /**
   * Reduce by computing the minimum value across time series for each alignment
   * period. This reducer is valid for DELTA and GAUGE metrics with numeric
   * values. The value_type of the output is the same as the value_type of the
   * input.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_MIN = 'REDUCE_MIN';
  /**
   * Reduce by computing the maximum value across time series for each alignment
   * period. This reducer is valid for DELTA and GAUGE metrics with numeric
   * values. The value_type of the output is the same as the value_type of the
   * input.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_MAX = 'REDUCE_MAX';
  /**
   * Reduce by computing the sum across time series for each alignment period.
   * This reducer is valid for DELTA and GAUGE metrics with numeric and
   * distribution values. The value_type of the output is the same as the
   * value_type of the input.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_SUM = 'REDUCE_SUM';
  /**
   * Reduce by computing the standard deviation across time series for each
   * alignment period. This reducer is valid for DELTA and GAUGE metrics with
   * numeric or distribution values. The value_type of the output is DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_STDDEV = 'REDUCE_STDDEV';
  /**
   * Reduce by computing the number of data points across time series for each
   * alignment period. This reducer is valid for DELTA and GAUGE metrics of
   * numeric, Boolean, distribution, and string value_type. The value_type of
   * the output is INT64.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_COUNT = 'REDUCE_COUNT';
  /**
   * Reduce by computing the number of True-valued data points across time
   * series for each alignment period. This reducer is valid for DELTA and GAUGE
   * metrics of Boolean value_type. The value_type of the output is INT64.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_COUNT_TRUE = 'REDUCE_COUNT_TRUE';
  /**
   * Reduce by computing the number of False-valued data points across time
   * series for each alignment period. This reducer is valid for DELTA and GAUGE
   * metrics of Boolean value_type. The value_type of the output is INT64.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_COUNT_FALSE = 'REDUCE_COUNT_FALSE';
  /**
   * Reduce by computing the ratio of the number of True-valued data points to
   * the total number of data points for each alignment period. This reducer is
   * valid for DELTA and GAUGE metrics of Boolean value_type. The output value
   * is in the range 0.0, 1.0 and has value_type DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_FRACTION_TRUE = 'REDUCE_FRACTION_TRUE';
  /**
   * Reduce by computing the 99th percentile
   * (https://en.wikipedia.org/wiki/Percentile) of data points across time
   * series for each alignment period. This reducer is valid for GAUGE and DELTA
   * metrics of numeric and distribution type. The value of the output is
   * DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_PERCENTILE_99 = 'REDUCE_PERCENTILE_99';
  /**
   * Reduce by computing the 95th percentile
   * (https://en.wikipedia.org/wiki/Percentile) of data points across time
   * series for each alignment period. This reducer is valid for GAUGE and DELTA
   * metrics of numeric and distribution type. The value of the output is
   * DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_PERCENTILE_95 = 'REDUCE_PERCENTILE_95';
  /**
   * Reduce by computing the 50th percentile
   * (https://en.wikipedia.org/wiki/Percentile) of data points across time
   * series for each alignment period. This reducer is valid for GAUGE and DELTA
   * metrics of numeric and distribution type. The value of the output is
   * DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_PERCENTILE_50 = 'REDUCE_PERCENTILE_50';
  /**
   * Reduce by computing the 5th percentile
   * (https://en.wikipedia.org/wiki/Percentile) of data points across time
   * series for each alignment period. This reducer is valid for GAUGE and DELTA
   * metrics of numeric and distribution type. The value of the output is
   * DOUBLE.
   */
  public const CROSS_SERIES_REDUCER_REDUCE_PERCENTILE_05 = 'REDUCE_PERCENTILE_05';
  /**
   * No alignment. Raw data is returned. Not valid if cross-series reduction is
   * requested. The value_type of the result is the same as the value_type of
   * the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_NONE = 'ALIGN_NONE';
  /**
   * Align and convert to DELTA. The output is delta = y1 - y0.This alignment is
   * valid for CUMULATIVE and DELTA metrics. If the selected alignment period
   * results in periods with no data, then the aligned value for such a period
   * is created by interpolation. The value_type of the aligned result is the
   * same as the value_type of the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_DELTA = 'ALIGN_DELTA';
  /**
   * Align and convert to a rate. The result is computed as rate = (y1 - y0)/(t1
   * - t0), or "delta over time". Think of this aligner as providing the slope
   * of the line that passes through the value at the start and at the end of
   * the alignment_period.This aligner is valid for CUMULATIVE and DELTA metrics
   * with numeric values. If the selected alignment period results in periods
   * with no data, then the aligned value for such a period is created by
   * interpolation. The output is a GAUGE metric with value_type DOUBLE.If, by
   * "rate", you mean "percentage change", see the ALIGN_PERCENT_CHANGE aligner
   * instead.
   */
  public const PER_SERIES_ALIGNER_ALIGN_RATE = 'ALIGN_RATE';
  /**
   * Align by interpolating between adjacent points around the alignment period
   * boundary. This aligner is valid for GAUGE metrics with numeric values. The
   * value_type of the aligned result is the same as the value_type of the
   * input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_INTERPOLATE = 'ALIGN_INTERPOLATE';
  /**
   * Align by moving the most recent data point before the end of the alignment
   * period to the boundary at the end of the alignment period. This aligner is
   * valid for GAUGE metrics. The value_type of the aligned result is the same
   * as the value_type of the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_NEXT_OLDER = 'ALIGN_NEXT_OLDER';
  /**
   * Align the time series by returning the minimum value in each alignment
   * period. This aligner is valid for GAUGE and DELTA metrics with numeric
   * values. The value_type of the aligned result is the same as the value_type
   * of the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_MIN = 'ALIGN_MIN';
  /**
   * Align the time series by returning the maximum value in each alignment
   * period. This aligner is valid for GAUGE and DELTA metrics with numeric
   * values. The value_type of the aligned result is the same as the value_type
   * of the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_MAX = 'ALIGN_MAX';
  /**
   * Align the time series by returning the mean value in each alignment period.
   * This aligner is valid for GAUGE and DELTA metrics with numeric values. The
   * value_type of the aligned result is DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_MEAN = 'ALIGN_MEAN';
  /**
   * Align the time series by returning the number of values in each alignment
   * period. This aligner is valid for GAUGE and DELTA metrics with numeric or
   * Boolean values. The value_type of the aligned result is INT64.
   */
  public const PER_SERIES_ALIGNER_ALIGN_COUNT = 'ALIGN_COUNT';
  /**
   * Align the time series by returning the sum of the values in each alignment
   * period. This aligner is valid for GAUGE and DELTA metrics with numeric and
   * distribution values. The value_type of the aligned result is the same as
   * the value_type of the input.
   */
  public const PER_SERIES_ALIGNER_ALIGN_SUM = 'ALIGN_SUM';
  /**
   * Align the time series by returning the standard deviation of the values in
   * each alignment period. This aligner is valid for GAUGE and DELTA metrics
   * with numeric values. The value_type of the output is DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_STDDEV = 'ALIGN_STDDEV';
  /**
   * Align the time series by returning the number of True values in each
   * alignment period. This aligner is valid for GAUGE metrics with Boolean
   * values. The value_type of the output is INT64.
   */
  public const PER_SERIES_ALIGNER_ALIGN_COUNT_TRUE = 'ALIGN_COUNT_TRUE';
  /**
   * Align the time series by returning the number of False values in each
   * alignment period. This aligner is valid for GAUGE metrics with Boolean
   * values. The value_type of the output is INT64.
   */
  public const PER_SERIES_ALIGNER_ALIGN_COUNT_FALSE = 'ALIGN_COUNT_FALSE';
  /**
   * Align the time series by returning the ratio of the number of True values
   * to the total number of values in each alignment period. This aligner is
   * valid for GAUGE metrics with Boolean values. The output value is in the
   * range 0.0, 1.0 and has value_type DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_FRACTION_TRUE = 'ALIGN_FRACTION_TRUE';
  /**
   * Align the time series by using percentile aggregation
   * (https://en.wikipedia.org/wiki/Percentile). The resulting data point in
   * each alignment period is the 99th percentile of all data points in the
   * period. This aligner is valid for GAUGE and DELTA metrics with distribution
   * values. The output is a GAUGE metric with value_type DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_PERCENTILE_99 = 'ALIGN_PERCENTILE_99';
  /**
   * Align the time series by using percentile aggregation
   * (https://en.wikipedia.org/wiki/Percentile). The resulting data point in
   * each alignment period is the 95th percentile of all data points in the
   * period. This aligner is valid for GAUGE and DELTA metrics with distribution
   * values. The output is a GAUGE metric with value_type DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_PERCENTILE_95 = 'ALIGN_PERCENTILE_95';
  /**
   * Align the time series by using percentile aggregation
   * (https://en.wikipedia.org/wiki/Percentile). The resulting data point in
   * each alignment period is the 50th percentile of all data points in the
   * period. This aligner is valid for GAUGE and DELTA metrics with distribution
   * values. The output is a GAUGE metric with value_type DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_PERCENTILE_50 = 'ALIGN_PERCENTILE_50';
  /**
   * Align the time series by using percentile aggregation
   * (https://en.wikipedia.org/wiki/Percentile). The resulting data point in
   * each alignment period is the 5th percentile of all data points in the
   * period. This aligner is valid for GAUGE and DELTA metrics with distribution
   * values. The output is a GAUGE metric with value_type DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_PERCENTILE_05 = 'ALIGN_PERCENTILE_05';
  /**
   * Align and convert to a percentage change. This aligner is valid for GAUGE
   * and DELTA metrics with numeric values. This alignment returns ((current -
   * previous)/previous) * 100, where the value of previous is determined based
   * on the alignment_period.If the values of current and previous are both 0,
   * then the returned value is 0. If only previous is 0, the returned value is
   * infinity.A 10-minute moving mean is computed at each point of the alignment
   * period prior to the above calculation to smooth the metric and prevent
   * false positives from very short-lived spikes. The moving mean is only
   * applicable for data whose values are >= 0. Any values < 0 are treated as a
   * missing datapoint, and are ignored. While DELTA metrics are accepted by
   * this alignment, special care should be taken that the values for the metric
   * will always be positive. The output is a GAUGE metric with value_type
   * DOUBLE.
   */
  public const PER_SERIES_ALIGNER_ALIGN_PERCENT_CHANGE = 'ALIGN_PERCENT_CHANGE';
  protected $collection_key = 'groupByFields';
  /**
   * The alignment_period specifies a time interval, in seconds, that is used to
   * divide the data in all the time series into consistent blocks of time. This
   * will be done before the per-series aligner can be applied to the data.The
   * value must be at least 60 seconds. If a per-series aligner other than
   * ALIGN_NONE is specified, this field is required or an error is returned. If
   * no per-series aligner is specified, or the aligner ALIGN_NONE is specified,
   * then this field is ignored.The maximum value of the alignment_period is 104
   * weeks (2 years) for charts, and 90,000 seconds (25 hours) for alerting
   * policies.
   *
   * @var string
   */
  public $alignmentPeriod;
  /**
   * The reduction operation to be used to combine time series into a single
   * time series, where the value of each data point in the resulting series is
   * a function of all the already aligned values in the input time series.Not
   * all reducer operations can be applied to all time series. The valid choices
   * depend on the metric_kind and the value_type of the original time series.
   * Reduction can yield a time series with a different metric_kind or
   * value_type than the input time series.Time series data must first be
   * aligned (see per_series_aligner) in order to perform cross-time series
   * reduction. If cross_series_reducer is specified, then per_series_aligner
   * must be specified, and must not be ALIGN_NONE. An alignment_period must
   * also be specified; otherwise, an error is returned.
   *
   * @var string
   */
  public $crossSeriesReducer;
  /**
   * The set of fields to preserve when cross_series_reducer is specified. The
   * group_by_fields determine how the time series are partitioned into subsets
   * prior to applying the aggregation operation. Each subset contains time
   * series that have the same value for each of the grouping fields. Each
   * individual time series is a member of exactly one subset. The
   * cross_series_reducer is applied to each subset of time series. It is not
   * possible to reduce across different resource types, so this field
   * implicitly contains resource.type. Fields not specified in group_by_fields
   * are aggregated away. If group_by_fields is not specified and all the time
   * series have the same resource type, then the time series are aggregated
   * into a single output time series. If cross_series_reducer is not defined,
   * this field is ignored.
   *
   * @var string[]
   */
  public $groupByFields;
  /**
   * An Aligner describes how to bring the data points in a single time series
   * into temporal alignment. Except for ALIGN_NONE, all alignments cause all
   * the data points in an alignment_period to be mathematically grouped
   * together, resulting in a single data point for each alignment_period with
   * end timestamp at the end of the period.Not all alignment operations may be
   * applied to all time series. The valid choices depend on the metric_kind and
   * value_type of the original time series. Alignment can change the
   * metric_kind or the value_type of the time series.Time series data must be
   * aligned in order to perform cross-time series reduction. If
   * cross_series_reducer is specified, then per_series_aligner must be
   * specified and not equal to ALIGN_NONE and alignment_period must be
   * specified; otherwise, an error is returned.
   *
   * @var string
   */
  public $perSeriesAligner;

  /**
   * The alignment_period specifies a time interval, in seconds, that is used to
   * divide the data in all the time series into consistent blocks of time. This
   * will be done before the per-series aligner can be applied to the data.The
   * value must be at least 60 seconds. If a per-series aligner other than
   * ALIGN_NONE is specified, this field is required or an error is returned. If
   * no per-series aligner is specified, or the aligner ALIGN_NONE is specified,
   * then this field is ignored.The maximum value of the alignment_period is 104
   * weeks (2 years) for charts, and 90,000 seconds (25 hours) for alerting
   * policies.
   *
   * @param string $alignmentPeriod
   */
  public function setAlignmentPeriod($alignmentPeriod)
  {
    $this->alignmentPeriod = $alignmentPeriod;
  }
  /**
   * @return string
   */
  public function getAlignmentPeriod()
  {
    return $this->alignmentPeriod;
  }
  /**
   * The reduction operation to be used to combine time series into a single
   * time series, where the value of each data point in the resulting series is
   * a function of all the already aligned values in the input time series.Not
   * all reducer operations can be applied to all time series. The valid choices
   * depend on the metric_kind and the value_type of the original time series.
   * Reduction can yield a time series with a different metric_kind or
   * value_type than the input time series.Time series data must first be
   * aligned (see per_series_aligner) in order to perform cross-time series
   * reduction. If cross_series_reducer is specified, then per_series_aligner
   * must be specified, and must not be ALIGN_NONE. An alignment_period must
   * also be specified; otherwise, an error is returned.
   *
   * Accepted values: REDUCE_NONE, REDUCE_MEAN, REDUCE_MIN, REDUCE_MAX,
   * REDUCE_SUM, REDUCE_STDDEV, REDUCE_COUNT, REDUCE_COUNT_TRUE,
   * REDUCE_COUNT_FALSE, REDUCE_FRACTION_TRUE, REDUCE_PERCENTILE_99,
   * REDUCE_PERCENTILE_95, REDUCE_PERCENTILE_50, REDUCE_PERCENTILE_05
   *
   * @param self::CROSS_SERIES_REDUCER_* $crossSeriesReducer
   */
  public function setCrossSeriesReducer($crossSeriesReducer)
  {
    $this->crossSeriesReducer = $crossSeriesReducer;
  }
  /**
   * @return self::CROSS_SERIES_REDUCER_*
   */
  public function getCrossSeriesReducer()
  {
    return $this->crossSeriesReducer;
  }
  /**
   * The set of fields to preserve when cross_series_reducer is specified. The
   * group_by_fields determine how the time series are partitioned into subsets
   * prior to applying the aggregation operation. Each subset contains time
   * series that have the same value for each of the grouping fields. Each
   * individual time series is a member of exactly one subset. The
   * cross_series_reducer is applied to each subset of time series. It is not
   * possible to reduce across different resource types, so this field
   * implicitly contains resource.type. Fields not specified in group_by_fields
   * are aggregated away. If group_by_fields is not specified and all the time
   * series have the same resource type, then the time series are aggregated
   * into a single output time series. If cross_series_reducer is not defined,
   * this field is ignored.
   *
   * @param string[] $groupByFields
   */
  public function setGroupByFields($groupByFields)
  {
    $this->groupByFields = $groupByFields;
  }
  /**
   * @return string[]
   */
  public function getGroupByFields()
  {
    return $this->groupByFields;
  }
  /**
   * An Aligner describes how to bring the data points in a single time series
   * into temporal alignment. Except for ALIGN_NONE, all alignments cause all
   * the data points in an alignment_period to be mathematically grouped
   * together, resulting in a single data point for each alignment_period with
   * end timestamp at the end of the period.Not all alignment operations may be
   * applied to all time series. The valid choices depend on the metric_kind and
   * value_type of the original time series. Alignment can change the
   * metric_kind or the value_type of the time series.Time series data must be
   * aligned in order to perform cross-time series reduction. If
   * cross_series_reducer is specified, then per_series_aligner must be
   * specified and not equal to ALIGN_NONE and alignment_period must be
   * specified; otherwise, an error is returned.
   *
   * Accepted values: ALIGN_NONE, ALIGN_DELTA, ALIGN_RATE, ALIGN_INTERPOLATE,
   * ALIGN_NEXT_OLDER, ALIGN_MIN, ALIGN_MAX, ALIGN_MEAN, ALIGN_COUNT, ALIGN_SUM,
   * ALIGN_STDDEV, ALIGN_COUNT_TRUE, ALIGN_COUNT_FALSE, ALIGN_FRACTION_TRUE,
   * ALIGN_PERCENTILE_99, ALIGN_PERCENTILE_95, ALIGN_PERCENTILE_50,
   * ALIGN_PERCENTILE_05, ALIGN_PERCENT_CHANGE
   *
   * @param self::PER_SERIES_ALIGNER_* $perSeriesAligner
   */
  public function setPerSeriesAligner($perSeriesAligner)
  {
    $this->perSeriesAligner = $perSeriesAligner;
  }
  /**
   * @return self::PER_SERIES_ALIGNER_*
   */
  public function getPerSeriesAligner()
  {
    return $this->perSeriesAligner;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Aggregation::class, 'Google_Service_Monitoring_Aggregation');
