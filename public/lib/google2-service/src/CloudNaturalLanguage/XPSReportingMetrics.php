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

class XPSReportingMetrics extends \Google\Collection
{
  protected $collection_key = 'metricEntries';
  /**
   * The effective time training used. If set, this is used for quota management
   * and billing. Deprecated. AutoML BE doesn't use this. Don't set.
   *
   * @deprecated
   * @var string
   */
  public $effectiveTrainingDuration;
  protected $metricEntriesType = XPSMetricEntry::class;
  protected $metricEntriesDataType = 'array';

  /**
   * The effective time training used. If set, this is used for quota management
   * and billing. Deprecated. AutoML BE doesn't use this. Don't set.
   *
   * @deprecated
   * @param string $effectiveTrainingDuration
   */
  public function setEffectiveTrainingDuration($effectiveTrainingDuration)
  {
    $this->effectiveTrainingDuration = $effectiveTrainingDuration;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEffectiveTrainingDuration()
  {
    return $this->effectiveTrainingDuration;
  }
  /**
   * One entry per metric name. The values must be aggregated per metric name.
   *
   * @param XPSMetricEntry[] $metricEntries
   */
  public function setMetricEntries($metricEntries)
  {
    $this->metricEntries = $metricEntries;
  }
  /**
   * @return XPSMetricEntry[]
   */
  public function getMetricEntries()
  {
    return $this->metricEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSReportingMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSReportingMetrics');
