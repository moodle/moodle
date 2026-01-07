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

class TransferParameters extends \Google\Collection
{
  /**
   * The state is unspecified.
   */
  public const TRANSFER_LOCK_STATE_TRANSFER_LOCK_STATE_UNSPECIFIED = 'TRANSFER_LOCK_STATE_UNSPECIFIED';
  /**
   * The domain is unlocked and can be transferred to another registrar.
   */
  public const TRANSFER_LOCK_STATE_UNLOCKED = 'UNLOCKED';
  /**
   * The domain is locked and cannot be transferred to another registrar.
   */
  public const TRANSFER_LOCK_STATE_LOCKED = 'LOCKED';
  protected $collection_key = 'supportedPrivacy';
  /**
   * The registrar that currently manages the domain.
   *
   * @var string
   */
  public $currentRegistrar;
  /**
   * The URL of the registrar that currently manages the domain.
   *
   * @var string
   */
  public $currentRegistrarUri;
  /**
   * The domain name. Unicode domain names are expressed in Punycode format.
   *
   * @var string
   */
  public $domainName;
  /**
   * The name servers that currently store the configuration of the domain.
   *
   * @var string[]
   */
  public $nameServers;
  /**
   * Contact privacy options that the domain supports.
   *
   * @var string[]
   */
  public $supportedPrivacy;
  /**
   * Indicates whether the domain is protected by a transfer lock. For a
   * transfer to succeed, this must show `UNLOCKED`. To unlock a domain, go to
   * its current registrar.
   *
   * @var string
   */
  public $transferLockState;
  protected $yearlyPriceType = Money::class;
  protected $yearlyPriceDataType = '';

  /**
   * The registrar that currently manages the domain.
   *
   * @param string $currentRegistrar
   */
  public function setCurrentRegistrar($currentRegistrar)
  {
    $this->currentRegistrar = $currentRegistrar;
  }
  /**
   * @return string
   */
  public function getCurrentRegistrar()
  {
    return $this->currentRegistrar;
  }
  /**
   * The URL of the registrar that currently manages the domain.
   *
   * @param string $currentRegistrarUri
   */
  public function setCurrentRegistrarUri($currentRegistrarUri)
  {
    $this->currentRegistrarUri = $currentRegistrarUri;
  }
  /**
   * @return string
   */
  public function getCurrentRegistrarUri()
  {
    return $this->currentRegistrarUri;
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
   * The name servers that currently store the configuration of the domain.
   *
   * @param string[] $nameServers
   */
  public function setNameServers($nameServers)
  {
    $this->nameServers = $nameServers;
  }
  /**
   * @return string[]
   */
  public function getNameServers()
  {
    return $this->nameServers;
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
   * Indicates whether the domain is protected by a transfer lock. For a
   * transfer to succeed, this must show `UNLOCKED`. To unlock a domain, go to
   * its current registrar.
   *
   * Accepted values: TRANSFER_LOCK_STATE_UNSPECIFIED, UNLOCKED, LOCKED
   *
   * @param self::TRANSFER_LOCK_STATE_* $transferLockState
   */
  public function setTransferLockState($transferLockState)
  {
    $this->transferLockState = $transferLockState;
  }
  /**
   * @return self::TRANSFER_LOCK_STATE_*
   */
  public function getTransferLockState()
  {
    return $this->transferLockState;
  }
  /**
   * Price to transfer or renew the domain for one year.
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
class_alias(TransferParameters::class, 'Google_Service_CloudDomains_TransferParameters');
