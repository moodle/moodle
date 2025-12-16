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

namespace Google\Service\Tasks;

class TaskList extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Task list identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Type of the resource. This is always "tasks#taskList".
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. URL pointing to this task list. Used to retrieve, update, or
   * delete this task list.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Title of the task list. Maximum length allowed: 1024 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. Last modification time of the task list (as a RFC 3339
   * timestamp).
   *
   * @var string
   */
  public $updated;

  /**
   * ETag of the resource.
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
   * Task list identifier.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Type of the resource. This is always "tasks#taskList".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. URL pointing to this task list. Used to retrieve, update, or
   * delete this task list.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Title of the task list. Maximum length allowed: 1024 characters.
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
   * Output only. Last modification time of the task list (as a RFC 3339
   * timestamp).
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskList::class, 'Google_Service_Tasks_TaskList');
