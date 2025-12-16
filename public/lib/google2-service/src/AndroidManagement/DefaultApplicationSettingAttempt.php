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

class DefaultApplicationSettingAttempt extends \Google\Model
{
  /**
   * Attempt outcome is unspecified. This is not used.
   */
  public const ATTEMPT_OUTCOME_ATTEMPT_OUTCOME_UNSPECIFIED = 'ATTEMPT_OUTCOME_UNSPECIFIED';
  /**
   * App is successfully set as the default.
   */
  public const ATTEMPT_OUTCOME_SUCCESS = 'SUCCESS';
  /**
   * Attempt failed as the app is not installed.
   */
  public const ATTEMPT_OUTCOME_APP_NOT_INSTALLED = 'APP_NOT_INSTALLED';
  /**
   * Attempt failed as the signing key certificate fingerprint of the app from
   * Play Store or from ApplicationPolicy.signingKeyCerts does not match the one
   * on the device.
   */
  public const ATTEMPT_OUTCOME_APP_SIGNING_CERT_MISMATCH = 'APP_SIGNING_CERT_MISMATCH';
  /**
   * Attempt failed due to other reasons.
   */
  public const ATTEMPT_OUTCOME_OTHER_FAILURE = 'OTHER_FAILURE';
  /**
   * Output only. The outcome of setting the app as the default.
   *
   * @var string
   */
  public $attemptOutcome;
  /**
   * Output only. The package name of the attempted application.
   *
   * @var string
   */
  public $packageName;

  /**
   * Output only. The outcome of setting the app as the default.
   *
   * Accepted values: ATTEMPT_OUTCOME_UNSPECIFIED, SUCCESS, APP_NOT_INSTALLED,
   * APP_SIGNING_CERT_MISMATCH, OTHER_FAILURE
   *
   * @param self::ATTEMPT_OUTCOME_* $attemptOutcome
   */
  public function setAttemptOutcome($attemptOutcome)
  {
    $this->attemptOutcome = $attemptOutcome;
  }
  /**
   * @return self::ATTEMPT_OUTCOME_*
   */
  public function getAttemptOutcome()
  {
    return $this->attemptOutcome;
  }
  /**
   * Output only. The package name of the attempted application.
   *
   * @param string $packageName
   */
  public function setPackageName($packageName)
  {
    $this->packageName = $packageName;
  }
  /**
   * @return string
   */
  public function getPackageName()
  {
    return $this->packageName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DefaultApplicationSettingAttempt::class, 'Google_Service_AndroidManagement_DefaultApplicationSettingAttempt');
