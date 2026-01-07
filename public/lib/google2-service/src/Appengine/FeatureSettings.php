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

namespace Google\Service\Appengine;

class FeatureSettings extends \Google\Model
{
  /**
   * Boolean value indicating if split health checks should be used instead of
   * the legacy health checks. At an app.yaml level, this means defaulting to
   * 'readiness_check' and 'liveness_check' values instead of 'health_check'
   * ones. Once the legacy 'health_check' behavior is deprecated, and this value
   * is always true, this setting can be removed.
   *
   * @var bool
   */
  public $splitHealthChecks;
  /**
   * If true, use Container-Optimized OS (https://cloud.google.com/container-
   * optimized-os/) base image for VMs, rather than a base Debian image.
   *
   * @var bool
   */
  public $useContainerOptimizedOs;

  /**
   * Boolean value indicating if split health checks should be used instead of
   * the legacy health checks. At an app.yaml level, this means defaulting to
   * 'readiness_check' and 'liveness_check' values instead of 'health_check'
   * ones. Once the legacy 'health_check' behavior is deprecated, and this value
   * is always true, this setting can be removed.
   *
   * @param bool $splitHealthChecks
   */
  public function setSplitHealthChecks($splitHealthChecks)
  {
    $this->splitHealthChecks = $splitHealthChecks;
  }
  /**
   * @return bool
   */
  public function getSplitHealthChecks()
  {
    return $this->splitHealthChecks;
  }
  /**
   * If true, use Container-Optimized OS (https://cloud.google.com/container-
   * optimized-os/) base image for VMs, rather than a base Debian image.
   *
   * @param bool $useContainerOptimizedOs
   */
  public function setUseContainerOptimizedOs($useContainerOptimizedOs)
  {
    $this->useContainerOptimizedOs = $useContainerOptimizedOs;
  }
  /**
   * @return bool
   */
  public function getUseContainerOptimizedOs()
  {
    return $this->useContainerOptimizedOs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FeatureSettings::class, 'Google_Service_Appengine_FeatureSettings');
