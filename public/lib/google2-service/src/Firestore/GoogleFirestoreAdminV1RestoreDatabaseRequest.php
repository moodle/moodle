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

class GoogleFirestoreAdminV1RestoreDatabaseRequest extends \Google\Model
{
  /**
   * Required. Backup to restore from. Must be from the same project as the
   * parent. The restored database will be created in the same location as the
   * source backup. Format is:
   * `projects/{project_id}/locations/{location}/backups/{backup}`
   *
   * @var string
   */
  public $backup;
  /**
   * Required. The ID to use for the database, which will become the final
   * component of the database's resource name. This database ID must not be
   * associated with an existing database. This value should be 4-63 characters.
   * Valid characters are /a-z-/ with first character a letter and the last a
   * letter or a number. Must not be UUID-like
   * /[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}/. "(default)" database ID is
   * also valid if the database is Standard edition.
   *
   * @var string
   */
  public $databaseId;
  protected $encryptionConfigType = GoogleFirestoreAdminV1EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Optional. Immutable. Tags to be bound to the restored database. The tags
   * should be provided in the format of `tagKeys/{tag_key_id} ->
   * tagValues/{tag_value_id}`.
   *
   * @var string[]
   */
  public $tags;

  /**
   * Required. Backup to restore from. Must be from the same project as the
   * parent. The restored database will be created in the same location as the
   * source backup. Format is:
   * `projects/{project_id}/locations/{location}/backups/{backup}`
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
   * Required. The ID to use for the database, which will become the final
   * component of the database's resource name. This database ID must not be
   * associated with an existing database. This value should be 4-63 characters.
   * Valid characters are /a-z-/ with first character a letter and the last a
   * letter or a number. Must not be UUID-like
   * /[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}/. "(default)" database ID is
   * also valid if the database is Standard edition.
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
   * Optional. Encryption configuration for the restored database. If this field
   * is not specified, the restored database will use the same encryption
   * configuration as the backup, namely use_source_encryption.
   *
   * @param GoogleFirestoreAdminV1EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(GoogleFirestoreAdminV1EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return GoogleFirestoreAdminV1EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Optional. Immutable. Tags to be bound to the restored database. The tags
   * should be provided in the format of `tagKeys/{tag_key_id} ->
   * tagValues/{tag_value_id}`.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1RestoreDatabaseRequest::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1RestoreDatabaseRequest');
