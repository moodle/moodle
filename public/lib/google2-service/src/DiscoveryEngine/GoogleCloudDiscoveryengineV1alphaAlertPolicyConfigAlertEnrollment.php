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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaAlertPolicyConfigAlertEnrollment extends \Google\Model
{
  /**
   * Default value. Used for customers who have not responded to the alert
   * policy.
   */
  public const ENROLL_STATE_ENROLL_STATES_UNSPECIFIED = 'ENROLL_STATES_UNSPECIFIED';
  /**
   * Customer is enrolled in this policy.
   */
  public const ENROLL_STATE_ENROLLED = 'ENROLLED';
  /**
   * Customer declined this policy.
   */
  public const ENROLL_STATE_DECLINED = 'DECLINED';
  /**
   * Immutable. The id of an alert.
   *
   * @var string
   */
  public $alertId;
  /**
   * Required. The enrollment status of a customer.
   *
   * @var string
   */
  public $enrollState;

  /**
   * Immutable. The id of an alert.
   *
   * @param string $alertId
   */
  public function setAlertId($alertId)
  {
    $this->alertId = $alertId;
  }
  /**
   * @return string
   */
  public function getAlertId()
  {
    return $this->alertId;
  }
  /**
   * Required. The enrollment status of a customer.
   *
   * Accepted values: ENROLL_STATES_UNSPECIFIED, ENROLLED, DECLINED
   *
   * @param self::ENROLL_STATE_* $enrollState
   */
  public function setEnrollState($enrollState)
  {
    $this->enrollState = $enrollState;
  }
  /**
   * @return self::ENROLL_STATE_*
   */
  public function getEnrollState()
  {
    return $this->enrollState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAlertPolicyConfigAlertEnrollment::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAlertPolicyConfigAlertEnrollment');
