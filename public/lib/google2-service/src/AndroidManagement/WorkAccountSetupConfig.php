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

class WorkAccountSetupConfig extends \Google\Model
{
  /**
   * Unspecified. Defaults to AUTHENTICATION_TYPE_NOT_ENFORCED.
   */
  public const AUTHENTICATION_TYPE_AUTHENTICATION_TYPE_UNSPECIFIED = 'AUTHENTICATION_TYPE_UNSPECIFIED';
  /**
   * Authentication status of user on device is not enforced.
   */
  public const AUTHENTICATION_TYPE_AUTHENTICATION_TYPE_NOT_ENFORCED = 'AUTHENTICATION_TYPE_NOT_ENFORCED';
  /**
   * Requires device to be managed with a Google authenticated account.
   */
  public const AUTHENTICATION_TYPE_GOOGLE_AUTHENTICATED = 'GOOGLE_AUTHENTICATED';
  /**
   * Optional. The authentication type of the user on the device.
   *
   * @var string
   */
  public $authenticationType;
  /**
   * Optional. The specific google work account email address to be added. This
   * field is only relevant if authenticationType is GOOGLE_AUTHENTICATED. This
   * must be an enterprise account and not a consumer account. Once set and a
   * Google authenticated account is added to the device, changing this field
   * will have no effect, and thus recommended to be set only once.
   *
   * @var string
   */
  public $requiredAccountEmail;

  /**
   * Optional. The authentication type of the user on the device.
   *
   * Accepted values: AUTHENTICATION_TYPE_UNSPECIFIED,
   * AUTHENTICATION_TYPE_NOT_ENFORCED, GOOGLE_AUTHENTICATED
   *
   * @param self::AUTHENTICATION_TYPE_* $authenticationType
   */
  public function setAuthenticationType($authenticationType)
  {
    $this->authenticationType = $authenticationType;
  }
  /**
   * @return self::AUTHENTICATION_TYPE_*
   */
  public function getAuthenticationType()
  {
    return $this->authenticationType;
  }
  /**
   * Optional. The specific google work account email address to be added. This
   * field is only relevant if authenticationType is GOOGLE_AUTHENTICATED. This
   * must be an enterprise account and not a consumer account. Once set and a
   * Google authenticated account is added to the device, changing this field
   * will have no effect, and thus recommended to be set only once.
   *
   * @param string $requiredAccountEmail
   */
  public function setRequiredAccountEmail($requiredAccountEmail)
  {
    $this->requiredAccountEmail = $requiredAccountEmail;
  }
  /**
   * @return string
   */
  public function getRequiredAccountEmail()
  {
    return $this->requiredAccountEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkAccountSetupConfig::class, 'Google_Service_AndroidManagement_WorkAccountSetupConfig');
