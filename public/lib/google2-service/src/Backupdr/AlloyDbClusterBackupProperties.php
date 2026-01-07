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

namespace Google\Service\Backupdr;

class AlloyDbClusterBackupProperties extends \Google\Model
{
  /**
   * Output only. The chain id of this backup. Backups belonging to the same
   * chain are sharing the same chain id. This property is calculated and
   * maintained by BackupDR.
   *
   * @var string
   */
  public $chainId;
  /**
   * Output only. The PostgreSQL major version of the AlloyDB cluster when the
   * backup was taken.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * An optional text description for the backup.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Storage usage of this particular backup
   *
   * @var string
   */
  public $storedBytes;

  /**
   * Output only. The chain id of this backup. Backups belonging to the same
   * chain are sharing the same chain id. This property is calculated and
   * maintained by BackupDR.
   *
   * @param string $chainId
   */
  public function setChainId($chainId)
  {
    $this->chainId = $chainId;
  }
  /**
   * @return string
   */
  public function getChainId()
  {
    return $this->chainId;
  }
  /**
   * Output only. The PostgreSQL major version of the AlloyDB cluster when the
   * backup was taken.
   *
   * @param string $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * An optional text description for the backup.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Storage usage of this particular backup
   *
   * @param string $storedBytes
   */
  public function setStoredBytes($storedBytes)
  {
    $this->storedBytes = $storedBytes;
  }
  /**
   * @return string
   */
  public function getStoredBytes()
  {
    return $this->storedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AlloyDbClusterBackupProperties::class, 'Google_Service_Backupdr_AlloyDbClusterBackupProperties');
