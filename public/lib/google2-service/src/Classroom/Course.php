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

class Course extends \Google\Collection
{
  /**
   * No course state. No returned Course message will use this value.
   */
  public const COURSE_STATE_COURSE_STATE_UNSPECIFIED = 'COURSE_STATE_UNSPECIFIED';
  /**
   * The course is active.
   */
  public const COURSE_STATE_ACTIVE = 'ACTIVE';
  /**
   * The course has been archived. You cannot modify it except to change it to a
   * different state.
   */
  public const COURSE_STATE_ARCHIVED = 'ARCHIVED';
  /**
   * The course has been created, but not yet activated. It is accessible by the
   * primary teacher and domain administrators, who may modify it or change it
   * to the `ACTIVE` or `DECLINED` states. A course may only be changed to
   * `PROVISIONED` if it is in the `DECLINED` state.
   */
  public const COURSE_STATE_PROVISIONED = 'PROVISIONED';
  /**
   * The course has been created, but declined. It is accessible by the course
   * owner and domain administrators, though it will not be displayed in the web
   * UI. You cannot modify the course except to change it to the `PROVISIONED`
   * state. A course may only be changed to `DECLINED` if it is in the
   * `PROVISIONED` state.
   */
  public const COURSE_STATE_DECLINED = 'DECLINED';
  /**
   * The course has been suspended. You cannot modify the course, and only the
   * user identified by the `owner_id` can view the course. A course may be
   * placed in this state if it potentially violates the Terms of Service.
   */
  public const COURSE_STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'courseMaterialSets';
  /**
   * Absolute link to this course in the Classroom web UI. Read-only.
   *
   * @var string
   */
  public $alternateLink;
  /**
   * The Calendar ID for a calendar that all course members can see, to which
   * Classroom adds events for course work and announcements in the course. The
   * Calendar for a course is created asynchronously when the course is set to
   * `CourseState.ACTIVE` for the first time (at creation time or when it is
   * updated to `ACTIVE` through the UI or the API). The Calendar ID will not be
   * populated until the creation process is completed. Read-only.
   *
   * @var string
   */
  public $calendarId;
  /**
   * The email address of a Google group containing all members of the course.
   * This group does not accept email and can only be used for permissions.
   * Read-only.
   *
   * @var string
   */
  public $courseGroupEmail;
  protected $courseMaterialSetsType = CourseMaterialSet::class;
  protected $courseMaterialSetsDataType = 'array';
  /**
   * State of the course. If unspecified, the default state is `PROVISIONED`.
   *
   * @var string
   */
  public $courseState;
  /**
   * Creation time of the course. Specifying this field in a course update mask
   * results in an error. Read-only.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Optional description. For example, "We'll be learning about the structure
   * of living creatures from a combination of textbooks, guest lectures, and
   * lab work. Expect to be excited!" If set, this field must be a valid UTF-8
   * string and no longer than 30,000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional heading for the description. For example, "Welcome to 10th Grade
   * Biology." If set, this field must be a valid UTF-8 string and no longer
   * than 3600 characters.
   *
   * @var string
   */
  public $descriptionHeading;
  /**
   * Enrollment code to use when joining this course. Specifying this field in a
   * course update mask results in an error. Read-only.
   *
   * @var string
   */
  public $enrollmentCode;
  protected $gradebookSettingsType = GradebookSettings::class;
  protected $gradebookSettingsDataType = '';
  /**
   * Whether or not guardian notifications are enabled for this course. Read-
   * only.
   *
   * @var bool
   */
  public $guardiansEnabled;
  /**
   * Identifier for this course assigned by Classroom. When creating a course,
   * you may optionally set this identifier to an alias string in the request to
   * create a corresponding alias. The `id` is still assigned by Classroom and
   * cannot be updated after the course is created. Specifying this field in a
   * course update mask results in an error.
   *
   * @var string
   */
  public $id;
  /**
   * Name of the course. For example, "10th Grade Biology". The name is
   * required. It must be between 1 and 750 characters and a valid UTF-8 string.
   *
   * @var string
   */
  public $name;
  /**
   * The identifier of the owner of a course. When specified as a parameter of a
   * create course request, this field is required. The identifier can be one of
   * the following: * the numeric identifier for the user * the email address of
   * the user * the string literal `"me"`, indicating the requesting user This
   * must be set in a create request. Admins can also specify this field in a
   * patch course request to transfer ownership. In other contexts, it is read-
   * only.
   *
   * @var string
   */
  public $ownerId;
  /**
   * Optional room location. For example, "301". If set, this field must be a
   * valid UTF-8 string and no longer than 650 characters.
   *
   * @var string
   */
  public $room;
  /**
   * Section of the course. For example, "Period 2". If set, this field must be
   * a valid UTF-8 string and no longer than 2800 characters.
   *
   * @var string
   */
  public $section;
  protected $teacherFolderType = DriveFolder::class;
  protected $teacherFolderDataType = '';
  /**
   * The email address of a Google group containing all teachers of the course.
   * This group does not accept email and can only be used for permissions.
   * Read-only.
   *
   * @var string
   */
  public $teacherGroupEmail;
  /**
   * Time of the most recent update to this course. Specifying this field in a
   * course update mask results in an error. Read-only.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Absolute link to this course in the Classroom web UI. Read-only.
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
   * The Calendar ID for a calendar that all course members can see, to which
   * Classroom adds events for course work and announcements in the course. The
   * Calendar for a course is created asynchronously when the course is set to
   * `CourseState.ACTIVE` for the first time (at creation time or when it is
   * updated to `ACTIVE` through the UI or the API). The Calendar ID will not be
   * populated until the creation process is completed. Read-only.
   *
   * @param string $calendarId
   */
  public function setCalendarId($calendarId)
  {
    $this->calendarId = $calendarId;
  }
  /**
   * @return string
   */
  public function getCalendarId()
  {
    return $this->calendarId;
  }
  /**
   * The email address of a Google group containing all members of the course.
   * This group does not accept email and can only be used for permissions.
   * Read-only.
   *
   * @param string $courseGroupEmail
   */
  public function setCourseGroupEmail($courseGroupEmail)
  {
    $this->courseGroupEmail = $courseGroupEmail;
  }
  /**
   * @return string
   */
  public function getCourseGroupEmail()
  {
    return $this->courseGroupEmail;
  }
  /**
   * Sets of materials that appear on the "about" page of this course. Read-
   * only.
   *
   * @deprecated
   * @param CourseMaterialSet[] $courseMaterialSets
   */
  public function setCourseMaterialSets($courseMaterialSets)
  {
    $this->courseMaterialSets = $courseMaterialSets;
  }
  /**
   * @deprecated
   * @return CourseMaterialSet[]
   */
  public function getCourseMaterialSets()
  {
    return $this->courseMaterialSets;
  }
  /**
   * State of the course. If unspecified, the default state is `PROVISIONED`.
   *
   * Accepted values: COURSE_STATE_UNSPECIFIED, ACTIVE, ARCHIVED, PROVISIONED,
   * DECLINED, SUSPENDED
   *
   * @param self::COURSE_STATE_* $courseState
   */
  public function setCourseState($courseState)
  {
    $this->courseState = $courseState;
  }
  /**
   * @return self::COURSE_STATE_*
   */
  public function getCourseState()
  {
    return $this->courseState;
  }
  /**
   * Creation time of the course. Specifying this field in a course update mask
   * results in an error. Read-only.
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
   * Optional description. For example, "We'll be learning about the structure
   * of living creatures from a combination of textbooks, guest lectures, and
   * lab work. Expect to be excited!" If set, this field must be a valid UTF-8
   * string and no longer than 30,000 characters.
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
   * Optional heading for the description. For example, "Welcome to 10th Grade
   * Biology." If set, this field must be a valid UTF-8 string and no longer
   * than 3600 characters.
   *
   * @param string $descriptionHeading
   */
  public function setDescriptionHeading($descriptionHeading)
  {
    $this->descriptionHeading = $descriptionHeading;
  }
  /**
   * @return string
   */
  public function getDescriptionHeading()
  {
    return $this->descriptionHeading;
  }
  /**
   * Enrollment code to use when joining this course. Specifying this field in a
   * course update mask results in an error. Read-only.
   *
   * @param string $enrollmentCode
   */
  public function setEnrollmentCode($enrollmentCode)
  {
    $this->enrollmentCode = $enrollmentCode;
  }
  /**
   * @return string
   */
  public function getEnrollmentCode()
  {
    return $this->enrollmentCode;
  }
  /**
   * The gradebook settings that specify how a student's overall grade for the
   * course will be calculated and who it will be displayed to. Read-only.
   *
   * @param GradebookSettings $gradebookSettings
   */
  public function setGradebookSettings(GradebookSettings $gradebookSettings)
  {
    $this->gradebookSettings = $gradebookSettings;
  }
  /**
   * @return GradebookSettings
   */
  public function getGradebookSettings()
  {
    return $this->gradebookSettings;
  }
  /**
   * Whether or not guardian notifications are enabled for this course. Read-
   * only.
   *
   * @param bool $guardiansEnabled
   */
  public function setGuardiansEnabled($guardiansEnabled)
  {
    $this->guardiansEnabled = $guardiansEnabled;
  }
  /**
   * @return bool
   */
  public function getGuardiansEnabled()
  {
    return $this->guardiansEnabled;
  }
  /**
   * Identifier for this course assigned by Classroom. When creating a course,
   * you may optionally set this identifier to an alias string in the request to
   * create a corresponding alias. The `id` is still assigned by Classroom and
   * cannot be updated after the course is created. Specifying this field in a
   * course update mask results in an error.
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
   * Name of the course. For example, "10th Grade Biology". The name is
   * required. It must be between 1 and 750 characters and a valid UTF-8 string.
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
   * The identifier of the owner of a course. When specified as a parameter of a
   * create course request, this field is required. The identifier can be one of
   * the following: * the numeric identifier for the user * the email address of
   * the user * the string literal `"me"`, indicating the requesting user This
   * must be set in a create request. Admins can also specify this field in a
   * patch course request to transfer ownership. In other contexts, it is read-
   * only.
   *
   * @param string $ownerId
   */
  public function setOwnerId($ownerId)
  {
    $this->ownerId = $ownerId;
  }
  /**
   * @return string
   */
  public function getOwnerId()
  {
    return $this->ownerId;
  }
  /**
   * Optional room location. For example, "301". If set, this field must be a
   * valid UTF-8 string and no longer than 650 characters.
   *
   * @param string $room
   */
  public function setRoom($room)
  {
    $this->room = $room;
  }
  /**
   * @return string
   */
  public function getRoom()
  {
    return $this->room;
  }
  /**
   * Section of the course. For example, "Period 2". If set, this field must be
   * a valid UTF-8 string and no longer than 2800 characters.
   *
   * @param string $section
   */
  public function setSection($section)
  {
    $this->section = $section;
  }
  /**
   * @return string
   */
  public function getSection()
  {
    return $this->section;
  }
  /**
   * Information about a Drive Folder that is shared with all teachers of the
   * course. This field will only be set for teachers of the course and domain
   * administrators. Read-only.
   *
   * @param DriveFolder $teacherFolder
   */
  public function setTeacherFolder(DriveFolder $teacherFolder)
  {
    $this->teacherFolder = $teacherFolder;
  }
  /**
   * @return DriveFolder
   */
  public function getTeacherFolder()
  {
    return $this->teacherFolder;
  }
  /**
   * The email address of a Google group containing all teachers of the course.
   * This group does not accept email and can only be used for permissions.
   * Read-only.
   *
   * @param string $teacherGroupEmail
   */
  public function setTeacherGroupEmail($teacherGroupEmail)
  {
    $this->teacherGroupEmail = $teacherGroupEmail;
  }
  /**
   * @return string
   */
  public function getTeacherGroupEmail()
  {
    return $this->teacherGroupEmail;
  }
  /**
   * Time of the most recent update to this course. Specifying this field in a
   * course update mask results in an error. Read-only.
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
class_alias(Course::class, 'Google_Service_Classroom_Course');
