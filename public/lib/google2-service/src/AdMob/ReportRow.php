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

namespace Google\Service\AdMob;

class ReportRow extends \Google\Model
{
  protected $dimensionValuesType = ReportRowDimensionValue::class;
  protected $dimensionValuesDataType = 'map';
  protected $metricValuesType = ReportRowMetricValue::class;
  protected $metricValuesDataType = 'map';

  /**
   * Map of dimension values in a row, with keys as enum name of the dimensions.
   *
   * @param ReportRowDimensionValue[] $dimensionValues
   */
  public function setDimensionValues($dimensionValues)
  {
    $this->dimensionValues = $dimensionValues;
  }
  /**
   * @return ReportRowDimensionValue[]
   */
  public function getDimensionValues()
  {
    return $this->dimensionValues;
  }
  /**
   * Map of metric values in a row, with keys as enum name of the metrics. If a
   * metric being requested has no value returned, the map will not include it.
   *
   * @param ReportRowMetricValue[] $metricValues
   */
  public function setMetricValues($metricValues)
  {
    $this->metricValues = $metricValues;
  }
  /**
   * @return ReportRowMetricValue[]
   */
  public function getMetricValues()
  {
    return $this->metricValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportRow::class, 'Google_Service_AdMob_ReportRow');
