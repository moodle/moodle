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

namespace Google\Service\MyBusinessVerifications;

class Verification extends \Google\Model
{
  /**
   * Default value, will result in errors.
   */
  public const METHOD_VERIFICATION_METHOD_UNSPECIFIED = 'VERIFICATION_METHOD_UNSPECIFIED';
  /**
   * Send a postcard with a verification PIN to a specific mailing address. The
   * PIN is used to complete verification with Google.
   */
  public const METHOD_ADDRESS = 'ADDRESS';
  /**
   * Send an email with a verification PIN to a specific email address. The PIN
   * is used to complete verification with Google.
   */
  public const METHOD_EMAIL = 'EMAIL';
  /**
   * Make a phone call with a verification PIN to a specific phone number. The
   * PIN is used to complete verification with Google.
   */
  public const METHOD_PHONE_CALL = 'PHONE_CALL';
  /**
   * Send an SMS with a verification PIN to a specific phone number. The PIN is
   * used to complete verification with Google.
   */
  public const METHOD_SMS = 'SMS';
  /**
   * Verify the location without additional user action. This option may not be
   * available for all locations.
   */
  public const METHOD_AUTO = 'AUTO';
  /**
   * This option may not be available for all locations.
   */
  public const METHOD_VETTED_PARTNER = 'VETTED_PARTNER';
  /**
   * Verify the location via a trusted partner.
   */
  public const METHOD_TRUSTED_PARTNER = 'TRUSTED_PARTNER';
  /**
   * Default value, will result in errors.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The verification is pending.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The verification is completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The verification is failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Optional. Response announcement set only if the method is VETTED_PARTNER.
   *
   * @var string
   */
  public $announcement;
  /**
   * The timestamp when the verification is requested.
   *
   * @var string
   */
  public $createTime;
  /**
   * The method of the verification.
   *
   * @var string
   */
  public $method;
  /**
   * Resource name of the verification.
   *
   * @var string
   */
  public $name;
  /**
   * The state of the verification.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Response announcement set only if the method is VETTED_PARTNER.
   *
   * @param string $announcement
   */
  public function setAnnouncement($announcement)
  {
    $this->announcement = $announcement;
  }
  /**
   * @return string
   */
  public function getAnnouncement()
  {
    return $this->announcement;
  }
  /**
   * The timestamp when the verification is requested.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The method of the verification.
   *
   * Accepted values: VERIFICATION_METHOD_UNSPECIFIED, ADDRESS, EMAIL,
   * PHONE_CALL, SMS, AUTO, VETTED_PARTNER, TRUSTED_PARTNER
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Resource name of the verification.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The state of the verification.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, COMPLETED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Verification::class, 'Google_Service_MyBusinessVerifications_Verification');
