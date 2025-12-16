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

namespace Google\Service\Translate;

class AdaptiveMtFile extends \Google\Model
{
  /**
   * Output only. Timestamp when this file was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The file's display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * The number of entries that the file contains.
   *
   * @var int
   */
  public $entryCount;
  /**
   * Required. The resource name of the file, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset}/adaptive
   * MtFiles/{file}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Timestamp when this file was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this file was created.
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
   * The file's display name.
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
   * The number of entries that the file contains.
   *
   * @param int $entryCount
   */
  public function setEntryCount($entryCount)
  {
    $this->entryCount = $entryCount;
  }
  /**
   * @return int
   */
  public function getEntryCount()
  {
    return $this->entryCount;
  }
  /**
   * Required. The resource name of the file, in form of `projects/{project-
   * number-or-id}/locations/{location_id}/adaptiveMtDatasets/{dataset}/adaptive
   * MtFiles/{file}`
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
   * Output only. Timestamp when this file was last updated.
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
class_alias(AdaptiveMtFile::class, 'Google_Service_Translate_AdaptiveMtFile');
