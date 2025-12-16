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

namespace Google\Service\Transcoder;

class Encryption extends \Google\Model
{
  protected $aes128Type = Aes128Encryption::class;
  protected $aes128DataType = '';
  protected $drmSystemsType = DrmSystems::class;
  protected $drmSystemsDataType = '';
  /**
   * Required. Identifier for this set of encryption options.
   *
   * @var string
   */
  public $id;
  protected $mpegCencType = MpegCommonEncryption::class;
  protected $mpegCencDataType = '';
  protected $sampleAesType = SampleAesEncryption::class;
  protected $sampleAesDataType = '';
  protected $secretManagerKeySourceType = SecretManagerSource::class;
  protected $secretManagerKeySourceDataType = '';

  /**
   * Configuration for AES-128 encryption.
   *
   * @param Aes128Encryption $aes128
   */
  public function setAes128(Aes128Encryption $aes128)
  {
    $this->aes128 = $aes128;
  }
  /**
   * @return Aes128Encryption
   */
  public function getAes128()
  {
    return $this->aes128;
  }
  /**
   * Required. DRM system(s) to use; at least one must be specified. If a DRM
   * system is omitted, it is considered disabled.
   *
   * @param DrmSystems $drmSystems
   */
  public function setDrmSystems(DrmSystems $drmSystems)
  {
    $this->drmSystems = $drmSystems;
  }
  /**
   * @return DrmSystems
   */
  public function getDrmSystems()
  {
    return $this->drmSystems;
  }
  /**
   * Required. Identifier for this set of encryption options.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Configuration for MPEG Common Encryption (MPEG-CENC).
   *
   * @param MpegCommonEncryption $mpegCenc
   */
  public function setMpegCenc(MpegCommonEncryption $mpegCenc)
  {
    $this->mpegCenc = $mpegCenc;
  }
  /**
   * @return MpegCommonEncryption
   */
  public function getMpegCenc()
  {
    return $this->mpegCenc;
  }
  /**
   * Configuration for SAMPLE-AES encryption.
   *
   * @param SampleAesEncryption $sampleAes
   */
  public function setSampleAes(SampleAesEncryption $sampleAes)
  {
    $this->sampleAes = $sampleAes;
  }
  /**
   * @return SampleAesEncryption
   */
  public function getSampleAes()
  {
    return $this->sampleAes;
  }
  /**
   * Keys are stored in Google Secret Manager.
   *
   * @param SecretManagerSource $secretManagerKeySource
   */
  public function setSecretManagerKeySource(SecretManagerSource $secretManagerKeySource)
  {
    $this->secretManagerKeySource = $secretManagerKeySource;
  }
  /**
   * @return SecretManagerSource
   */
  public function getSecretManagerKeySource()
  {
    return $this->secretManagerKeySource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Encryption::class, 'Google_Service_Transcoder_Encryption');
