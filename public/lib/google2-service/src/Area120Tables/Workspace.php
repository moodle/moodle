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

namespace Google\Service\Area120Tables;

class Workspace extends \Google\Collection
{
  protected $collection_key = 'tables';
  /**
   * Time when the workspace was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The human readable title of the workspace.
   *
   * @var string
   */
  public $displayName;
  /**
   * The resource name of the workspace. Workspace names have the form
   * `workspaces/{workspace}`.
   *
   * @var string
   */
  public $name;
  protected $tablesType = Table::class;
  protected $tablesDataType = 'array';
  /**
   * Time when the workspace was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Time when the workspace was created.
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
   * The human readable title of the workspace.
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
   * The resource name of the workspace. Workspace names have the form
   * `workspaces/{workspace}`.
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
   * The list of tables in the workspace.
   *
   * @param Table[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return Table[]
   */
  public function getTables()
  {
    return $this->tables;
  }
  /**
   * Time when the workspace was last updated.
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
class_alias(Workspace::class, 'Google_Service_Area120Tables_Workspace');
