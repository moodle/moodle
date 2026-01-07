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

namespace Google\Service\Kmsinventory;

class GoogleCloudKmsV1ExternalProtectionLevelOptions extends \Google\Model
{
  /**
   * The path to the external key material on the EKM when using EkmConnection
   * e.g., "v0/my/key". Set this field instead of external_key_uri when using an
   * EkmConnection.
   *
   * @var string
   */
  public $ekmConnectionKeyPath;
  /**
   * The URI for an external resource that this CryptoKeyVersion represents.
   *
   * @var string
   */
  public $externalKeyUri;

  /**
   * The path to the external key material on the EKM when using EkmConnection
   * e.g., "v0/my/key". Set this field instead of external_key_uri when using an
   * EkmConnection.
   *
   * @param string $ekmConnectionKeyPath
   */
  public function setEkmConnectionKeyPath($ekmConnectionKeyPath)
  {
    $this->ekmConnectionKeyPath = $ekmConnectionKeyPath;
  }
  /**
   * @return string
   */
  public function getEkmConnectionKeyPath()
  {
    return $this->ekmConnectionKeyPath;
  }
  /**
   * The URI for an external resource that this CryptoKeyVersion represents.
   *
   * @param string $externalKeyUri
   */
  public function setExternalKeyUri($externalKeyUri)
  {
    $this->externalKeyUri = $externalKeyUri;
  }
  /**
   * @return string
   */
  public function getExternalKeyUri()
  {
    return $this->externalKeyUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudKmsV1ExternalProtectionLevelOptions::class, 'Google_Service_Kmsinventory_GoogleCloudKmsV1ExternalProtectionLevelOptions');
