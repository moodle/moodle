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

class SignBlobResponse extends \Google\Model
{
  /**
   * The ID of the key used to sign the blob. The key used for signing will
   * remain valid for at least 12 hours after the blob is signed. To verify the
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
   * The signature for the blob. Does not include the original blob. After the
   * key pair referenced by the `key_id` response field expires, Google no
   * longer exposes the public key that can be used to verify the blob. As a
   * result, the receiver can no longer verify the signature.
   *
   * @var string
   */
  public $signedBlob;

  /**
   * The ID of the key used to sign the blob. The key used for signing will
   * remain valid for at least 12 hours after the blob is signed. To verify the
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
   * The signature for the blob. Does not include the original blob. After the
   * key pair referenced by the `key_id` response field expires, Google no
   * longer exposes the public key that can be used to verify the blob. As a
   * result, the receiver can no longer verify the signature.
   *
   * @param string $signedBlob
   */
  public function setSignedBlob($signedBlob)
  {
    $this->signedBlob = $signedBlob;
  }
  /**
   * @return string
   */
  public function getSignedBlob()
  {
    return $this->signedBlob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SignBlobResponse::class, 'Google_Service_IAMCredentials_SignBlobResponse');
