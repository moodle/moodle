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

namespace Google\Service\CloudAsset;

class SavedQuery extends \Google\Model
{
  protected $contentType = QueryContent::class;
  protected $contentDataType = '';
  /**
   * Output only. The create time of this saved query.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The account's email address who has created this saved query.
   *
   * @var string
   */
  public $creator;
  /**
   * The description of this saved query. This value should be fewer than 255
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Labels applied on the resource. This value should not contain more than 10
   * entries. The key and value of each entry must be non-empty and fewer than
   * 64 characters.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The last update time of this saved query.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * Output only. The account's email address who has updated this saved query
   * most recently.
   *
   * @var string
   */
  public $lastUpdater;
  /**
   * The resource name of the saved query. The format must be: *
   * projects/project_number/savedQueries/saved_query_id *
   * folders/folder_number/savedQueries/saved_query_id *
   * organizations/organization_number/savedQueries/saved_query_id
   *
   * @var string
   */
  public $name;

  /**
   * The query content.
   *
   * @param QueryContent $content
   */
  public function setContent(QueryContent $content)
  {
    $this->content = $content;
  }
  /**
   * @return QueryContent
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. The create time of this saved query.
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
   * Output only. The account's email address who has created this saved query.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * The description of this saved query. This value should be fewer than 255
   * characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Labels applied on the resource. This value should not contain more than 10
   * entries. The key and value of each entry must be non-empty and fewer than
   * 64 characters.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The last update time of this saved query.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Output only. The account's email address who has updated this saved query
   * most recently.
   *
   * @param string $lastUpdater
   */
  public function setLastUpdater($lastUpdater)
  {
    $this->lastUpdater = $lastUpdater;
  }
  /**
   * @return string
   */
  public function getLastUpdater()
  {
    return $this->lastUpdater;
  }
  /**
   * The resource name of the saved query. The format must be: *
   * projects/project_number/savedQueries/saved_query_id *
   * folders/folder_number/savedQueries/saved_query_id *
   * organizations/organization_number/savedQueries/saved_query_id
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SavedQuery::class, 'Google_Service_CloudAsset_SavedQuery');
