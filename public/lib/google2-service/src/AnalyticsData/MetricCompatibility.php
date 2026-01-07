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

class MetricCompatibility extends \Google\Model
{
  /**
   * Unspecified compatibility.
   */
  public const COMPATIBILITY_COMPATIBILITY_UNSPECIFIED = 'COMPATIBILITY_UNSPECIFIED';
  /**
   * The dimension or metric is compatible. This dimension or metric can be
   * successfully added to a report.
   */
  public const COMPATIBILITY_COMPATIBLE = 'COMPATIBLE';
  /**
   * The dimension or metric is incompatible. This dimension or metric cannot be
   * successfully added to a report.
   */
  public const COMPATIBILITY_INCOMPATIBLE = 'INCOMPATIBLE';
  /**
   * The compatibility of this metric. If the compatibility is COMPATIBLE, this
   * metric can be successfully added to the report.
   *
   * @var string
   */
  public $compatibility;
  protected $metricMetadataType = MetricMetadata::class;
  protected $metricMetadataDataType = '';

  /**
   * The compatibility of this metric. If the compatibility is COMPATIBLE, this
   * metric can be successfully added to the report.
   *
   * Accepted values: COMPATIBILITY_UNSPECIFIED, COMPATIBLE, INCOMPATIBLE
   *
   * @param self::COMPATIBILITY_* $compatibility
   */
  public function setCompatibility($compatibility)
  {
    $this->compatibility = $compatibility;
  }
  /**
   * @return self::COMPATIBILITY_*
   */
  public function getCompatibility()
  {
    return $this->compatibility;
  }
  /**
   * The metric metadata contains the API name for this compatibility
   * information. The metric metadata also contains other helpful information
   * like the UI name and description.
   *
   * @param MetricMetadata $metricMetadata
   */
  public function setMetricMetadata(MetricMetadata $metricMetadata)
  {
    $this->metricMetadata = $metricMetadata;
  }
  /**
   * @return MetricMetadata
   */
  public function getMetricMetadata()
  {
    return $this->metricMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricCompatibility::class, 'Google_Service_AnalyticsData_MetricCompatibility');
