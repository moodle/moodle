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

namespace Google\Service\Container;

class AutoMonitoringConfig extends \Google\Model
{
  /**
   * Not set.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * Auto-Monitoring is enabled for all supported applications.
   */
  public const SCOPE_ALL = 'ALL';
  /**
   * Disable Auto-Monitoring.
   */
  public const SCOPE_NONE = 'NONE';
  /**
   * Scope for GKE Workload Auto-Monitoring.
   *
   * @var string
   */
  public $scope;

  /**
   * Scope for GKE Workload Auto-Monitoring.
   *
   * Accepted values: SCOPE_UNSPECIFIED, ALL, NONE
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoMonitoringConfig::class, 'Google_Service_Container_AutoMonitoringConfig');
