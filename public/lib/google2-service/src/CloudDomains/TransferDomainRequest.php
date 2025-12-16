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

namespace Google\Service\CloudDomains;

class TransferDomainRequest extends \Google\Collection
{
  protected $collection_key = 'contactNotices';
  protected $authorizationCodeType = AuthorizationCode::class;
  protected $authorizationCodeDataType = '';
  /**
   * The list of contact notices that you acknowledge. The notices needed here
   * depend on the values specified in `registration.contact_settings`.
   *
   * @var string[]
   */
  public $contactNotices;
  protected $registrationType = Registration::class;
  protected $registrationDataType = '';
  /**
   * Validate the request without actually transferring the domain.
   *
   * @var bool
   */
  public $validateOnly;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

  /**
   * The domain's transfer authorization code. You can obtain this from the
   * domain's current registrar.
   *
   * @param AuthorizationCode $authorizationCode
   */
  public function setAuthorizationCode(AuthorizationCode $authorizationCode)
  {
    $this->authorizationCode = $authorizationCode;
  }
  /**
   * @return AuthorizationCode
   */
  public function getAuthorizationCode()
  {
    return $this->authorizationCode;
  }
  /**
   * The list of contact notices that you acknowledge. The notices needed here
   * depend on the values specified in `registration.contact_settings`.
   *
   * @param string[] $contactNotices
   */
  public function setContactNotices($contactNotices)
  {
    $this->contactNotices = $contactNotices;
  }
  /**
   * @return string[]
   */
  public function getContactNotices()
  {
    return $this->contactNotices;
  }
  /**
   * Required. The complete `Registration` resource to be created. You can leave
   * `registration.dns_settings` unset to import the domain's current DNS
   * configuration from its current registrar. Use this option only if you are
   * sure that the domain's current DNS service does not cease upon transfer, as
   * is often the case for DNS services provided for free by the registrar.
   *
   * @param Registration $registration
   */
  public function setRegistration(Registration $registration)
  {
    $this->registration = $registration;
  }
  /**
   * @return Registration
   */
  public function getRegistration()
  {
    return $this->registration;
  }
  /**
   * Validate the request without actually transferring the domain.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
  /**
   * Required. Acknowledgement of the price to transfer or renew the domain for
   * one year. Call `RetrieveTransferParameters` to obtain the price, which you
   * must acknowledge.
   *
   * @param Money $yearlyPrice
   */
  public function setYearlyPrice(Money $yearlyPrice)
  {
    $this->yearlyPrice = $yearlyPrice;
  }
  /**
   * @return Money
   */
  public function getYearlyPrice()
  {
    return $this->yearlyPrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferDomainRequest::class, 'Google_Service_CloudDomains_TransferDomainRequest');
