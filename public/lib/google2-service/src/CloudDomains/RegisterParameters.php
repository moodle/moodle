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

class RegisterParameters extends \Google\Collection
{
  /**
   * The availability is unspecified.
   */
  public const AVAILABILITY_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';
  /**
   * The domain is available for registration.
   */
  public const AVAILABILITY_AVAILABLE = 'AVAILABLE';
  /**
   * The domain is not available for registration. Generally this means it is
   * already registered to another party.
   */
  public const AVAILABILITY_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * The domain is not currently supported by Cloud Domains, but may be
   * available elsewhere.
   */
  public const AVAILABILITY_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * Cloud Domains is unable to determine domain availability, generally due to
   * system maintenance at the domain name registry.
   */
  public const AVAILABILITY_UNKNOWN = 'UNKNOWN';
  protected $collection_key = 'supportedPrivacy';
  /**
   * Indicates whether the domain is available for registration. This value is
   * accurate when obtained by calling `RetrieveRegisterParameters`, but is
   * approximate when obtained by calling `SearchDomains`.
   *
   * @var string
   */
  public $availability;
  /**
   * The domain name. Unicode domain names are expressed in Punycode format.
   *
   * @var string
   */
  public $domainName;
  /**
   * Notices about special properties of the domain.
   *
   * @var string[]
   */
  public $domainNotices;
  /**
   * Contact privacy options that the domain supports.
   *
   * @var string[]
   */
  public $supportedPrivacy;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

  /**
   * Indicates whether the domain is available for registration. This value is
   * accurate when obtained by calling `RetrieveRegisterParameters`, but is
   * approximate when obtained by calling `SearchDomains`.
   *
   * Accepted values: AVAILABILITY_UNSPECIFIED, AVAILABLE, UNAVAILABLE,
   * UNSUPPORTED, UNKNOWN
   *
   * @param self::AVAILABILITY_* $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return self::AVAILABILITY_*
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * The domain name. Unicode domain names are expressed in Punycode format.
   *
   * @param string $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
  /**
   * Notices about special properties of the domain.
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
   * Contact privacy options that the domain supports.
   *
   * @param string[] $supportedPrivacy
   */
  public function setSupportedPrivacy($supportedPrivacy)
  {
    $this->supportedPrivacy = $supportedPrivacy;
  }
  /**
   * @return string[]
   */
  public function getSupportedPrivacy()
  {
    return $this->supportedPrivacy;
  }
  /**
   * Price to register or renew the domain for one year.
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
class_alias(RegisterParameters::class, 'Google_Service_CloudDomains_RegisterParameters');
