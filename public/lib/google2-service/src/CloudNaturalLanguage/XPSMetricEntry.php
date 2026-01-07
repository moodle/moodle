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

namespace Google\Service\CloudNaturalLanguage;

class XPSMetricEntry extends \Google\Collection
{
  protected $collection_key = 'systemLabels';
  /**
   * For billing metrics that are using legacy sku's, set the legacy billing
   * metric id here. This will be sent to Chemist as the
   * "cloudbilling.googleapis.com/argentum_metric_id" label. Otherwise leave
   * empty.
   *
   * @var string
   */
  public $argentumMetricId;
  /**
   * A double value.
   *
   * @var 
   */
  public $doubleValue;
  /**
   * A signed 64-bit integer value.
   *
   * @var string
   */
  public $int64Value;
  /**
   * The metric name defined in the service configuration.
   *
   * @var string
   */
  public $metricName;
  protected $systemLabelsType = XPSMetricEntryLabel::class;
  protected $systemLabelsDataType = 'array';

  /**
   * For billing metrics that are using legacy sku's, set the legacy billing
   * metric id here. This will be sent to Chemist as the
   * "cloudbilling.googleapis.com/argentum_metric_id" label. Otherwise leave
   * empty.
   *
   * @param string $argentumMetricId
   */
  public function setArgentumMetricId($argentumMetricId)
  {
    $this->argentumMetricId = $argentumMetricId;
  }
  /**
   * @return string
   */
  public function getArgentumMetricId()
  {
    return $this->argentumMetricId;
  }
  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }
  public function getDoubleValue()
  {
    return $this->doubleValue;
  }
  /**
   * A signed 64-bit integer value.
   *
   * @param string $int64Value
   */
  public function setInt64Value($int64Value)
  {
    $this->int64Value = $int64Value;
  }
  /**
   * @return string
   */
  public function getInt64Value()
  {
    return $this->int64Value;
  }
  /**
   * The metric name defined in the service configuration.
   *
   * @param string $metricName
   */
  public function setMetricName($metricName)
  {
    $this->metricName = $metricName;
  }
  /**
   * @return string
   */
  public function getMetricName()
  {
    return $this->metricName;
  }
  /**
   * Billing system labels for this (metric, value) pair.
   *
   * @param XPSMetricEntryLabel[] $systemLabels
   */
  public function setSystemLabels($systemLabels)
  {
    $this->systemLabels = $systemLabels;
  }
  /**
   * @return XPSMetricEntryLabel[]
   */
  public function getSystemLabels()
  {
    return $this->systemLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSMetricEntry::class, 'Google_Service_CloudNaturalLanguage_XPSMetricEntry');
