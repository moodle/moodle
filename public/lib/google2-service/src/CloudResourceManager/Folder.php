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

namespace Google\Service\CloudResourceManager;

class Folder extends \Google\Collection
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The folder has been marked for deletion by the user.
   */
  public const STATE_DELETE_REQUESTED = 'DELETE_REQUESTED';
  protected $collection_key = 'configuredCapabilities';
  /**
   * Output only. Optional capabilities configured for this folder (via
   * UpdateCapability API). Example: `folders/123/capabilities/app-management`.
   *
   * @var string[]
   */
  public $configuredCapabilities;
  /**
   * Output only. Timestamp when the folder was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Timestamp when the folder was requested to be deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * The folder's display name. A folder's display name must be unique amongst
   * its siblings. For example, no two folders with the same parent can share
   * the same display name. The display name must start and end with a letter or
   * digit, may contain letters, digits, spaces, hyphens and underscores and can
   * be no longer than 30 characters. This is captured by the regular
   * expression: `[\p{L}\p{N}]([\p{L}\p{N}_- ]{0,28}[\p{L}\p{N}])?`.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the folder resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Management Project associated with this folder (if app-
   * management capability is enabled). Example: `projects/google-mp-123` OUTPUT
   * ONLY.
   *
   * @var string
   */
  public $managementProject;
  /**
   * Identifier. The resource name of the folder. Its format is
   * `folders/{folder_id}`, for example: "folders/1234".
   *
   * @var string
   */
  public $name;
  /**
   * Required. The folder's parent's resource name. Updates to the folder's
   * parent must be performed using MoveFolder.
   *
   * @var string
   */
  public $parent;
  /**
   * Output only. The lifecycle state of the folder. Updates to the state must
   * be performed using DeleteFolder and UndeleteFolder.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * folder. Each item in the map must be expressed as " : ". For example:
   * "123/environment" : "production", "123/costCenter" : "marketing" Note:
   * Currently this field is in Preview.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Output only. Timestamp when the folder was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Optional capabilities configured for this folder (via
   * UpdateCapability API). Example: `folders/123/capabilities/app-management`.
   *
   * @param string[] $configuredCapabilities
   */
  public function setConfiguredCapabilities($configuredCapabilities)
  {
    $this->configuredCapabilities = $configuredCapabilities;
  }
  /**
   * @return string[]
   */
  public function getConfiguredCapabilities()
  {
    return $this->configuredCapabilities;
  }
  /**
   * Output only. Timestamp when the folder was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Timestamp when the folder was requested to be deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * The folder's display name. A folder's display name must be unique amongst
   * its siblings. For example, no two folders with the same parent can share
   * the same display name. The display name must start and end with a letter or
   * digit, may contain letters, digits, spaces, hyphens and underscores and can
   * be no longer than 30 characters. This is captured by the regular
   * expression: `[\p{L}\p{N}]([\p{L}\p{N}_- ]{0,28}[\p{L}\p{N}])?`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. A checksum computed by the server based on the current value
   * of the folder resource. This may be sent on update and delete requests to
   * ensure the client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Management Project associated with this folder (if app-
   * management capability is enabled). Example: `projects/google-mp-123` OUTPUT
   * ONLY.
   *
   * @param string $managementProject
   */
  public function setManagementProject($managementProject)
  {
    $this->managementProject = $managementProject;
  }
  /**
   * @return string
   */
  public function getManagementProject()
  {
    return $this->managementProject;
  }
  /**
   * Identifier. The resource name of the folder. Its format is
   * `folders/{folder_id}`, for example: "folders/1234".
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Required. The folder's parent's resource name. Updates to the folder's
   * parent must be performed using MoveFolder.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Output only. The lifecycle state of the folder. Updates to the state must
   * be performed using DeleteFolder and UndeleteFolder.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * folder. Each item in the map must be expressed as " : ". For example:
   * "123/environment" : "production", "123/costCenter" : "marketing" Note:
   * Currently this field is in Preview.
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
  /**
   * Output only. Timestamp when the folder was last modified.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Folder::class, 'Google_Service_CloudResourceManager_Folder');
