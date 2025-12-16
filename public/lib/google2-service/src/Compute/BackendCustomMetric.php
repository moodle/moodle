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

namespace Google\Service\Compute;

class BackendCustomMetric extends \Google\Model
{
  /**
   * If true, the metric data is collected and reported to Cloud Monitoring, but
   * is not used for load balancing.
   *
   * @var bool
   */
  public $dryRun;
  /**
   * Optional parameter to define a target utilization for the Custom Metrics
   * balancing mode. The valid range is [0.0, 1.0].
   *
   * @var float
   */
  public $maxUtilization;
  /**
   * Name of a custom utilization signal. The name must be 1-64 characters long
   * and match the regular expression `[a-z]([-_.a-z0-9]*[a-z0-9])?` which means
   * that the first character must be a lowercase letter, and all following
   * characters must be a dash, period, underscore, lowercase letter, or digit,
   * except the last character, which cannot be a dash, period, or underscore.
   * For usage guidelines, see Custom Metrics balancing mode. This field can
   * only be used for a global or regional backend service with the
   * loadBalancingScheme set to EXTERNAL_MANAGED,INTERNAL_MANAGED
   * INTERNAL_SELF_MANAGED.
   *
   * @var string
   */
  public $name;

  /**
   * If true, the metric data is collected and reported to Cloud Monitoring, but
   * is not used for load balancing.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * Optional parameter to define a target utilization for the Custom Metrics
   * balancing mode. The valid range is [0.0, 1.0].
   *
   * @param float $maxUtilization
   */
  public function setMaxUtilization($maxUtilization)
  {
    $this->maxUtilization = $maxUtilization;
  }
  /**
   * @return float
   */
  public function getMaxUtilization()
  {
    return $this->maxUtilization;
  }
  /**
   * Name of a custom utilization signal. The name must be 1-64 characters long
   * and match the regular expression `[a-z]([-_.a-z0-9]*[a-z0-9])?` which means
   * that the first character must be a lowercase letter, and all following
   * characters must be a dash, period, underscore, lowercase letter, or digit,
   * except the last character, which cannot be a dash, period, or underscore.
   * For usage guidelines, see Custom Metrics balancing mode. This field can
   * only be used for a global or regional backend service with the
   * loadBalancingScheme set to EXTERNAL_MANAGED,INTERNAL_MANAGED
   * INTERNAL_SELF_MANAGED.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendCustomMetric::class, 'Google_Service_Compute_BackendCustomMetric');
