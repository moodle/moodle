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

class GoogleFirestoreAdminV1SourceInfo extends \Google\Model
{
  protected $backupType = GoogleFirestoreAdminV1BackupSource::class;
  protected $backupDataType = '';
  /**
   * The associated long-running operation. This field may not be set after the
   * operation has completed. Format:
   * `projects/{project}/databases/{database}/operations/{operation}`.
   *
   * @var string
   */
  public $operation;

  /**
   * If set, this database was restored from the specified backup (or a snapshot
   * thereof).
   *
   * @param GoogleFirestoreAdminV1BackupSource $backup
   */
  public function setBackup(GoogleFirestoreAdminV1BackupSource $backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return GoogleFirestoreAdminV1BackupSource
   */
  public function getBackup()
  {
    return $this->backup;
  }
  /**
   * The associated long-running operation. This field may not be set after the
   * operation has completed. Format:
   * `projects/{project}/databases/{database}/operations/{operation}`.
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1SourceInfo::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1SourceInfo');
