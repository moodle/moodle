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

class WorkloadPolicyConfig extends \Google\Model
{
  /**
   * If true, workloads can use NET_ADMIN capability.
   *
   * @var bool
   */
  public $allowNetAdmin;
  /**
   * If true, enables the GCW Auditor that audits workloads on standard
   * clusters.
   *
   * @var bool
   */
  public $autopilotCompatibilityAuditingEnabled;

  /**
   * If true, workloads can use NET_ADMIN capability.
   *
   * @param bool $allowNetAdmin
   */
  public function setAllowNetAdmin($allowNetAdmin)
  {
    $this->allowNetAdmin = $allowNetAdmin;
  }
  /**
   * @return bool
   */
  public function getAllowNetAdmin()
  {
    return $this->allowNetAdmin;
  }
  /**
   * If true, enables the GCW Auditor that audits workloads on standard
   * clusters.
   *
   * @param bool $autopilotCompatibilityAuditingEnabled
   */
  public function setAutopilotCompatibilityAuditingEnabled($autopilotCompatibilityAuditingEnabled)
  {
    $this->autopilotCompatibilityAuditingEnabled = $autopilotCompatibilityAuditingEnabled;
  }
  /**
   * @return bool
   */
  public function getAutopilotCompatibilityAuditingEnabled()
  {
    return $this->autopilotCompatibilityAuditingEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkloadPolicyConfig::class, 'Google_Service_Container_WorkloadPolicyConfig');
