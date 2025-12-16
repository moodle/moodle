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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaRunAccessReportRequest extends \Google\Collection
{
  protected $collection_key = 'orderBys';
  protected $dateRangesType = GoogleAnalyticsAdminV1betaAccessDateRange::class;
  protected $dateRangesDataType = 'array';
  protected $dimensionFilterType = GoogleAnalyticsAdminV1betaAccessFilterExpression::class;
  protected $dimensionFilterDataType = '';
  protected $dimensionsType = GoogleAnalyticsAdminV1betaAccessDimension::class;
  protected $dimensionsDataType = 'array';
  /**
   * Optional. Decides whether to return the users within user groups. This
   * field works only when include_all_users is set to true. If true, it will
   * return all users with access to the specified property or account. If
   * false, only the users with direct access will be returned.
   *
   * @var bool
   */
  public $expandGroups;
  /**
   * Optional. Determines whether to include users who have never made an API
   * call in the response. If true, all users with access to the specified
   * property or account are included in the response, regardless of whether
   * they have made an API call or not. If false, only the users who have made
   * an API call will be included.
   *
   * @var bool
   */
  public $includeAllUsers;
  /**
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 100,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API may return fewer rows than the
   * requested `limit`, if there aren't as many remaining rows as the `limit`.
   * For instance, there are fewer than 300 possible values for the dimension
   * `country`, so when reporting on only `country`, you can't get more than 300
   * rows, even if you set `limit` to a higher value. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var string
   */
  public $limit;
  protected $metricFilterType = GoogleAnalyticsAdminV1betaAccessFilterExpression::class;
  protected $metricFilterDataType = '';
  protected $metricsType = GoogleAnalyticsAdminV1betaAccessMetric::class;
  protected $metricsDataType = 'array';
  /**
   * The row count of the start row. The first row is counted as row 0. If
   * offset is unspecified, it is treated as 0. If offset is zero, then this
   * method will return the first page of results with `limit` entries. To learn
   * more about this pagination parameter, see [Pagination](https://developers.g
   * oogle.com/analytics/devguides/reporting/data/v1/basics#pagination).
   *
   * @var string
   */
  public $offset;
  protected $orderBysType = GoogleAnalyticsAdminV1betaAccessOrderBy::class;
  protected $orderBysDataType = 'array';
  /**
   * Toggles whether to return the current state of this Analytics Property's
   * quota. Quota is returned in [AccessQuota](#AccessQuota). For account-level
   * requests, this field must be false.
   *
   * @var bool
   */
  public $returnEntityQuota;
  /**
   * This request's time zone if specified. If unspecified, the property's time
   * zone is used. The request's time zone is used to interpret the start & end
   * dates of the report. Formatted as strings from the IANA Time Zone database
   * (https://www.iana.org/time-zones); for example "America/New_York" or
   * "Asia/Tokyo".
   *
   * @var string
   */
  public $timeZone;

  /**
   * Date ranges of access records to read. If multiple date ranges are
   * requested, each response row will contain a zero based date range index. If
   * two date ranges overlap, the access records for the overlapping days is
   * included in the response rows for both date ranges. Requests are allowed up
   * to 2 date ranges.
   *
   * @param GoogleAnalyticsAdminV1betaAccessDateRange[] $dateRanges
   */
  public function setDateRanges($dateRanges)
  {
    $this->dateRanges = $dateRanges;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessDateRange[]
   */
  public function getDateRanges()
  {
    return $this->dateRanges;
  }
  /**
   * Dimension filters let you restrict report response to specific dimension
   * values which match the filter. For example, filtering on access records of
   * a single user. To learn more, see [Fundamentals of Dimension Filters](https
   * ://developers.google.com/analytics/devguides/reporting/data/v1/basics#dimen
   * sion_filters) for examples. Metrics cannot be used in this filter.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilterExpression $dimensionFilter
   */
  public function setDimensionFilter(GoogleAnalyticsAdminV1betaAccessFilterExpression $dimensionFilter)
  {
    $this->dimensionFilter = $dimensionFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilterExpression
   */
  public function getDimensionFilter()
  {
    return $this->dimensionFilter;
  }
  /**
   * The dimensions requested and displayed in the response. Requests are
   * allowed up to 9 dimensions.
   *
   * @param GoogleAnalyticsAdminV1betaAccessDimension[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessDimension[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Optional. Decides whether to return the users within user groups. This
   * field works only when include_all_users is set to true. If true, it will
   * return all users with access to the specified property or account. If
   * false, only the users with direct access will be returned.
   *
   * @param bool $expandGroups
   */
  public function setExpandGroups($expandGroups)
  {
    $this->expandGroups = $expandGroups;
  }
  /**
   * @return bool
   */
  public function getExpandGroups()
  {
    return $this->expandGroups;
  }
  /**
   * Optional. Determines whether to include users who have never made an API
   * call in the response. If true, all users with access to the specified
   * property or account are included in the response, regardless of whether
   * they have made an API call or not. If false, only the users who have made
   * an API call will be included.
   *
   * @param bool $includeAllUsers
   */
  public function setIncludeAllUsers($includeAllUsers)
  {
    $this->includeAllUsers = $includeAllUsers;
  }
  /**
   * @return bool
   */
  public function getIncludeAllUsers()
  {
    return $this->includeAllUsers;
  }
  /**
   * The number of rows to return. If unspecified, 10,000 rows are returned. The
   * API returns a maximum of 100,000 rows per request, no matter how many you
   * ask for. `limit` must be positive. The API may return fewer rows than the
   * requested `limit`, if there aren't as many remaining rows as the `limit`.
   * For instance, there are fewer than 300 possible values for the dimension
   * `country`, so when reporting on only `country`, you can't get more than 300
   * rows, even if you set `limit` to a higher value. To learn more about this
   * pagination parameter, see [Pagination](https://developers.google.com/analyt
   * ics/devguides/reporting/data/v1/basics#pagination).
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
   * Metric filters allow you to restrict report response to specific metric
   * values which match the filter. Metric filters are applied after aggregating
   * the report's rows, similar to SQL having-clause. Dimensions cannot be used
   * in this filter.
   *
   * @param GoogleAnalyticsAdminV1betaAccessFilterExpression $metricFilter
   */
  public function setMetricFilter(GoogleAnalyticsAdminV1betaAccessFilterExpression $metricFilter)
  {
    $this->metricFilter = $metricFilter;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessFilterExpression
   */
  public function getMetricFilter()
  {
    return $this->metricFilter;
  }
  /**
   * The metrics requested and displayed in the response. Requests are allowed
   * up to 10 metrics.
   *
   * @param GoogleAnalyticsAdminV1betaAccessMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * The row count of the start row. The first row is counted as row 0. If
   * offset is unspecified, it is treated as 0. If offset is zero, then this
   * method will return the first page of results with `limit` entries. To learn
   * more about this pagination parameter, see [Pagination](https://developers.g
   * oogle.com/analytics/devguides/reporting/data/v1/basics#pagination).
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
   * Specifies how rows are ordered in the response.
   *
   * @param GoogleAnalyticsAdminV1betaAccessOrderBy[] $orderBys
   */
  public function setOrderBys($orderBys)
  {
    $this->orderBys = $orderBys;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessOrderBy[]
   */
  public function getOrderBys()
  {
    return $this->orderBys;
  }
  /**
   * Toggles whether to return the current state of this Analytics Property's
   * quota. Quota is returned in [AccessQuota](#AccessQuota). For account-level
   * requests, this field must be false.
   *
   * @param bool $returnEntityQuota
   */
  public function setReturnEntityQuota($returnEntityQuota)
  {
    $this->returnEntityQuota = $returnEntityQuota;
  }
  /**
   * @return bool
   */
  public function getReturnEntityQuota()
  {
    return $this->returnEntityQuota;
  }
  /**
   * This request's time zone if specified. If unspecified, the property's time
   * zone is used. The request's time zone is used to interpret the start & end
   * dates of the report. Formatted as strings from the IANA Time Zone database
   * (https://www.iana.org/time-zones); for example "America/New_York" or
   * "Asia/Tokyo".
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaRunAccessReportRequest::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaRunAccessReportRequest');
