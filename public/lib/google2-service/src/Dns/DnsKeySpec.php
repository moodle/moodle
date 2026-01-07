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

class DnsKeySpec extends \Google\Model
{
  public const ALGORITHM_rsasha1 = 'rsasha1';
  public const ALGORITHM_rsasha256 = 'rsasha256';
  public const ALGORITHM_rsasha512 = 'rsasha512';
  public const ALGORITHM_ecdsap256sha256 = 'ecdsap256sha256';
  public const ALGORITHM_ecdsap384sha384 = 'ecdsap384sha384';
  public const KEY_TYPE_keySigning = 'keySigning';
  public const KEY_TYPE_zoneSigning = 'zoneSigning';
  /**
   * String mnemonic specifying the DNSSEC algorithm of this key.
   *
   * @var string
   */
  public $algorithm;
  /**
   * Length of the keys in bits.
   *
   * @var string
   */
  public $keyLength;
  /**
   * Specifies whether this is a key signing key (KSK) or a zone signing key
   * (ZSK). Key signing keys have the Secure Entry Point flag set and, when
   * active, are only used to sign resource record sets of type DNSKEY. Zone
   * signing keys do not have the Secure Entry Point flag set and are used to
   * sign all other types of resource record sets.
   *
   * @var string
   */
  public $keyType;
  /**
   * @var string
   */
  public $kind;

  /**
   * String mnemonic specifying the DNSSEC algorithm of this key.
   *
   * Accepted values: rsasha1, rsasha256, rsasha512, ecdsap256sha256,
   * ecdsap384sha384
   *
   * @param self::ALGORITHM_* $algorithm
   */
  public function setAlgorithm($algorithm)
  {
    $this->algorithm = $algorithm;
  }
  /**
   * @return self::ALGORITHM_*
   */
  public function getAlgorithm()
  {
    return $this->algorithm;
  }
  /**
   * Length of the keys in bits.
   *
   * @param string $keyLength
   */
  public function setKeyLength($keyLength)
  {
    $this->keyLength = $keyLength;
  }
  /**
   * @return string
   */
  public function getKeyLength()
  {
    return $this->keyLength;
  }
  /**
   * Specifies whether this is a key signing key (KSK) or a zone signing key
   * (ZSK). Key signing keys have the Secure Entry Point flag set and, when
   * active, are only used to sign resource record sets of type DNSKEY. Zone
   * signing keys do not have the Secure Entry Point flag set and are used to
   * sign all other types of resource record sets.
   *
   * Accepted values: keySigning, zoneSigning
   *
   * @param self::KEY_TYPE_* $keyType
   */
  public function setKeyType($keyType)
  {
    $this->keyType = $keyType;
  }
  /**
   * @return self::KEY_TYPE_*
   */
  public function getKeyType()
  {
    return $this->keyType;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsKeySpec::class, 'Google_Service_Dns_DnsKeySpec');
