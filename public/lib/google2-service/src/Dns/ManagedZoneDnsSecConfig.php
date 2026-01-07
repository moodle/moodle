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

namespace Google\Service\Dns;

class ManagedZoneDnsSecConfig extends \Google\Collection
{
  /**
   * Indicates that Cloud DNS will sign records in the managed zone according to
   * RFC 4034 and respond with NSEC records for names that do not exist.
   */
  public const NON_EXISTENCE_nsec = 'nsec';
  /**
   * Indicates that Cloud DNS will sign records in the managed zone according to
   * RFC 5155 and respond with NSEC3 records for names that do not exist.
   */
  public const NON_EXISTENCE_nsec3 = 'nsec3';
  /**
   * DNSSEC is disabled; the zone is not signed.
   */
  public const STATE_off = 'off';
  /**
   * DNSSEC is enabled; the zone is signed and fully managed.
   */
  public const STATE_on = 'on';
  /**
   * DNSSEC is enabled, but in a "transfer" mode.
   */
  public const STATE_transfer = 'transfer';
  protected $collection_key = 'defaultKeySpecs';
  protected $defaultKeySpecsType = DnsKeySpec::class;
  protected $defaultKeySpecsDataType = 'array';
  /**
   * @var string
   */
  public $kind;
  /**
   * Specifies the mechanism for authenticated denial-of-existence responses.
   * Can only be changed while the state is OFF.
   *
   * @var string
   */
  public $nonExistence;
  /**
   * Specifies whether DNSSEC is enabled, and what mode it is in.
   *
   * @var string
   */
  public $state;

  /**
   * Specifies parameters for generating initial DnsKeys for this ManagedZone.
   * Can only be changed while the state is OFF.
   *
   * @param DnsKeySpec[] $defaultKeySpecs
   */
  public function setDefaultKeySpecs($defaultKeySpecs)
  {
    $this->defaultKeySpecs = $defaultKeySpecs;
  }
  /**
   * @return DnsKeySpec[]
   */
  public function getDefaultKeySpecs()
  {
    return $this->defaultKeySpecs;
  }
  /**
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Specifies the mechanism for authenticated denial-of-existence responses.
   * Can only be changed while the state is OFF.
   *
   * Accepted values: nsec, nsec3
   *
   * @param self::NON_EXISTENCE_* $nonExistence
   */
  public function setNonExistence($nonExistence)
  {
    $this->nonExistence = $nonExistence;
  }
  /**
   * @return self::NON_EXISTENCE_*
   */
  public function getNonExistence()
  {
    return $this->nonExistence;
  }
  /**
   * Specifies whether DNSSEC is enabled, and what mode it is in.
   *
   * Accepted values: off, on, transfer
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
class_alias(ManagedZoneDnsSecConfig::class, 'Google_Service_Dns_ManagedZoneDnsSecConfig');
