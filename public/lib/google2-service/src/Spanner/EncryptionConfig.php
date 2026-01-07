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

namespace Google\Service\Spanner;

class EncryptionConfig extends \Google\Collection
{
  protected $collection_key = 'kmsKeyNames';
  /**
   * The Cloud KMS key to be used for encrypting and decrypting the database.
   * Values are of the form `projects//locations//keyRings//cryptoKeys/`.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Specifies the KMS configuration for one or more keys used to encrypt the
   * database. Values are of the form
   * `projects//locations//keyRings//cryptoKeys/`. The keys referenced by
   * `kms_key_names` must fully cover all regions of the database's instance
   * configuration. Some examples: * For regional (single-region) instance
   * configurations, specify a regional location KMS key. * For multi-region
   * instance configurations of type `GOOGLE_MANAGED`, either specify a multi-
   * region location KMS key or multiple regional location KMS keys that cover
   * all regions in the instance configuration. * For an instance configuration
   * of type `USER_MANAGED`, specify only regional location KMS keys to cover
   * each region in the instance configuration. Multi-region location KMS keys
   * aren't supported for `USER_MANAGED` type instance configurations.
   *
   * @var string[]
   */
  public $kmsKeyNames;

  /**
   * The Cloud KMS key to be used for encrypting and decrypting the database.
   * Values are of the form `projects//locations//keyRings//cryptoKeys/`.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Specifies the KMS configuration for one or more keys used to encrypt the
   * database. Values are of the form
   * `projects//locations//keyRings//cryptoKeys/`. The keys referenced by
   * `kms_key_names` must fully cover all regions of the database's instance
   * configuration. Some examples: * For regional (single-region) instance
   * configurations, specify a regional location KMS key. * For multi-region
   * instance configurations of type `GOOGLE_MANAGED`, either specify a multi-
   * region location KMS key or multiple regional location KMS keys that cover
   * all regions in the instance configuration. * For an instance configuration
   * of type `USER_MANAGED`, specify only regional location KMS keys to cover
   * each region in the instance configuration. Multi-region location KMS keys
   * aren't supported for `USER_MANAGED` type instance configurations.
   *
   * @param string[] $kmsKeyNames
   */
  public function setKmsKeyNames($kmsKeyNames)
  {
    $this->kmsKeyNames = $kmsKeyNames;
  }
  /**
   * @return string[]
   */
  public function getKmsKeyNames()
  {
    return $this->kmsKeyNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionConfig::class, 'Google_Service_Spanner_EncryptionConfig');
