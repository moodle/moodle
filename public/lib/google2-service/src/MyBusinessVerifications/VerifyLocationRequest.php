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

class VerifyLocationRequest extends \Google\Model
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
  protected $contextType = ServiceBusinessContext::class;
  protected $contextDataType = '';
  /**
   * Optional. The input for EMAIL method. Email address where the PIN should be
   * sent to. An email address is accepted only if it is one of the addresses
   * provided by FetchVerificationOptions. If the EmailVerificationData has
   * is_user_name_editable set to true, the client may specify a different user
   * name (local-part) but must match the domain name.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Optional. The BCP 47 language code representing the language that is to be
   * used for the verification process.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. The input for ADDRESS method. Contact name the mail should be
   * sent to.
   *
   * @var string
   */
  public $mailerContact;
  /**
   * Required. Verification method.
   *
   * @var string
   */
  public $method;
  /**
   * Optional. The input for PHONE_CALL/SMS method The phone number that should
   * be called or be sent SMS to. It must be one of the phone numbers in the
   * eligible options.
   *
   * @var string
   */
  public $phoneNumber;
  protected $tokenType = VerificationToken::class;
  protected $tokenDataType = '';
  /**
   * The input for TRUSTED_PARTNER method The verification token that is
   * associated to the location.
   *
   * @var string
   */
  public $trustedPartnerToken;

  /**
   * Optional. Extra context information for the verification of service
   * businesses. It is only required for the locations whose business type is
   * CUSTOMER_LOCATION_ONLY. For ADDRESS verification, the address will be used
   * to send out postcard. For other methods, it should be the same as the one
   * that is passed to GetVerificationOptions. INVALID_ARGUMENT will be thrown
   * if it is set for other types of business locations.
   *
   * @param ServiceBusinessContext $context
   */
  public function setContext(ServiceBusinessContext $context)
  {
    $this->context = $context;
  }
  /**
   * @return ServiceBusinessContext
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * Optional. The input for EMAIL method. Email address where the PIN should be
   * sent to. An email address is accepted only if it is one of the addresses
   * provided by FetchVerificationOptions. If the EmailVerificationData has
   * is_user_name_editable set to true, the client may specify a different user
   * name (local-part) but must match the domain name.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * Optional. The BCP 47 language code representing the language that is to be
   * used for the verification process.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Optional. The input for ADDRESS method. Contact name the mail should be
   * sent to.
   *
   * @param string $mailerContact
   */
  public function setMailerContact($mailerContact)
  {
    $this->mailerContact = $mailerContact;
  }
  /**
   * @return string
   */
  public function getMailerContact()
  {
    return $this->mailerContact;
  }
  /**
   * Required. Verification method.
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
   * Optional. The input for PHONE_CALL/SMS method The phone number that should
   * be called or be sent SMS to. It must be one of the phone numbers in the
   * eligible options.
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
   * Optional. The input for VETTED_PARTNER method available to select
   * [partners.](https://support.google.com/business/answer/7674102) The input
   * is not needed for a vetted account. Token that is associated to the
   * location. Token that is associated to the location.
   *
   * @param VerificationToken $token
   */
  public function setToken(VerificationToken $token)
  {
    $this->token = $token;
  }
  /**
   * @return VerificationToken
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * The input for TRUSTED_PARTNER method The verification token that is
   * associated to the location.
   *
   * @param string $trustedPartnerToken
   */
  public function setTrustedPartnerToken($trustedPartnerToken)
  {
    $this->trustedPartnerToken = $trustedPartnerToken;
  }
  /**
   * @return string
   */
  public function getTrustedPartnerToken()
  {
    return $this->trustedPartnerToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyLocationRequest::class, 'Google_Service_MyBusinessVerifications_VerifyLocationRequest');
