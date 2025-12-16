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

class Task extends \Google\Collection
{
  protected $collection_key = 'links';
  protected $assignmentInfoType = AssignmentInfo::class;
  protected $assignmentInfoDataType = '';
  /**
   * Completion date of the task (as a RFC 3339 timestamp). This field is
   * omitted if the task has not been completed.
   *
   * @var string
   */
  public $completed;
  /**
   * Flag indicating whether the task has been deleted. For assigned tasks this
   * field is read-only. They can only be deleted by calling tasks.delete, in
   * which case both the assigned task and the original task (in Docs or Chat
   * Spaces) are deleted. To delete the assigned task only, navigate to the
   * assignment surface and unassign the task from there. The default is False.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Scheduled date for the task (as an RFC 3339 timestamp). Optional. This
   * represents the day that the task should be done, or that the task is
   * visible on the calendar grid. It doesn't represent the deadline of the
   * task. Only date information is recorded; the time portion of the timestamp
   * is discarded when setting this field. It isn't possible to read or write
   * the time that a task is scheduled for using the API.
   *
   * @var string
   */
  public $due;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Flag indicating whether the task is hidden. This is the case if the task
   * had been marked completed when the task list was last cleared. The default
   * is False. This field is read-only.
   *
   * @var bool
   */
  public $hidden;
  /**
   * Task identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Type of the resource. This is always "tasks#task".
   *
   * @var string
   */
  public $kind;
  protected $linksType = TaskLinks::class;
  protected $linksDataType = 'array';
  /**
   * Notes describing the task. Tasks assigned from Google Docs cannot have
   * notes. Optional. Maximum length allowed: 8192 characters.
   *
   * @var string
   */
  public $notes;
  /**
   * Output only. Parent task identifier. This field is omitted if it is a top-
   * level task. Use the "move" method to move the task under a different parent
   * or to the top level. A parent task can never be an assigned task (from Chat
   * Spaces, Docs). This field is read-only.
   *
   * @var string
   */
  public $parent;
  /**
   * Output only. String indicating the position of the task among its sibling
   * tasks under the same parent task or at the top level. If this string is
   * greater than another task's corresponding position string according to
   * lexicographical ordering, the task is positioned after the other task under
   * the same parent task (or at the top level). Use the "move" method to move
   * the task to another position.
   *
   * @var string
   */
  public $position;
  /**
   * Output only. URL pointing to this task. Used to retrieve, update, or delete
   * this task.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Status of the task. This is either "needsAction" or "completed".
   *
   * @var string
   */
  public $status;
  /**
   * Title of the task. Maximum length allowed: 1024 characters.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. Last modification time of the task (as a RFC 3339 timestamp).
   *
   * @var string
   */
  public $updated;
  /**
   * Output only. An absolute link to the task in the Google Tasks Web UI.
   *
   * @var string
   */
  public $webViewLink;

  /**
   * Output only. Context information for assigned tasks. A task can be assigned
   * to a user, currently possible from surfaces like Docs and Chat Spaces. This
   * field is populated for tasks assigned to the current user and identifies
   * where the task was assigned from. This field is read-only.
   *
   * @param AssignmentInfo $assignmentInfo
   */
  public function setAssignmentInfo(AssignmentInfo $assignmentInfo)
  {
    $this->assignmentInfo = $assignmentInfo;
  }
  /**
   * @return AssignmentInfo
   */
  public function getAssignmentInfo()
  {
    return $this->assignmentInfo;
  }
  /**
   * Completion date of the task (as a RFC 3339 timestamp). This field is
   * omitted if the task has not been completed.
   *
   * @param string $completed
   */
  public function setCompleted($completed)
  {
    $this->completed = $completed;
  }
  /**
   * @return string
   */
  public function getCompleted()
  {
    return $this->completed;
  }
  /**
   * Flag indicating whether the task has been deleted. For assigned tasks this
   * field is read-only. They can only be deleted by calling tasks.delete, in
   * which case both the assigned task and the original task (in Docs or Chat
   * Spaces) are deleted. To delete the assigned task only, navigate to the
   * assignment surface and unassign the task from there. The default is False.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Scheduled date for the task (as an RFC 3339 timestamp). Optional. This
   * represents the day that the task should be done, or that the task is
   * visible on the calendar grid. It doesn't represent the deadline of the
   * task. Only date information is recorded; the time portion of the timestamp
   * is discarded when setting this field. It isn't possible to read or write
   * the time that a task is scheduled for using the API.
   *
   * @param string $due
   */
  public function setDue($due)
  {
    $this->due = $due;
  }
  /**
   * @return string
   */
  public function getDue()
  {
    return $this->due;
  }
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
   * Flag indicating whether the task is hidden. This is the case if the task
   * had been marked completed when the task list was last cleared. The default
   * is False. This field is read-only.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * Task identifier.
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
   * Output only. Type of the resource. This is always "tasks#task".
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
   * Output only. Collection of links. This collection is read-only.
   *
   * @param TaskLinks[] $links
   */
  public function setLinks($links)
  {
    $this->links = $links;
  }
  /**
   * @return TaskLinks[]
   */
  public function getLinks()
  {
    return $this->links;
  }
  /**
   * Notes describing the task. Tasks assigned from Google Docs cannot have
   * notes. Optional. Maximum length allowed: 8192 characters.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Output only. Parent task identifier. This field is omitted if it is a top-
   * level task. Use the "move" method to move the task under a different parent
   * or to the top level. A parent task can never be an assigned task (from Chat
   * Spaces, Docs). This field is read-only.
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
   * Output only. String indicating the position of the task among its sibling
   * tasks under the same parent task or at the top level. If this string is
   * greater than another task's corresponding position string according to
   * lexicographical ordering, the task is positioned after the other task under
   * the same parent task (or at the top level). Use the "move" method to move
   * the task to another position.
   *
   * @param string $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }
  /**
   * @return string
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Output only. URL pointing to this task. Used to retrieve, update, or delete
   * this task.
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
   * Status of the task. This is either "needsAction" or "completed".
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Title of the task. Maximum length allowed: 1024 characters.
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
   * Output only. Last modification time of the task (as a RFC 3339 timestamp).
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
  /**
   * Output only. An absolute link to the task in the Google Tasks Web UI.
   *
   * @param string $webViewLink
   */
  public function setWebViewLink($webViewLink)
  {
    $this->webViewLink = $webViewLink;
  }
  /**
   * @return string
   */
  public function getWebViewLink()
  {
    return $this->webViewLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Task::class, 'Google_Service_Tasks_Task');
