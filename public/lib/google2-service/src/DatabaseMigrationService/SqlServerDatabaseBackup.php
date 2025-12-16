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

namespace Google\Service\DatabaseMigrationService;

class SqlServerDatabaseBackup extends \Google\Model
{
  /**
   * Required. Name of a SQL Server database for which to define backup
   * configuration.
   *
   * @var string
   */
  public $database;
  protected $encryptionOptionsType = SqlServerEncryptionOptions::class;
  protected $encryptionOptionsDataType = '';

  /**
   * Required. Name of a SQL Server database for which to define backup
   * configuration.
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
   * Optional. Encryption settings for the database. Required if provided
   * database backups are encrypted. Encryption settings include path to
   * certificate, path to certificate private key, and key password.
   *
   * @param SqlServerEncryptionOptions $encryptionOptions
   */
  public function setEncryptionOptions(SqlServerEncryptionOptions $encryptionOptions)
  {
    $this->encryptionOptions = $encryptionOptions;
  }
  /**
   * @return SqlServerEncryptionOptions
   */
  public function getEncryptionOptions()
  {
    return $this->encryptionOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlServerDatabaseBackup::class, 'Google_Service_DatabaseMigrationService_SqlServerDatabaseBackup');
