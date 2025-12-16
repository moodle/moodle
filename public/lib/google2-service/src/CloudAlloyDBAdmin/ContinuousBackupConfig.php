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

namespace Google\Service\CloudAlloyDBAdmin;

class ContinuousBackupConfig extends \Google\Model
{
  /**
   * Whether ContinuousBackup is enabled.
   *
   * @var bool
   */
  public $enabled;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * The number of days that are eligible to restore from using PITR. To support
   * the entire recovery window, backups and logs are retained for one day more
   * than the recovery window. If not set, defaults to 14 days.
   *
   * @var int
   */
  public $recoveryWindowDays;

  /**
   * Whether ContinuousBackup is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * The encryption config can be specified to encrypt the backups with a
   * customer-managed encryption key (CMEK). When this field is not specified,
   * the backup will use the cluster's encryption config.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * The number of days that are eligible to restore from using PITR. To support
   * the entire recovery window, backups and logs are retained for one day more
   * than the recovery window. If not set, defaults to 14 days.
   *
   * @param int $recoveryWindowDays
   */
  public function setRecoveryWindowDays($recoveryWindowDays)
  {
    $this->recoveryWindowDays = $recoveryWindowDays;
  }
  /**
   * @return int
   */
  public function getRecoveryWindowDays()
  {
    return $this->recoveryWindowDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContinuousBackupConfig::class, 'Google_Service_CloudAlloyDBAdmin_ContinuousBackupConfig');
