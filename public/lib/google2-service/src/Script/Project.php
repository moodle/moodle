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

namespace Google\Service\Script;

class Project extends \Google\Model
{
  /**
   * When the script was created.
   *
   * @var string
   */
  public $createTime;
  protected $creatorType = GoogleAppsScriptTypeUser::class;
  protected $creatorDataType = '';
  protected $lastModifyUserType = GoogleAppsScriptTypeUser::class;
  protected $lastModifyUserDataType = '';
  /**
   * The parent's Drive ID that the script will be attached to. This is usually
   * the ID of a Google Document or Google Sheet. This field is optional, and if
   * not set, a stand-alone script will be created.
   *
   * @var string
   */
  public $parentId;
  /**
   * The script project's Drive ID.
   *
   * @var string
   */
  public $scriptId;
  /**
   * The title for the project.
   *
   * @var string
   */
  public $title;
  /**
   * When the script was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * When the script was created.
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
   * User who originally created the script.
   *
   * @param GoogleAppsScriptTypeUser $creator
   */
  public function setCreator(GoogleAppsScriptTypeUser $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return GoogleAppsScriptTypeUser
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * User who last modified the script.
   *
   * @param GoogleAppsScriptTypeUser $lastModifyUser
   */
  public function setLastModifyUser(GoogleAppsScriptTypeUser $lastModifyUser)
  {
    $this->lastModifyUser = $lastModifyUser;
  }
  /**
   * @return GoogleAppsScriptTypeUser
   */
  public function getLastModifyUser()
  {
    return $this->lastModifyUser;
  }
  /**
   * The parent's Drive ID that the script will be attached to. This is usually
   * the ID of a Google Document or Google Sheet. This field is optional, and if
   * not set, a stand-alone script will be created.
   *
   * @param string $parentId
   */
  public function setParentId($parentId)
  {
    $this->parentId = $parentId;
  }
  /**
   * @return string
   */
  public function getParentId()
  {
    return $this->parentId;
  }
  /**
   * The script project's Drive ID.
   *
   * @param string $scriptId
   */
  public function setScriptId($scriptId)
  {
    $this->scriptId = $scriptId;
  }
  /**
   * @return string
   */
  public function getScriptId()
  {
    return $this->scriptId;
  }
  /**
   * The title for the project.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * When the script was last updated.
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
class_alias(Project::class, 'Google_Service_Script_Project');
