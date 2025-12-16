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

class RunReportResponse extends \Google\Collection
{
  protected $collection_key = 'totals';
  protected $dimensionHeadersType = DimensionHeader::class;
  protected $dimensionHeadersDataType = 'array';
  /**
   * Identifies what kind of resource this message is. This `kind` is always the
   * fixed string "analyticsData#runReport". Useful to distinguish between
   * response types in JSON.
   *
   * @var string
   */
  public $kind;
  protected $maximumsType = Row::class;
  protected $maximumsDataType = 'array';
  protected $metadataType = ResponseMetaData::class;
  protected $metadataDataType = '';
  protected $metricHeadersType = MetricHeader::class;
  protected $metricHeadersDataType = 'array';
  protected $minimumsType = Row::class;
  protected $minimumsDataType = 'array';
  protected $propertyQuotaType = PropertyQuota::class;
  protected $propertyQuotaDataType = '';
  /**
   * The total number of rows in the query result. `rowCount` is independent of
   * the number of rows returned in the response, the `limit` request parameter,
   * and the `offset` request parameter. For example if a query returns 175 rows
   * and includes `limit` of 50 in the API request, the response will contain
   * `rowCount` of 175 but only 50 rows. To learn more about this pagination
   * parameter, see [Pagination](https://developers.google.com/analytics/devguid
   * es/reporting/data/v1/basics#pagination).
   *
   * @var int
   */
  public $rowCount;
  protected $rowsType = Row::class;
  protected $rowsDataType = 'array';
  protected $totalsType = Row::class;
  protected $totalsDataType = 'array';

  /**
   * Describes dimension columns. The number of DimensionHeaders and ordering of
   * DimensionHeaders matches the dimensions present in rows.
   *
   * @param DimensionHeader[] $dimensionHeaders
   */
  public function setDimensionHeaders($dimensionHeaders)
  {
    $this->dimensionHeaders = $dimensionHeaders;
  }
  /**
   * @return DimensionHeader[]
   */
  public function getDimensionHeaders()
  {
    return $this->dimensionHeaders;
  }
  /**
   * Identifies what kind of resource this message is. This `kind` is always the
   * fixed string "analyticsData#runReport". Useful to distinguish between
   * response types in JSON.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * If requested, the maximum values of metrics.
   *
   * @param Row[] $maximums
   */
  public function setMaximums($maximums)
  {
    $this->maximums = $maximums;
  }
  /**
   * @return Row[]
   */
  public function getMaximums()
  {
    return $this->maximums;
  }
  /**
   * Metadata for the report.
   *
   * @param ResponseMetaData $metadata
   */
  public function setMetadata(ResponseMetaData $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return ResponseMetaData
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Describes metric columns. The number of MetricHeaders and ordering of
   * MetricHeaders matches the metrics present in rows.
   *
   * @param MetricHeader[] $metricHeaders
   */
  public function setMetricHeaders($metricHeaders)
  {
    $this->metricHeaders = $metricHeaders;
  }
  /**
   * @return MetricHeader[]
   */
  public function getMetricHeaders()
  {
    return $this->metricHeaders;
  }
  /**
   * If requested, the minimum values of metrics.
   *
   * @param Row[] $minimums
   */
  public function setMinimums($minimums)
  {
    $this->minimums = $minimums;
  }
  /**
   * @return Row[]
   */
  public function getMinimums()
  {
    return $this->minimums;
  }
  /**
   * This Google Analytics property's quota state including this request.
   *
   * @param PropertyQuota $propertyQuota
   */
  public function setPropertyQuota(PropertyQuota $propertyQuota)
  {
    $this->propertyQuota = $propertyQuota;
  }
  /**
   * @return PropertyQuota
   */
  public function getPropertyQuota()
  {
    return $this->propertyQuota;
  }
  /**
   * The total number of rows in the query result. `rowCount` is independent of
   * the number of rows returned in the response, the `limit` request parameter,
   * and the `offset` request parameter. For example if a query returns 175 rows
   * and includes `limit` of 50 in the API request, the response will contain
   * `rowCount` of 175 but only 50 rows. To learn more about this pagination
   * parameter, see [Pagination](https://developers.google.com/analytics/devguid
   * es/reporting/data/v1/basics#pagination).
   *
   * @param int $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return int
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
  /**
   * Rows of dimension value combinations and metric values in the report.
   *
   * @param Row[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return Row[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * If requested, the totaled values of metrics.
   *
   * @param Row[] $totals
   */
  public function setTotals($totals)
  {
    $this->totals = $totals;
  }
  /**
   * @return Row[]
   */
  public function getTotals()
  {
    return $this->totals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunReportResponse::class, 'Google_Service_AnalyticsData_RunReportResponse');
