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

class TeamDriveCapabilities extends \Google\Model
{
  /**
   * Whether the current user can add children to folders in this Team Drive.
   *
   * @var bool
   */
  public $canAddChildren;
  /**
   * Whether the current user can change the `copyRequiresWriterPermission`
   * restriction of this Team Drive.
   *
   * @var bool
   */
  public $canChangeCopyRequiresWriterPermissionRestriction;
  /**
   * Whether the current user can change the `domainUsersOnly` restriction of
   * this Team Drive.
   *
   * @var bool
   */
  public $canChangeDomainUsersOnlyRestriction;
  /**
   * Whether the current user can change organizer-applied download restrictions
   * of this shared drive.
   *
   * @var bool
   */
  public $canChangeDownloadRestriction;
  /**
   * Whether the current user can change the
   * `sharingFoldersRequiresOrganizerPermission` restriction of this Team Drive.
   *
   * @var bool
   */
  public $canChangeSharingFoldersRequiresOrganizerPermissionRestriction;
  /**
   * Whether the current user can change the background of this Team Drive.
   *
   * @var bool
   */
  public $canChangeTeamDriveBackground;
  /**
   * Whether the current user can change the `teamMembersOnly` restriction of
   * this Team Drive.
   *
   * @var bool
   */
  public $canChangeTeamMembersOnlyRestriction;
  /**
   * Whether the current user can comment on files in this Team Drive.
   *
   * @var bool
   */
  public $canComment;
  /**
   * Whether the current user can copy files in this Team Drive.
   *
   * @var bool
   */
  public $canCopy;
  /**
   * Whether the current user can delete children from folders in this Team
   * Drive.
   *
   * @var bool
   */
  public $canDeleteChildren;
  /**
   * Whether the current user can delete this Team Drive. Attempting to delete
   * the Team Drive may still fail if there are untrashed items inside the Team
   * Drive.
   *
   * @var bool
   */
  public $canDeleteTeamDrive;
  /**
   * Whether the current user can download files in this Team Drive.
   *
   * @var bool
   */
  public $canDownload;
  /**
   * Whether the current user can edit files in this Team Drive
   *
   * @var bool
   */
  public $canEdit;
  /**
   * Whether the current user can list the children of folders in this Team
   * Drive.
   *
   * @var bool
   */
  public $canListChildren;
  /**
   * Whether the current user can add members to this Team Drive or remove them
   * or change their role.
   *
   * @var bool
   */
  public $canManageMembers;
  /**
   * Whether the current user can read the revisions resource of files in this
   * Team Drive.
   *
   * @var bool
   */
  public $canReadRevisions;
  /**
   * Deprecated: Use `canDeleteChildren` or `canTrashChildren` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canRemoveChildren;
  /**
   * Whether the current user can rename files or folders in this Team Drive.
   *
   * @var bool
   */
  public $canRename;
  /**
   * Whether the current user can rename this Team Drive.
   *
   * @var bool
   */
  public $canRenameTeamDrive;
  /**
   * Whether the current user can reset the Team Drive restrictions to defaults.
   *
   * @var bool
   */
  public $canResetTeamDriveRestrictions;
  /**
   * Whether the current user can share files or folders in this Team Drive.
   *
   * @var bool
   */
  public $canShare;
  /**
   * Whether the current user can trash children from folders in this Team
   * Drive.
   *
   * @var bool
   */
  public $canTrashChildren;

  /**
   * Whether the current user can add children to folders in this Team Drive.
   *
   * @param bool $canAddChildren
   */
  public function setCanAddChildren($canAddChildren)
  {
    $this->canAddChildren = $canAddChildren;
  }
  /**
   * @return bool
   */
  public function getCanAddChildren()
  {
    return $this->canAddChildren;
  }
  /**
   * Whether the current user can change the `copyRequiresWriterPermission`
   * restriction of this Team Drive.
   *
   * @param bool $canChangeCopyRequiresWriterPermissionRestriction
   */
  public function setCanChangeCopyRequiresWriterPermissionRestriction($canChangeCopyRequiresWriterPermissionRestriction)
  {
    $this->canChangeCopyRequiresWriterPermissionRestriction = $canChangeCopyRequiresWriterPermissionRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeCopyRequiresWriterPermissionRestriction()
  {
    return $this->canChangeCopyRequiresWriterPermissionRestriction;
  }
  /**
   * Whether the current user can change the `domainUsersOnly` restriction of
   * this Team Drive.
   *
   * @param bool $canChangeDomainUsersOnlyRestriction
   */
  public function setCanChangeDomainUsersOnlyRestriction($canChangeDomainUsersOnlyRestriction)
  {
    $this->canChangeDomainUsersOnlyRestriction = $canChangeDomainUsersOnlyRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeDomainUsersOnlyRestriction()
  {
    return $this->canChangeDomainUsersOnlyRestriction;
  }
  /**
   * Whether the current user can change organizer-applied download restrictions
   * of this shared drive.
   *
   * @param bool $canChangeDownloadRestriction
   */
  public function setCanChangeDownloadRestriction($canChangeDownloadRestriction)
  {
    $this->canChangeDownloadRestriction = $canChangeDownloadRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeDownloadRestriction()
  {
    return $this->canChangeDownloadRestriction;
  }
  /**
   * Whether the current user can change the
   * `sharingFoldersRequiresOrganizerPermission` restriction of this Team Drive.
   *
   * @param bool $canChangeSharingFoldersRequiresOrganizerPermissionRestriction
   */
  public function setCanChangeSharingFoldersRequiresOrganizerPermissionRestriction($canChangeSharingFoldersRequiresOrganizerPermissionRestriction)
  {
    $this->canChangeSharingFoldersRequiresOrganizerPermissionRestriction = $canChangeSharingFoldersRequiresOrganizerPermissionRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeSharingFoldersRequiresOrganizerPermissionRestriction()
  {
    return $this->canChangeSharingFoldersRequiresOrganizerPermissionRestriction;
  }
  /**
   * Whether the current user can change the background of this Team Drive.
   *
   * @param bool $canChangeTeamDriveBackground
   */
  public function setCanChangeTeamDriveBackground($canChangeTeamDriveBackground)
  {
    $this->canChangeTeamDriveBackground = $canChangeTeamDriveBackground;
  }
  /**
   * @return bool
   */
  public function getCanChangeTeamDriveBackground()
  {
    return $this->canChangeTeamDriveBackground;
  }
  /**
   * Whether the current user can change the `teamMembersOnly` restriction of
   * this Team Drive.
   *
   * @param bool $canChangeTeamMembersOnlyRestriction
   */
  public function setCanChangeTeamMembersOnlyRestriction($canChangeTeamMembersOnlyRestriction)
  {
    $this->canChangeTeamMembersOnlyRestriction = $canChangeTeamMembersOnlyRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeTeamMembersOnlyRestriction()
  {
    return $this->canChangeTeamMembersOnlyRestriction;
  }
  /**
   * Whether the current user can comment on files in this Team Drive.
   *
   * @param bool $canComment
   */
  public function setCanComment($canComment)
  {
    $this->canComment = $canComment;
  }
  /**
   * @return bool
   */
  public function getCanComment()
  {
    return $this->canComment;
  }
  /**
   * Whether the current user can copy files in this Team Drive.
   *
   * @param bool $canCopy
   */
  public function setCanCopy($canCopy)
  {
    $this->canCopy = $canCopy;
  }
  /**
   * @return bool
   */
  public function getCanCopy()
  {
    return $this->canCopy;
  }
  /**
   * Whether the current user can delete children from folders in this Team
   * Drive.
   *
   * @param bool $canDeleteChildren
   */
  public function setCanDeleteChildren($canDeleteChildren)
  {
    $this->canDeleteChildren = $canDeleteChildren;
  }
  /**
   * @return bool
   */
  public function getCanDeleteChildren()
  {
    return $this->canDeleteChildren;
  }
  /**
   * Whether the current user can delete this Team Drive. Attempting to delete
   * the Team Drive may still fail if there are untrashed items inside the Team
   * Drive.
   *
   * @param bool $canDeleteTeamDrive
   */
  public function setCanDeleteTeamDrive($canDeleteTeamDrive)
  {
    $this->canDeleteTeamDrive = $canDeleteTeamDrive;
  }
  /**
   * @return bool
   */
  public function getCanDeleteTeamDrive()
  {
    return $this->canDeleteTeamDrive;
  }
  /**
   * Whether the current user can download files in this Team Drive.
   *
   * @param bool $canDownload
   */
  public function setCanDownload($canDownload)
  {
    $this->canDownload = $canDownload;
  }
  /**
   * @return bool
   */
  public function getCanDownload()
  {
    return $this->canDownload;
  }
  /**
   * Whether the current user can edit files in this Team Drive
   *
   * @param bool $canEdit
   */
  public function setCanEdit($canEdit)
  {
    $this->canEdit = $canEdit;
  }
  /**
   * @return bool
   */
  public function getCanEdit()
  {
    return $this->canEdit;
  }
  /**
   * Whether the current user can list the children of folders in this Team
   * Drive.
   *
   * @param bool $canListChildren
   */
  public function setCanListChildren($canListChildren)
  {
    $this->canListChildren = $canListChildren;
  }
  /**
   * @return bool
   */
  public function getCanListChildren()
  {
    return $this->canListChildren;
  }
  /**
   * Whether the current user can add members to this Team Drive or remove them
   * or change their role.
   *
   * @param bool $canManageMembers
   */
  public function setCanManageMembers($canManageMembers)
  {
    $this->canManageMembers = $canManageMembers;
  }
  /**
   * @return bool
   */
  public function getCanManageMembers()
  {
    return $this->canManageMembers;
  }
  /**
   * Whether the current user can read the revisions resource of files in this
   * Team Drive.
   *
   * @param bool $canReadRevisions
   */
  public function setCanReadRevisions($canReadRevisions)
  {
    $this->canReadRevisions = $canReadRevisions;
  }
  /**
   * @return bool
   */
  public function getCanReadRevisions()
  {
    return $this->canReadRevisions;
  }
  /**
   * Deprecated: Use `canDeleteChildren` or `canTrashChildren` instead.
   *
   * @deprecated
   * @param bool $canRemoveChildren
   */
  public function setCanRemoveChildren($canRemoveChildren)
  {
    $this->canRemoveChildren = $canRemoveChildren;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanRemoveChildren()
  {
    return $this->canRemoveChildren;
  }
  /**
   * Whether the current user can rename files or folders in this Team Drive.
   *
   * @param bool $canRename
   */
  public function setCanRename($canRename)
  {
    $this->canRename = $canRename;
  }
  /**
   * @return bool
   */
  public function getCanRename()
  {
    return $this->canRename;
  }
  /**
   * Whether the current user can rename this Team Drive.
   *
   * @param bool $canRenameTeamDrive
   */
  public function setCanRenameTeamDrive($canRenameTeamDrive)
  {
    $this->canRenameTeamDrive = $canRenameTeamDrive;
  }
  /**
   * @return bool
   */
  public function getCanRenameTeamDrive()
  {
    return $this->canRenameTeamDrive;
  }
  /**
   * Whether the current user can reset the Team Drive restrictions to defaults.
   *
   * @param bool $canResetTeamDriveRestrictions
   */
  public function setCanResetTeamDriveRestrictions($canResetTeamDriveRestrictions)
  {
    $this->canResetTeamDriveRestrictions = $canResetTeamDriveRestrictions;
  }
  /**
   * @return bool
   */
  public function getCanResetTeamDriveRestrictions()
  {
    return $this->canResetTeamDriveRestrictions;
  }
  /**
   * Whether the current user can share files or folders in this Team Drive.
   *
   * @param bool $canShare
   */
  public function setCanShare($canShare)
  {
    $this->canShare = $canShare;
  }
  /**
   * @return bool
   */
  public function getCanShare()
  {
    return $this->canShare;
  }
  /**
   * Whether the current user can trash children from folders in this Team
   * Drive.
   *
   * @param bool $canTrashChildren
   */
  public function setCanTrashChildren($canTrashChildren)
  {
    $this->canTrashChildren = $canTrashChildren;
  }
  /**
   * @return bool
   */
  public function getCanTrashChildren()
  {
    return $this->canTrashChildren;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TeamDriveCapabilities::class, 'Google_Service_Drive_TeamDriveCapabilities');
