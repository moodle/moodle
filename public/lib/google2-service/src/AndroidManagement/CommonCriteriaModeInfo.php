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

class CommonCriteriaModeInfo extends \Google\Model
{
  /**
   * Unknown status.
   */
  public const COMMON_CRITERIA_MODE_STATUS_COMMON_CRITERIA_MODE_STATUS_UNKNOWN = 'COMMON_CRITERIA_MODE_STATUS_UNKNOWN';
  /**
   * Common Criteria Mode is currently disabled.
   */
  public const COMMON_CRITERIA_MODE_STATUS_COMMON_CRITERIA_MODE_DISABLED = 'COMMON_CRITERIA_MODE_DISABLED';
  /**
   * Common Criteria Mode is currently enabled.
   */
  public const COMMON_CRITERIA_MODE_STATUS_COMMON_CRITERIA_MODE_ENABLED = 'COMMON_CRITERIA_MODE_ENABLED';
  /**
   * Unspecified. The verification status has not been reported. This is set
   * only if statusReportingSettings.commonCriteriaModeEnabled is false.
   */
  public const POLICY_SIGNATURE_VERIFICATION_STATUS_POLICY_SIGNATURE_VERIFICATION_STATUS_UNSPECIFIED = 'POLICY_SIGNATURE_VERIFICATION_STATUS_UNSPECIFIED';
  /**
   * Policy signature verification is disabled on the device as
   * common_criteria_mode is set to false.
   */
  public const POLICY_SIGNATURE_VERIFICATION_STATUS_POLICY_SIGNATURE_VERIFICATION_DISABLED = 'POLICY_SIGNATURE_VERIFICATION_DISABLED';
  /**
   * Policy signature verification succeeded.
   */
  public const POLICY_SIGNATURE_VERIFICATION_STATUS_POLICY_SIGNATURE_VERIFICATION_SUCCEEDED = 'POLICY_SIGNATURE_VERIFICATION_SUCCEEDED';
  /**
   * Policy signature verification is not supported, e.g. because the device has
   * been enrolled with a CloudDPC version that does not support the policy
   * signature verification.
   */
  public const POLICY_SIGNATURE_VERIFICATION_STATUS_POLICY_SIGNATURE_VERIFICATION_NOT_SUPPORTED = 'POLICY_SIGNATURE_VERIFICATION_NOT_SUPPORTED';
  /**
   * The policy signature verification failed. The policy has not been applied.
   */
  public const POLICY_SIGNATURE_VERIFICATION_STATUS_POLICY_SIGNATURE_VERIFICATION_FAILED = 'POLICY_SIGNATURE_VERIFICATION_FAILED';
  /**
   * Whether Common Criteria Mode is enabled.
   *
   * @var string
   */
  public $commonCriteriaModeStatus;
  /**
   * Output only. The status of policy signature verification.
   *
   * @var string
   */
  public $policySignatureVerificationStatus;

  /**
   * Whether Common Criteria Mode is enabled.
   *
   * Accepted values: COMMON_CRITERIA_MODE_STATUS_UNKNOWN,
   * COMMON_CRITERIA_MODE_DISABLED, COMMON_CRITERIA_MODE_ENABLED
   *
   * @param self::COMMON_CRITERIA_MODE_STATUS_* $commonCriteriaModeStatus
   */
  public function setCommonCriteriaModeStatus($commonCriteriaModeStatus)
  {
    $this->commonCriteriaModeStatus = $commonCriteriaModeStatus;
  }
  /**
   * @return self::COMMON_CRITERIA_MODE_STATUS_*
   */
  public function getCommonCriteriaModeStatus()
  {
    return $this->commonCriteriaModeStatus;
  }
  /**
   * Output only. The status of policy signature verification.
   *
   * Accepted values: POLICY_SIGNATURE_VERIFICATION_STATUS_UNSPECIFIED,
   * POLICY_SIGNATURE_VERIFICATION_DISABLED,
   * POLICY_SIGNATURE_VERIFICATION_SUCCEEDED,
   * POLICY_SIGNATURE_VERIFICATION_NOT_SUPPORTED,
   * POLICY_SIGNATURE_VERIFICATION_FAILED
   *
   * @param self::POLICY_SIGNATURE_VERIFICATION_STATUS_* $policySignatureVerificationStatus
   */
  public function setPolicySignatureVerificationStatus($policySignatureVerificationStatus)
  {
    $this->policySignatureVerificationStatus = $policySignatureVerificationStatus;
  }
  /**
   * @return self::POLICY_SIGNATURE_VERIFICATION_STATUS_*
   */
  public function getPolicySignatureVerificationStatus()
  {
    return $this->policySignatureVerificationStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommonCriteriaModeInfo::class, 'Google_Service_AndroidManagement_CommonCriteriaModeInfo');
