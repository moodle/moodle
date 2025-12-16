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

namespace Google\Service\NetAppFiles;

class RestoreBackupFilesRequest extends \Google\Collection
{
  protected $collection_key = 'fileList';
  /**
   * Required. The backup resource name, in the format `projects/{project_id}/lo
   * cations/{location}/backupVaults/{backup_vault_id}/backups/{backup_id}`
   *
   * @var string
   */
  public $backup;
  /**
   * Required. List of files to be restored, specified by their absolute path in
   * the source volume.
   *
   * @var string[]
   */
  public $fileList;
  /**
   * Optional. Absolute directory path in the destination volume. This is
   * required if the `file_list` is provided.
   *
   * @var string
   */
  public $restoreDestinationPath;

  /**
   * Required. The backup resource name, in the format `projects/{project_id}/lo
   * cations/{location}/backupVaults/{backup_vault_id}/backups/{backup_id}`
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
   * Required. List of files to be restored, specified by their absolute path in
   * the source volume.
   *
   * @param string[] $fileList
   */
  public function setFileList($fileList)
  {
    $this->fileList = $fileList;
  }
  /**
   * @return string[]
   */
  public function getFileList()
  {
    return $this->fileList;
  }
  /**
   * Optional. Absolute directory path in the destination volume. This is
   * required if the `file_list` is provided.
   *
   * @param string $restoreDestinationPath
   */
  public function setRestoreDestinationPath($restoreDestinationPath)
  {
    $this->restoreDestinationPath = $restoreDestinationPath;
  }
  /**
   * @return string
   */
  public function getRestoreDestinationPath()
  {
    return $this->restoreDestinationPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreBackupFilesRequest::class, 'Google_Service_NetAppFiles_RestoreBackupFilesRequest');
