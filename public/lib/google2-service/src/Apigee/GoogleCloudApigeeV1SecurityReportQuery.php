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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityReportQuery extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Delimiter used in the CSV file, if `outputFormat` is set to `csv`. Defaults
   * to the `,` (comma) character. Supported delimiter characters include comma
   * (`,`), pipe (`|`), and tab (`\t`).
   *
   * @var string
   */
  public $csvDelimiter;
  /**
   * A list of dimensions. https://docs.apigee.com/api-
   * platform/analytics/analytics-reference#dimensions
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * Security Report display name which users can specify.
   *
   * @var string
   */
  public $displayName;
  /**
   * Hostname needs to be specified if query intends to run at host level. This
   * field is only allowed when query is submitted by CreateHostSecurityReport
   * where analytics data will be grouped by organization and hostname.
   *
   * @var string
   */
  public $envgroupHostname;
  /**
   * Boolean expression that can be used to filter data. Filter expressions can
   * be combined using AND/OR terms and should be fully parenthesized to avoid
   * ambiguity. See Analytics metrics, dimensions, and filters reference
   * https://docs.apigee.com/api-platform/analytics/analytics-reference for more
   * information on the fields available to filter on. For more information on
   * the tokens that you use to build filter expressions, see Filter expression
   * syntax. https://docs.apigee.com/api-platform/analytics/asynch-reports-
   * api#filter-expression-syntax
   *
   * @var string
   */
  public $filter;
  /**
   * Time unit used to group the result set. Valid values include: second,
   * minute, hour, day, week, or month. If a query includes groupByTimeUnit,
   * then the result is an aggregation based on the specified time unit and the
   * resultant timestamp does not include milliseconds precision. If a query
   * omits groupByTimeUnit, then the resultant timestamp includes milliseconds
   * precision.
   *
   * @var string
   */
  public $groupByTimeUnit;
  /**
   * Maximum number of rows that can be returned in the result.
   *
   * @var int
   */
  public $limit;
  protected $metricsType = GoogleCloudApigeeV1SecurityReportQueryMetric::class;
  protected $metricsDataType = 'array';
  /**
   * Valid values include: `csv` or `json`. Defaults to `json`. Note: Configure
   * the delimiter for CSV output using the csvDelimiter property.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Report Definition ID.
   *
   * @var string
   */
  public $reportDefinitionId;
  /**
   * Required. Time range for the query. Can use the following predefined
   * strings to specify the time range: `last60minutes` `last24hours`
   * `last7days` Or, specify the timeRange as a structure describing start and
   * end timestamps in the ISO format: yyyy-mm-ddThh:mm:ssZ. Example:
   * "timeRange": { "start": "2018-07-29T00:13:00Z", "end":
   * "2018-08-01T00:18:00Z" }
   *
   * @var array
   */
  public $timeRange;

  /**
   * Delimiter used in the CSV file, if `outputFormat` is set to `csv`. Defaults
   * to the `,` (comma) character. Supported delimiter characters include comma
   * (`,`), pipe (`|`), and tab (`\t`).
   *
   * @param string $csvDelimiter
   */
  public function setCsvDelimiter($csvDelimiter)
  {
    $this->csvDelimiter = $csvDelimiter;
  }
  /**
   * @return string
   */
  public function getCsvDelimiter()
  {
    return $this->csvDelimiter;
  }
  /**
   * A list of dimensions. https://docs.apigee.com/api-
   * platform/analytics/analytics-reference#dimensions
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * Security Report display name which users can specify.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Hostname needs to be specified if query intends to run at host level. This
   * field is only allowed when query is submitted by CreateHostSecurityReport
   * where analytics data will be grouped by organization and hostname.
   *
   * @param string $envgroupHostname
   */
  public function setEnvgroupHostname($envgroupHostname)
  {
    $this->envgroupHostname = $envgroupHostname;
  }
  /**
   * @return string
   */
  public function getEnvgroupHostname()
  {
    return $this->envgroupHostname;
  }
  /**
   * Boolean expression that can be used to filter data. Filter expressions can
   * be combined using AND/OR terms and should be fully parenthesized to avoid
   * ambiguity. See Analytics metrics, dimensions, and filters reference
   * https://docs.apigee.com/api-platform/analytics/analytics-reference for more
   * information on the fields available to filter on. For more information on
   * the tokens that you use to build filter expressions, see Filter expression
   * syntax. https://docs.apigee.com/api-platform/analytics/asynch-reports-
   * api#filter-expression-syntax
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
   * Time unit used to group the result set. Valid values include: second,
   * minute, hour, day, week, or month. If a query includes groupByTimeUnit,
   * then the result is an aggregation based on the specified time unit and the
   * resultant timestamp does not include milliseconds precision. If a query
   * omits groupByTimeUnit, then the resultant timestamp includes milliseconds
   * precision.
   *
   * @param string $groupByTimeUnit
   */
  public function setGroupByTimeUnit($groupByTimeUnit)
  {
    $this->groupByTimeUnit = $groupByTimeUnit;
  }
  /**
   * @return string
   */
  public function getGroupByTimeUnit()
  {
    return $this->groupByTimeUnit;
  }
  /**
   * Maximum number of rows that can be returned in the result.
   *
   * @param int $limit
   */
  public function setLimit($limit)
  {
    $this->limit = $limit;
  }
  /**
   * @return int
   */
  public function getLimit()
  {
    return $this->limit;
  }
  /**
   * A list of Metrics.
   *
   * @param GoogleCloudApigeeV1SecurityReportQueryMetric[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityReportQueryMetric[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Valid values include: `csv` or `json`. Defaults to `json`. Note: Configure
   * the delimiter for CSV output using the csvDelimiter property.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Report Definition ID.
   *
   * @param string $reportDefinitionId
   */
  public function setReportDefinitionId($reportDefinitionId)
  {
    $this->reportDefinitionId = $reportDefinitionId;
  }
  /**
   * @return string
   */
  public function getReportDefinitionId()
  {
    return $this->reportDefinitionId;
  }
  /**
   * Required. Time range for the query. Can use the following predefined
   * strings to specify the time range: `last60minutes` `last24hours`
   * `last7days` Or, specify the timeRange as a structure describing start and
   * end timestamps in the ISO format: yyyy-mm-ddThh:mm:ssZ. Example:
   * "timeRange": { "start": "2018-07-29T00:13:00Z", "end":
   * "2018-08-01T00:18:00Z" }
   *
   * @param array $timeRange
   */
  public function setTimeRange($timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return array
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityReportQuery::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityReportQuery');
