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

class AutomatedBackupPolicy extends \Google\Model
{
  /**
   * The length of the time window during which a backup can be taken. If a
   * backup does not succeed within this time window, it will be canceled and
   * considered failed. The backup window must be at least 5 minutes long. There
   * is no upper bound on the window. If not set, it defaults to 1 hour.
   *
   * @var string
   */
  public $backupWindow;
  /**
   * Whether automated automated backups are enabled. If not set, defaults to
   * true.
   *
   * @var bool
   */
  public $enabled;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Labels to apply to backups created using this configuration.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The location where the backup will be stored. Currently, the only supported
   * option is to store the backup in the same region as the cluster. If empty,
   * defaults to the region of the cluster.
   *
   * @var string
   */
  public $location;
  protected $quantityBasedRetentionType = QuantityBasedRetention::class;
  protected $quantityBasedRetentionDataType = '';
  protected $timeBasedRetentionType = TimeBasedRetention::class;
  protected $timeBasedRetentionDataType = '';
  protected $weeklyScheduleType = WeeklySchedule::class;
  protected $weeklyScheduleDataType = '';

  /**
   * The length of the time window during which a backup can be taken. If a
   * backup does not succeed within this time window, it will be canceled and
   * considered failed. The backup window must be at least 5 minutes long. There
   * is no upper bound on the window. If not set, it defaults to 1 hour.
   *
   * @param string $backupWindow
   */
  public function setBackupWindow($backupWindow)
  {
    $this->backupWindow = $backupWindow;
  }
  /**
   * @return string
   */
  public function getBackupWindow()
  {
    return $this->backupWindow;
  }
  /**
   * Whether automated automated backups are enabled. If not set, defaults to
   * true.
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
   * Optional. The encryption config can be specified to encrypt the backups
   * with a customer-managed encryption key (CMEK). When this field is not
   * specified, the backup will use the cluster's encryption config.
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
   * Labels to apply to backups created using this configuration.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The location where the backup will be stored. Currently, the only supported
   * option is to store the backup in the same region as the cluster. If empty,
   * defaults to the region of the cluster.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Quantity-based Backup retention policy to retain recent backups.
   *
   * @param QuantityBasedRetention $quantityBasedRetention
   */
  public function setQuantityBasedRetention(QuantityBasedRetention $quantityBasedRetention)
  {
    $this->quantityBasedRetention = $quantityBasedRetention;
  }
  /**
   * @return QuantityBasedRetention
   */
  public function getQuantityBasedRetention()
  {
    return $this->quantityBasedRetention;
  }
  /**
   * Time-based Backup retention policy.
   *
   * @param TimeBasedRetention $timeBasedRetention
   */
  public function setTimeBasedRetention(TimeBasedRetention $timeBasedRetention)
  {
    $this->timeBasedRetention = $timeBasedRetention;
  }
  /**
   * @return TimeBasedRetention
   */
  public function getTimeBasedRetention()
  {
    return $this->timeBasedRetention;
  }
  /**
   * Weekly schedule for the Backup.
   *
   * @param WeeklySchedule $weeklySchedule
   */
  public function setWeeklySchedule(WeeklySchedule $weeklySchedule)
  {
    $this->weeklySchedule = $weeklySchedule;
  }
  /**
   * @return WeeklySchedule
   */
  public function getWeeklySchedule()
  {
    return $this->weeklySchedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutomatedBackupPolicy::class, 'Google_Service_CloudAlloyDBAdmin_AutomatedBackupPolicy');
