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

class DriveFileCapabilities extends \Google\Model
{
  /**
   * Output only. Whether the current user is the pending owner of the file. Not
   * populated for shared drive files.
   *
   * @var bool
   */
  public $canAcceptOwnership;
  /**
   * Output only. Whether the current user can add children to this folder. This
   * is always `false` when the item isn't a folder.
   *
   * @var bool
   */
  public $canAddChildren;
  /**
   * Output only. Whether the current user can add a folder from another drive
   * (different shared drive or My Drive) to this folder. This is `false` when
   * the item isn't a folder. Only populated for items in shared drives.
   *
   * @var bool
   */
  public $canAddFolderFromAnotherDrive;
  /**
   * Output only. Whether the current user can add a parent for the item without
   * removing an existing parent in the same request. Not populated for shared
   * drive files.
   *
   * @var bool
   */
  public $canAddMyDriveParent;
  /**
   * Output only. Whether the current user can change the
   * `copyRequiresWriterPermission` restriction of this file.
   *
   * @var bool
   */
  public $canChangeCopyRequiresWriterPermission;
  /**
   * Output only. Whether the current user can change the owner or organizer-
   * applied download restrictions of the file.
   *
   * @var bool
   */
  public $canChangeItemDownloadRestriction;
  /**
   * Output only. Whether the current user can change the
   * `securityUpdateEnabled` field on link share metadata.
   *
   * @var bool
   */
  public $canChangeSecurityUpdateEnabled;
  /**
   * Deprecated: Output only.
   *
   * @deprecated
   * @var bool
   */
  public $canChangeViewersCanCopyContent;
  /**
   * Output only. Whether the current user can comment on this file.
   *
   * @var bool
   */
  public $canComment;
  /**
   * Output only. Whether the current user can copy this file. For an item in a
   * shared drive, whether the current user can copy non-folder descendants of
   * this item, or this item if it's not a folder.
   *
   * @var bool
   */
  public $canCopy;
  /**
   * Output only. Whether the current user can delete this file.
   *
   * @var bool
   */
  public $canDelete;
  /**
   * Output only. Whether the current user can delete children of this folder.
   * This is `false` when the item isn't a folder. Only populated for items in
   * shared drives.
   *
   * @var bool
   */
  public $canDeleteChildren;
  /**
   * Whether a user can disable inherited permissions.
   *
   * @var bool
   */
  public $canDisableInheritedPermissions;
  /**
   * Output only. Whether the current user can download this file.
   *
   * @var bool
   */
  public $canDownload;
  /**
   * Output only. Whether the current user can edit this file. Other factors may
   * limit the type of changes a user can make to a file. For example, see
   * `canChangeCopyRequiresWriterPermission` or `canModifyContent`.
   *
   * @var bool
   */
  public $canEdit;
  /**
   * Whether a user can re-enable inherited permissions.
   *
   * @var bool
   */
  public $canEnableInheritedPermissions;
  /**
   * Output only. Whether the current user can list the children of this folder.
   * This is always `false` when the item isn't a folder.
   *
   * @var bool
   */
  public $canListChildren;
  /**
   * Output only. Whether the current user can modify the content of this file.
   *
   * @var bool
   */
  public $canModifyContent;
  /**
   * Deprecated: Output only. Use one of `canModifyEditorContentRestriction`,
   * `canModifyOwnerContentRestriction`, or `canRemoveContentRestriction`.
   *
   * @deprecated
   * @var bool
   */
  public $canModifyContentRestriction;
  /**
   * Output only. Whether the current user can add or modify content
   * restrictions on the file which are editor restricted.
   *
   * @var bool
   */
  public $canModifyEditorContentRestriction;
  /**
   * Output only. Whether the current user can modify the labels on the file.
   *
   * @var bool
   */
  public $canModifyLabels;
  /**
   * Output only. Whether the current user can add or modify content
   * restrictions which are owner restricted.
   *
   * @var bool
   */
  public $canModifyOwnerContentRestriction;
  /**
   * Output only. Whether the current user can move children of this folder
   * outside of the shared drive. This is `false` when the item isn't a folder.
   * Only populated for items in shared drives.
   *
   * @var bool
   */
  public $canMoveChildrenOutOfDrive;
  /**
   * Deprecated: Output only. Use `canMoveChildrenOutOfDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveChildrenOutOfTeamDrive;
  /**
   * Output only. Whether the current user can move children of this folder
   * within this drive. This is `false` when the item isn't a folder. Note that
   * a request to move the child may still fail depending on the current user's
   * access to the child and to the destination folder.
   *
   * @var bool
   */
  public $canMoveChildrenWithinDrive;
  /**
   * Deprecated: Output only. Use `canMoveChildrenWithinDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveChildrenWithinTeamDrive;
  /**
   * Deprecated: Output only. Use `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveItemIntoTeamDrive;
  /**
   * Output only. Whether the current user can move this item outside of this
   * drive by changing its parent. Note that a request to change the parent of
   * the item may still fail depending on the new parent that's being added.
   *
   * @var bool
   */
  public $canMoveItemOutOfDrive;
  /**
   * Deprecated: Output only. Use `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveItemOutOfTeamDrive;
  /**
   * Output only. Whether the current user can move this item within this drive.
   * Note that a request to change the parent of the item may still fail
   * depending on the new parent that's being added and the parent that is being
   * removed.
   *
   * @var bool
   */
  public $canMoveItemWithinDrive;
  /**
   * Deprecated: Output only. Use `canMoveItemWithinDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveItemWithinTeamDrive;
  /**
   * Deprecated: Output only. Use `canMoveItemWithinDrive` or
   * `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canMoveTeamDriveItem;
  /**
   * Output only. Whether the current user can read the shared drive to which
   * this file belongs. Only populated for items in shared drives.
   *
   * @var bool
   */
  public $canReadDrive;
  /**
   * Output only. Whether the current user can read the labels on the file.
   *
   * @var bool
   */
  public $canReadLabels;
  /**
   * Output only. Whether the current user can read the revisions resource of
   * this file. For a shared drive item, whether revisions of non-folder
   * descendants of this item, or this item if it's not a folder, can be read.
   *
   * @var bool
   */
  public $canReadRevisions;
  /**
   * Deprecated: Output only. Use `canReadDrive` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canReadTeamDrive;
  /**
   * Output only. Whether the current user can remove children from this folder.
   * This is always `false` when the item isn't a folder. For a folder in a
   * shared drive, use `canDeleteChildren` or `canTrashChildren` instead.
   *
   * @var bool
   */
  public $canRemoveChildren;
  /**
   * Output only. Whether there's a content restriction on the file that can be
   * removed by the current user.
   *
   * @var bool
   */
  public $canRemoveContentRestriction;
  /**
   * Output only. Whether the current user can remove a parent from the item
   * without adding another parent in the same request. Not populated for shared
   * drive files.
   *
   * @var bool
   */
  public $canRemoveMyDriveParent;
  /**
   * Output only. Whether the current user can rename this file.
   *
   * @var bool
   */
  public $canRename;
  /**
   * Output only. Whether the current user can modify the sharing settings for
   * this file.
   *
   * @var bool
   */
  public $canShare;
  /**
   * Output only. Whether the current user can move this file to trash.
   *
   * @var bool
   */
  public $canTrash;
  /**
   * Output only. Whether the current user can trash children of this folder.
   * This is `false` when the item isn't a folder. Only populated for items in
   * shared drives.
   *
   * @var bool
   */
  public $canTrashChildren;
  /**
   * Output only. Whether the current user can restore this file from trash.
   *
   * @var bool
   */
  public $canUntrash;

  /**
   * Output only. Whether the current user is the pending owner of the file. Not
   * populated for shared drive files.
   *
   * @param bool $canAcceptOwnership
   */
  public function setCanAcceptOwnership($canAcceptOwnership)
  {
    $this->canAcceptOwnership = $canAcceptOwnership;
  }
  /**
   * @return bool
   */
  public function getCanAcceptOwnership()
  {
    return $this->canAcceptOwnership;
  }
  /**
   * Output only. Whether the current user can add children to this folder. This
   * is always `false` when the item isn't a folder.
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
   * Output only. Whether the current user can add a folder from another drive
   * (different shared drive or My Drive) to this folder. This is `false` when
   * the item isn't a folder. Only populated for items in shared drives.
   *
   * @param bool $canAddFolderFromAnotherDrive
   */
  public function setCanAddFolderFromAnotherDrive($canAddFolderFromAnotherDrive)
  {
    $this->canAddFolderFromAnotherDrive = $canAddFolderFromAnotherDrive;
  }
  /**
   * @return bool
   */
  public function getCanAddFolderFromAnotherDrive()
  {
    return $this->canAddFolderFromAnotherDrive;
  }
  /**
   * Output only. Whether the current user can add a parent for the item without
   * removing an existing parent in the same request. Not populated for shared
   * drive files.
   *
   * @param bool $canAddMyDriveParent
   */
  public function setCanAddMyDriveParent($canAddMyDriveParent)
  {
    $this->canAddMyDriveParent = $canAddMyDriveParent;
  }
  /**
   * @return bool
   */
  public function getCanAddMyDriveParent()
  {
    return $this->canAddMyDriveParent;
  }
  /**
   * Output only. Whether the current user can change the
   * `copyRequiresWriterPermission` restriction of this file.
   *
   * @param bool $canChangeCopyRequiresWriterPermission
   */
  public function setCanChangeCopyRequiresWriterPermission($canChangeCopyRequiresWriterPermission)
  {
    $this->canChangeCopyRequiresWriterPermission = $canChangeCopyRequiresWriterPermission;
  }
  /**
   * @return bool
   */
  public function getCanChangeCopyRequiresWriterPermission()
  {
    return $this->canChangeCopyRequiresWriterPermission;
  }
  /**
   * Output only. Whether the current user can change the owner or organizer-
   * applied download restrictions of the file.
   *
   * @param bool $canChangeItemDownloadRestriction
   */
  public function setCanChangeItemDownloadRestriction($canChangeItemDownloadRestriction)
  {
    $this->canChangeItemDownloadRestriction = $canChangeItemDownloadRestriction;
  }
  /**
   * @return bool
   */
  public function getCanChangeItemDownloadRestriction()
  {
    return $this->canChangeItemDownloadRestriction;
  }
  /**
   * Output only. Whether the current user can change the
   * `securityUpdateEnabled` field on link share metadata.
   *
   * @param bool $canChangeSecurityUpdateEnabled
   */
  public function setCanChangeSecurityUpdateEnabled($canChangeSecurityUpdateEnabled)
  {
    $this->canChangeSecurityUpdateEnabled = $canChangeSecurityUpdateEnabled;
  }
  /**
   * @return bool
   */
  public function getCanChangeSecurityUpdateEnabled()
  {
    return $this->canChangeSecurityUpdateEnabled;
  }
  /**
   * Deprecated: Output only.
   *
   * @deprecated
   * @param bool $canChangeViewersCanCopyContent
   */
  public function setCanChangeViewersCanCopyContent($canChangeViewersCanCopyContent)
  {
    $this->canChangeViewersCanCopyContent = $canChangeViewersCanCopyContent;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanChangeViewersCanCopyContent()
  {
    return $this->canChangeViewersCanCopyContent;
  }
  /**
   * Output only. Whether the current user can comment on this file.
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
   * Output only. Whether the current user can copy this file. For an item in a
   * shared drive, whether the current user can copy non-folder descendants of
   * this item, or this item if it's not a folder.
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
   * Output only. Whether the current user can delete this file.
   *
   * @param bool $canDelete
   */
  public function setCanDelete($canDelete)
  {
    $this->canDelete = $canDelete;
  }
  /**
   * @return bool
   */
  public function getCanDelete()
  {
    return $this->canDelete;
  }
  /**
   * Output only. Whether the current user can delete children of this folder.
   * This is `false` when the item isn't a folder. Only populated for items in
   * shared drives.
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
   * Whether a user can disable inherited permissions.
   *
   * @param bool $canDisableInheritedPermissions
   */
  public function setCanDisableInheritedPermissions($canDisableInheritedPermissions)
  {
    $this->canDisableInheritedPermissions = $canDisableInheritedPermissions;
  }
  /**
   * @return bool
   */
  public function getCanDisableInheritedPermissions()
  {
    return $this->canDisableInheritedPermissions;
  }
  /**
   * Output only. Whether the current user can download this file.
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
   * Output only. Whether the current user can edit this file. Other factors may
   * limit the type of changes a user can make to a file. For example, see
   * `canChangeCopyRequiresWriterPermission` or `canModifyContent`.
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
   * Whether a user can re-enable inherited permissions.
   *
   * @param bool $canEnableInheritedPermissions
   */
  public function setCanEnableInheritedPermissions($canEnableInheritedPermissions)
  {
    $this->canEnableInheritedPermissions = $canEnableInheritedPermissions;
  }
  /**
   * @return bool
   */
  public function getCanEnableInheritedPermissions()
  {
    return $this->canEnableInheritedPermissions;
  }
  /**
   * Output only. Whether the current user can list the children of this folder.
   * This is always `false` when the item isn't a folder.
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
   * Output only. Whether the current user can modify the content of this file.
   *
   * @param bool $canModifyContent
   */
  public function setCanModifyContent($canModifyContent)
  {
    $this->canModifyContent = $canModifyContent;
  }
  /**
   * @return bool
   */
  public function getCanModifyContent()
  {
    return $this->canModifyContent;
  }
  /**
   * Deprecated: Output only. Use one of `canModifyEditorContentRestriction`,
   * `canModifyOwnerContentRestriction`, or `canRemoveContentRestriction`.
   *
   * @deprecated
   * @param bool $canModifyContentRestriction
   */
  public function setCanModifyContentRestriction($canModifyContentRestriction)
  {
    $this->canModifyContentRestriction = $canModifyContentRestriction;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanModifyContentRestriction()
  {
    return $this->canModifyContentRestriction;
  }
  /**
   * Output only. Whether the current user can add or modify content
   * restrictions on the file which are editor restricted.
   *
   * @param bool $canModifyEditorContentRestriction
   */
  public function setCanModifyEditorContentRestriction($canModifyEditorContentRestriction)
  {
    $this->canModifyEditorContentRestriction = $canModifyEditorContentRestriction;
  }
  /**
   * @return bool
   */
  public function getCanModifyEditorContentRestriction()
  {
    return $this->canModifyEditorContentRestriction;
  }
  /**
   * Output only. Whether the current user can modify the labels on the file.
   *
   * @param bool $canModifyLabels
   */
  public function setCanModifyLabels($canModifyLabels)
  {
    $this->canModifyLabels = $canModifyLabels;
  }
  /**
   * @return bool
   */
  public function getCanModifyLabels()
  {
    return $this->canModifyLabels;
  }
  /**
   * Output only. Whether the current user can add or modify content
   * restrictions which are owner restricted.
   *
   * @param bool $canModifyOwnerContentRestriction
   */
  public function setCanModifyOwnerContentRestriction($canModifyOwnerContentRestriction)
  {
    $this->canModifyOwnerContentRestriction = $canModifyOwnerContentRestriction;
  }
  /**
   * @return bool
   */
  public function getCanModifyOwnerContentRestriction()
  {
    return $this->canModifyOwnerContentRestriction;
  }
  /**
   * Output only. Whether the current user can move children of this folder
   * outside of the shared drive. This is `false` when the item isn't a folder.
   * Only populated for items in shared drives.
   *
   * @param bool $canMoveChildrenOutOfDrive
   */
  public function setCanMoveChildrenOutOfDrive($canMoveChildrenOutOfDrive)
  {
    $this->canMoveChildrenOutOfDrive = $canMoveChildrenOutOfDrive;
  }
  /**
   * @return bool
   */
  public function getCanMoveChildrenOutOfDrive()
  {
    return $this->canMoveChildrenOutOfDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveChildrenOutOfDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveChildrenOutOfTeamDrive
   */
  public function setCanMoveChildrenOutOfTeamDrive($canMoveChildrenOutOfTeamDrive)
  {
    $this->canMoveChildrenOutOfTeamDrive = $canMoveChildrenOutOfTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveChildrenOutOfTeamDrive()
  {
    return $this->canMoveChildrenOutOfTeamDrive;
  }
  /**
   * Output only. Whether the current user can move children of this folder
   * within this drive. This is `false` when the item isn't a folder. Note that
   * a request to move the child may still fail depending on the current user's
   * access to the child and to the destination folder.
   *
   * @param bool $canMoveChildrenWithinDrive
   */
  public function setCanMoveChildrenWithinDrive($canMoveChildrenWithinDrive)
  {
    $this->canMoveChildrenWithinDrive = $canMoveChildrenWithinDrive;
  }
  /**
   * @return bool
   */
  public function getCanMoveChildrenWithinDrive()
  {
    return $this->canMoveChildrenWithinDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveChildrenWithinDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveChildrenWithinTeamDrive
   */
  public function setCanMoveChildrenWithinTeamDrive($canMoveChildrenWithinTeamDrive)
  {
    $this->canMoveChildrenWithinTeamDrive = $canMoveChildrenWithinTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveChildrenWithinTeamDrive()
  {
    return $this->canMoveChildrenWithinTeamDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveItemIntoTeamDrive
   */
  public function setCanMoveItemIntoTeamDrive($canMoveItemIntoTeamDrive)
  {
    $this->canMoveItemIntoTeamDrive = $canMoveItemIntoTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveItemIntoTeamDrive()
  {
    return $this->canMoveItemIntoTeamDrive;
  }
  /**
   * Output only. Whether the current user can move this item outside of this
   * drive by changing its parent. Note that a request to change the parent of
   * the item may still fail depending on the new parent that's being added.
   *
   * @param bool $canMoveItemOutOfDrive
   */
  public function setCanMoveItemOutOfDrive($canMoveItemOutOfDrive)
  {
    $this->canMoveItemOutOfDrive = $canMoveItemOutOfDrive;
  }
  /**
   * @return bool
   */
  public function getCanMoveItemOutOfDrive()
  {
    return $this->canMoveItemOutOfDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveItemOutOfTeamDrive
   */
  public function setCanMoveItemOutOfTeamDrive($canMoveItemOutOfTeamDrive)
  {
    $this->canMoveItemOutOfTeamDrive = $canMoveItemOutOfTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveItemOutOfTeamDrive()
  {
    return $this->canMoveItemOutOfTeamDrive;
  }
  /**
   * Output only. Whether the current user can move this item within this drive.
   * Note that a request to change the parent of the item may still fail
   * depending on the new parent that's being added and the parent that is being
   * removed.
   *
   * @param bool $canMoveItemWithinDrive
   */
  public function setCanMoveItemWithinDrive($canMoveItemWithinDrive)
  {
    $this->canMoveItemWithinDrive = $canMoveItemWithinDrive;
  }
  /**
   * @return bool
   */
  public function getCanMoveItemWithinDrive()
  {
    return $this->canMoveItemWithinDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveItemWithinDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveItemWithinTeamDrive
   */
  public function setCanMoveItemWithinTeamDrive($canMoveItemWithinTeamDrive)
  {
    $this->canMoveItemWithinTeamDrive = $canMoveItemWithinTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveItemWithinTeamDrive()
  {
    return $this->canMoveItemWithinTeamDrive;
  }
  /**
   * Deprecated: Output only. Use `canMoveItemWithinDrive` or
   * `canMoveItemOutOfDrive` instead.
   *
   * @deprecated
   * @param bool $canMoveTeamDriveItem
   */
  public function setCanMoveTeamDriveItem($canMoveTeamDriveItem)
  {
    $this->canMoveTeamDriveItem = $canMoveTeamDriveItem;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanMoveTeamDriveItem()
  {
    return $this->canMoveTeamDriveItem;
  }
  /**
   * Output only. Whether the current user can read the shared drive to which
   * this file belongs. Only populated for items in shared drives.
   *
   * @param bool $canReadDrive
   */
  public function setCanReadDrive($canReadDrive)
  {
    $this->canReadDrive = $canReadDrive;
  }
  /**
   * @return bool
   */
  public function getCanReadDrive()
  {
    return $this->canReadDrive;
  }
  /**
   * Output only. Whether the current user can read the labels on the file.
   *
   * @param bool $canReadLabels
   */
  public function setCanReadLabels($canReadLabels)
  {
    $this->canReadLabels = $canReadLabels;
  }
  /**
   * @return bool
   */
  public function getCanReadLabels()
  {
    return $this->canReadLabels;
  }
  /**
   * Output only. Whether the current user can read the revisions resource of
   * this file. For a shared drive item, whether revisions of non-folder
   * descendants of this item, or this item if it's not a folder, can be read.
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
   * Deprecated: Output only. Use `canReadDrive` instead.
   *
   * @deprecated
   * @param bool $canReadTeamDrive
   */
  public function setCanReadTeamDrive($canReadTeamDrive)
  {
    $this->canReadTeamDrive = $canReadTeamDrive;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanReadTeamDrive()
  {
    return $this->canReadTeamDrive;
  }
  /**
   * Output only. Whether the current user can remove children from this folder.
   * This is always `false` when the item isn't a folder. For a folder in a
   * shared drive, use `canDeleteChildren` or `canTrashChildren` instead.
   *
   * @param bool $canRemoveChildren
   */
  public function setCanRemoveChildren($canRemoveChildren)
  {
    $this->canRemoveChildren = $canRemoveChildren;
  }
  /**
   * @return bool
   */
  public function getCanRemoveChildren()
  {
    return $this->canRemoveChildren;
  }
  /**
   * Output only. Whether there's a content restriction on the file that can be
   * removed by the current user.
   *
   * @param bool $canRemoveContentRestriction
   */
  public function setCanRemoveContentRestriction($canRemoveContentRestriction)
  {
    $this->canRemoveContentRestriction = $canRemoveContentRestriction;
  }
  /**
   * @return bool
   */
  public function getCanRemoveContentRestriction()
  {
    return $this->canRemoveContentRestriction;
  }
  /**
   * Output only. Whether the current user can remove a parent from the item
   * without adding another parent in the same request. Not populated for shared
   * drive files.
   *
   * @param bool $canRemoveMyDriveParent
   */
  public function setCanRemoveMyDriveParent($canRemoveMyDriveParent)
  {
    $this->canRemoveMyDriveParent = $canRemoveMyDriveParent;
  }
  /**
   * @return bool
   */
  public function getCanRemoveMyDriveParent()
  {
    return $this->canRemoveMyDriveParent;
  }
  /**
   * Output only. Whether the current user can rename this file.
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
   * Output only. Whether the current user can modify the sharing settings for
   * this file.
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
   * Output only. Whether the current user can move this file to trash.
   *
   * @param bool $canTrash
   */
  public function setCanTrash($canTrash)
  {
    $this->canTrash = $canTrash;
  }
  /**
   * @return bool
   */
  public function getCanTrash()
  {
    return $this->canTrash;
  }
  /**
   * Output only. Whether the current user can trash children of this folder.
   * This is `false` when the item isn't a folder. Only populated for items in
   * shared drives.
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
  /**
   * Output only. Whether the current user can restore this file from trash.
   *
   * @param bool $canUntrash
   */
  public function setCanUntrash($canUntrash)
  {
    $this->canUntrash = $canUntrash;
  }
  /**
   * @return bool
   */
  public function getCanUntrash()
  {
    return $this->canUntrash;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFileCapabilities::class, 'Google_Service_Drive_DriveFileCapabilities');
