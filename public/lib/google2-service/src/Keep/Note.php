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

namespace Google\Service\Keep;

class Note extends \Google\Collection
{
  protected $collection_key = 'permissions';
  protected $attachmentsType = Attachment::class;
  protected $attachmentsDataType = 'array';
  protected $bodyType = Section::class;
  protected $bodyDataType = '';
  /**
   * Output only. When this note was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of this note. See general note on
   * identifiers in KeepService.
   *
   * @var string
   */
  public $name;
  protected $permissionsType = Permission::class;
  protected $permissionsDataType = 'array';
  /**
   * The title of the note. Length must be less than 1,000 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. When this note was trashed. If `trashed`, the note is
   * eventually deleted. If the note is not trashed, this field is not set (and
   * the trashed field is `false`).
   *
   * @var string
   */
  public $trashTime;
  /**
   * Output only. `true` if this note has been trashed. If trashed, the note is
   * eventually deleted.
   *
   * @var bool
   */
  public $trashed;
  /**
   * Output only. When this note was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The attachments attached to this note.
   *
   * @param Attachment[] $attachments
   */
  public function setAttachments($attachments)
  {
    $this->attachments = $attachments;
  }
  /**
   * @return Attachment[]
   */
  public function getAttachments()
  {
    return $this->attachments;
  }
  /**
   * The body of the note.
   *
   * @param Section $body
   */
  public function setBody(Section $body)
  {
    $this->body = $body;
  }
  /**
   * @return Section
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Output only. When this note was created.
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
   * Output only. The resource name of this note. See general note on
   * identifiers in KeepService.
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
   * Output only. The list of permissions set on the note. Contains at least one
   * entry for the note owner.
   *
   * @param Permission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return Permission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * The title of the note. Length must be less than 1,000 characters.
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
   * Output only. When this note was trashed. If `trashed`, the note is
   * eventually deleted. If the note is not trashed, this field is not set (and
   * the trashed field is `false`).
   *
   * @param string $trashTime
   */
  public function setTrashTime($trashTime)
  {
    $this->trashTime = $trashTime;
  }
  /**
   * @return string
   */
  public function getTrashTime()
  {
    return $this->trashTime;
  }
  /**
   * Output only. `true` if this note has been trashed. If trashed, the note is
   * eventually deleted.
   *
   * @param bool $trashed
   */
  public function setTrashed($trashed)
  {
    $this->trashed = $trashed;
  }
  /**
   * @return bool
   */
  public function getTrashed()
  {
    return $this->trashed;
  }
  /**
   * Output only. When this note was last modified.
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
class_alias(Note::class, 'Google_Service_Keep_Note');
