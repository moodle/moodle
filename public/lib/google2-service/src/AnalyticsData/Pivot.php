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

class Pivot extends \Google\Collection
{
  protected $collection_key = 'orderBys';
  /**
   * Dimension names for visible columns in the report response. Including
   * "dateRange" produces a date range column; for each row in the response,
   * dimension values in the date range column will indicate the corresponding
   * date range from the request.
   *
   * @var string[]
   */
  public $fieldNames;
  /**
   * The number of unique combinations of dimension values to return in this
   * pivot. The `limit` parameter is required. A `limit` of 10,000 is common for
   * single pivot requests. The product of the `limit` for each `pivot` in a
   * `RunPivotReportRequest` must not exceed 250,000. For example, a two pivot
   * request with `limit: 1000` in each pivot will fail because the product is
   * `1,000,000`.
   *
   * @var string
   */
  public $limit;
  /**
   * Aggregate the metrics by dimensions in this pivot using the specified
   * metric_aggregations.
   *
   * @var string[]
   */
  public $metricAggregations;
  /**
   * The row count of the start row. The first row is counted as row 0.
   *
   * @var string
   */
  public $offset;
  protected $orderBysType = OrderBy::class;
  protected $orderBysDataType = 'array';

  /**
   * Dimension names for visible columns in the report response. Including
   * "dateRange" produces a date range column; for each row in the response,
   * dimension values in the date range column will indicate the corresponding
   * date range from the request.
   *
   * @param string[] $fieldNames
   */
  public function setFieldNames($fieldNames)
  {
    $this->fieldNames = $fieldNames;
  }
  /**
   * @return string[]
   */
  public function getFieldNames()
  {
    return $this->fieldNames;
  }
  /**
   * The number of unique combinations of dimension values to return in this
   * pivot. The `limit` parameter is required. A `limit` of 10,000 is common for
   * single pivot requests. The product of the `limit` for each `pivot` in a
   * `RunPivotReportRequest` must not exceed 250,000. For example, a two pivot
   * request with `limit: 1000` in each pivot will fail because the product is
   * `1,000,000`.
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
   * Aggregate the metrics by dimensions in this pivot using the specified
   * metric_aggregations.
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
   * The row count of the start row. The first row is counted as row 0.
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
   * Specifies how dimensions are ordered in the pivot. In the first Pivot, the
   * OrderBys determine Row and PivotDimensionHeader ordering; in subsequent
   * Pivots, the OrderBys determine only PivotDimensionHeader ordering.
   * Dimensions specified in these OrderBys must be a subset of
   * Pivot.field_names.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Pivot::class, 'Google_Service_AnalyticsData_Pivot');
