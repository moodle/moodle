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

class VerificationOption extends \Google\Model
{
  /**
   * Default value, will result in errors.
   */
  public const VERIFICATION_METHOD_VERIFICATION_METHOD_UNSPECIFIED = 'VERIFICATION_METHOD_UNSPECIFIED';
  /**
   * Send a postcard with a verification PIN to a specific mailing address. The
   * PIN is used to complete verification with Google.
   */
  public const VERIFICATION_METHOD_ADDRESS = 'ADDRESS';
  /**
   * Send an email with a verification PIN to a specific email address. The PIN
   * is used to complete verification with Google.
   */
  public const VERIFICATION_METHOD_EMAIL = 'EMAIL';
  /**
   * Make a phone call with a verification PIN to a specific phone number. The
   * PIN is used to complete verification with Google.
   */
  public const VERIFICATION_METHOD_PHONE_CALL = 'PHONE_CALL';
  /**
   * Send an SMS with a verification PIN to a specific phone number. The PIN is
   * used to complete verification with Google.
   */
  public const VERIFICATION_METHOD_SMS = 'SMS';
  /**
   * Verify the location without additional user action. This option may not be
   * available for all locations.
   */
  public const VERIFICATION_METHOD_AUTO = 'AUTO';
  /**
   * This option may not be available for all locations.
   */
  public const VERIFICATION_METHOD_VETTED_PARTNER = 'VETTED_PARTNER';
  /**
   * Verify the location via a trusted partner.
   */
  public const VERIFICATION_METHOD_TRUSTED_PARTNER = 'TRUSTED_PARTNER';
  protected $addressDataType = AddressVerificationData::class;
  protected $addressDataDataType = '';
  /**
   * Set only if the method is VETTED_PARTNER.
   *
   * @var string
   */
  public $announcement;
  protected $emailDataType = EmailVerificationData::class;
  protected $emailDataDataType = '';
  /**
   * Set only if the method is PHONE_CALL or SMS. Phone number that the PIN will
   * be sent to.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * Method to verify the location.
   *
   * @var string
   */
  public $verificationMethod;

  /**
   * Set only if the method is MAIL.
   *
   * @param AddressVerificationData $addressData
   */
  public function setAddressData(AddressVerificationData $addressData)
  {
    $this->addressData = $addressData;
  }
  /**
   * @return AddressVerificationData
   */
  public function getAddressData()
  {
    return $this->addressData;
  }
  /**
   * Set only if the method is VETTED_PARTNER.
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
   * Set only if the method is EMAIL.
   *
   * @param EmailVerificationData $emailData
   */
  public function setEmailData(EmailVerificationData $emailData)
  {
    $this->emailData = $emailData;
  }
  /**
   * @return EmailVerificationData
   */
  public function getEmailData()
  {
    return $this->emailData;
  }
  /**
   * Set only if the method is PHONE_CALL or SMS. Phone number that the PIN will
   * be sent to.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * Method to verify the location.
   *
   * Accepted values: VERIFICATION_METHOD_UNSPECIFIED, ADDRESS, EMAIL,
   * PHONE_CALL, SMS, AUTO, VETTED_PARTNER, TRUSTED_PARTNER
   *
   * @param self::VERIFICATION_METHOD_* $verificationMethod
   */
  public function setVerificationMethod($verificationMethod)
  {
    $this->verificationMethod = $verificationMethod;
  }
  /**
   * @return self::VERIFICATION_METHOD_*
   */
  public function getVerificationMethod()
  {
    return $this->verificationMethod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerificationOption::class, 'Google_Service_MyBusinessVerifications_VerificationOption');
