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

namespace Google\Service\Classroom;

class Announcement extends \Google\Collection
{
  /**
   * No mode specified. This is never returned.
   */
  public const ASSIGNEE_MODE_ASSIGNEE_MODE_UNSPECIFIED = 'ASSIGNEE_MODE_UNSPECIFIED';
  /**
   * All students can see the item. This is the default state.
   */
  public const ASSIGNEE_MODE_ALL_STUDENTS = 'ALL_STUDENTS';
  /**
   * A subset of the students can see the item.
   */
  public const ASSIGNEE_MODE_INDIVIDUAL_STUDENTS = 'INDIVIDUAL_STUDENTS';
  /**
   * No state specified. This is never returned.
   */
  public const STATE_ANNOUNCEMENT_STATE_UNSPECIFIED = 'ANNOUNCEMENT_STATE_UNSPECIFIED';
  /**
   * Status for announcement that has been published. This is the default state.
   */
  public const STATE_PUBLISHED = 'PUBLISHED';
  /**
   * Status for an announcement that is not yet published. Announcement in this
   * state is visible only to course teachers and domain administrators.
   */
  public const STATE_DRAFT = 'DRAFT';
  /**
   * Status for announcement that was published but is now deleted. Announcement
   * in this state is visible only to course teachers and domain administrators.
   * Announcement in this state is deleted after some time.
   */
  public const STATE_DELETED = 'DELETED';
  protected $collection_key = 'materials';
  /**
   * Absolute link to this announcement in the Classroom web UI. This is only
   * populated if `state` is `PUBLISHED`. Read-only.
   *
   * @var string
   */
  public $alternateLink;
  /**
   * Assignee mode of the announcement. If unspecified, the default value is
   * `ALL_STUDENTS`.
   *
   * @var string
   */
  public $assigneeMode;
  /**
   * Identifier of the course. Read-only.
   *
   * @var string
   */
  public $courseId;
  /**
   * Timestamp when this announcement was created. Read-only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Identifier for the user that created the announcement. Read-only.
   *
   * @var string
   */
  public $creatorUserId;
  /**
   * Classroom-assigned identifier of this announcement, unique per course.
   * Read-only.
   *
   * @var string
   */
  public $id;
  protected $individualStudentsOptionsType = IndividualStudentsOptions::class;
  protected $individualStudentsOptionsDataType = '';
  protected $materialsType = Material::class;
  protected $materialsDataType = 'array';
  /**
   * Optional timestamp when this announcement is scheduled to be published.
   *
   * @var string
   */
  public $scheduledTime;
  /**
   * Status of this announcement. If unspecified, the default state is `DRAFT`.
   *
   * @var string
   */
  public $state;
  /**
   * Description of this announcement. The text must be a valid UTF-8 string
   * containing no more than 30,000 characters.
   *
   * @var string
   */
  public $text;
  /**
   * Timestamp of the most recent change to this announcement. Read-only.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Absolute link to this announcement in the Classroom web UI. This is only
   * populated if `state` is `PUBLISHED`. Read-only.
   *
   * @param string $alternateLink
   */
  public function setAlternateLink($alternateLink)
  {
    $this->alternateLink = $alternateLink;
  }
  /**
   * @return string
   */
  public function getAlternateLink()
  {
    return $this->alternateLink;
  }
  /**
   * Assignee mode of the announcement. If unspecified, the default value is
   * `ALL_STUDENTS`.
   *
   * Accepted values: ASSIGNEE_MODE_UNSPECIFIED, ALL_STUDENTS,
   * INDIVIDUAL_STUDENTS
   *
   * @param self::ASSIGNEE_MODE_* $assigneeMode
   */
  public function setAssigneeMode($assigneeMode)
  {
    $this->assigneeMode = $assigneeMode;
  }
  /**
   * @return self::ASSIGNEE_MODE_*
   */
  public function getAssigneeMode()
  {
    return $this->assigneeMode;
  }
  /**
   * Identifier of the course. Read-only.
   *
   * @param string $courseId
   */
  public function setCourseId($courseId)
  {
    $this->courseId = $courseId;
  }
  /**
   * @return string
   */
  public function getCourseId()
  {
    return $this->courseId;
  }
  /**
   * Timestamp when this announcement was created. Read-only.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Identifier for the user that created the announcement. Read-only.
   *
   * @param string $creatorUserId
   */
  public function setCreatorUserId($creatorUserId)
  {
    $this->creatorUserId = $creatorUserId;
  }
  /**
   * @return string
   */
  public function getCreatorUserId()
  {
    return $this->creatorUserId;
  }
  /**
   * Classroom-assigned identifier of this announcement, unique per course.
   * Read-only.
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
   * Identifiers of students with access to the announcement. This field is set
   * only if `assigneeMode` is `INDIVIDUAL_STUDENTS`. If the `assigneeMode` is
   * `INDIVIDUAL_STUDENTS`, then only students specified in this field can see
   * the announcement.
   *
   * @param IndividualStudentsOptions $individualStudentsOptions
   */
  public function setIndividualStudentsOptions(IndividualStudentsOptions $individualStudentsOptions)
  {
    $this->individualStudentsOptions = $individualStudentsOptions;
  }
  /**
   * @return IndividualStudentsOptions
   */
  public function getIndividualStudentsOptions()
  {
    return $this->individualStudentsOptions;
  }
  /**
   * Additional materials. Announcements must have no more than 20 material
   * items.
   *
   * @param Material[] $materials
   */
  public function setMaterials($materials)
  {
    $this->materials = $materials;
  }
  /**
   * @return Material[]
   */
  public function getMaterials()
  {
    return $this->materials;
  }
  /**
   * Optional timestamp when this announcement is scheduled to be published.
   *
   * @param string $scheduledTime
   */
  public function setScheduledTime($scheduledTime)
  {
    $this->scheduledTime = $scheduledTime;
  }
  /**
   * @return string
   */
  public function getScheduledTime()
  {
    return $this->scheduledTime;
  }
  /**
   * Status of this announcement. If unspecified, the default state is `DRAFT`.
   *
   * Accepted values: ANNOUNCEMENT_STATE_UNSPECIFIED, PUBLISHED, DRAFT, DELETED
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
   * Description of this announcement. The text must be a valid UTF-8 string
   * containing no more than 30,000 characters.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
  /**
   * Timestamp of the most recent change to this announcement. Read-only.
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
class_alias(Announcement::class, 'Google_Service_Classroom_Announcement');
