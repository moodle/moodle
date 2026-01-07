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

namespace Google\Service\Sheets;

class AddChartRequest extends \Google\Model
{
  protected $chartType = EmbeddedChart::class;
  protected $chartDataType = '';

  /**
   * The chart that should be added to the spreadsheet, including the position
   * where it should be placed. The chartId field is optional; if one is not
   * set, an id will be randomly generated. (It is an error to specify the ID of
   * an embedded object that already exists.)
   *
   * @param EmbeddedChart $chart
   */
  public function setChart(EmbeddedChart $chart)
  {
    $this->chart = $chart;
  }
  /**
   * @return EmbeddedChart
   */
  public function getChart()
  {
    return $this->chart;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddChartRequest::class, 'Google_Service_Sheets_AddChartRequest');
