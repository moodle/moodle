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

class DsRecord extends \Google\Model
{
  /**
   * The algorithm is unspecified.
   */
  public const ALGORITHM_ALGORITHM_UNSPECIFIED = 'ALGORITHM_UNSPECIFIED';
  /**
   * RSA/MD5. Cannot be used for new deployments.
   */
  public const ALGORITHM_RSAMD5 = 'RSAMD5';
  /**
   * Diffie-Hellman. Cannot be used for new deployments.
   */
  public const ALGORITHM_DH = 'DH';
  /**
   * DSA/SHA1. Not recommended for new deployments.
   */
  public const ALGORITHM_DSA = 'DSA';
  /**
   * ECC. Not recommended for new deployments.
   */
  public const ALGORITHM_ECC = 'ECC';
  /**
   * RSA/SHA-1. Not recommended for new deployments.
   */
  public const ALGORITHM_RSASHA1 = 'RSASHA1';
  /**
   * DSA-NSEC3-SHA1. Not recommended for new deployments.
   */
  public const ALGORITHM_DSANSEC3SHA1 = 'DSANSEC3SHA1';
  /**
   * RSA/SHA1-NSEC3-SHA1. Not recommended for new deployments.
   */
  public const ALGORITHM_RSASHA1NSEC3SHA1 = 'RSASHA1NSEC3SHA1';
  /**
   * RSA/SHA-256.
   */
  public const ALGORITHM_RSASHA256 = 'RSASHA256';
  /**
   * RSA/SHA-512.
   */
  public const ALGORITHM_RSASHA512 = 'RSASHA512';
  /**
   * GOST R 34.10-2001.
   */
  public const ALGORITHM_ECCGOST = 'ECCGOST';
  /**
   * ECDSA Curve P-256 with SHA-256.
   */
  public const ALGORITHM_ECDSAP256SHA256 = 'ECDSAP256SHA256';
  /**
   * ECDSA Curve P-384 with SHA-384.
   */
  public const ALGORITHM_ECDSAP384SHA384 = 'ECDSAP384SHA384';
  /**
   * Ed25519.
   */
  public const ALGORITHM_ED25519 = 'ED25519';
  /**
   * Ed448.
   */
  public const ALGORITHM_ED448 = 'ED448';
  /**
   * Reserved for Indirect Keys. Cannot be used for new deployments.
   */
  public const ALGORITHM_INDIRECT = 'INDIRECT';
  /**
   * Private algorithm. Cannot be used for new deployments.
   */
  public const ALGORITHM_PRIVATEDNS = 'PRIVATEDNS';
  /**
   * Private algorithm OID. Cannot be used for new deployments.
   */
  public const ALGORITHM_PRIVATEOID = 'PRIVATEOID';
  /**
   * The DigestType is unspecified.
   */
  public const DIGEST_TYPE_DIGEST_TYPE_UNSPECIFIED = 'DIGEST_TYPE_UNSPECIFIED';
  /**
   * SHA-1. Not recommended for new deployments.
   */
  public const DIGEST_TYPE_SHA1 = 'SHA1';
  /**
   * SHA-256.
   */
  public const DIGEST_TYPE_SHA256 = 'SHA256';
  /**
   * GOST R 34.11-94.
   */
  public const DIGEST_TYPE_GOST3411 = 'GOST3411';
  /**
   * SHA-384.
   */
  public const DIGEST_TYPE_SHA384 = 'SHA384';
  /**
   * The algorithm used to generate the referenced DNSKEY.
   *
   * @var string
   */
  public $algorithm;
  /**
   * The digest generated from the referenced DNSKEY.
   *
   * @var string
   */
  public $digest;
  /**
   * The hash function used to generate the digest of the referenced DNSKEY.
   *
   * @var string
   */
  public $digestType;
  /**
   * The key tag of the record. Must be set in range 0 -- 65535.
   *
   * @var int
   */
  public $keyTag;

  /**
   * The algorithm used to generate the referenced DNSKEY.
   *
   * Accepted values: ALGORITHM_UNSPECIFIED, RSAMD5, DH, DSA, ECC, RSASHA1,
   * DSANSEC3SHA1, RSASHA1NSEC3SHA1, RSASHA256, RSASHA512, ECCGOST,
   * ECDSAP256SHA256, ECDSAP384SHA384, ED25519, ED448, INDIRECT, PRIVATEDNS,
   * PRIVATEOID
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
   * The digest generated from the referenced DNSKEY.
   *
   * @param string $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return string
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * The hash function used to generate the digest of the referenced DNSKEY.
   *
   * Accepted values: DIGEST_TYPE_UNSPECIFIED, SHA1, SHA256, GOST3411, SHA384
   *
   * @param self::DIGEST_TYPE_* $digestType
   */
  public function setDigestType($digestType)
  {
    $this->digestType = $digestType;
  }
  /**
   * @return self::DIGEST_TYPE_*
   */
  public function getDigestType()
  {
    return $this->digestType;
  }
  /**
   * The key tag of the record. Must be set in range 0 -- 65535.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DsRecord::class, 'Google_Service_CloudDomains_DsRecord');
