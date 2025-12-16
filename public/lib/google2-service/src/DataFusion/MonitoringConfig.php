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

namespace Google\Service\DataFusion;

class MonitoringConfig extends \Google\Model
{
  /**
   * Optional. Option to enable the instance v2 metrics for this instance. This
   * field is supported only in CDF versions 6.11.1.1 and above.
   *
   * @var bool
   */
  public $enableInstanceV2Metrics;

  /**
   * Optional. Option to enable the instance v2 metrics for this instance. This
   * field is supported only in CDF versions 6.11.1.1 and above.
   *
   * @param bool $enableInstanceV2Metrics
   */
  public function setEnableInstanceV2Metrics($enableInstanceV2Metrics)
  {
    $this->enableInstanceV2Metrics = $enableInstanceV2Metrics;
  }
  /**
   * @return bool
   */
  public function getEnableInstanceV2Metrics()
  {
    return $this->enableInstanceV2Metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitoringConfig::class, 'Google_Service_DataFusion_MonitoringConfig');
