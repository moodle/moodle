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

namespace Google\Service\Looker;

class EncryptionConfig extends \Google\Model
{
  /**
   * CMEK status not specified.
   */
  public const KMS_KEY_STATE_KMS_KEY_STATE_UNSPECIFIED = 'KMS_KEY_STATE_UNSPECIFIED';
  /**
   * CMEK key is currently valid.
   */
  public const KMS_KEY_STATE_VALID = 'VALID';
  /**
   * CMEK key is currently revoked (instance should in restricted mode).
   */
  public const KMS_KEY_STATE_REVOKED = 'REVOKED';
  /**
   * Name of the CMEK key in KMS (input parameter).
   *
   * @var string
   */
  public $kmsKeyName;
  /**
   * Output only. Full name and version of the CMEK key currently in use to
   * encrypt Looker data. Format: `projects/{project}/locations/{location}/keyRi
   * ngs/{ring}/cryptoKeys/{key}/cryptoKeyVersions/{version}`. Empty if CMEK is
   * not configured in this instance.
   *
   * @var string
   */
  public $kmsKeyNameVersion;
  /**
   * Output only. Status of the CMEK key.
   *
   * @var string
   */
  public $kmsKeyState;

  /**
   * Name of the CMEK key in KMS (input parameter).
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
   * Output only. Full name and version of the CMEK key currently in use to
   * encrypt Looker data. Format: `projects/{project}/locations/{location}/keyRi
   * ngs/{ring}/cryptoKeys/{key}/cryptoKeyVersions/{version}`. Empty if CMEK is
   * not configured in this instance.
   *
   * @param string $kmsKeyNameVersion
   */
  public function setKmsKeyNameVersion($kmsKeyNameVersion)
  {
    $this->kmsKeyNameVersion = $kmsKeyNameVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyNameVersion()
  {
    return $this->kmsKeyNameVersion;
  }
  /**
   * Output only. Status of the CMEK key.
   *
   * Accepted values: KMS_KEY_STATE_UNSPECIFIED, VALID, REVOKED
   *
   * @param self::KMS_KEY_STATE_* $kmsKeyState
   */
  public function setKmsKeyState($kmsKeyState)
  {
    $this->kmsKeyState = $kmsKeyState;
  }
  /**
   * @return self::KMS_KEY_STATE_*
   */
  public function getKmsKeyState()
  {
    return $this->kmsKeyState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EncryptionConfig::class, 'Google_Service_Looker_EncryptionConfig');
