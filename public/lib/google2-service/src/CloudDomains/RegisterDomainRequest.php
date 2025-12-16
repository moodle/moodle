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

class RegisterDomainRequest extends \Google\Collection
{
  protected $collection_key = 'domainNotices';
  /**
   * The list of contact notices that the caller acknowledges. The notices
   * needed here depend on the values specified in
   * `registration.contact_settings`.
   *
   * @var string[]
   */
  public $contactNotices;
  /**
   * The list of domain notices that you acknowledge. Call
   * `RetrieveRegisterParameters` to see the notices that need acknowledgement.
   *
   * @var string[]
   */
  public $domainNotices;
  protected $registrationType = Registration::class;
  protected $registrationDataType = '';
  /**
   * When true, only validation is performed, without actually registering the
   * domain. Follows:
   * https://cloud.google.com/apis/design/design_patterns#request_validation
   *
   * @var bool
   */
  public $validateOnly;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

  /**
   * The list of contact notices that the caller acknowledges. The notices
   * needed here depend on the values specified in
   * `registration.contact_settings`.
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
   * The list of domain notices that you acknowledge. Call
   * `RetrieveRegisterParameters` to see the notices that need acknowledgement.
   *
   * @param string[] $domainNotices
   */
  public function setDomainNotices($domainNotices)
  {
    $this->domainNotices = $domainNotices;
  }
  /**
   * @return string[]
   */
  public function getDomainNotices()
  {
    return $this->domainNotices;
  }
  /**
   * Required. The complete `Registration` resource to be created.
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
   * When true, only validation is performed, without actually registering the
   * domain. Follows:
   * https://cloud.google.com/apis/design/design_patterns#request_validation
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
   * Required. Yearly price to register or renew the domain. The value that
   * should be put here can be obtained from RetrieveRegisterParameters or
   * SearchDomains calls.
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
class_alias(RegisterDomainRequest::class, 'Google_Service_CloudDomains_RegisterDomainRequest');
