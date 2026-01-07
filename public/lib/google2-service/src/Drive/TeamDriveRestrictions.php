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

namespace Google\Service\Drive;

class TeamDriveRestrictions extends \Google\Model
{
  /**
   * Whether administrative privileges on this Team Drive are required to modify
   * restrictions.
   *
   * @var bool
   */
  public $adminManagedRestrictions;
  /**
   * Whether the options to copy, print, or download files inside this Team
   * Drive, should be disabled for readers and commenters. When this restriction
   * is set to `true`, it will override the similarly named field to `true` for
   * any file inside this Team Drive.
   *
   * @var bool
   */
  public $copyRequiresWriterPermission;
  /**
   * Whether access to this Team Drive and items inside this Team Drive is
   * restricted to users of the domain to which this Team Drive belongs. This
   * restriction may be overridden by other sharing policies controlled outside
   * of this Team Drive.
   *
   * @var bool
   */
  public $domainUsersOnly;
  protected $downloadRestrictionType = DownloadRestriction::class;
  protected $downloadRestrictionDataType = '';
  /**
   * If true, only users with the organizer role can share folders. If false,
   * users with either the organizer role or the file organizer role can share
   * folders.
   *
   * @var bool
   */
  public $sharingFoldersRequiresOrganizerPermission;
  /**
   * Whether access to items inside this Team Drive is restricted to members of
   * this Team Drive.
   *
   * @var bool
   */
  public $teamMembersOnly;

  /**
   * Whether administrative privileges on this Team Drive are required to modify
   * restrictions.
   *
   * @param bool $adminManagedRestrictions
   */
  public function setAdminManagedRestrictions($adminManagedRestrictions)
  {
    $this->adminManagedRestrictions = $adminManagedRestrictions;
  }
  /**
   * @return bool
   */
  public function getAdminManagedRestrictions()
  {
    return $this->adminManagedRestrictions;
  }
  /**
   * Whether the options to copy, print, or download files inside this Team
   * Drive, should be disabled for readers and commenters. When this restriction
   * is set to `true`, it will override the similarly named field to `true` for
   * any file inside this Team Drive.
   *
   * @param bool $copyRequiresWriterPermission
   */
  public function setCopyRequiresWriterPermission($copyRequiresWriterPermission)
  {
    $this->copyRequiresWriterPermission = $copyRequiresWriterPermission;
  }
  /**
   * @return bool
   */
  public function getCopyRequiresWriterPermission()
  {
    return $this->copyRequiresWriterPermission;
  }
  /**
   * Whether access to this Team Drive and items inside this Team Drive is
   * restricted to users of the domain to which this Team Drive belongs. This
   * restriction may be overridden by other sharing policies controlled outside
   * of this Team Drive.
   *
   * @param bool $domainUsersOnly
   */
  public function setDomainUsersOnly($domainUsersOnly)
  {
    $this->domainUsersOnly = $domainUsersOnly;
  }
  /**
   * @return bool
   */
  public function getDomainUsersOnly()
  {
    return $this->domainUsersOnly;
  }
  /**
   * Download restrictions applied by shared drive managers.
   *
   * @param DownloadRestriction $downloadRestriction
   */
  public function setDownloadRestriction(DownloadRestriction $downloadRestriction)
  {
    $this->downloadRestriction = $downloadRestriction;
  }
  /**
   * @return DownloadRestriction
   */
  public function getDownloadRestriction()
  {
    return $this->downloadRestriction;
  }
  /**
   * If true, only users with the organizer role can share folders. If false,
   * users with either the organizer role or the file organizer role can share
   * folders.
   *
   * @param bool $sharingFoldersRequiresOrganizerPermission
   */
  public function setSharingFoldersRequiresOrganizerPermission($sharingFoldersRequiresOrganizerPermission)
  {
    $this->sharingFoldersRequiresOrganizerPermission = $sharingFoldersRequiresOrganizerPermission;
  }
  /**
   * @return bool
   */
  public function getSharingFoldersRequiresOrganizerPermission()
  {
    return $this->sharingFoldersRequiresOrganizerPermission;
  }
  /**
   * Whether access to items inside this Team Drive is restricted to members of
   * this Team Drive.
   *
   * @param bool $teamMembersOnly
   */
  public function setTeamMembersOnly($teamMembersOnly)
  {
    $this->teamMembersOnly = $teamMembersOnly;
  }
  /**
   * @return bool
   */
  public function getTeamMembersOnly()
  {
    return $this->teamMembersOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TeamDriveRestrictions::class, 'Google_Service_Drive_TeamDriveRestrictions');
