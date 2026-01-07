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

namespace Google\Service\OracleDatabase;

class DbBackupConfig extends \Google\Collection
{
  /**
   * The day of the week is unspecified.
   */
  public const AUTO_FULL_BACKUP_DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const AUTO_FULL_BACKUP_DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const AUTO_FULL_BACKUP_DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const AUTO_FULL_BACKUP_DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const AUTO_FULL_BACKUP_DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const AUTO_FULL_BACKUP_DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const AUTO_FULL_BACKUP_DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const AUTO_FULL_BACKUP_DAY_SUNDAY = 'SUNDAY';
  /**
   * Default unspecified value.
   */
  public const AUTO_FULL_BACKUP_WINDOW_BACKUP_WINDOW_UNSPECIFIED = 'BACKUP_WINDOW_UNSPECIFIED';
  /**
   * 12:00 AM - 2:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_ONE = 'SLOT_ONE';
  /**
   * 2:00 AM - 4:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_TWO = 'SLOT_TWO';
  /**
   * 4:00 AM - 6:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_THREE = 'SLOT_THREE';
  /**
   * 6:00 AM - 8:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_FOUR = 'SLOT_FOUR';
  /**
   * 8:00 AM - 10:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_FIVE = 'SLOT_FIVE';
  /**
   * 10:00 AM - 12:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_SIX = 'SLOT_SIX';
  /**
   * 12:00 PM - 2:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_SEVEN = 'SLOT_SEVEN';
  /**
   * 2:00 PM - 4:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_EIGHT = 'SLOT_EIGHT';
  /**
   * 4:00 PM - 6:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_NINE = 'SLOT_NINE';
  /**
   * 6:00 PM - 8:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_TEN = 'SLOT_TEN';
  /**
   * 8:00 PM - 10:00 PM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_ELEVEN = 'SLOT_ELEVEN';
  /**
   * 10:00 PM - 12:00 AM
   */
  public const AUTO_FULL_BACKUP_WINDOW_SLOT_TWELVE = 'SLOT_TWELVE';
  /**
   * Default unspecified value.
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_BACKUP_WINDOW_UNSPECIFIED = 'BACKUP_WINDOW_UNSPECIFIED';
  /**
   * 12:00 AM - 2:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_ONE = 'SLOT_ONE';
  /**
   * 2:00 AM - 4:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_TWO = 'SLOT_TWO';
  /**
   * 4:00 AM - 6:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_THREE = 'SLOT_THREE';
  /**
   * 6:00 AM - 8:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_FOUR = 'SLOT_FOUR';
  /**
   * 8:00 AM - 10:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_FIVE = 'SLOT_FIVE';
  /**
   * 10:00 AM - 12:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_SIX = 'SLOT_SIX';
  /**
   * 12:00 PM - 2:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_SEVEN = 'SLOT_SEVEN';
  /**
   * 2:00 PM - 4:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_EIGHT = 'SLOT_EIGHT';
  /**
   * 4:00 PM - 6:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_NINE = 'SLOT_NINE';
  /**
   * 6:00 PM - 8:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_TEN = 'SLOT_TEN';
  /**
   * 8:00 PM - 10:00 PM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_ELEVEN = 'SLOT_ELEVEN';
  /**
   * 10:00 PM - 12:00 AM
   */
  public const AUTO_INCREMENTAL_BACKUP_WINDOW_SLOT_TWELVE = 'SLOT_TWELVE';
  /**
   * Default unspecified value.
   */
  public const BACKUP_DELETION_POLICY_BACKUP_DELETION_POLICY_UNSPECIFIED = 'BACKUP_DELETION_POLICY_UNSPECIFIED';
  /**
   * Keeps the backup for predefined time i.e. 72 hours and then delete
   * permanently.
   */
  public const BACKUP_DELETION_POLICY_DELETE_IMMEDIATELY = 'DELETE_IMMEDIATELY';
  /**
   * Keeps the backups as per the policy defined for database backups.
   */
  public const BACKUP_DELETION_POLICY_DELETE_AFTER_RETENTION_PERIOD = 'DELETE_AFTER_RETENTION_PERIOD';
  protected $collection_key = 'backupDestinationDetails';
  /**
   * Optional. If set to true, enables automatic backups on the database.
   *
   * @var bool
   */
  public $autoBackupEnabled;
  /**
   * Optional. The day of the week on which the full backup should be performed
   * on the database. If no value is provided, it will default to Sunday.
   *
   * @var string
   */
  public $autoFullBackupDay;
  /**
   * Optional. The window in which the full backup should be performed on the
   * database. If no value is provided, the default is anytime.
   *
   * @var string
   */
  public $autoFullBackupWindow;
  /**
   * Optional. The window in which the incremental backup should be performed on
   * the database. If no value is provided, the default is anytime except the
   * auto full backup day.
   *
   * @var string
   */
  public $autoIncrementalBackupWindow;
  /**
   * Optional. This defines when the backups will be deleted after Database
   * termination.
   *
   * @var string
   */
  public $backupDeletionPolicy;
  protected $backupDestinationDetailsType = BackupDestinationDetails::class;
  protected $backupDestinationDetailsDataType = 'array';
  /**
   * Optional. The number of days an automatic backup is retained before being
   * automatically deleted. This value determines the earliest point in time to
   * which a database can be restored. Min: 1, Max: 60.
   *
   * @var int
   */
  public $retentionPeriodDays;

  /**
   * Optional. If set to true, enables automatic backups on the database.
   *
   * @param bool $autoBackupEnabled
   */
  public function setAutoBackupEnabled($autoBackupEnabled)
  {
    $this->autoBackupEnabled = $autoBackupEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoBackupEnabled()
  {
    return $this->autoBackupEnabled;
  }
  /**
   * Optional. The day of the week on which the full backup should be performed
   * on the database. If no value is provided, it will default to Sunday.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::AUTO_FULL_BACKUP_DAY_* $autoFullBackupDay
   */
  public function setAutoFullBackupDay($autoFullBackupDay)
  {
    $this->autoFullBackupDay = $autoFullBackupDay;
  }
  /**
   * @return self::AUTO_FULL_BACKUP_DAY_*
   */
  public function getAutoFullBackupDay()
  {
    return $this->autoFullBackupDay;
  }
  /**
   * Optional. The window in which the full backup should be performed on the
   * database. If no value is provided, the default is anytime.
   *
   * Accepted values: BACKUP_WINDOW_UNSPECIFIED, SLOT_ONE, SLOT_TWO, SLOT_THREE,
   * SLOT_FOUR, SLOT_FIVE, SLOT_SIX, SLOT_SEVEN, SLOT_EIGHT, SLOT_NINE,
   * SLOT_TEN, SLOT_ELEVEN, SLOT_TWELVE
   *
   * @param self::AUTO_FULL_BACKUP_WINDOW_* $autoFullBackupWindow
   */
  public function setAutoFullBackupWindow($autoFullBackupWindow)
  {
    $this->autoFullBackupWindow = $autoFullBackupWindow;
  }
  /**
   * @return self::AUTO_FULL_BACKUP_WINDOW_*
   */
  public function getAutoFullBackupWindow()
  {
    return $this->autoFullBackupWindow;
  }
  /**
   * Optional. The window in which the incremental backup should be performed on
   * the database. If no value is provided, the default is anytime except the
   * auto full backup day.
   *
   * Accepted values: BACKUP_WINDOW_UNSPECIFIED, SLOT_ONE, SLOT_TWO, SLOT_THREE,
   * SLOT_FOUR, SLOT_FIVE, SLOT_SIX, SLOT_SEVEN, SLOT_EIGHT, SLOT_NINE,
   * SLOT_TEN, SLOT_ELEVEN, SLOT_TWELVE
   *
   * @param self::AUTO_INCREMENTAL_BACKUP_WINDOW_* $autoIncrementalBackupWindow
   */
  public function setAutoIncrementalBackupWindow($autoIncrementalBackupWindow)
  {
    $this->autoIncrementalBackupWindow = $autoIncrementalBackupWindow;
  }
  /**
   * @return self::AUTO_INCREMENTAL_BACKUP_WINDOW_*
   */
  public function getAutoIncrementalBackupWindow()
  {
    return $this->autoIncrementalBackupWindow;
  }
  /**
   * Optional. This defines when the backups will be deleted after Database
   * termination.
   *
   * Accepted values: BACKUP_DELETION_POLICY_UNSPECIFIED, DELETE_IMMEDIATELY,
   * DELETE_AFTER_RETENTION_PERIOD
   *
   * @param self::BACKUP_DELETION_POLICY_* $backupDeletionPolicy
   */
  public function setBackupDeletionPolicy($backupDeletionPolicy)
  {
    $this->backupDeletionPolicy = $backupDeletionPolicy;
  }
  /**
   * @return self::BACKUP_DELETION_POLICY_*
   */
  public function getBackupDeletionPolicy()
  {
    return $this->backupDeletionPolicy;
  }
  /**
   * Optional. Details of the database backup destinations.
   *
   * @param BackupDestinationDetails[] $backupDestinationDetails
   */
  public function setBackupDestinationDetails($backupDestinationDetails)
  {
    $this->backupDestinationDetails = $backupDestinationDetails;
  }
  /**
   * @return BackupDestinationDetails[]
   */
  public function getBackupDestinationDetails()
  {
    return $this->backupDestinationDetails;
  }
  /**
   * Optional. The number of days an automatic backup is retained before being
   * automatically deleted. This value determines the earliest point in time to
   * which a database can be restored. Min: 1, Max: 60.
   *
   * @param int $retentionPeriodDays
   */
  public function setRetentionPeriodDays($retentionPeriodDays)
  {
    $this->retentionPeriodDays = $retentionPeriodDays;
  }
  /**
   * @return int
   */
  public function getRetentionPeriodDays()
  {
    return $this->retentionPeriodDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DbBackupConfig::class, 'Google_Service_OracleDatabase_DbBackupConfig');
