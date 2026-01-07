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

namespace Google\Service\CertificateAuthorityService;

class EcKeyType extends \Google\Model
{
  /**
   * Not specified. Signifies that any signature algorithm may be used.
   */
  public const SIGNATURE_ALGORITHM_EC_SIGNATURE_ALGORITHM_UNSPECIFIED = 'EC_SIGNATURE_ALGORITHM_UNSPECIFIED';
  /**
   * Refers to the Elliptic Curve Digital Signature Algorithm over the NIST
   * P-256 curve.
   */
  public const SIGNATURE_ALGORITHM_ECDSA_P256 = 'ECDSA_P256';
  /**
   * Refers to the Elliptic Curve Digital Signature Algorithm over the NIST
   * P-384 curve.
   */
  public const SIGNATURE_ALGORITHM_ECDSA_P384 = 'ECDSA_P384';
  /**
   * Refers to the Edwards-curve Digital Signature Algorithm over curve 25519,
   * as described in RFC 8410.
   */
  public const SIGNATURE_ALGORITHM_EDDSA_25519 = 'EDDSA_25519';
  /**
   * Optional. A signature algorithm that must be used. If this is omitted, any
   * EC-based signature algorithm will be allowed.
   *
   * @var string
   */
  public $signatureAlgorithm;

  /**
   * Optional. A signature algorithm that must be used. If this is omitted, any
   * EC-based signature algorithm will be allowed.
   *
   * Accepted values: EC_SIGNATURE_ALGORITHM_UNSPECIFIED, ECDSA_P256,
   * ECDSA_P384, EDDSA_25519
   *
   * @param self::SIGNATURE_ALGORITHM_* $signatureAlgorithm
   */
  public function setSignatureAlgorithm($signatureAlgorithm)
  {
    $this->signatureAlgorithm = $signatureAlgorithm;
  }
  /**
   * @return self::SIGNATURE_ALGORITHM_*
   */
  public function getSignatureAlgorithm()
  {
    return $this->signatureAlgorithm;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EcKeyType::class, 'Google_Service_CertificateAuthorityService_EcKeyType');
