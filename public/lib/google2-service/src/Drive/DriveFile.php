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

class DriveFile extends \Google\Collection
{
  protected $collection_key = 'spaces';
  /**
   * A collection of arbitrary key-value pairs which are private to the
   * requesting app. Entries with null values are cleared in update and copy
   * requests. These properties can only be retrieved using an authenticated
   * request. An authenticated request uses an access token obtained with a
   * OAuth 2 client ID. You cannot use an API key to retrieve private
   * properties.
   *
   * @var string[]
   */
  public $appProperties;
  protected $capabilitiesType = DriveFileCapabilities::class;
  protected $capabilitiesDataType = '';
  protected $contentHintsType = DriveFileContentHints::class;
  protected $contentHintsDataType = '';
  protected $contentRestrictionsType = ContentRestriction::class;
  protected $contentRestrictionsDataType = 'array';
  /**
   * Whether the options to copy, print, or download this file should be
   * disabled for readers and commenters.
   *
   * @var bool
   */
  public $copyRequiresWriterPermission;
  /**
   * The time at which the file was created (RFC 3339 date-time).
   *
   * @var string
   */
  public $createdTime;
  /**
   * A short description of the file.
   *
   * @var string
   */
  public $description;
  protected $downloadRestrictionsType = DownloadRestrictionsMetadata::class;
  protected $downloadRestrictionsDataType = '';
  /**
   * Output only. ID of the shared drive the file resides in. Only populated for
   * items in shared drives.
   *
   * @var string
   */
  public $driveId;
  /**
   * Output only. Whether the file has been explicitly trashed, as opposed to
   * recursively trashed from a parent folder.
   *
   * @var bool
   */
  public $explicitlyTrashed;
  /**
   * Output only. Links for exporting Docs Editors files to specific formats.
   *
   * @var string[]
   */
  public $exportLinks;
  /**
   * Output only. The final component of `fullFileExtension`. This is only
   * available for files with binary content in Google Drive.
   *
   * @var string
   */
  public $fileExtension;
  /**
   * The color for a folder or a shortcut to a folder as an RGB hex string. The
   * supported colors are published in the `folderColorPalette` field of the
   * [`about`](/workspace/drive/api/reference/rest/v3/about) resource. If an
   * unsupported color is specified, the closest color in the palette is used
   * instead.
   *
   * @var string
   */
  public $folderColorRgb;
  /**
   * Output only. The full file extension extracted from the `name` field. May
   * contain multiple concatenated extensions, such as "tar.gz". This is only
   * available for files with binary content in Google Drive. This is
   * automatically updated when the `name` field changes, however it's not
   * cleared if the new name doesn't contain a valid extension.
   *
   * @var string
   */
  public $fullFileExtension;
  /**
   * Output only. Whether there are permissions directly on this file. This
   * field is only populated for items in shared drives.
   *
   * @var bool
   */
  public $hasAugmentedPermissions;
  /**
   * Output only. Whether this file has a thumbnail. This doesn't indicate
   * whether the requesting app has access to the thumbnail. To check access,
   * look for the presence of the thumbnailLink field.
   *
   * @var bool
   */
  public $hasThumbnail;
  /**
   * Output only. The ID of the file's head revision. This is currently only
   * available for files with binary content in Google Drive.
   *
   * @var string
   */
  public $headRevisionId;
  /**
   * Output only. A static, unauthenticated link to the file's icon.
   *
   * @var string
   */
  public $iconLink;
  /**
   * The ID of the file.
   *
   * @var string
   */
  public $id;
  protected $imageMediaMetadataType = DriveFileImageMediaMetadata::class;
  protected $imageMediaMetadataDataType = '';
  /**
   * Whether this file has inherited permissions disabled. Inherited permissions
   * are enabled by default.
   *
   * @var bool
   */
  public $inheritedPermissionsDisabled;
  /**
   * Output only. Whether the file was created or opened by the requesting app.
   *
   * @var bool
   */
  public $isAppAuthorized;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#file"`.
   *
   * @var string
   */
  public $kind;
  protected $labelInfoType = DriveFileLabelInfo::class;
  protected $labelInfoDataType = '';
  protected $lastModifyingUserType = User::class;
  protected $lastModifyingUserDataType = '';
  protected $linkShareMetadataType = DriveFileLinkShareMetadata::class;
  protected $linkShareMetadataDataType = '';
  /**
   * Output only. The MD5 checksum for the content of the file. This is only
   * applicable to files with binary content in Google Drive.
   *
   * @var string
   */
  public $md5Checksum;
  /**
   * The MIME type of the file. Google Drive attempts to automatically detect an
   * appropriate value from uploaded content, if no value is provided. The value
   * cannot be changed unless a new revision is uploaded. If a file is created
   * with a Google Doc MIME type, the uploaded content is imported, if possible.
   * The supported import formats are published in the
   * [`about`](/workspace/drive/api/reference/rest/v3/about) resource.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Output only. Whether the file has been modified by this user.
   *
   * @var bool
   */
  public $modifiedByMe;
  /**
   * The last time the file was modified by the user (RFC 3339 date-time).
   *
   * @var string
   */
  public $modifiedByMeTime;
  /**
   * he last time the file was modified by anyone (RFC 3339 date-time). Note
   * that setting modifiedTime will also update modifiedByMeTime for the user.
   *
   * @var string
   */
  public $modifiedTime;
  /**
   * The name of the file. This isn't necessarily unique within a folder. Note
   * that for immutable items such as the top-level folders of shared drives,
   * the My Drive root folder, and the Application Data folder, the name is
   * constant.
   *
   * @var string
   */
  public $name;
  /**
   * The original filename of the uploaded content if available, or else the
   * original value of the `name` field. This is only available for files with
   * binary content in Google Drive.
   *
   * @var string
   */
  public $originalFilename;
  /**
   * Output only. Whether the user owns the file. Not populated for items in
   * shared drives.
   *
   * @var bool
   */
  public $ownedByMe;
  protected $ownersType = User::class;
  protected $ownersDataType = 'array';
  /**
   * The ID of the parent folder containing the file. A file can only have one
   * parent folder; specifying multiple parents isn't supported. If not
   * specified as part of a create request, the file is placed directly in the
   * user's My Drive folder. If not specified as part of a copy request, the
   * file inherits any discoverable parent of the source file. Update requests
   * must use the `addParents` and `removeParents` parameters to modify the
   * parents list.
   *
   * @var string[]
   */
  public $parents;
  /**
   * Output only. List of permission IDs for users with access to this file.
   *
   * @var string[]
   */
  public $permissionIds;
  protected $permissionsType = Permission::class;
  protected $permissionsDataType = 'array';
  /**
   * A collection of arbitrary key-value pairs which are visible to all apps.
   * Entries with null values are cleared in update and copy requests.
   *
   * @var string[]
   */
  public $properties;
  /**
   * Output only. The number of storage quota bytes used by the file. This
   * includes the head revision as well as previous revisions with `keepForever`
   * enabled.
   *
   * @var string
   */
  public $quotaBytesUsed;
  /**
   * Output only. A key needed to access the item via a shared link.
   *
   * @var string
   */
  public $resourceKey;
  /**
   * Output only. The SHA1 checksum associated with this file, if available.
   * This field is only populated for files with content stored in Google Drive;
   * it's not populated for Docs Editors or shortcut files.
   *
   * @var string
   */
  public $sha1Checksum;
  /**
   * Output only. The SHA256 checksum associated with this file, if available.
   * This field is only populated for files with content stored in Google Drive;
   * it's not populated for Docs Editors or shortcut files.
   *
   * @var string
   */
  public $sha256Checksum;
  /**
   * Output only. Whether the file has been shared. Not populated for items in
   * shared drives.
   *
   * @var bool
   */
  public $shared;
  /**
   * The time at which the file was shared with the user, if applicable (RFC
   * 3339 date-time).
   *
   * @var string
   */
  public $sharedWithMeTime;
  protected $sharingUserType = User::class;
  protected $sharingUserDataType = '';
  protected $shortcutDetailsType = DriveFileShortcutDetails::class;
  protected $shortcutDetailsDataType = '';
  /**
   * Output only. Size in bytes of blobs and Google Workspace editor files.
   * Won't be populated for files that have no size, like shortcuts and folders.
   *
   * @var string
   */
  public $size;
  /**
   * Output only. The list of spaces which contain the file. The currently
   * supported values are `drive`, `appDataFolder`, and `photos`.
   *
   * @var string[]
   */
  public $spaces;
  /**
   * Whether the user has starred the file.
   *
   * @var bool
   */
  public $starred;
  /**
   * Deprecated: Output only. Use `driveId` instead.
   *
   * @deprecated
   * @var string
   */
  public $teamDriveId;
  /**
   * Output only. A short-lived link to the file's thumbnail, if available.
   * Typically lasts on the order of hours. Not intended for direct usage on web
   * applications due to [Cross-Origin Resource Sharing
   * (CORS)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) policies.
   * Consider using a proxy server. Only populated when the requesting app can
   * access the file's content. If the file isn't shared publicly, the URL
   * returned in `files.thumbnailLink` must be fetched using a credentialed
   * request.
   *
   * @var string
   */
  public $thumbnailLink;
  /**
   * Output only. The thumbnail version for use in thumbnail cache invalidation.
   *
   * @var string
   */
  public $thumbnailVersion;
  /**
   * Whether the file has been trashed, either explicitly or from a trashed
   * parent folder. Only the owner may trash a file, and other users cannot see
   * files in the owner's trash.
   *
   * @var bool
   */
  public $trashed;
  /**
   * The time that the item was trashed (RFC 3339 date-time). Only populated for
   * items in shared drives.
   *
   * @var string
   */
  public $trashedTime;
  protected $trashingUserType = User::class;
  protected $trashingUserDataType = '';
  /**
   * Output only. A monotonically increasing version number for the file. This
   * reflects every change made to the file on the server, even those not
   * visible to the user.
   *
   * @var string
   */
  public $version;
  protected $videoMediaMetadataType = DriveFileVideoMediaMetadata::class;
  protected $videoMediaMetadataDataType = '';
  /**
   * Output only. Whether the file has been viewed by this user.
   *
   * @var bool
   */
  public $viewedByMe;
  /**
   * The last time the file was viewed by the user (RFC 3339 date-time).
   *
   * @var string
   */
  public $viewedByMeTime;
  /**
   * Deprecated: Use `copyRequiresWriterPermission` instead.
   *
   * @deprecated
   * @var bool
   */
  public $viewersCanCopyContent;
  /**
   * Output only. A link for downloading the content of the file in a browser.
   * This is only available for files with binary content in Google Drive.
   *
   * @var string
   */
  public $webContentLink;
  /**
   * Output only. A link for opening the file in a relevant Google editor or
   * viewer in a browser.
   *
   * @var string
   */
  public $webViewLink;
  /**
   * Whether users with only `writer` permission can modify the file's
   * permissions. Not populated for items in shared drives.
   *
   * @var bool
   */
  public $writersCanShare;

  /**
   * A collection of arbitrary key-value pairs which are private to the
   * requesting app. Entries with null values are cleared in update and copy
   * requests. These properties can only be retrieved using an authenticated
   * request. An authenticated request uses an access token obtained with a
   * OAuth 2 client ID. You cannot use an API key to retrieve private
   * properties.
   *
   * @param string[] $appProperties
   */
  public function setAppProperties($appProperties)
  {
    $this->appProperties = $appProperties;
  }
  /**
   * @return string[]
   */
  public function getAppProperties()
  {
    return $this->appProperties;
  }
  /**
   * Output only. Capabilities the current user has on this file. Each
   * capability corresponds to a fine-grained action that a user may take. For
   * more information, see [Understand file capabilities](https://developers.goo
   * gle.com/workspace/drive/api/guides/manage-sharing#capabilities).
   *
   * @param DriveFileCapabilities $capabilities
   */
  public function setCapabilities(DriveFileCapabilities $capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return DriveFileCapabilities
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * Additional information about the content of the file. These fields are
   * never populated in responses.
   *
   * @param DriveFileContentHints $contentHints
   */
  public function setContentHints(DriveFileContentHints $contentHints)
  {
    $this->contentHints = $contentHints;
  }
  /**
   * @return DriveFileContentHints
   */
  public function getContentHints()
  {
    return $this->contentHints;
  }
  /**
   * Restrictions for accessing the content of the file. Only populated if such
   * a restriction exists.
   *
   * @param ContentRestriction[] $contentRestrictions
   */
  public function setContentRestrictions($contentRestrictions)
  {
    $this->contentRestrictions = $contentRestrictions;
  }
  /**
   * @return ContentRestriction[]
   */
  public function getContentRestrictions()
  {
    return $this->contentRestrictions;
  }
  /**
   * Whether the options to copy, print, or download this file should be
   * disabled for readers and commenters.
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
   * The time at which the file was created (RFC 3339 date-time).
   *
   * @param string $createdTime
   */
  public function setCreatedTime($createdTime)
  {
    $this->createdTime = $createdTime;
  }
  /**
   * @return string
   */
  public function getCreatedTime()
  {
    return $this->createdTime;
  }
  /**
   * A short description of the file.
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
   * Download restrictions applied on the file.
   *
   * @param DownloadRestrictionsMetadata $downloadRestrictions
   */
  public function setDownloadRestrictions(DownloadRestrictionsMetadata $downloadRestrictions)
  {
    $this->downloadRestrictions = $downloadRestrictions;
  }
  /**
   * @return DownloadRestrictionsMetadata
   */
  public function getDownloadRestrictions()
  {
    return $this->downloadRestrictions;
  }
  /**
   * Output only. ID of the shared drive the file resides in. Only populated for
   * items in shared drives.
   *
   * @param string $driveId
   */
  public function setDriveId($driveId)
  {
    $this->driveId = $driveId;
  }
  /**
   * @return string
   */
  public function getDriveId()
  {
    return $this->driveId;
  }
  /**
   * Output only. Whether the file has been explicitly trashed, as opposed to
   * recursively trashed from a parent folder.
   *
   * @param bool $explicitlyTrashed
   */
  public function setExplicitlyTrashed($explicitlyTrashed)
  {
    $this->explicitlyTrashed = $explicitlyTrashed;
  }
  /**
   * @return bool
   */
  public function getExplicitlyTrashed()
  {
    return $this->explicitlyTrashed;
  }
  /**
   * Output only. Links for exporting Docs Editors files to specific formats.
   *
   * @param string[] $exportLinks
   */
  public function setExportLinks($exportLinks)
  {
    $this->exportLinks = $exportLinks;
  }
  /**
   * @return string[]
   */
  public function getExportLinks()
  {
    return $this->exportLinks;
  }
  /**
   * Output only. The final component of `fullFileExtension`. This is only
   * available for files with binary content in Google Drive.
   *
   * @param string $fileExtension
   */
  public function setFileExtension($fileExtension)
  {
    $this->fileExtension = $fileExtension;
  }
  /**
   * @return string
   */
  public function getFileExtension()
  {
    return $this->fileExtension;
  }
  /**
   * The color for a folder or a shortcut to a folder as an RGB hex string. The
   * supported colors are published in the `folderColorPalette` field of the
   * [`about`](/workspace/drive/api/reference/rest/v3/about) resource. If an
   * unsupported color is specified, the closest color in the palette is used
   * instead.
   *
   * @param string $folderColorRgb
   */
  public function setFolderColorRgb($folderColorRgb)
  {
    $this->folderColorRgb = $folderColorRgb;
  }
  /**
   * @return string
   */
  public function getFolderColorRgb()
  {
    return $this->folderColorRgb;
  }
  /**
   * Output only. The full file extension extracted from the `name` field. May
   * contain multiple concatenated extensions, such as "tar.gz". This is only
   * available for files with binary content in Google Drive. This is
   * automatically updated when the `name` field changes, however it's not
   * cleared if the new name doesn't contain a valid extension.
   *
   * @param string $fullFileExtension
   */
  public function setFullFileExtension($fullFileExtension)
  {
    $this->fullFileExtension = $fullFileExtension;
  }
  /**
   * @return string
   */
  public function getFullFileExtension()
  {
    return $this->fullFileExtension;
  }
  /**
   * Output only. Whether there are permissions directly on this file. This
   * field is only populated for items in shared drives.
   *
   * @param bool $hasAugmentedPermissions
   */
  public function setHasAugmentedPermissions($hasAugmentedPermissions)
  {
    $this->hasAugmentedPermissions = $hasAugmentedPermissions;
  }
  /**
   * @return bool
   */
  public function getHasAugmentedPermissions()
  {
    return $this->hasAugmentedPermissions;
  }
  /**
   * Output only. Whether this file has a thumbnail. This doesn't indicate
   * whether the requesting app has access to the thumbnail. To check access,
   * look for the presence of the thumbnailLink field.
   *
   * @param bool $hasThumbnail
   */
  public function setHasThumbnail($hasThumbnail)
  {
    $this->hasThumbnail = $hasThumbnail;
  }
  /**
   * @return bool
   */
  public function getHasThumbnail()
  {
    return $this->hasThumbnail;
  }
  /**
   * Output only. The ID of the file's head revision. This is currently only
   * available for files with binary content in Google Drive.
   *
   * @param string $headRevisionId
   */
  public function setHeadRevisionId($headRevisionId)
  {
    $this->headRevisionId = $headRevisionId;
  }
  /**
   * @return string
   */
  public function getHeadRevisionId()
  {
    return $this->headRevisionId;
  }
  /**
   * Output only. A static, unauthenticated link to the file's icon.
   *
   * @param string $iconLink
   */
  public function setIconLink($iconLink)
  {
    $this->iconLink = $iconLink;
  }
  /**
   * @return string
   */
  public function getIconLink()
  {
    return $this->iconLink;
  }
  /**
   * The ID of the file.
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
   * Output only. Additional metadata about image media, if available.
   *
   * @param DriveFileImageMediaMetadata $imageMediaMetadata
   */
  public function setImageMediaMetadata(DriveFileImageMediaMetadata $imageMediaMetadata)
  {
    $this->imageMediaMetadata = $imageMediaMetadata;
  }
  /**
   * @return DriveFileImageMediaMetadata
   */
  public function getImageMediaMetadata()
  {
    return $this->imageMediaMetadata;
  }
  /**
   * Whether this file has inherited permissions disabled. Inherited permissions
   * are enabled by default.
   *
   * @param bool $inheritedPermissionsDisabled
   */
  public function setInheritedPermissionsDisabled($inheritedPermissionsDisabled)
  {
    $this->inheritedPermissionsDisabled = $inheritedPermissionsDisabled;
  }
  /**
   * @return bool
   */
  public function getInheritedPermissionsDisabled()
  {
    return $this->inheritedPermissionsDisabled;
  }
  /**
   * Output only. Whether the file was created or opened by the requesting app.
   *
   * @param bool $isAppAuthorized
   */
  public function setIsAppAuthorized($isAppAuthorized)
  {
    $this->isAppAuthorized = $isAppAuthorized;
  }
  /**
   * @return bool
   */
  public function getIsAppAuthorized()
  {
    return $this->isAppAuthorized;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#file"`.
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
   * Output only. An overview of the labels on the file.
   *
   * @param DriveFileLabelInfo $labelInfo
   */
  public function setLabelInfo(DriveFileLabelInfo $labelInfo)
  {
    $this->labelInfo = $labelInfo;
  }
  /**
   * @return DriveFileLabelInfo
   */
  public function getLabelInfo()
  {
    return $this->labelInfo;
  }
  /**
   * Output only. The last user to modify the file. This field is only populated
   * when the last modification was performed by a signed-in user.
   *
   * @param User $lastModifyingUser
   */
  public function setLastModifyingUser(User $lastModifyingUser)
  {
    $this->lastModifyingUser = $lastModifyingUser;
  }
  /**
   * @return User
   */
  public function getLastModifyingUser()
  {
    return $this->lastModifyingUser;
  }
  /**
   * Contains details about the link URLs that clients are using to refer to
   * this item.
   *
   * @param DriveFileLinkShareMetadata $linkShareMetadata
   */
  public function setLinkShareMetadata(DriveFileLinkShareMetadata $linkShareMetadata)
  {
    $this->linkShareMetadata = $linkShareMetadata;
  }
  /**
   * @return DriveFileLinkShareMetadata
   */
  public function getLinkShareMetadata()
  {
    return $this->linkShareMetadata;
  }
  /**
   * Output only. The MD5 checksum for the content of the file. This is only
   * applicable to files with binary content in Google Drive.
   *
   * @param string $md5Checksum
   */
  public function setMd5Checksum($md5Checksum)
  {
    $this->md5Checksum = $md5Checksum;
  }
  /**
   * @return string
   */
  public function getMd5Checksum()
  {
    return $this->md5Checksum;
  }
  /**
   * The MIME type of the file. Google Drive attempts to automatically detect an
   * appropriate value from uploaded content, if no value is provided. The value
   * cannot be changed unless a new revision is uploaded. If a file is created
   * with a Google Doc MIME type, the uploaded content is imported, if possible.
   * The supported import formats are published in the
   * [`about`](/workspace/drive/api/reference/rest/v3/about) resource.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Output only. Whether the file has been modified by this user.
   *
   * @param bool $modifiedByMe
   */
  public function setModifiedByMe($modifiedByMe)
  {
    $this->modifiedByMe = $modifiedByMe;
  }
  /**
   * @return bool
   */
  public function getModifiedByMe()
  {
    return $this->modifiedByMe;
  }
  /**
   * The last time the file was modified by the user (RFC 3339 date-time).
   *
   * @param string $modifiedByMeTime
   */
  public function setModifiedByMeTime($modifiedByMeTime)
  {
    $this->modifiedByMeTime = $modifiedByMeTime;
  }
  /**
   * @return string
   */
  public function getModifiedByMeTime()
  {
    return $this->modifiedByMeTime;
  }
  /**
   * he last time the file was modified by anyone (RFC 3339 date-time). Note
   * that setting modifiedTime will also update modifiedByMeTime for the user.
   *
   * @param string $modifiedTime
   */
  public function setModifiedTime($modifiedTime)
  {
    $this->modifiedTime = $modifiedTime;
  }
  /**
   * @return string
   */
  public function getModifiedTime()
  {
    return $this->modifiedTime;
  }
  /**
   * The name of the file. This isn't necessarily unique within a folder. Note
   * that for immutable items such as the top-level folders of shared drives,
   * the My Drive root folder, and the Application Data folder, the name is
   * constant.
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
   * The original filename of the uploaded content if available, or else the
   * original value of the `name` field. This is only available for files with
   * binary content in Google Drive.
   *
   * @param string $originalFilename
   */
  public function setOriginalFilename($originalFilename)
  {
    $this->originalFilename = $originalFilename;
  }
  /**
   * @return string
   */
  public function getOriginalFilename()
  {
    return $this->originalFilename;
  }
  /**
   * Output only. Whether the user owns the file. Not populated for items in
   * shared drives.
   *
   * @param bool $ownedByMe
   */
  public function setOwnedByMe($ownedByMe)
  {
    $this->ownedByMe = $ownedByMe;
  }
  /**
   * @return bool
   */
  public function getOwnedByMe()
  {
    return $this->ownedByMe;
  }
  /**
   * Output only. The owner of this file. Only certain legacy files may have
   * more than one owner. This field isn't populated for items in shared drives.
   *
   * @param User[] $owners
   */
  public function setOwners($owners)
  {
    $this->owners = $owners;
  }
  /**
   * @return User[]
   */
  public function getOwners()
  {
    return $this->owners;
  }
  /**
   * The ID of the parent folder containing the file. A file can only have one
   * parent folder; specifying multiple parents isn't supported. If not
   * specified as part of a create request, the file is placed directly in the
   * user's My Drive folder. If not specified as part of a copy request, the
   * file inherits any discoverable parent of the source file. Update requests
   * must use the `addParents` and `removeParents` parameters to modify the
   * parents list.
   *
   * @param string[] $parents
   */
  public function setParents($parents)
  {
    $this->parents = $parents;
  }
  /**
   * @return string[]
   */
  public function getParents()
  {
    return $this->parents;
  }
  /**
   * Output only. List of permission IDs for users with access to this file.
   *
   * @param string[] $permissionIds
   */
  public function setPermissionIds($permissionIds)
  {
    $this->permissionIds = $permissionIds;
  }
  /**
   * @return string[]
   */
  public function getPermissionIds()
  {
    return $this->permissionIds;
  }
  /**
   * Output only. The full list of permissions for the file. This is only
   * available if the requesting user can share the file. Not populated for
   * items in shared drives.
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
   * A collection of arbitrary key-value pairs which are visible to all apps.
   * Entries with null values are cleared in update and copy requests.
   *
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. The number of storage quota bytes used by the file. This
   * includes the head revision as well as previous revisions with `keepForever`
   * enabled.
   *
   * @param string $quotaBytesUsed
   */
  public function setQuotaBytesUsed($quotaBytesUsed)
  {
    $this->quotaBytesUsed = $quotaBytesUsed;
  }
  /**
   * @return string
   */
  public function getQuotaBytesUsed()
  {
    return $this->quotaBytesUsed;
  }
  /**
   * Output only. A key needed to access the item via a shared link.
   *
   * @param string $resourceKey
   */
  public function setResourceKey($resourceKey)
  {
    $this->resourceKey = $resourceKey;
  }
  /**
   * @return string
   */
  public function getResourceKey()
  {
    return $this->resourceKey;
  }
  /**
   * Output only. The SHA1 checksum associated with this file, if available.
   * This field is only populated for files with content stored in Google Drive;
   * it's not populated for Docs Editors or shortcut files.
   *
   * @param string $sha1Checksum
   */
  public function setSha1Checksum($sha1Checksum)
  {
    $this->sha1Checksum = $sha1Checksum;
  }
  /**
   * @return string
   */
  public function getSha1Checksum()
  {
    return $this->sha1Checksum;
  }
  /**
   * Output only. The SHA256 checksum associated with this file, if available.
   * This field is only populated for files with content stored in Google Drive;
   * it's not populated for Docs Editors or shortcut files.
   *
   * @param string $sha256Checksum
   */
  public function setSha256Checksum($sha256Checksum)
  {
    $this->sha256Checksum = $sha256Checksum;
  }
  /**
   * @return string
   */
  public function getSha256Checksum()
  {
    return $this->sha256Checksum;
  }
  /**
   * Output only. Whether the file has been shared. Not populated for items in
   * shared drives.
   *
   * @param bool $shared
   */
  public function setShared($shared)
  {
    $this->shared = $shared;
  }
  /**
   * @return bool
   */
  public function getShared()
  {
    return $this->shared;
  }
  /**
   * The time at which the file was shared with the user, if applicable (RFC
   * 3339 date-time).
   *
   * @param string $sharedWithMeTime
   */
  public function setSharedWithMeTime($sharedWithMeTime)
  {
    $this->sharedWithMeTime = $sharedWithMeTime;
  }
  /**
   * @return string
   */
  public function getSharedWithMeTime()
  {
    return $this->sharedWithMeTime;
  }
  /**
   * Output only. The user who shared the file with the requesting user, if
   * applicable.
   *
   * @param User $sharingUser
   */
  public function setSharingUser(User $sharingUser)
  {
    $this->sharingUser = $sharingUser;
  }
  /**
   * @return User
   */
  public function getSharingUser()
  {
    return $this->sharingUser;
  }
  /**
   * Shortcut file details. Only populated for shortcut files, which have the
   * mimeType field set to `application/vnd.google-apps.shortcut`. Can only be
   * set on `files.create` requests.
   *
   * @param DriveFileShortcutDetails $shortcutDetails
   */
  public function setShortcutDetails(DriveFileShortcutDetails $shortcutDetails)
  {
    $this->shortcutDetails = $shortcutDetails;
  }
  /**
   * @return DriveFileShortcutDetails
   */
  public function getShortcutDetails()
  {
    return $this->shortcutDetails;
  }
  /**
   * Output only. Size in bytes of blobs and Google Workspace editor files.
   * Won't be populated for files that have no size, like shortcuts and folders.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * Output only. The list of spaces which contain the file. The currently
   * supported values are `drive`, `appDataFolder`, and `photos`.
   *
   * @param string[] $spaces
   */
  public function setSpaces($spaces)
  {
    $this->spaces = $spaces;
  }
  /**
   * @return string[]
   */
  public function getSpaces()
  {
    return $this->spaces;
  }
  /**
   * Whether the user has starred the file.
   *
   * @param bool $starred
   */
  public function setStarred($starred)
  {
    $this->starred = $starred;
  }
  /**
   * @return bool
   */
  public function getStarred()
  {
    return $this->starred;
  }
  /**
   * Deprecated: Output only. Use `driveId` instead.
   *
   * @deprecated
   * @param string $teamDriveId
   */
  public function setTeamDriveId($teamDriveId)
  {
    $this->teamDriveId = $teamDriveId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTeamDriveId()
  {
    return $this->teamDriveId;
  }
  /**
   * Output only. A short-lived link to the file's thumbnail, if available.
   * Typically lasts on the order of hours. Not intended for direct usage on web
   * applications due to [Cross-Origin Resource Sharing
   * (CORS)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) policies.
   * Consider using a proxy server. Only populated when the requesting app can
   * access the file's content. If the file isn't shared publicly, the URL
   * returned in `files.thumbnailLink` must be fetched using a credentialed
   * request.
   *
   * @param string $thumbnailLink
   */
  public function setThumbnailLink($thumbnailLink)
  {
    $this->thumbnailLink = $thumbnailLink;
  }
  /**
   * @return string
   */
  public function getThumbnailLink()
  {
    return $this->thumbnailLink;
  }
  /**
   * Output only. The thumbnail version for use in thumbnail cache invalidation.
   *
   * @param string $thumbnailVersion
   */
  public function setThumbnailVersion($thumbnailVersion)
  {
    $this->thumbnailVersion = $thumbnailVersion;
  }
  /**
   * @return string
   */
  public function getThumbnailVersion()
  {
    return $this->thumbnailVersion;
  }
  /**
   * Whether the file has been trashed, either explicitly or from a trashed
   * parent folder. Only the owner may trash a file, and other users cannot see
   * files in the owner's trash.
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
   * The time that the item was trashed (RFC 3339 date-time). Only populated for
   * items in shared drives.
   *
   * @param string $trashedTime
   */
  public function setTrashedTime($trashedTime)
  {
    $this->trashedTime = $trashedTime;
  }
  /**
   * @return string
   */
  public function getTrashedTime()
  {
    return $this->trashedTime;
  }
  /**
   * Output only. If the file has been explicitly trashed, the user who trashed
   * it. Only populated for items in shared drives.
   *
   * @param User $trashingUser
   */
  public function setTrashingUser(User $trashingUser)
  {
    $this->trashingUser = $trashingUser;
  }
  /**
   * @return User
   */
  public function getTrashingUser()
  {
    return $this->trashingUser;
  }
  /**
   * Output only. A monotonically increasing version number for the file. This
   * reflects every change made to the file on the server, even those not
   * visible to the user.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * Output only. Additional metadata about video media. This may not be
   * available immediately upon upload.
   *
   * @param DriveFileVideoMediaMetadata $videoMediaMetadata
   */
  public function setVideoMediaMetadata(DriveFileVideoMediaMetadata $videoMediaMetadata)
  {
    $this->videoMediaMetadata = $videoMediaMetadata;
  }
  /**
   * @return DriveFileVideoMediaMetadata
   */
  public function getVideoMediaMetadata()
  {
    return $this->videoMediaMetadata;
  }
  /**
   * Output only. Whether the file has been viewed by this user.
   *
   * @param bool $viewedByMe
   */
  public function setViewedByMe($viewedByMe)
  {
    $this->viewedByMe = $viewedByMe;
  }
  /**
   * @return bool
   */
  public function getViewedByMe()
  {
    return $this->viewedByMe;
  }
  /**
   * The last time the file was viewed by the user (RFC 3339 date-time).
   *
   * @param string $viewedByMeTime
   */
  public function setViewedByMeTime($viewedByMeTime)
  {
    $this->viewedByMeTime = $viewedByMeTime;
  }
  /**
   * @return string
   */
  public function getViewedByMeTime()
  {
    return $this->viewedByMeTime;
  }
  /**
   * Deprecated: Use `copyRequiresWriterPermission` instead.
   *
   * @deprecated
   * @param bool $viewersCanCopyContent
   */
  public function setViewersCanCopyContent($viewersCanCopyContent)
  {
    $this->viewersCanCopyContent = $viewersCanCopyContent;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getViewersCanCopyContent()
  {
    return $this->viewersCanCopyContent;
  }
  /**
   * Output only. A link for downloading the content of the file in a browser.
   * This is only available for files with binary content in Google Drive.
   *
   * @param string $webContentLink
   */
  public function setWebContentLink($webContentLink)
  {
    $this->webContentLink = $webContentLink;
  }
  /**
   * @return string
   */
  public function getWebContentLink()
  {
    return $this->webContentLink;
  }
  /**
   * Output only. A link for opening the file in a relevant Google editor or
   * viewer in a browser.
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
  /**
   * Whether users with only `writer` permission can modify the file's
   * permissions. Not populated for items in shared drives.
   *
   * @param bool $writersCanShare
   */
  public function setWritersCanShare($writersCanShare)
  {
    $this->writersCanShare = $writersCanShare;
  }
  /**
   * @return bool
   */
  public function getWritersCanShare()
  {
    return $this->writersCanShare;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DriveFile::class, 'Google_Service_Drive_DriveFile');
