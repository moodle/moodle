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

namespace Google\Service\CloudKMS;

class KeyAccessJustificationsEnrollmentConfig extends \Google\Model
{
  /**
   * Whether the project has KAJ logging enabled.
   *
   * @var bool
   */
  public $auditLogging;
  /**
   * Whether the project is enrolled in KAJ policy enforcement.
   *
   * @var bool
   */
  public $policyEnforcement;

  /**
   * Whether the project has KAJ logging enabled.
   *
   * @param bool $auditLogging
   */
  public function setAuditLogging($auditLogging)
  {
    $this->auditLogging = $auditLogging;
  }
  /**
   * @return bool
   */
  public function getAuditLogging()
  {
    return $this->auditLogging;
  }
  /**
   * Whether the project is enrolled in KAJ policy enforcement.
   *
   * @param bool $policyEnforcement
   */
  public function setPolicyEnforcement($policyEnforcement)
  {
    $this->policyEnforcement = $policyEnforcement;
  }
  /**
   * @return bool
   */
  public function getPolicyEnforcement()
  {
    return $this->policyEnforcement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyAccessJustificationsEnrollmentConfig::class, 'Google_Service_CloudKMS_KeyAccessJustificationsEnrollmentConfig');
