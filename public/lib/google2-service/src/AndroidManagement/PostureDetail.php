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

namespace Google\Service\AndroidManagement;

class PostureDetail extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const SECURITY_RISK_SECURITY_RISK_UNSPECIFIED = 'SECURITY_RISK_UNSPECIFIED';
  /**
   * Play Integrity API detects that the device is running an unknown OS
   * (basicIntegrity check succeeds but ctsProfileMatch fails).
   */
  public const SECURITY_RISK_UNKNOWN_OS = 'UNKNOWN_OS';
  /**
   * Play Integrity API detects that the device is running a compromised OS
   * (basicIntegrity check fails).
   */
  public const SECURITY_RISK_COMPROMISED_OS = 'COMPROMISED_OS';
  /**
   * Play Integrity API detects that the device does not have a strong guarantee
   * of system integrity, if the MEETS_STRONG_INTEGRITY label doesn't show in
   * the device integrity field
   * (https://developer.android.com/google/play/integrity/verdicts#device-
   * integrity-field).
   */
  public const SECURITY_RISK_HARDWARE_BACKED_EVALUATION_FAILED = 'HARDWARE_BACKED_EVALUATION_FAILED';
  protected $collection_key = 'advice';
  protected $adviceType = UserFacingMessage::class;
  protected $adviceDataType = 'array';
  /**
   * A specific security risk that negatively affects the security posture of
   * the device.
   *
   * @var string
   */
  public $securityRisk;

  /**
   * Corresponding admin-facing advice to mitigate this security risk and
   * improve the security posture of the device.
   *
   * @param UserFacingMessage[] $advice
   */
  public function setAdvice($advice)
  {
    $this->advice = $advice;
  }
  /**
   * @return UserFacingMessage[]
   */
  public function getAdvice()
  {
    return $this->advice;
  }
  /**
   * A specific security risk that negatively affects the security posture of
   * the device.
   *
   * Accepted values: SECURITY_RISK_UNSPECIFIED, UNKNOWN_OS, COMPROMISED_OS,
   * HARDWARE_BACKED_EVALUATION_FAILED
   *
   * @param self::SECURITY_RISK_* $securityRisk
   */
  public function setSecurityRisk($securityRisk)
  {
    $this->securityRisk = $securityRisk;
  }
  /**
   * @return self::SECURITY_RISK_*
   */
  public function getSecurityRisk()
  {
    return $this->securityRisk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PostureDetail::class, 'Google_Service_AndroidManagement_PostureDetail');
