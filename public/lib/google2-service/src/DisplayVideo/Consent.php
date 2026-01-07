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

namespace Google\Service\DisplayVideo;

class Consent extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const AD_PERSONALIZATION_CONSENT_STATUS_UNSPECIFIED = 'CONSENT_STATUS_UNSPECIFIED';
  /**
   * Consent is granted.
   */
  public const AD_PERSONALIZATION_CONSENT_STATUS_GRANTED = 'CONSENT_STATUS_GRANTED';
  /**
   * Consent is denied.
   */
  public const AD_PERSONALIZATION_CONSENT_STATUS_DENIED = 'CONSENT_STATUS_DENIED';
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const AD_USER_DATA_CONSENT_STATUS_UNSPECIFIED = 'CONSENT_STATUS_UNSPECIFIED';
  /**
   * Consent is granted.
   */
  public const AD_USER_DATA_CONSENT_STATUS_GRANTED = 'CONSENT_STATUS_GRANTED';
  /**
   * Consent is denied.
   */
  public const AD_USER_DATA_CONSENT_STATUS_DENIED = 'CONSENT_STATUS_DENIED';
  /**
   * Represents consent for ad personalization.
   *
   * @var string
   */
  public $adPersonalization;
  /**
   * Represents consent for ad user data.
   *
   * @var string
   */
  public $adUserData;

  /**
   * Represents consent for ad personalization.
   *
   * Accepted values: CONSENT_STATUS_UNSPECIFIED, CONSENT_STATUS_GRANTED,
   * CONSENT_STATUS_DENIED
   *
   * @param self::AD_PERSONALIZATION_* $adPersonalization
   */
  public function setAdPersonalization($adPersonalization)
  {
    $this->adPersonalization = $adPersonalization;
  }
  /**
   * @return self::AD_PERSONALIZATION_*
   */
  public function getAdPersonalization()
  {
    return $this->adPersonalization;
  }
  /**
   * Represents consent for ad user data.
   *
   * Accepted values: CONSENT_STATUS_UNSPECIFIED, CONSENT_STATUS_GRANTED,
   * CONSENT_STATUS_DENIED
   *
   * @param self::AD_USER_DATA_* $adUserData
   */
  public function setAdUserData($adUserData)
  {
    $this->adUserData = $adUserData;
  }
  /**
   * @return self::AD_USER_DATA_*
   */
  public function getAdUserData()
  {
    return $this->adUserData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Consent::class, 'Google_Service_DisplayVideo_Consent');
