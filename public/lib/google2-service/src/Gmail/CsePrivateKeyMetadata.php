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

namespace Google\Service\Gmail;

class CsePrivateKeyMetadata extends \Google\Model
{
  protected $hardwareKeyMetadataType = HardwareKeyMetadata::class;
  protected $hardwareKeyMetadataDataType = '';
  protected $kaclsKeyMetadataType = KaclsKeyMetadata::class;
  protected $kaclsKeyMetadataDataType = '';
  /**
   * Output only. The immutable ID for the private key metadata instance.
   *
   * @var string
   */
  public $privateKeyMetadataId;

  /**
   * Metadata for hardware keys.
   *
   * @param HardwareKeyMetadata $hardwareKeyMetadata
   */
  public function setHardwareKeyMetadata(HardwareKeyMetadata $hardwareKeyMetadata)
  {
    $this->hardwareKeyMetadata = $hardwareKeyMetadata;
  }
  /**
   * @return HardwareKeyMetadata
   */
  public function getHardwareKeyMetadata()
  {
    return $this->hardwareKeyMetadata;
  }
  /**
   * Metadata for a private key instance managed by an external key access
   * control list service.
   *
   * @param KaclsKeyMetadata $kaclsKeyMetadata
   */
  public function setKaclsKeyMetadata(KaclsKeyMetadata $kaclsKeyMetadata)
  {
    $this->kaclsKeyMetadata = $kaclsKeyMetadata;
  }
  /**
   * @return KaclsKeyMetadata
   */
  public function getKaclsKeyMetadata()
  {
    return $this->kaclsKeyMetadata;
  }
  /**
   * Output only. The immutable ID for the private key metadata instance.
   *
   * @param string $privateKeyMetadataId
   */
  public function setPrivateKeyMetadataId($privateKeyMetadataId)
  {
    $this->privateKeyMetadataId = $privateKeyMetadataId;
  }
  /**
   * @return string
   */
  public function getPrivateKeyMetadataId()
  {
    return $this->privateKeyMetadataId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CsePrivateKeyMetadata::class, 'Google_Service_Gmail_CsePrivateKeyMetadata');
