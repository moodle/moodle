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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1Backup extends \Google\Model
{
  /**
   * The state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The pending backup is still being created. Operations on the backup will be
   * rejected in this state.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup is complete and ready to use.
   */
  public const STATE_READY = 'READY';
  /**
   * The backup is not available at this moment.
   */
  public const STATE_NOT_AVAILABLE = 'NOT_AVAILABLE';
  /**
   * Output only. Name of the Firestore database that the backup is from. Format
   * is `projects/{project}/databases/{database}`.
   *
   * @var string
   */
  public $database;
  /**
   * Output only. The system-generated UUID4 for the Firestore database that the
   * backup is from.
   *
   * @var string
   */
  public $databaseUid;
  /**
   * Output only. The timestamp at which this backup expires.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The unique resource name of the Backup. Format is
   * `projects/{project}/locations/{location}/backups/{backup}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The backup contains an externally consistent copy of the
   * database at this time.
   *
   * @var string
   */
  public $snapshotTime;
  /**
   * Output only. The current state of the backup.
   *
   * @var string
   */
  public $state;
  protected $statsType = GoogleFirestoreAdminV1Stats::class;
  protected $statsDataType = '';

  /**
   * Output only. Name of the Firestore database that the backup is from. Format
   * is `projects/{project}/databases/{database}`.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Output only. The system-generated UUID4 for the Firestore database that the
   * backup is from.
   *
   * @param string $databaseUid
   */
  public function setDatabaseUid($databaseUid)
  {
    $this->databaseUid = $databaseUid;
  }
  /**
   * @return string
   */
  public function getDatabaseUid()
  {
    return $this->databaseUid;
  }
  /**
   * Output only. The timestamp at which this backup expires.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The unique resource name of the Backup. Format is
   * `projects/{project}/locations/{location}/backups/{backup}`.
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
   * Output only. The backup contains an externally consistent copy of the
   * database at this time.
   *
   * @param string $snapshotTime
   */
  public function setSnapshotTime($snapshotTime)
  {
    $this->snapshotTime = $snapshotTime;
  }
  /**
   * @return string
   */
  public function getSnapshotTime()
  {
    return $this->snapshotTime;
  }
  /**
   * Output only. The current state of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, NOT_AVAILABLE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Statistics about the backup. This data only becomes available
   * after the backup is fully materialized to secondary storage. This field
   * will be empty till then.
   *
   * @param GoogleFirestoreAdminV1Stats $stats
   */
  public function setStats(GoogleFirestoreAdminV1Stats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return GoogleFirestoreAdminV1Stats
   */
  public function getStats()
  {
    return $this->stats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1Backup::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1Backup');
