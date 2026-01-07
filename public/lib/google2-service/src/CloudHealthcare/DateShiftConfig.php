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

namespace Google\Service\CloudHealthcare;

class DateShiftConfig extends \Google\Model
{
  /**
   * An AES 128/192/256 bit key. The date shift is computed based on this key
   * and the patient ID. If the patient ID is empty for a DICOM resource, the
   * date shift is computed based on this key and the study instance UID. If
   * `crypto_key` is not set, then `kms_wrapped` is used to calculate the date
   * shift. If neither is set, a default key is generated for each de-identify
   * operation. Must not be set if `kms_wrapped` is set.
   *
   * @var string
   */
  public $cryptoKey;
  protected $kmsWrappedType = KmsWrappedCryptoKey::class;
  protected $kmsWrappedDataType = '';

  /**
   * An AES 128/192/256 bit key. The date shift is computed based on this key
   * and the patient ID. If the patient ID is empty for a DICOM resource, the
   * date shift is computed based on this key and the study instance UID. If
   * `crypto_key` is not set, then `kms_wrapped` is used to calculate the date
   * shift. If neither is set, a default key is generated for each de-identify
   * operation. Must not be set if `kms_wrapped` is set.
   *
   * @param string $cryptoKey
   */
  public function setCryptoKey($cryptoKey)
  {
    $this->cryptoKey = $cryptoKey;
  }
  /**
   * @return string
   */
  public function getCryptoKey()
  {
    return $this->cryptoKey;
  }
  /**
   * KMS wrapped key. If `kms_wrapped` is not set, then `crypto_key` is used to
   * calculate the date shift. If neither is set, a default key is generated for
   * each de-identify operation. Must not be set if `crypto_key` is set.
   *
   * @param KmsWrappedCryptoKey $kmsWrapped
   */
  public function setKmsWrapped(KmsWrappedCryptoKey $kmsWrapped)
  {
    $this->kmsWrapped = $kmsWrapped;
  }
  /**
   * @return KmsWrappedCryptoKey
   */
  public function getKmsWrapped()
  {
    return $this->kmsWrapped;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DateShiftConfig::class, 'Google_Service_CloudHealthcare_DateShiftConfig');
