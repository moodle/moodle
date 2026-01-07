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

namespace Google\Service\Monitoring;

class ListTimeSeriesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $executionErrorsType = Status::class;
  protected $executionErrorsDataType = 'array';
  /**
   * If there are more results than have been returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
   *
   * @var string
   */
  public $nextPageToken;
  protected $timeSeriesType = TimeSeries::class;
  protected $timeSeriesDataType = 'array';
  /**
   * The unit in which all time_series point values are reported. unit follows
   * the UCUM format for units as seen in https://unitsofmeasure.org/ucum.html.
   * If different time_series have different units (for example, because they
   * come from different metric types, or a unit is absent), then unit will be
   * "{not_a_unit}".
   *
   * @var string
   */
  public $unit;
  /**
   * Cloud regions that were unreachable which may have caused incomplete data
   * to be returned.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * Query execution errors that may have caused the time series data returned
   * to be incomplete.
   *
   * @param Status[] $executionErrors
   */
  public function setExecutionErrors($executionErrors)
  {
    $this->executionErrors = $executionErrors;
  }
  /**
   * @return Status[]
   */
  public function getExecutionErrors()
  {
    return $this->executionErrors;
  }
  /**
   * If there are more results than have been returned, then this field is set
   * to a non-empty value. To see the additional results, use that value as
   * page_token in the next call to this method.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * One or more time series that match the filter included in the request.
   *
   * @param TimeSeries[] $timeSeries
   */
  public function setTimeSeries($timeSeries)
  {
    $this->timeSeries = $timeSeries;
  }
  /**
   * @return TimeSeries[]
   */
  public function getTimeSeries()
  {
    return $this->timeSeries;
  }
  /**
   * The unit in which all time_series point values are reported. unit follows
   * the UCUM format for units as seen in https://unitsofmeasure.org/ucum.html.
   * If different time_series have different units (for example, because they
   * come from different metric types, or a unit is absent), then unit will be
   * "{not_a_unit}".
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * Cloud regions that were unreachable which may have caused incomplete data
   * to be returned.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTimeSeriesResponse::class, 'Google_Service_Monitoring_ListTimeSeriesResponse');
