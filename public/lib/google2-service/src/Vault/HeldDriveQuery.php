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

namespace Google\Service\Vault;

class HeldDriveQuery extends \Google\Model
{
  /**
   * To include files in shared drives in the hold, set to **true**.
   *
   * @var bool
   */
  public $includeSharedDriveFiles;
  /**
   * To include files in Team Drives in the hold, set to **true**.
   *
   * @deprecated
   * @var bool
   */
  public $includeTeamDriveFiles;

  /**
   * To include files in shared drives in the hold, set to **true**.
   *
   * @param bool $includeSharedDriveFiles
   */
  public function setIncludeSharedDriveFiles($includeSharedDriveFiles)
  {
    $this->includeSharedDriveFiles = $includeSharedDriveFiles;
  }
  /**
   * @return bool
   */
  public function getIncludeSharedDriveFiles()
  {
    return $this->includeSharedDriveFiles;
  }
  /**
   * To include files in Team Drives in the hold, set to **true**.
   *
   * @deprecated
   * @param bool $includeTeamDriveFiles
   */
  public function setIncludeTeamDriveFiles($includeTeamDriveFiles)
  {
    $this->includeTeamDriveFiles = $includeTeamDriveFiles;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIncludeTeamDriveFiles()
  {
    return $this->includeTeamDriveFiles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HeldDriveQuery::class, 'Google_Service_Vault_HeldDriveQuery');
