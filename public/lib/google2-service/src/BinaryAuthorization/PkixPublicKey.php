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

namespace Google\Service\BinaryAuthorization;

class PkixPublicKey extends \Google\Model
{
  /**
   * Not specified.
   */
  public const SIGNATURE_ALGORITHM_SIGNATURE_ALGORITHM_UNSPECIFIED = 'SIGNATURE_ALGORITHM_UNSPECIFIED';
  /**
   * RSASSA-PSS 2048 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_PSS_2048_SHA256 = 'RSA_PSS_2048_SHA256';
  /**
   * RSASSA-PSS 2048 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PSS_2048_SHA256 = 'RSA_SIGN_PSS_2048_SHA256';
  /**
   * RSASSA-PSS 3072 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_PSS_3072_SHA256 = 'RSA_PSS_3072_SHA256';
  /**
   * RSASSA-PSS 3072 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PSS_3072_SHA256 = 'RSA_SIGN_PSS_3072_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_PSS_4096_SHA256 = 'RSA_PSS_4096_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PSS_4096_SHA256 = 'RSA_SIGN_PSS_4096_SHA256';
  /**
   * RSASSA-PSS 4096 bit key with a SHA512 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_PSS_4096_SHA512 = 'RSA_PSS_4096_SHA512';
  /**
   * RSASSA-PSS 4096 bit key with a SHA512 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PSS_4096_SHA512 = 'RSA_SIGN_PSS_4096_SHA512';
  /**
   * RSASSA-PKCS1-v1_5 with a 2048 bit key and a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PKCS1_2048_SHA256 = 'RSA_SIGN_PKCS1_2048_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 3072 bit key and a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PKCS1_3072_SHA256 = 'RSA_SIGN_PKCS1_3072_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PKCS1_4096_SHA256 = 'RSA_SIGN_PKCS1_4096_SHA256';
  /**
   * RSASSA-PKCS1-v1_5 with a 4096 bit key and a SHA512 digest.
   */
  public const SIGNATURE_ALGORITHM_RSA_SIGN_PKCS1_4096_SHA512 = 'RSA_SIGN_PKCS1_4096_SHA512';
  /**
   * ECDSA on the NIST P-256 curve with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_ECDSA_P256_SHA256 = 'ECDSA_P256_SHA256';
  /**
   * ECDSA on the NIST P-256 curve with a SHA256 digest.
   */
  public const SIGNATURE_ALGORITHM_EC_SIGN_P256_SHA256 = 'EC_SIGN_P256_SHA256';
  /**
   * ECDSA on the NIST P-384 curve with a SHA384 digest.
   */
  public const SIGNATURE_ALGORITHM_ECDSA_P384_SHA384 = 'ECDSA_P384_SHA384';
  /**
   * ECDSA on the NIST P-384 curve with a SHA384 digest.
   */
  public const SIGNATURE_ALGORITHM_EC_SIGN_P384_SHA384 = 'EC_SIGN_P384_SHA384';
  /**
   * ECDSA on the NIST P-521 curve with a SHA512 digest.
   */
  public const SIGNATURE_ALGORITHM_ECDSA_P521_SHA512 = 'ECDSA_P521_SHA512';
  /**
   * ECDSA on the NIST P-521 curve with a SHA512 digest.
   */
  public const SIGNATURE_ALGORITHM_EC_SIGN_P521_SHA512 = 'EC_SIGN_P521_SHA512';
  /**
   * Optional. The ID of this public key. Signatures verified by Binary
   * Authorization must include the ID of the public key that can be used to
   * verify them. The ID must match exactly contents of the `key_id` field
   * exactly. The ID may be explicitly provided by the caller, but it MUST be a
   * valid RFC3986 URI. If `key_id` is left blank and this `PkixPublicKey` is
   * not used in the context of a wrapper (see next paragraph), a default key ID
   * will be computed based on the digest of the DER encoding of the public key.
   * If this `PkixPublicKey` is used in the context of a wrapper that has its
   * own notion of key ID (e.g. `AttestorPublicKey`), then this field can either
   * match that value exactly, or be left blank, in which case it behaves
   * exactly as though it is equal to that wrapper value.
   *
   * @var string
   */
  public $keyId;
  /**
   * A PEM-encoded public key, as described in
   * https://tools.ietf.org/html/rfc7468#section-13
   *
   * @var string
   */
  public $publicKeyPem;
  /**
   * The signature algorithm used to verify a message against a signature using
   * this key. These signature algorithm must match the structure and any object
   * identifiers encoded in `public_key_pem` (i.e. this algorithm must match
   * that of the public key).
   *
   * @var string
   */
  public $signatureAlgorithm;

  /**
   * Optional. The ID of this public key. Signatures verified by Binary
   * Authorization must include the ID of the public key that can be used to
   * verify them. The ID must match exactly contents of the `key_id` field
   * exactly. The ID may be explicitly provided by the caller, but it MUST be a
   * valid RFC3986 URI. If `key_id` is left blank and this `PkixPublicKey` is
   * not used in the context of a wrapper (see next paragraph), a default key ID
   * will be computed based on the digest of the DER encoding of the public key.
   * If this `PkixPublicKey` is used in the context of a wrapper that has its
   * own notion of key ID (e.g. `AttestorPublicKey`), then this field can either
   * match that value exactly, or be left blank, in which case it behaves
   * exactly as though it is equal to that wrapper value.
   *
   * @param string $keyId
   */
  public function setKeyId($keyId)
  {
    $this->keyId = $keyId;
  }
  /**
   * @return string
   */
  public function getKeyId()
  {
    return $this->keyId;
  }
  /**
   * A PEM-encoded public key, as described in
   * https://tools.ietf.org/html/rfc7468#section-13
   *
   * @param string $publicKeyPem
   */
  public function setPublicKeyPem($publicKeyPem)
  {
    $this->publicKeyPem = $publicKeyPem;
  }
  /**
   * @return string
   */
  public function getPublicKeyPem()
  {
    return $this->publicKeyPem;
  }
  /**
   * The signature algorithm used to verify a message against a signature using
   * this key. These signature algorithm must match the structure and any object
   * identifiers encoded in `public_key_pem` (i.e. this algorithm must match
   * that of the public key).
   *
   * Accepted values: SIGNATURE_ALGORITHM_UNSPECIFIED, RSA_PSS_2048_SHA256,
   * RSA_SIGN_PSS_2048_SHA256, RSA_PSS_3072_SHA256, RSA_SIGN_PSS_3072_SHA256,
   * RSA_PSS_4096_SHA256, RSA_SIGN_PSS_4096_SHA256, RSA_PSS_4096_SHA512,
   * RSA_SIGN_PSS_4096_SHA512, RSA_SIGN_PKCS1_2048_SHA256,
   * RSA_SIGN_PKCS1_3072_SHA256, RSA_SIGN_PKCS1_4096_SHA256,
   * RSA_SIGN_PKCS1_4096_SHA512, ECDSA_P256_SHA256, EC_SIGN_P256_SHA256,
   * ECDSA_P384_SHA384, EC_SIGN_P384_SHA384, ECDSA_P521_SHA512,
   * EC_SIGN_P521_SHA512
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
class_alias(PkixPublicKey::class, 'Google_Service_BinaryAuthorization_PkixPublicKey');
