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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1IdentityMappingStore extends \Google\Model
{
  protected $cmekConfigType = GoogleCloudDiscoveryengineV1CmekConfig::class;
  protected $cmekConfigDataType = '';
  /**
   * Input only. The KMS key to be used to protect this Identity Mapping Store
   * at creation time. Must be set for requests that need to comply with CMEK
   * Org Policy protections. If this field is set and processed successfully,
   * the Identity Mapping Store will be protected by the KMS key, as indicated
   * in the cmek_config field.
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Immutable. The full resource name of the identity mapping store. Format: `p
   * rojects/{project}/locations/{location}/identityMappingStores/{identity_mapp
   * ing_store}`. This field must be a UTF-8 encoded string with a length limit
   * of 1024 characters.
   *
   * @var string
   */
  public $name;

  /**
   * Output only. CMEK-related information for the Identity Mapping Store.
   *
   * @param GoogleCloudDiscoveryengineV1CmekConfig $cmekConfig
   */
  public function setCmekConfig(GoogleCloudDiscoveryengineV1CmekConfig $cmekConfig)
  {
    $this->cmekConfig = $cmekConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CmekConfig
   */
  public function getCmekConfig()
  {
    return $this->cmekConfig;
  }
  /**
   * Input only. The KMS key to be used to protect this Identity Mapping Store
   * at creation time. Must be set for requests that need to comply with CMEK
   * Org Policy protections. If this field is set and processed successfully,
   * the Identity Mapping Store will be protected by the KMS key, as indicated
   * in the cmek_config field.
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
   * Immutable. The full resource name of the identity mapping store. Format: `p
   * rojects/{project}/locations/{location}/identityMappingStores/{identity_mapp
   * ing_store}`. This field must be a UTF-8 encoded string with a length limit
   * of 1024 characters.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1IdentityMappingStore::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1IdentityMappingStore');
