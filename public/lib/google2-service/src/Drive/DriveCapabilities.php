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

class DriveCapabilities extends \Google\Model
{
  /**
   * Output only. Whether the current user can add children to folders in this
   * shared drive.
   *
   * @var bool
   */
  public $canAddChildren;
  /**
   * Output only. Whether the current user can change the
   * `copyRequiresWriterPermission` restriction of this shared drive.
   *
   * @var bool
   */
  public $canChangeCopyRequiresWriterPermissionRestriction;
  /**
   * Output only. Whether the current user can change the `domainUsersOnly`
   * restriction of this shared drive.
   *
   * @var bool
   */
  public $canChangeDomainUsersOnlyRestriction;
  /**
   * Output only. Whether the current user can change organizer-applied download
   * restrictions of this shared drive.
   *
   * @var bool
   */
  public $canChangeDownloadRestriction;
  /**
   * Output only. Whether the current user can change the background of this
   * shared drive.
   *
   * @var bool
   */
  public $canChangeDriveBackground;
  /**
   * Output only. Whether the current user can change the `driveMembersOnly`
   * restriction of this shared drive.
   *
   * @var bool
   */
  public $canChangeDriveMembersOnlyRestriction;
  /**
   * Output only. Whether the current user can change the
   * `sharingFoldersRequiresOrganizerPermission` restriction of this shared
   * drive.
   *
   * @var bool
   */
  public $canChangeSharingFoldersRequiresOrganizerPermissionRestriction;
  /**
   * Output only. Whether the current user can comment on files in this shared
   * drive.
   *
   * @var bool
   */
  public $canComment;
  /**
   * Output only. Whether the current user can copy files in this shared drive.
   *
   * @var bool
   */
  public $canCopy;
  /**
   * Output only. Whether the current user can delete children from folders in
   * this shared drive.
   *
   * @var bool
   */
  public $canDeleteChildren;
  /**
   * Output only. Whether the current user can delete this shared drive.
   * Attempting to delete the shared drive may still fail if there are untrashed
   * items inside the shared drive.
   *
   * @var bool
   */
  public $canDeleteDrive;
  /**
   * Output only. Whether the current user can download files in this shared
   * drive.
   *
   * @var bool
   */
  public $canDownload;
  /**
   * Output only. Whether the current user can edit files in this shared drive
   *
   * @var bool
   */
  public $canEdit;
  /**
   * Output only. Whether the current user can list the children of folders in
   * this shared drive.
   *
   * @var bool
   */
  public $canListChildren;
  /**
   * Output only. Whether the current user can add members to this shared drive
   * or remove them or change their role.
   *
   * @var bool
   */
  public $canManageMembers;
  /**
   * Output only. Whether the current user can read the revisions resource of
   * files in this shared drive.
   *
   * @var bool
   */
  public $canReadRevisions;
  /**
   * Output only. Whether the current user can rename files or folders in this
   * shared drive.
   *
   * @var bool
   */
  public $canRename;
  /**
   * Output only. Whether the current user can rename this shared drive.
   *
   * @var bool
   */
  public $canRenameDrive;
  /**
   * Output only. Whether the current user can reset the shared drive
   * restrictions to defaults.
   *
   * @var bool
   */
  public $canResetDriveRestrictions;
  /**
   * Output only. Whether the current user can share files or folders in this
   * shared drive.
   *
   * @var bool
   */
  public $canShare;
  /**
   * Output only. Whether the current user can trash children from folders in
   * this shared drive.
   *
   * @var bool
   */
  public $canTrashChildren;

  /**
   * Output only. Whether the current user can add children to folders in this
   * shared drive.
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
   * Output only. Whether the current user can change the
   * `copyRequiresWriterPermission` restriction of this shared drive.
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
   * Output only. Whether the current user can change the `domainUsersOnly`
   * restriction of this shared drive.
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
   * Output only. Whether the current user can change organizer-applied download
   * restrictions of this shared drive.
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
   * Output only. Whether the current user can change the background of this
   * shared drive.
   *
   * @param bool $canChangeDriveBackground
   */
  public function setCanChangeDriveBackground($canChangeDriveBackground)
  {
    $this->canChangeDriveBackground = $canChangeDriveBackground;
  }
  /**
   * @return bool
   */
  public function getCanChangeDriveBackground()
  {
    return $this->canChangeDriveBackground;
  }
  /**
   * Output only. Whether the current user can change the `driveMembersOnly`
   * restriction of this shared drive.
   *
   * @param bool $canChangeDriveMembersOnlyRestriction
   */
  public function setCanChangeDriveMembersOnlyRestriction($canChangeDriveMembersOnlyRestriction)
  {
    $this->canChangeDriveMembersOnlyRestriction = $canChangeDriveMembersOnlyRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeDriveMembersOnlyRestriction()
  {
    return $this->canChangeDriveMembersOnlyRestriction;
  }
  /**
   * Output only. Whether the current user can change the
   * `sharingFoldersRequiresOrganizerPermission` restriction of this shared
   * drive.
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
   * Output only. Whether the current user can comment on files in this shared
   * drive.
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
   * Output only. Whether the current user can copy files in this shared drive.
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
   * Output only. Whether the current user can delete children from folders in
   * this shared drive.
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
   * Output only. Whether the current user can delete this shared drive.
   * Attempting to delete the shared drive may still fail if there are untrashed
   * items inside the shared drive.
   *
   * @param bool $canDeleteDrive
   */
  public function setCanDeleteDrive($canDeleteDrive)
  {
    $this->canDeleteDrive = $canDeleteDrive;
  }
  /**
   * @return bool
   */
  public function getCanDeleteDrive()
  {
    return $this->canDeleteDrive;
  }
  /**
   * Output only. Whether the current user can download files in this shared
   * drive.
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
   * Output only. Whether the current user can edit files in this shared drive
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
   * Output only. Whether the current user can list the children of folders in
   * this shared drive.
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
   * Output only. Whether the current user can add members to this shared drive
   * or remove them or change their role.
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
   * Output only. Whether the current user can read the revisions resource of
   * files in this shared drive.
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
   * Output only. Whether the current user can rename files or folders in this
   * shared drive.
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
   * Output only. Whether the current user can rename this shared drive.
   *
   * @param bool $canRenameDrive
   */
  public function setCanRenameDrive($canRenameDrive)
  {
    $this->canRenameDrive = $canRenameDrive;
  }
  /**
   * @return bool
   */
  public function getCanRenameDrive()
  {
    return $this->canRenameDrive;
  }
  /**
   * Output only. Whether the current user can reset the shared drive
   * restrictions to defaults.
   *
   * @param bool $canResetDriveRestrictions
   */
  public function setCanResetDriveRestrictions($canResetDriveRestrictions)
  {
    $this->canResetDriveRestrictions = $canResetDriveRestrictions;
  }
  /**
   * @return bool
   */
  public function getCanResetDriveRestrictions()
  {
    return $this->canResetDriveRestrictions;
  }
  /**
   * Output only. Whether the current user can share files or folders in this
   * shared drive.
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
   * Output only. Whether the current user can trash children from folders in
   * this shared drive.
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
class_alias(DriveCapabilities::class, 'Google_Service_Drive_DriveCapabilities');
