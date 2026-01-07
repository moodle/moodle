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

class DatabaseMoveConfig extends \Google\Model
{
  /**
   * Required. The unique identifier of the database resource in the Instance.
   * For example, if the database uri is
   * `projects/foo/instances/bar/databases/baz`, then the id to supply here is
   * baz.
   *
   * @var string
   */
  public $databaseId;
  protected $encryptionConfigType = InstanceEncryptionConfig::class;
  protected $encryptionConfigDataType = '';

  /**
   * Required. The unique identifier of the database resource in the Instance.
   * For example, if the database uri is
   * `projects/foo/instances/bar/databases/baz`, then the id to supply here is
   * baz.
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
   * Optional. Encryption configuration to be used for the database in the
   * target configuration. The encryption configuration must be specified for
   * every database which currently uses CMEK encryption. If a database
   * currently uses Google-managed encryption and a target encryption
   * configuration is not specified, then the database defaults to Google-
   * managed encryption. If a database currently uses Google-managed encryption
   * and a target CMEK encryption is specified, the request is rejected. If a
   * database currently uses CMEK encryption, then a target encryption
   * configuration must be specified. You can't move a CMEK database to a
   * Google-managed encryption database using the MoveInstance API.
   *
   * @param InstanceEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(InstanceEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return InstanceEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseMoveConfig::class, 'Google_Service_Spanner_DatabaseMoveConfig');
