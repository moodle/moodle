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

namespace Google\Service\MigrationCenterAPI;

class ReportSummaryChartData extends \Google\Collection
{
  protected $collection_key = 'dataPoints';
  protected $dataPointsType = ReportSummaryChartDataDataPoint::class;
  protected $dataPointsDataType = 'array';

  /**
   * Each data point in the chart is represented as a name-value pair with the
   * name being the x-axis label, and the value being the y-axis value.
   *
   * @param ReportSummaryChartDataDataPoint[] $dataPoints
   */
  public function setDataPoints($dataPoints)
  {
    $this->dataPoints = $dataPoints;
  }
  /**
   * @return ReportSummaryChartDataDataPoint[]
   */
  public function getDataPoints()
  {
    return $this->dataPoints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportSummaryChartData::class, 'Google_Service_MigrationCenterAPI_ReportSummaryChartData');
