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

namespace Google\Service\TagManager;

class CreateContainerVersionResponse extends \Google\Model
{
  /**
   * Compiler errors or not.
   *
   * @var bool
   */
  public $compilerError;
  protected $containerVersionType = ContainerVersion::class;
  protected $containerVersionDataType = '';
  /**
   * Auto generated workspace path created as a result of version creation. This
   * field should only be populated if the created version was not a quick
   * preview.
   *
   * @var string
   */
  public $newWorkspacePath;
  protected $syncStatusType = SyncStatus::class;
  protected $syncStatusDataType = '';

  /**
   * Compiler errors or not.
   *
   * @param bool $compilerError
   */
  public function setCompilerError($compilerError)
  {
    $this->compilerError = $compilerError;
  }
  /**
   * @return bool
   */
  public function getCompilerError()
  {
    return $this->compilerError;
  }
  /**
   * The container version created.
   *
   * @param ContainerVersion $containerVersion
   */
  public function setContainerVersion(ContainerVersion $containerVersion)
  {
    $this->containerVersion = $containerVersion;
  }
  /**
   * @return ContainerVersion
   */
  public function getContainerVersion()
  {
    return $this->containerVersion;
  }
  /**
   * Auto generated workspace path created as a result of version creation. This
   * field should only be populated if the created version was not a quick
   * preview.
   *
   * @param string $newWorkspacePath
   */
  public function setNewWorkspacePath($newWorkspacePath)
  {
    $this->newWorkspacePath = $newWorkspacePath;
  }
  /**
   * @return string
   */
  public function getNewWorkspacePath()
  {
    return $this->newWorkspacePath;
  }
  /**
   * Whether version creation failed when syncing the workspace to the latest
   * container version.
   *
   * @param SyncStatus $syncStatus
   */
  public function setSyncStatus(SyncStatus $syncStatus)
  {
    $this->syncStatus = $syncStatus;
  }
  /**
   * @return SyncStatus
   */
  public function getSyncStatus()
  {
    return $this->syncStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateContainerVersionResponse::class, 'Google_Service_TagManager_CreateContainerVersionResponse');
