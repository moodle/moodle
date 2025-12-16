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

namespace Google\Service\IAMCredentials;

class SignJwtResponse extends \Google\Model
{
  /**
   * The ID of the key used to sign the JWT. The key used for signing will
   * remain valid for at least 12 hours after the JWT is signed. To verify the
   * signature, you can retrieve the public key in several formats from the
   * following endpoints: - RSA public key wrapped in an X.509 v3 certificate: `
   * https://www.googleapis.com/service_accounts/v1/metadata/x509/{ACCOUNT_EMAIL
   * }` - Raw key in JSON format: `https://www.googleapis.com/service_accounts/v
   * 1/metadata/raw/{ACCOUNT_EMAIL}` - JSON Web Key (JWK): `https://www.googleap
   * is.com/service_accounts/v1/metadata/jwk/{ACCOUNT_EMAIL}`
   *
   * @var string
   */
  public $keyId;
  /**
   * The signed JWT. Contains the automatically generated header; the client-
   * supplied payload; and the signature, which is generated using the key
   * referenced by the `kid` field in the header. After the key pair referenced
   * by the `key_id` response field expires, Google no longer exposes the public
   * key that can be used to verify the JWT. As a result, the receiver can no
   * longer verify the signature.
   *
   * @var string
   */
  public $signedJwt;

  /**
   * The ID of the key used to sign the JWT. The key used for signing will
   * remain valid for at least 12 hours after the JWT is signed. To verify the
   * signature, you can retrieve the public key in several formats from the
   * following endpoints: - RSA public key wrapped in an X.509 v3 certificate: `
   * https://www.googleapis.com/service_accounts/v1/metadata/x509/{ACCOUNT_EMAIL
   * }` - Raw key in JSON format: `https://www.googleapis.com/service_accounts/v
   * 1/metadata/raw/{ACCOUNT_EMAIL}` - JSON Web Key (JWK): `https://www.googleap
   * is.com/service_accounts/v1/metadata/jwk/{ACCOUNT_EMAIL}`
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
   * The signed JWT. Contains the automatically generated header; the client-
   * supplied payload; and the signature, which is generated using the key
   * referenced by the `kid` field in the header. After the key pair referenced
   * by the `key_id` response field expires, Google no longer exposes the public
   * key that can be used to verify the JWT. As a result, the receiver can no
   * longer verify the signature.
   *
   * @param string $signedJwt
   */
  public function setSignedJwt($signedJwt)
  {
    $this->signedJwt = $signedJwt;
  }
  /**
   * @return string
   */
  public function getSignedJwt()
  {
    return $this->signedJwt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignJwtResponse::class, 'Google_Service_IAMCredentials_SignJwtResponse');
