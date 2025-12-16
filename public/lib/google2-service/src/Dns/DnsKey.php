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

class DnsKey extends \Google\Collection
{
  public const ALGORITHM_rsasha1 = 'rsasha1';
  public const ALGORITHM_rsasha256 = 'rsasha256';
  public const ALGORITHM_rsasha512 = 'rsasha512';
  public const ALGORITHM_ecdsap256sha256 = 'ecdsap256sha256';
  public const ALGORITHM_ecdsap384sha384 = 'ecdsap384sha384';
  public const TYPE_keySigning = 'keySigning';
  public const TYPE_zoneSigning = 'zoneSigning';
  protected $collection_key = 'digests';
  /**
   * String mnemonic specifying the DNSSEC algorithm of this key. Immutable
   * after creation time.
   *
   * @var string
   */
  public $algorithm;
  /**
   * The time that this resource was created in the control plane. This is in
   * RFC3339 text format. Output only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the resource's function.
   *
   * @var string
   */
  public $description;
  protected $digestsType = DnsKeyDigest::class;
  protected $digestsDataType = 'array';
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @var string
   */
  public $id;
  /**
   * Active keys are used to sign subsequent changes to the ManagedZone.
   * Inactive keys are still present as DNSKEY Resource Records for the use of
   * resolvers validating existing signatures.
   *
   * @var bool
   */
  public $isActive;
  /**
   * Length of the key in bits. Specified at creation time, and then immutable.
   *
   * @var string
   */
  public $keyLength;
  /**
   * The key tag is a non-cryptographic hash of the a DNSKEY resource record
   * associated with this DnsKey. The key tag can be used to identify a DNSKEY
   * more quickly (but it is not a unique identifier). In particular, the key
   * tag is used in a parent zone's DS record to point at the DNSKEY in this
   * child ManagedZone. The key tag is a number in the range [0, 65535] and the
   * algorithm to calculate it is specified in RFC4034 Appendix B. Output only.
   *
   * @var int
   */
  public $keyTag;
  /**
   * @var string
   */
  public $kind;
  /**
   * Base64 encoded public half of this key. Output only.
   *
   * @var string
   */
  public $publicKey;
  /**
   * One of "KEY_SIGNING" or "ZONE_SIGNING". Keys of type KEY_SIGNING have the
   * Secure Entry Point flag set and, when active, are used to sign only
   * resource record sets of type DNSKEY. Otherwise, the Secure Entry Point flag
   * is cleared, and this key is used to sign only resource record sets of other
   * types. Immutable after creation time.
   *
   * @var string
   */
  public $type;

  /**
   * String mnemonic specifying the DNSSEC algorithm of this key. Immutable
   * after creation time.
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
   * The time that this resource was created in the control plane. This is in
   * RFC3339 text format. Output only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * A mutable string of at most 1024 characters associated with this resource
   * for the user's convenience. Has no effect on the resource's function.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Cryptographic hashes of the DNSKEY resource record associated with this
   * DnsKey. These digests are needed to construct a DS record that points at
   * this DNS key. Output only.
   *
   * @param DnsKeyDigest[] $digests
   */
  public function setDigests($digests)
  {
    $this->digests = $digests;
  }
  /**
   * @return DnsKeyDigest[]
   */
  public function getDigests()
  {
    return $this->digests;
  }
  /**
   * Unique identifier for the resource; defined by the server (output only).
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Active keys are used to sign subsequent changes to the ManagedZone.
   * Inactive keys are still present as DNSKEY Resource Records for the use of
   * resolvers validating existing signatures.
   *
   * @param bool $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return bool
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
  /**
   * Length of the key in bits. Specified at creation time, and then immutable.
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
   * The key tag is a non-cryptographic hash of the a DNSKEY resource record
   * associated with this DnsKey. The key tag can be used to identify a DNSKEY
   * more quickly (but it is not a unique identifier). In particular, the key
   * tag is used in a parent zone's DS record to point at the DNSKEY in this
   * child ManagedZone. The key tag is a number in the range [0, 65535] and the
   * algorithm to calculate it is specified in RFC4034 Appendix B. Output only.
   *
   * @param int $keyTag
   */
  public function setKeyTag($keyTag)
  {
    $this->keyTag = $keyTag;
  }
  /**
   * @return int
   */
  public function getKeyTag()
  {
    return $this->keyTag;
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
   * Base64 encoded public half of this key. Output only.
   *
   * @param string $publicKey
   */
  public function setPublicKey($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  /**
   * @return string
   */
  public function getPublicKey()
  {
    return $this->publicKey;
  }
  /**
   * One of "KEY_SIGNING" or "ZONE_SIGNING". Keys of type KEY_SIGNING have the
   * Secure Entry Point flag set and, when active, are used to sign only
   * resource record sets of type DNSKEY. Otherwise, the Secure Entry Point flag
   * is cleared, and this key is used to sign only resource record sets of other
   * types. Immutable after creation time.
   *
   * Accepted values: keySigning, zoneSigning
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DnsKey::class, 'Google_Service_Dns_DnsKey');
