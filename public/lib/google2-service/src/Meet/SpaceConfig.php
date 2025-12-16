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

namespace Google\Service\Meet;

class SpaceConfig extends \Google\Model
{
  /**
   * Default value specified by the user's organization. Note: This is never
   * returned, as the configured access type is returned instead.
   */
  public const ACCESS_TYPE_ACCESS_TYPE_UNSPECIFIED = 'ACCESS_TYPE_UNSPECIFIED';
  /**
   * Anyone with the join information (for example, the URL or phone access
   * information) can join without knocking.
   */
  public const ACCESS_TYPE_OPEN = 'OPEN';
  /**
   * Members of the host's organization, invited external users, and dial-in
   * users can join without knocking. Everyone else must knock.
   */
  public const ACCESS_TYPE_TRUSTED = 'TRUSTED';
  /**
   * Only invitees can join without knocking. Everyone else must knock.
   */
  public const ACCESS_TYPE_RESTRICTED = 'RESTRICTED';
  /**
   * Default value specified by user policy. This should never be returned.
   */
  public const ATTENDANCE_REPORT_GENERATION_TYPE_ATTENDANCE_REPORT_GENERATION_TYPE_UNSPECIFIED = 'ATTENDANCE_REPORT_GENERATION_TYPE_UNSPECIFIED';
  /**
   * Attendance report will be generated and sent to drive/email.
   */
  public const ATTENDANCE_REPORT_GENERATION_TYPE_GENERATE_REPORT = 'GENERATE_REPORT';
  /**
   * Attendance report will not be generated.
   */
  public const ATTENDANCE_REPORT_GENERATION_TYPE_DO_NOT_GENERATE = 'DO_NOT_GENERATE';
  /**
   * Unused.
   */
  public const ENTRY_POINT_ACCESS_ENTRY_POINT_ACCESS_UNSPECIFIED = 'ENTRY_POINT_ACCESS_UNSPECIFIED';
  /**
   * All entry points are allowed.
   */
  public const ENTRY_POINT_ACCESS_ALL = 'ALL';
  /**
   * Only entry points owned by the Google Cloud project that created the space
   * can be used to join meetings in this space. Apps can use the Meet Embed SDK
   * Web or mobile Meet SDKs to create owned entry points.
   */
  public const ENTRY_POINT_ACCESS_CREATOR_APP_ONLY = 'CREATOR_APP_ONLY';
  /**
   * Moderation type is not specified. This is used to indicate the user hasn't
   * specified any value as the user does not intend to update the state. Users
   * are not allowed to set the value as unspecified.
   */
  public const MODERATION_MODERATION_UNSPECIFIED = 'MODERATION_UNSPECIFIED';
  /**
   * Moderation is off.
   */
  public const MODERATION_OFF = 'OFF';
  /**
   * Moderation is on.
   */
  public const MODERATION_ON = 'ON';
  /**
   * Access type of the meeting space that determines who can join without
   * knocking. Default: The user's default access settings. Controlled by the
   * user's admin for enterprise users or RESTRICTED.
   *
   * @var string
   */
  public $accessType;
  protected $artifactConfigType = ArtifactConfig::class;
  protected $artifactConfigDataType = '';
  /**
   * Whether attendance report is enabled for the meeting space.
   *
   * @var string
   */
  public $attendanceReportGenerationType;
  /**
   * Defines the entry points that can be used to join meetings hosted in this
   * meeting space. Default: EntryPointAccess.ALL
   *
   * @var string
   */
  public $entryPointAccess;
  /**
   * The pre-configured moderation mode for the Meeting. Default: Controlled by
   * the user's policies.
   *
   * @var string
   */
  public $moderation;
  protected $moderationRestrictionsType = ModerationRestrictions::class;
  protected $moderationRestrictionsDataType = '';

  /**
   * Access type of the meeting space that determines who can join without
   * knocking. Default: The user's default access settings. Controlled by the
   * user's admin for enterprise users or RESTRICTED.
   *
   * Accepted values: ACCESS_TYPE_UNSPECIFIED, OPEN, TRUSTED, RESTRICTED
   *
   * @param self::ACCESS_TYPE_* $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return self::ACCESS_TYPE_*
   */
  public function getAccessType()
  {
    return $this->accessType;
  }
  /**
   * Configuration pertaining to the auto-generated artifacts that the meeting
   * supports.
   *
   * @param ArtifactConfig $artifactConfig
   */
  public function setArtifactConfig(ArtifactConfig $artifactConfig)
  {
    $this->artifactConfig = $artifactConfig;
  }
  /**
   * @return ArtifactConfig
   */
  public function getArtifactConfig()
  {
    return $this->artifactConfig;
  }
  /**
   * Whether attendance report is enabled for the meeting space.
   *
   * Accepted values: ATTENDANCE_REPORT_GENERATION_TYPE_UNSPECIFIED,
   * GENERATE_REPORT, DO_NOT_GENERATE
   *
   * @param self::ATTENDANCE_REPORT_GENERATION_TYPE_* $attendanceReportGenerationType
   */
  public function setAttendanceReportGenerationType($attendanceReportGenerationType)
  {
    $this->attendanceReportGenerationType = $attendanceReportGenerationType;
  }
  /**
   * @return self::ATTENDANCE_REPORT_GENERATION_TYPE_*
   */
  public function getAttendanceReportGenerationType()
  {
    return $this->attendanceReportGenerationType;
  }
  /**
   * Defines the entry points that can be used to join meetings hosted in this
   * meeting space. Default: EntryPointAccess.ALL
   *
   * Accepted values: ENTRY_POINT_ACCESS_UNSPECIFIED, ALL, CREATOR_APP_ONLY
   *
   * @param self::ENTRY_POINT_ACCESS_* $entryPointAccess
   */
  public function setEntryPointAccess($entryPointAccess)
  {
    $this->entryPointAccess = $entryPointAccess;
  }
  /**
   * @return self::ENTRY_POINT_ACCESS_*
   */
  public function getEntryPointAccess()
  {
    return $this->entryPointAccess;
  }
  /**
   * The pre-configured moderation mode for the Meeting. Default: Controlled by
   * the user's policies.
   *
   * Accepted values: MODERATION_UNSPECIFIED, OFF, ON
   *
   * @param self::MODERATION_* $moderation
   */
  public function setModeration($moderation)
  {
    $this->moderation = $moderation;
  }
  /**
   * @return self::MODERATION_*
   */
  public function getModeration()
  {
    return $this->moderation;
  }
  /**
   * When moderation.ON, these restrictions go into effect for the meeting. When
   * moderation.OFF, will be reset to default ModerationRestrictions.
   *
   * @param ModerationRestrictions $moderationRestrictions
   */
  public function setModerationRestrictions(ModerationRestrictions $moderationRestrictions)
  {
    $this->moderationRestrictions = $moderationRestrictions;
  }
  /**
   * @return ModerationRestrictions
   */
  public function getModerationRestrictions()
  {
    return $this->moderationRestrictions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpaceConfig::class, 'Google_Service_Meet_SpaceConfig');
