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

namespace Google\Service\Adsense;

class ReportResult extends \Google\Collection
{
  protected $collection_key = 'warnings';
  protected $averagesType = Row::class;
  protected $averagesDataType = '';
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  protected $headersType = Header::class;
  protected $headersDataType = 'array';
  protected $rowsType = Row::class;
  protected $rowsDataType = 'array';
  protected $startDateType = Date::class;
  protected $startDateDataType = '';
  /**
   * The total number of rows matched by the report request.
   *
   * @var string
   */
  public $totalMatchedRows;
  protected $totalsType = Row::class;
  protected $totalsDataType = '';
  /**
   * Any warnings associated with generation of the report. These warnings are
   * always returned in English.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * The averages of the report. This is the same length as any other row in the
   * report; cells corresponding to dimension columns are empty.
   *
   * @param Row $averages
   */
  public function setAverages(Row $averages)
  {
    $this->averages = $averages;
  }
  /**
   * @return Row
   */
  public function getAverages()
  {
    return $this->averages;
  }
  /**
   * Required. End date of the range (inclusive).
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * The header information; one for each dimension in the request, followed by
   * one for each metric in the request.
   *
   * @param Header[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return Header[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * The output rows of the report. Each row is a list of cells; one for each
   * dimension in the request, followed by one for each metric in the request.
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
   * Required. Start date of the range (inclusive).
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The total number of rows matched by the report request.
   *
   * @param string $totalMatchedRows
   */
  public function setTotalMatchedRows($totalMatchedRows)
  {
    $this->totalMatchedRows = $totalMatchedRows;
  }
  /**
   * @return string
   */
  public function getTotalMatchedRows()
  {
    return $this->totalMatchedRows;
  }
  /**
   * The totals of the report. This is the same length as any other row in the
   * report; cells corresponding to dimension columns are empty.
   *
   * @param Row $totals
   */
  public function setTotals(Row $totals)
  {
    $this->totals = $totals;
  }
  /**
   * @return Row
   */
  public function getTotals()
  {
    return $this->totals;
  }
  /**
   * Any warnings associated with generation of the report. These warnings are
   * always returned in English.
   *
   * @param string[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportResult::class, 'Google_Service_Adsense_ReportResult');
