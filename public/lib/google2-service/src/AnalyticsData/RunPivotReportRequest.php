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

namespace Google\Service\AnalyticsData;

class RunPivotReportRequest extends \Google\Collection
{
  protected $collection_key = 'pivots';
  protected $cohortSpecType = CohortSpec::class;
  protected $cohortSpecDataType = '';
  protected $comparisonsType = Comparison::class;
  protected $comparisonsDataType = 'array';
  /**
   * A currency code in ISO4217 format, such as "AED", "USD", "JPY". If the
   * field is empty, the report uses the property's default currency.
   *
   * @var string
   */
  public $currencyCode;
  protected $dateRangesType = DateRange::class;
  protected $dateRangesDataType = 'array';
  protected $dimensionFilterType = FilterExpression::class;
  protected $dimensionFilterDataType = '';
  protected $dimensionsType = Dimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * If false or unspecified, each row with all metrics equal to 0 will not be
   * returned. If true, these rows will be returned if they are not separately
   * removed by a filter. Regardless of this `keep_empty_rows` setting, only
   * data recorded by the Google Analytics property can be displayed in a
   * report. For example if a property never logs a `purchase` event, then a
   * query for the `eventName` dimension and `eventCount` metric will not have a
   * row eventName: "purchase" and eventCount: 0.
   *
   * @var bool
   */
  public $keepEmptyRows;
  protected $metricFilterType = FilterExpression::class;
  protected $metricFilterDataType = '';
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  protected $pivotsType = Pivot::class;
  protected $pivotsDataType = 'array';
  /**
   * A Google Analytics property identifier whose events are tracked. Specified
   * in the URL path and not the body. To learn more, see [where to find your
   * Property ID](https://developers.google.com/analytics/devguides/reporting/da
   * ta/v1/property-id). Within a batch request, this property should either be
   * unspecified or consistent with the batch-level property. Example:
   * properties/1234
   *
   * @var string
   */
  public $property;
  /**
   * Toggles whether to return the current state of this Google Analytics
   * property's quota. Quota is returned in [PropertyQuota](#PropertyQuota).
   *
   * @var bool
   */
  public $returnPropertyQuota;

  /**
   * Cohort group associated with this request. If there is a cohort group in
   * the request the 'cohort' dimension must be present.
   *
   * @param CohortSpec $cohortSpec
   */
  public function setCohortSpec(CohortSpec $cohortSpec)
  {
    $this->cohortSpec = $cohortSpec;
  }
  /**
   * @return CohortSpec
   */
  public function getCohortSpec()
  {
    return $this->cohortSpec;
  }
  /**
   * Optional. The configuration of comparisons requested and displayed. The
   * request requires both a comparisons field and a comparisons dimension to
   * receive a comparison column in the response.
   *
   * @param Comparison[] $comparisons
   */
  public function setComparisons($comparisons)
  {
    $this->comparisons = $comparisons;
  }
  /**
   * @return Comparison[]
   */
  public function getComparisons()
  {
    return $this->comparisons;
  }
  /**
   * A currency code in ISO4217 format, such as "AED", "USD", "JPY". If the
   * field is empty, the report uses the property's default currency.
   *
   * @param string $currencyCode
   */
  public function setCurrencyCode($currencyCode)
  {
    $this->currencyCode = $currencyCode;
  }
  /**
   * @return string
   */
  public function getCurrencyCode()
  {
    return $this->currencyCode;
  }
  /**
   * The date range to retrieve event data for the report. If multiple date
   * ranges are specified, event data from each date range is used in the
   * report. A special dimension with field name "dateRange" can be included in
   * a Pivot's field names; if included, the report compares between date
   * ranges. In a cohort request, this `dateRanges` must be unspecified.
   *
   * @param DateRange[] $dateRanges
   */
  public function setDateRanges($dateRanges)
  {
    $this->dateRanges = $dateRanges;
  }
  /**
   * @return DateRange[]
   */
  public function getDateRanges()
  {
    return $this->dateRanges;
  }
  /**
   * The filter clause of dimensions. Dimensions must be requested to be used in
   * this filter. Metrics cannot be used in this filter.
   *
   * @param FilterExpression $dimensionFilter
   */
  public function setDimensionFilter(FilterExpression $dimensionFilter)
  {
    $this->dimensionFilter = $dimensionFilter;
  }
  /**
   * @return FilterExpression
   */
  public function getDimensionFilter()
  {
    return $this->dimensionFilter;
  }
  /**
   * The dimensions requested. All defined dimensions must be used by one of the
   * following: dimension_expression, dimension_filter, pivots, order_bys.
   *
   * @param Dimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return Dimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * If false or unspecified, each row with all metrics equal to 0 will not be
   * returned. If true, these rows will be returned if they are not separately
   * removed by a filter. Regardless of this `keep_empty_rows` setting, only
   * data recorded by the Google Analytics property can be displayed in a
   * report. For example if a property never logs a `purchase` event, then a
   * query for the `eventName` dimension and `eventCount` metric will not have a
   * row eventName: "purchase" and eventCount: 0.
   *
   * @param bool $keepEmptyRows
   */
  public function setKeepEmptyRows($keepEmptyRows)
  {
    $this->keepEmptyRows = $keepEmptyRows;
  }
  /**
   * @return bool
   */
  public function getKeepEmptyRows()
  {
    return $this->keepEmptyRows;
  }
  /**
   * The filter clause of metrics. Applied at post aggregation phase, similar to
   * SQL having-clause. Metrics must be requested to be used in this filter.
   * Dimensions cannot be used in this filter.
   *
   * @param FilterExpression $metricFilter
   */
  public function setMetricFilter(FilterExpression $metricFilter)
  {
    $this->metricFilter = $metricFilter;
  }
  /**
   * @return FilterExpression
   */
  public function getMetricFilter()
  {
    return $this->metricFilter;
  }
  /**
   * The metrics requested, at least one metric needs to be specified. All
   * defined metrics must be used by one of the following: metric_expression,
   * metric_filter, order_bys.
   *
   * @param Metric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return Metric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Describes the visual format of the report's dimensions in columns or rows.
   * The union of the fieldNames (dimension names) in all pivots must be a
   * subset of dimension names defined in Dimensions. No two pivots can share a
   * dimension. A dimension is only visible if it appears in a pivot.
   *
   * @param Pivot[] $pivots
   */
  public function setPivots($pivots)
  {
    $this->pivots = $pivots;
  }
  /**
   * @return Pivot[]
   */
  public function getPivots()
  {
    return $this->pivots;
  }
  /**
   * A Google Analytics property identifier whose events are tracked. Specified
   * in the URL path and not the body. To learn more, see [where to find your
   * Property ID](https://developers.google.com/analytics/devguides/reporting/da
   * ta/v1/property-id). Within a batch request, this property should either be
   * unspecified or consistent with the batch-level property. Example:
   * properties/1234
   *
   * @param string $property
   */
  public function setProperty($property)
  {
    $this->property = $property;
  }
  /**
   * @return string
   */
  public function getProperty()
  {
    return $this->property;
  }
  /**
   * Toggles whether to return the current state of this Google Analytics
   * property's quota. Quota is returned in [PropertyQuota](#PropertyQuota).
   *
   * @param bool $returnPropertyQuota
   */
  public function setReturnPropertyQuota($returnPropertyQuota)
  {
    $this->returnPropertyQuota = $returnPropertyQuota;
  }
  /**
   * @return bool
   */
  public function getReturnPropertyQuota()
  {
    return $this->returnPropertyQuota;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunPivotReportRequest::class, 'Google_Service_AnalyticsData_RunPivotReportRequest');
