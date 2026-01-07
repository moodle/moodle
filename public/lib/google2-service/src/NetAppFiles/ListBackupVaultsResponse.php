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

class ListBackupVaultsResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $backupVaultsType = BackupVault::class;
  protected $backupVaultsDataType = 'array';
  /**
   * The token you can use to retrieve the next page of results. Not returned if
   * there are no more results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached.
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A list of backupVaults in the project for the specified location.
   *
   * @param BackupVault[] $backupVaults
   */
  public function setBackupVaults($backupVaults)
  {
    $this->backupVaults = $backupVaults;
  }
  /**
   * @return BackupVault[]
   */
  public function getBackupVaults()
  {
    return $this->backupVaults;
  }
  /**
   * The token you can use to retrieve the next page of results. Not returned if
   * there are no more results in the list.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Locations that could not be reached.
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListBackupVaultsResponse::class, 'Google_Service_NetAppFiles_ListBackupVaultsResponse');
