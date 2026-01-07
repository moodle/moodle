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

class ReportApiColumnSpec extends \Google\Model
{
  /**
   * Name of a DoubleClick Search column to include in the report.
   *
   * @var string
   */
  public $columnName;
  /**
   * Segments a report by a custom dimension. The report must be scoped to an
   * advertiser or lower, and the custom dimension must already be set up in
   * DoubleClick Search. The custom dimension name, which appears in DoubleClick
   * Search, is case sensitive.\ If used in a conversion report, returns the
   * value of the specified custom dimension for the given conversion, if set.
   * This column does not segment the conversion report.
   *
   * @var string
   */
  public $customDimensionName;
  /**
   * Name of a custom metric to include in the report. The report must be scoped
   * to an advertiser or lower, and the custom metric must already be set up in
   * DoubleClick Search. The custom metric name, which appears in DoubleClick
   * Search, is case sensitive.
   *
   * @var string
   */
  public $customMetricName;
  /**
   * Inclusive day in YYYY-MM-DD format. When provided, this overrides the
   * overall time range of the report for this column only. Must be provided
   * together with `startDate`.
   *
   * @var string
   */
  public $endDate;
  /**
   * Synchronous report only. Set to `true` to group by this column. Defaults to
   * `false`.
   *
   * @var bool
   */
  public $groupByColumn;
  /**
   * Text used to identify this column in the report output; defaults to
   * `columnName` or `savedColumnName` when not specified. This can be used to
   * prevent collisions between DoubleClick Search columns and saved columns
   * with the same name.
   *
   * @var string
   */
  public $headerText;
  /**
   * The platform that is used to provide data for the custom dimension.
   * Acceptable values are "floodlight".
   *
   * @var string
   */
  public $platformSource;
  /**
   * Returns metrics only for a specific type of product activity. Accepted
   * values are: - "`sold`": returns metrics only for products that were sold -
   * "`advertised`": returns metrics only for products that were advertised in a
   * Shopping campaign, and that might or might not have been sold
   *
   * @var string
   */
  public $productReportPerspective;
  /**
   * Name of a saved column to include in the report. The report must be scoped
   * at advertiser or lower, and this saved column must already be created in
   * the DoubleClick Search UI.
   *
   * @var string
   */
  public $savedColumnName;
  /**
   * Inclusive date in YYYY-MM-DD format. When provided, this overrides the
   * overall time range of the report for this column only. Must be provided
   * together with `endDate`.
   *
   * @var string
   */
  public $startDate;

  /**
   * Name of a DoubleClick Search column to include in the report.
   *
   * @param string $columnName
   */
  public function setColumnName($columnName)
  {
    $this->columnName = $columnName;
  }
  /**
   * @return string
   */
  public function getColumnName()
  {
    return $this->columnName;
  }
  /**
   * Segments a report by a custom dimension. The report must be scoped to an
   * advertiser or lower, and the custom dimension must already be set up in
   * DoubleClick Search. The custom dimension name, which appears in DoubleClick
   * Search, is case sensitive.\ If used in a conversion report, returns the
   * value of the specified custom dimension for the given conversion, if set.
   * This column does not segment the conversion report.
   *
   * @param string $customDimensionName
   */
  public function setCustomDimensionName($customDimensionName)
  {
    $this->customDimensionName = $customDimensionName;
  }
  /**
   * @return string
   */
  public function getCustomDimensionName()
  {
    return $this->customDimensionName;
  }
  /**
   * Name of a custom metric to include in the report. The report must be scoped
   * to an advertiser or lower, and the custom metric must already be set up in
   * DoubleClick Search. The custom metric name, which appears in DoubleClick
   * Search, is case sensitive.
   *
   * @param string $customMetricName
   */
  public function setCustomMetricName($customMetricName)
  {
    $this->customMetricName = $customMetricName;
  }
  /**
   * @return string
   */
  public function getCustomMetricName()
  {
    return $this->customMetricName;
  }
  /**
   * Inclusive day in YYYY-MM-DD format. When provided, this overrides the
   * overall time range of the report for this column only. Must be provided
   * together with `startDate`.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Synchronous report only. Set to `true` to group by this column. Defaults to
   * `false`.
   *
   * @param bool $groupByColumn
   */
  public function setGroupByColumn($groupByColumn)
  {
    $this->groupByColumn = $groupByColumn;
  }
  /**
   * @return bool
   */
  public function getGroupByColumn()
  {
    return $this->groupByColumn;
  }
  /**
   * Text used to identify this column in the report output; defaults to
   * `columnName` or `savedColumnName` when not specified. This can be used to
   * prevent collisions between DoubleClick Search columns and saved columns
   * with the same name.
   *
   * @param string $headerText
   */
  public function setHeaderText($headerText)
  {
    $this->headerText = $headerText;
  }
  /**
   * @return string
   */
  public function getHeaderText()
  {
    return $this->headerText;
  }
  /**
   * The platform that is used to provide data for the custom dimension.
   * Acceptable values are "floodlight".
   *
   * @param string $platformSource
   */
  public function setPlatformSource($platformSource)
  {
    $this->platformSource = $platformSource;
  }
  /**
   * @return string
   */
  public function getPlatformSource()
  {
    return $this->platformSource;
  }
  /**
   * Returns metrics only for a specific type of product activity. Accepted
   * values are: - "`sold`": returns metrics only for products that were sold -
   * "`advertised`": returns metrics only for products that were advertised in a
   * Shopping campaign, and that might or might not have been sold
   *
   * @param string $productReportPerspective
   */
  public function setProductReportPerspective($productReportPerspective)
  {
    $this->productReportPerspective = $productReportPerspective;
  }
  /**
   * @return string
   */
  public function getProductReportPerspective()
  {
    return $this->productReportPerspective;
  }
  /**
   * Name of a saved column to include in the report. The report must be scoped
   * at advertiser or lower, and this saved column must already be created in
   * the DoubleClick Search UI.
   *
   * @param string $savedColumnName
   */
  public function setSavedColumnName($savedColumnName)
  {
    $this->savedColumnName = $savedColumnName;
  }
  /**
   * @return string
   */
  public function getSavedColumnName()
  {
    return $this->savedColumnName;
  }
  /**
   * Inclusive date in YYYY-MM-DD format. When provided, this overrides the
   * overall time range of the report for this column only. Must be provided
   * together with `endDate`.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportApiColumnSpec::class, 'Google_Service_Doubleclicksearch_ReportApiColumnSpec');
