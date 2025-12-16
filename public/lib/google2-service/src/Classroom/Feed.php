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

class Feed extends \Google\Model
{
  /**
   * Should never be returned or provided.
   */
  public const FEED_TYPE_FEED_TYPE_UNSPECIFIED = 'FEED_TYPE_UNSPECIFIED';
  /**
   * All roster changes for a particular domain. Notifications will be generated
   * whenever a user joins or leaves a course. No notifications will be
   * generated when an invitation is created or deleted, but notifications will
   * be generated when a user joins a course by accepting an invitation.
   */
  public const FEED_TYPE_DOMAIN_ROSTER_CHANGES = 'DOMAIN_ROSTER_CHANGES';
  /**
   * All roster changes for a particular course. Notifications will be generated
   * whenever a user joins or leaves a course. No notifications will be
   * generated when an invitation is created or deleted, but notifications will
   * be generated when a user joins a course by accepting an invitation.
   */
  public const FEED_TYPE_COURSE_ROSTER_CHANGES = 'COURSE_ROSTER_CHANGES';
  /**
   * All course work activity for a particular course. Notifications will be
   * generated when a CourseWork or StudentSubmission object is created or
   * modified. No notification will be generated when a StudentSubmission object
   * is created in connection with the creation or modification of its parent
   * CourseWork object (but a notification will be generated for that CourseWork
   * object's creation or modification).
   */
  public const FEED_TYPE_COURSE_WORK_CHANGES = 'COURSE_WORK_CHANGES';
  protected $courseRosterChangesInfoType = CourseRosterChangesInfo::class;
  protected $courseRosterChangesInfoDataType = '';
  protected $courseWorkChangesInfoType = CourseWorkChangesInfo::class;
  protected $courseWorkChangesInfoDataType = '';
  /**
   * The type of feed.
   *
   * @var string
   */
  public $feedType;

  /**
   * Information about a `Feed` with a `feed_type` of `COURSE_ROSTER_CHANGES`.
   * This field must be specified if `feed_type` is `COURSE_ROSTER_CHANGES`.
   *
   * @param CourseRosterChangesInfo $courseRosterChangesInfo
   */
  public function setCourseRosterChangesInfo(CourseRosterChangesInfo $courseRosterChangesInfo)
  {
    $this->courseRosterChangesInfo = $courseRosterChangesInfo;
  }
  /**
   * @return CourseRosterChangesInfo
   */
  public function getCourseRosterChangesInfo()
  {
    return $this->courseRosterChangesInfo;
  }
  /**
   * Information about a `Feed` with a `feed_type` of `COURSE_WORK_CHANGES`.
   * This field must be specified if `feed_type` is `COURSE_WORK_CHANGES`.
   *
   * @param CourseWorkChangesInfo $courseWorkChangesInfo
   */
  public function setCourseWorkChangesInfo(CourseWorkChangesInfo $courseWorkChangesInfo)
  {
    $this->courseWorkChangesInfo = $courseWorkChangesInfo;
  }
  /**
   * @return CourseWorkChangesInfo
   */
  public function getCourseWorkChangesInfo()
  {
    return $this->courseWorkChangesInfo;
  }
  /**
   * The type of feed.
   *
   * Accepted values: FEED_TYPE_UNSPECIFIED, DOMAIN_ROSTER_CHANGES,
   * COURSE_ROSTER_CHANGES, COURSE_WORK_CHANGES
   *
   * @param self::FEED_TYPE_* $feedType
   */
  public function setFeedType($feedType)
  {
    $this->feedType = $feedType;
  }
  /**
   * @return self::FEED_TYPE_*
   */
  public function getFeedType()
  {
    return $this->feedType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Feed::class, 'Google_Service_Classroom_Feed');
