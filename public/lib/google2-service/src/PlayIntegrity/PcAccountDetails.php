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

namespace Google\Service\PlayIntegrity;

class PcAccountDetails extends \Google\Model
{
  /**
   * Play does not have sufficient information to evaluate licensing details
   */
  public const APP_LICENSING_VERDICT_UNKNOWN = 'UNKNOWN';
  /**
   * The user has a valid license to use the app.
   */
  public const APP_LICENSING_VERDICT_LICENSED = 'LICENSED';
  /**
   * The user does not have a valid license to use the app.
   */
  public const APP_LICENSING_VERDICT_UNLICENSED = 'UNLICENSED';
  /**
   * Licensing details were not evaluated since a necessary requirement was
   * missed.
   */
  public const APP_LICENSING_VERDICT_UNEVALUATED = 'UNEVALUATED';
  /**
   * Required. Details about the licensing status of the user for the app in the
   * scope.
   *
   * @var string
   */
  public $appLicensingVerdict;

  /**
   * Required. Details about the licensing status of the user for the app in the
   * scope.
   *
   * Accepted values: UNKNOWN, LICENSED, UNLICENSED, UNEVALUATED
   *
   * @param self::APP_LICENSING_VERDICT_* $appLicensingVerdict
   */
  public function setAppLicensingVerdict($appLicensingVerdict)
  {
    $this->appLicensingVerdict = $appLicensingVerdict;
  }
  /**
   * @return self::APP_LICENSING_VERDICT_*
   */
  public function getAppLicensingVerdict()
  {
    return $this->appLicensingVerdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PcAccountDetails::class, 'Google_Service_PlayIntegrity_PcAccountDetails');
