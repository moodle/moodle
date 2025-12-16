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

namespace Google\Service\Doubleclicksearch;

class ReportRequest extends \Google\Collection
{
  protected $collection_key = 'orderBy';
  protected $columnsType = ReportApiColumnSpec::class;
  protected $columnsDataType = 'array';
  /**
   * Format that the report should be returned in. Currently `csv` or `tsv` is
   * supported.
   *
   * @var string
   */
  public $downloadFormat;
  protected $filtersType = ReportRequestFilters::class;
  protected $filtersDataType = 'array';
  /**
   * Determines if removed entities should be included in the report. Defaults
   * to `false`. Deprecated, please use `includeRemovedEntities` instead.
   *
   * @var bool
   */
  public $includeDeletedEntities;
  /**
   * Determines if removed entities should be included in the report. Defaults
   * to `false`.
   *
   * @var bool
   */
  public $includeRemovedEntities;
  /**
   * Asynchronous report only. The maximum number of rows per report file. A
   * large report is split into many files based on this field. Acceptable
   * values are `1000000` to `100000000`, inclusive.
   *
   * @var int
   */
  public $maxRowsPerFile;
  protected $orderByType = ReportRequestOrderBy::class;
  protected $orderByDataType = 'array';
  protected $reportScopeType = ReportRequestReportScope::class;
  protected $reportScopeDataType = '';
  /**
   * Determines the type of rows that are returned in the report. For example,
   * if you specify `reportType: keyword`, each row in the report will contain
   * data about a keyword. See the [Types of Reports](/search-ads/v2/report-
   * types/) reference for the columns that are available for each type.
   *
   * @var string
   */
  public $reportType;
  /**
   * Synchronous report only. The maximum number of rows to return; additional
   * rows are dropped. Acceptable values are `0` to `10000`, inclusive. Defaults
   * to `10000`.
   *
   * @var int
   */
  public $rowCount;
  /**
   * Synchronous report only. Zero-based index of the first row to return.
   * Acceptable values are `0` to `50000`, inclusive. Defaults to `0`.
   *
   * @var int
   */
  public $startRow;
  /**
   * Specifies the currency in which monetary will be returned. Possible values
   * are: `usd`, `agency` (valid if the report is scoped to agency or lower),
   * `advertiser` (valid if the report is scoped to * advertiser or lower), or
   * `account` (valid if the report is scoped to engine account or lower).
   *
   * @var string
   */
  public $statisticsCurrency;
  protected $timeRangeType = ReportRequestTimeRange::class;
  protected $timeRangeDataType = '';
  /**
   * If `true`, the report would only be created if all the requested stat data
   * are sourced from a single timezone. Defaults to `false`.
   *
   * @var bool
   */
  public $verifySingleTimeZone;

  /**
   * The columns to include in the report. This includes both DoubleClick Search
   * columns and saved columns. For DoubleClick Search columns, only the
   * `columnName` parameter is required. For saved columns only the
   * `savedColumnName` parameter is required. Both `columnName` and
   * `savedColumnName` cannot be set in the same stanza.\ The maximum number of
   * columns per request is 300.
   *
   * @param ReportApiColumnSpec[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return ReportApiColumnSpec[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Format that the report should be returned in. Currently `csv` or `tsv` is
   * supported.
   *
   * @param string $downloadFormat
   */
  public function setDownloadFormat($downloadFormat)
  {
    $this->downloadFormat = $downloadFormat;
  }
  /**
   * @return string
   */
  public function getDownloadFormat()
  {
    return $this->downloadFormat;
  }
  /**
   * A list of filters to be applied to the report.\ The maximum number of
   * filters per request is 300.
   *
   * @param ReportRequestFilters[] $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return ReportRequestFilters[]
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * Determines if removed entities should be included in the report. Defaults
   * to `false`. Deprecated, please use `includeRemovedEntities` instead.
   *
   * @param bool $includeDeletedEntities
   */
  public function setIncludeDeletedEntities($includeDeletedEntities)
  {
    $this->includeDeletedEntities = $includeDeletedEntities;
  }
  /**
   * @return bool
   */
  public function getIncludeDeletedEntities()
  {
    return $this->includeDeletedEntities;
  }
  /**
   * Determines if removed entities should be included in the report. Defaults
   * to `false`.
   *
   * @param bool $includeRemovedEntities
   */
  public function setIncludeRemovedEntities($includeRemovedEntities)
  {
    $this->includeRemovedEntities = $includeRemovedEntities;
  }
  /**
   * @return bool
   */
  public function getIncludeRemovedEntities()
  {
    return $this->includeRemovedEntities;
  }
  /**
   * Asynchronous report only. The maximum number of rows per report file. A
   * large report is split into many files based on this field. Acceptable
   * values are `1000000` to `100000000`, inclusive.
   *
   * @param int $maxRowsPerFile
   */
  public function setMaxRowsPerFile($maxRowsPerFile)
  {
    $this->maxRowsPerFile = $maxRowsPerFile;
  }
  /**
   * @return int
   */
  public function getMaxRowsPerFile()
  {
    return $this->maxRowsPerFile;
  }
  /**
   * Synchronous report only. A list of columns and directions defining sorting
   * to be performed on the report rows.\ The maximum number of orderings per
   * request is 300.
   *
   * @param ReportRequestOrderBy[] $orderBy
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return ReportRequestOrderBy[]
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
  /**
   * The reportScope is a set of IDs that are used to determine which subset of
   * entities will be returned in the report. The full lineage of IDs from the
   * lowest scoped level desired up through agency is required.
   *
   * @param ReportRequestReportScope $reportScope
   */
  public function setReportScope(ReportRequestReportScope $reportScope)
  {
    $this->reportScope = $reportScope;
  }
  /**
   * @return ReportRequestReportScope
   */
  public function getReportScope()
  {
    return $this->reportScope;
  }
  /**
   * Determines the type of rows that are returned in the report. For example,
   * if you specify `reportType: keyword`, each row in the report will contain
   * data about a keyword. See the [Types of Reports](/search-ads/v2/report-
   * types/) reference for the columns that are available for each type.
   *
   * @param string $reportType
   */
  public function setReportType($reportType)
  {
    $this->reportType = $reportType;
  }
  /**
   * @return string
   */
  public function getReportType()
  {
    return $this->reportType;
  }
  /**
   * Synchronous report only. The maximum number of rows to return; additional
   * rows are dropped. Acceptable values are `0` to `10000`, inclusive. Defaults
   * to `10000`.
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
   * Synchronous report only. Zero-based index of the first row to return.
   * Acceptable values are `0` to `50000`, inclusive. Defaults to `0`.
   *
   * @param int $startRow
   */
  public function setStartRow($startRow)
  {
    $this->startRow = $startRow;
  }
  /**
   * @return int
   */
  public function getStartRow()
  {
    return $this->startRow;
  }
  /**
   * Specifies the currency in which monetary will be returned. Possible values
   * are: `usd`, `agency` (valid if the report is scoped to agency or lower),
   * `advertiser` (valid if the report is scoped to * advertiser or lower), or
   * `account` (valid if the report is scoped to engine account or lower).
   *
   * @param string $statisticsCurrency
   */
  public function setStatisticsCurrency($statisticsCurrency)
  {
    $this->statisticsCurrency = $statisticsCurrency;
  }
  /**
   * @return string
   */
  public function getStatisticsCurrency()
  {
    return $this->statisticsCurrency;
  }
  /**
   * If metrics are requested in a report, this argument will be used to
   * restrict the metrics to a specific time range.
   *
   * @param ReportRequestTimeRange $timeRange
   */
  public function setTimeRange(ReportRequestTimeRange $timeRange)
  {
    $this->timeRange = $timeRange;
  }
  /**
   * @return ReportRequestTimeRange
   */
  public function getTimeRange()
  {
    return $this->timeRange;
  }
  /**
   * If `true`, the report would only be created if all the requested stat data
   * are sourced from a single timezone. Defaults to `false`.
   *
   * @param bool $verifySingleTimeZone
   */
  public function setVerifySingleTimeZone($verifySingleTimeZone)
  {
    $this->verifySingleTimeZone = $verifySingleTimeZone;
  }
  /**
   * @return bool
   */
  public function getVerifySingleTimeZone()
  {
    return $this->verifySingleTimeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRequest::class, 'Google_Service_Doubleclicksearch_ReportRequest');
