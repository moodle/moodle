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

class Domain extends \Google\Model
{
  /**
   * The assessment is undefined.
   */
  public const RESOURCE_STATE_RESOURCE_STATE_UNSPECIFIED = 'RESOURCE_STATE_UNSPECIFIED';
  /**
   * A `Registration` resource can be created for this domain by calling
   * `ImportDomain`.
   */
  public const RESOURCE_STATE_IMPORTABLE = 'IMPORTABLE';
  /**
   * A `Registration` resource cannot be created for this domain because it is
   * not supported by Cloud Domains; for example, the top-level domain is not
   * supported or the registry charges non-standard pricing for yearly renewals.
   */
  public const RESOURCE_STATE_UNSUPPORTED = 'UNSUPPORTED';
  /**
   * A `Registration` resource cannot be created for this domain because it is
   * suspended and needs to be resolved with Google Domains.
   */
  public const RESOURCE_STATE_SUSPENDED = 'SUSPENDED';
  /**
   * A `Registration` resource cannot be created for this domain because it is
   * expired and needs to be renewed with Google Domains.
   */
  public const RESOURCE_STATE_EXPIRED = 'EXPIRED';
  /**
   * A `Registration` resource cannot be created for this domain because it is
   * deleted, but it may be possible to restore it with Google Domains.
   */
  public const RESOURCE_STATE_DELETED = 'DELETED';
  /**
   * The domain name. Unicode domain names are expressed in Punycode format.
   *
   * @var string
   */
  public $domainName;
  /**
   * The state of this domain as a `Registration` resource.
   *
   * @var string
   */
  public $resourceState;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

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
   * The state of this domain as a `Registration` resource.
   *
   * Accepted values: RESOURCE_STATE_UNSPECIFIED, IMPORTABLE, UNSUPPORTED,
   * SUSPENDED, EXPIRED, DELETED
   *
   * @param self::RESOURCE_STATE_* $resourceState
   */
  public function setResourceState($resourceState)
  {
    $this->resourceState = $resourceState;
  }
  /**
   * @return self::RESOURCE_STATE_*
   */
  public function getResourceState()
  {
    return $this->resourceState;
  }
  /**
   * Price to renew the domain for one year. Only set when `resource_state` is
   * `IMPORTABLE`.
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
class_alias(Domain::class, 'Google_Service_CloudDomains_Domain');
