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

namespace Google\Service\Baremetalsolution;

class ReimageInstanceRequest extends \Google\Collection
{
  protected $collection_key = 'sshKeys';
  /**
   * Optional. Name of the KMS crypto key version used to encrypt the initial
   * passwords. The key has to have ASYMMETRIC_DECRYPT purpose. Format is `proje
   * cts/{project}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}/cryp
   * toKeyVersions/{version}`.
   *
   * @var string
   */
  public $kmsKeyVersion;
  /**
   * Required. The OS image code of the image which will be used in the reimage
   * operation.
   *
   * @var string
   */
  public $osImage;
  /**
   * Optional. List of SSH Keys used during reimaging an instance.
   *
   * @var string[]
   */
  public $sshKeys;

  /**
   * Optional. Name of the KMS crypto key version used to encrypt the initial
   * passwords. The key has to have ASYMMETRIC_DECRYPT purpose. Format is `proje
   * cts/{project}/locations/{location}/keyRings/{keyring}/cryptoKeys/{key}/cryp
   * toKeyVersions/{version}`.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
  /**
   * Required. The OS image code of the image which will be used in the reimage
   * operation.
   *
   * @param string $osImage
   */
  public function setOsImage($osImage)
  {
    $this->osImage = $osImage;
  }
  /**
   * @return string
   */
  public function getOsImage()
  {
    return $this->osImage;
  }
  /**
   * Optional. List of SSH Keys used during reimaging an instance.
   *
   * @param string[] $sshKeys
   */
  public function setSshKeys($sshKeys)
  {
    $this->sshKeys = $sshKeys;
  }
  /**
   * @return string[]
   */
  public function getSshKeys()
  {
    return $this->sshKeys;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReimageInstanceRequest::class, 'Google_Service_Baremetalsolution_ReimageInstanceRequest');
