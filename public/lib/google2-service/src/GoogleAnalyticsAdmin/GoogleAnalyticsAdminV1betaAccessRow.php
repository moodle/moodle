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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccessRow extends \Google\Collection
{
  protected $collection_key = 'metricValues';
  protected $dimensionValuesType = GoogleAnalyticsAdminV1betaAccessDimensionValue::class;
  protected $dimensionValuesDataType = 'array';
  protected $metricValuesType = GoogleAnalyticsAdminV1betaAccessMetricValue::class;
  protected $metricValuesDataType = 'array';

  /**
   * List of dimension values. These values are in the same order as specified
   * in the request.
   *
   * @param GoogleAnalyticsAdminV1betaAccessDimensionValue[] $dimensionValues
   */
  public function setDimensionValues($dimensionValues)
  {
    $this->dimensionValues = $dimensionValues;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessDimensionValue[]
   */
  public function getDimensionValues()
  {
    return $this->dimensionValues;
  }
  /**
   * List of metric values. These values are in the same order as specified in
   * the request.
   *
   * @param GoogleAnalyticsAdminV1betaAccessMetricValue[] $metricValues
   */
  public function setMetricValues($metricValues)
  {
    $this->metricValues = $metricValues;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaAccessMetricValue[]
   */
  public function getMetricValues()
  {
    return $this->metricValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccessRow::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccessRow');
