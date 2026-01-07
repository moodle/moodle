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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ToolVersion extends \Google\Model
{
  /**
   * Output only. Last time the tool version was created or modified.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name of the tool version.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The unique identifier of the tool version. Format:
   * `projects//locations//agents//tools//versions/`.
   *
   * @var string
   */
  public $name;
  protected $toolType = GoogleCloudDialogflowCxV3Tool::class;
  protected $toolDataType = '';
  /**
   * Output only. Last time the tool version was created or modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Last time the tool version was created or modified.
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
   * Required. The display name of the tool version.
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
   * Identifier. The unique identifier of the tool version. Format:
   * `projects//locations//agents//tools//versions/`.
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
   * Required. Snapshot of the tool to be associated with this version.
   *
   * @param GoogleCloudDialogflowCxV3Tool $tool
   */
  public function setTool(GoogleCloudDialogflowCxV3Tool $tool)
  {
    $this->tool = $tool;
  }
  /**
   * @return GoogleCloudDialogflowCxV3Tool
   */
  public function getTool()
  {
    return $this->tool;
  }
  /**
   * Output only. Last time the tool version was created or modified.
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
class_alias(GoogleCloudDialogflowCxV3ToolVersion::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolVersion');
