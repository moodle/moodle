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

namespace Google\Service\CloudKMS;

class KeyHandle extends \Google\Model
{
  /**
   * Output only. Name of a CryptoKey that has been provisioned for Customer
   * Managed Encryption Key (CMEK) use in the KeyHandle project and location for
   * the requested resource type. The CryptoKey project will reflect the value
   * configured in the AutokeyConfig on the resource project's ancestor folder
   * at the time of the KeyHandle creation. If more than one ancestor folder has
   * a configured AutokeyConfig, the nearest of these configurations is used.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Identifier. Name of the KeyHandle resource, e.g.
   * `projects/{PROJECT_ID}/locations/{LOCATION}/keyHandles/{KEY_HANDLE_ID}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. Indicates the resource type that the resulting CryptoKey is meant
   * to protect, e.g. `{SERVICE}.googleapis.com/{TYPE}`. See documentation for
   * supported resource types.
   *
   * @var string
   */
  public $resourceTypeSelector;

  /**
   * Output only. Name of a CryptoKey that has been provisioned for Customer
   * Managed Encryption Key (CMEK) use in the KeyHandle project and location for
   * the requested resource type. The CryptoKey project will reflect the value
   * configured in the AutokeyConfig on the resource project's ancestor folder
   * at the time of the KeyHandle creation. If more than one ancestor folder has
   * a configured AutokeyConfig, the nearest of these configurations is used.
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
   * Identifier. Name of the KeyHandle resource, e.g.
   * `projects/{PROJECT_ID}/locations/{LOCATION}/keyHandles/{KEY_HANDLE_ID}`.
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
  /**
   * Required. Indicates the resource type that the resulting CryptoKey is meant
   * to protect, e.g. `{SERVICE}.googleapis.com/{TYPE}`. See documentation for
   * supported resource types.
   *
   * @param string $resourceTypeSelector
   */
  public function setResourceTypeSelector($resourceTypeSelector)
  {
    $this->resourceTypeSelector = $resourceTypeSelector;
  }
  /**
   * @return string
   */
  public function getResourceTypeSelector()
  {
    return $this->resourceTypeSelector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KeyHandle::class, 'Google_Service_CloudKMS_KeyHandle');
