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

class RunReportRequest extends \Google\Collection
{
  protected $collection_key = 'orderBys';
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
  /**
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 250,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API can also return fewer rows than
   * the requested `limit`, if there aren't as many dimension values as the
   * `limit`. For instance, there are fewer than 300 possible values for the
   * dimension `country`, so when reporting on only `country`, you can't get
   * more than 300 rows, even if you set `limit` to a higher value. To learn
   * more about this pagination parameter, see [Pagination](https://developers.g
   * oogle.com/analytics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var string
   */
  public $limit;
  /**
   * Aggregation of metrics. Aggregated metric values will be shown in rows
   * where the dimension_values are set to "RESERVED_(MetricAggregation)".
   * Aggregates including both comparisons and multiple date ranges will be
   * aggregated based on the date ranges.
   *
   * @var string[]
   */
  public $metricAggregations;
  protected $metricFilterType = FilterExpression::class;
  protected $metricFilterDataType = '';
  protected $metricsType = Metric::class;
  protected $metricsDataType = 'array';
  /**
   * The row count of the start row. The first row is counted as row 0. When
   * paging, the first request does not specify offset; or equivalently, sets
   * offset to 0; the first request returns the first `limit` of rows. The
   * second request sets offset to the `limit` of the first request; the second
   * request returns the second `limit` of rows. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var string
   */
  public $offset;
  protected $orderBysType = OrderBy::class;
  protected $orderBysDataType = 'array';
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
   * request only requires a comparisons field in order to receive a comparison
   * column in the response.
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
   * Date ranges of data to read. If multiple date ranges are requested, each
   * response row will contain a zero based date range index. If two date ranges
   * overlap, the event data for the overlapping days is included in the
   * response rows for both date ranges. In a cohort request, this `dateRanges`
   * must be unspecified.
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
   * Dimension filters let you ask for only specific dimension values in the
   * report. To learn more, see [Fundamentals of Dimension Filters](https://deve
   * lopers.google.com/analytics/devguides/reporting/data/v1/basics#dimension_fi
   * lters) for examples. Metrics cannot be used in this filter.
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
   * The dimensions requested and displayed.
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
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 250,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API can also return fewer rows than
   * the requested `limit`, if there aren't as many dimension values as the
   * `limit`. For instance, there are fewer than 300 possible values for the
   * dimension `country`, so when reporting on only `country`, you can't get
   * more than 300 rows, even if you set `limit` to a higher value. To learn
   * more about this pagination parameter, see [Pagination](https://developers.g
   * oogle.com/analytics/devguides/reporting/data/v1/basics#pagination).
   *
   * @param string $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return string
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * Aggregation of metrics. Aggregated metric values will be shown in rows
   * where the dimension_values are set to "RESERVED_(MetricAggregation)".
   * Aggregates including both comparisons and multiple date ranges will be
   * aggregated based on the date ranges.
   *
   * @param string[] $metricAggregations
   */
  public function setMetricAggregations($metricAggregations)
  {
    $this->metricAggregations = $metricAggregations;
  }
  /**
   * @return string[]
   */
  public function getMetricAggregations()
  {
    return $this->metricAggregations;
  }
  /**
   * The filter clause of metrics. Applied after aggregating the report's rows,
   * similar to SQL having-clause. Dimensions cannot be used in this filter.
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
   * The metrics requested and displayed.
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
   * The row count of the start row. The first row is counted as row 0. When
   * paging, the first request does not specify offset; or equivalently, sets
   * offset to 0; the first request returns the first `limit` of rows. The
   * second request sets offset to the `limit` of the first request; the second
   * request returns the second `limit` of rows. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
   *
   * @param string $offset
   */
  public function setOffset($offset)
  {
    $this->offset = $offset;
  }
  /**
   * @return string
   */
  public function getOffset()
  {
    return $this->offset;
  }
  /**
   * Specifies how rows are ordered in the response. Requests including both
   * comparisons and multiple date ranges will have order bys applied on the
   * comparisons.
   *
   * @param OrderBy[] $orderBys
   */
  public function setOrderBys($orderBys)
  {
    $this->orderBys = $orderBys;
  }
  /**
   * @return OrderBy[]
   */
  public function getOrderBys()
  {
    return $this->orderBys;
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
class_alias(RunReportRequest::class, 'Google_Service_AnalyticsData_RunReportRequest');
