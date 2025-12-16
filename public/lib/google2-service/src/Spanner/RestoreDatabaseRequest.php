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

namespace Google\Service\Spanner;

class RestoreDatabaseRequest extends \Google\Model
{
  /**
   * Name of the backup from which to restore. Values are of the form
   * `projects//instances//backups/`.
   *
   * @var string
   */
  public $backup;
  /**
   * Required. The id of the database to create and restore to. This database
   * must not already exist. The `database_id` appended to `parent` forms the
   * full database name of the form `projects//instances//databases/`.
   *
   * @var string
   */
  public $databaseId;
  protected $encryptionConfigType = RestoreDatabaseEncryptionConfig::class;
  protected $encryptionConfigDataType = '';

  /**
   * Name of the backup from which to restore. Values are of the form
   * `projects//instances//backups/`.
   *
   * @param string $backup
   */
  public function setBackup($backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return string
   */
  public function getBackup()
  {
    return $this->backup;
  }
  /**
   * Required. The id of the database to create and restore to. This database
   * must not already exist. The `database_id` appended to `parent` forms the
   * full database name of the form `projects//instances//databases/`.
   *
   * @param string $databaseId
   */
  public function setDatabaseId($databaseId)
  {
    $this->databaseId = $databaseId;
  }
  /**
   * @return string
   */
  public function getDatabaseId()
  {
    return $this->databaseId;
  }
  /**
   * Optional. An encryption configuration describing the encryption type and
   * key resources in Cloud KMS used to encrypt/decrypt the database to restore
   * to. If this field is not specified, the restored database will use the same
   * encryption configuration as the backup by default, namely encryption_type =
   * `USE_CONFIG_DEFAULT_OR_BACKUP_ENCRYPTION`.
   *
   * @param RestoreDatabaseEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(RestoreDatabaseEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return RestoreDatabaseEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreDatabaseRequest::class, 'Google_Service_Spanner_RestoreDatabaseRequest');
