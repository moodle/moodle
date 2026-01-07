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

class RunPivotReportResponse extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $aggregatesType = Row::class;
  protected $aggregatesDataType = 'array';
  protected $dimensionHeadersType = DimensionHeader::class;
  protected $dimensionHeadersDataType = 'array';
  /**
   * Identifies what kind of resource this message is. This `kind` is always the
   * fixed string "analyticsData#runPivotReport". Useful to distinguish between
   * response types in JSON.
   *
   * @var string
   */
  public $kind;
  protected $metadataType = ResponseMetaData::class;
  protected $metadataDataType = '';
  protected $metricHeadersType = MetricHeader::class;
  protected $metricHeadersDataType = 'array';
  protected $pivotHeadersType = PivotHeader::class;
  protected $pivotHeadersDataType = 'array';
  protected $propertyQuotaType = PropertyQuota::class;
  protected $propertyQuotaDataType = '';
  protected $rowsType = Row::class;
  protected $rowsDataType = 'array';

  /**
   * Aggregation of metric values. Can be totals, minimums, or maximums. The
   * returned aggregations are controlled by the metric_aggregations in the
   * pivot. The type of aggregation returned in each row is shown by the
   * dimension_values which are set to "RESERVED_".
   *
   * @param Row[] $aggregates
   */
  public function setAggregates($aggregates)
  {
    $this->aggregates = $aggregates;
  }
  /**
   * @return Row[]
   */
  public function getAggregates()
  {
    return $this->aggregates;
  }
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
   * fixed string "analyticsData#runPivotReport". Useful to distinguish between
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
   * Summarizes the columns and rows created by a pivot. Each pivot in the
   * request produces one header in the response. If we have a request like
   * this: "pivots": [{ "fieldNames": ["country", "city"] }, { "fieldNames":
   * "eventName" }] We will have the following `pivotHeaders` in the response:
   * "pivotHeaders" : [{ "dimensionHeaders": [{ "dimensionValues": [ { "value":
   * "United Kingdom" }, { "value": "London" } ] }, { "dimensionValues": [ {
   * "value": "Japan" }, { "value": "Osaka" } ] }] }, { "dimensionHeaders": [{
   * "dimensionValues": [{ "value": "session_start" }] }, { "dimensionValues":
   * [{ "value": "scroll" }] }] }]
   *
   * @param PivotHeader[] $pivotHeaders
   */
  public function setPivotHeaders($pivotHeaders)
  {
    $this->pivotHeaders = $pivotHeaders;
  }
  /**
   * @return PivotHeader[]
   */
  public function getPivotHeaders()
  {
    return $this->pivotHeaders;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunPivotReportResponse::class, 'Google_Service_AnalyticsData_RunPivotReportResponse');
