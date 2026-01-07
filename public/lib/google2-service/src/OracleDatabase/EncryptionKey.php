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

namespace Google\Service\OracleDatabase;

class EncryptionKey extends \Google\Model
{
  /**
   * Default unspecified value.
   */
  public const PROVIDER_PROVIDER_UNSPECIFIED = 'PROVIDER_UNSPECIFIED';
  /**
   * Google Managed KMS key, if selected, please provide the KMS key name.
   */
  public const PROVIDER_GOOGLE_MANAGED = 'GOOGLE_MANAGED';
  /**
   * Oracle Managed.
   */
  public const PROVIDER_ORACLE_MANAGED = 'ORACLE_MANAGED';
  /**
   * Optional. The KMS key used to encrypt the Autonomous Database. This field
   * is required if the provider is GOOGLE_MANAGED. The name of the KMS key
   * resource in the following format: `projects/{project}/locations/{location}/
   * keyRings/{key_ring}/cryptoKeys/{crypto_key}`.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. The provider of the encryption key.
   *
   * @var string
   */
  public $provider;

  /**
   * Optional. The KMS key used to encrypt the Autonomous Database. This field
   * is required if the provider is GOOGLE_MANAGED. The name of the KMS key
   * resource in the following format: `projects/{project}/locations/{location}/
   * keyRings/{key_ring}/cryptoKeys/{crypto_key}`.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. The provider of the encryption key.
   *
   * Accepted values: PROVIDER_UNSPECIFIED, GOOGLE_MANAGED, ORACLE_MANAGED
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionKey::class, 'Google_Service_OracleDatabase_EncryptionKey');
