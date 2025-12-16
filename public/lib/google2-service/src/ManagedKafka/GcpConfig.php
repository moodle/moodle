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

namespace Google\Service\ManagedKafka;

class GcpConfig extends \Google\Model
{
  protected $accessConfigType = AccessConfig::class;
  protected $accessConfigDataType = '';
  /**
   * Optional. Immutable. The Cloud KMS Key name to use for encryption. The key
   * must be located in the same region as the cluster and cannot be changed.
   * Structured like: projects/{project}/locations/{location}/keyRings/{key_ring
   * }/cryptoKeys/{crypto_key}.
   *
   * @var string
   */
  public $kmsKey;

  /**
   * Required. Access configuration for the Kafka cluster.
   *
   * @param AccessConfig $accessConfig
   */
  public function setAccessConfig(AccessConfig $accessConfig)
  {
    $this->accessConfig = $accessConfig;
  }
  /**
   * @return AccessConfig
   */
  public function getAccessConfig()
  {
    return $this->accessConfig;
  }
  /**
   * Optional. Immutable. The Cloud KMS Key name to use for encryption. The key
   * must be located in the same region as the cluster and cannot be changed.
   * Structured like: projects/{project}/locations/{location}/keyRings/{key_ring
   * }/cryptoKeys/{crypto_key}.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcpConfig::class, 'Google_Service_ManagedKafka_GcpConfig');
