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

class About extends \Google\Collection
{
  protected $collection_key = 'teamDriveThemes';
  /**
   * Whether the user has installed the requesting app.
   *
   * @var bool
   */
  public $appInstalled;
  /**
   * Whether the user can create shared drives.
   *
   * @var bool
   */
  public $canCreateDrives;
  /**
   * Deprecated: Use `canCreateDrives` instead.
   *
   * @deprecated
   * @var bool
   */
  public $canCreateTeamDrives;
  protected $driveThemesType = AboutDriveThemes::class;
  protected $driveThemesDataType = 'array';
  /**
   * A map of source MIME type to possible targets for all supported exports.
   *
   * @var string[]
   */
  public $exportFormats;
  /**
   * The currently supported folder colors as RGB hex strings.
   *
   * @var string[]
   */
  public $folderColorPalette;
  /**
   * A map of source MIME type to possible targets for all supported imports.
   *
   * @var string[]
   */
  public $importFormats;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#about"`.
   *
   * @var string
   */
  public $kind;
  /**
   * A map of maximum import sizes by MIME type, in bytes.
   *
   * @var string[]
   */
  public $maxImportSizes;
  /**
   * The maximum upload size in bytes.
   *
   * @var string
   */
  public $maxUploadSize;
  protected $storageQuotaType = AboutStorageQuota::class;
  protected $storageQuotaDataType = '';
  protected $teamDriveThemesType = AboutTeamDriveThemes::class;
  protected $teamDriveThemesDataType = 'array';
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * Whether the user has installed the requesting app.
   *
   * @param bool $appInstalled
   */
  public function setAppInstalled($appInstalled)
  {
    $this->appInstalled = $appInstalled;
  }
  /**
   * @return bool
   */
  public function getAppInstalled()
  {
    return $this->appInstalled;
  }
  /**
   * Whether the user can create shared drives.
   *
   * @param bool $canCreateDrives
   */
  public function setCanCreateDrives($canCreateDrives)
  {
    $this->canCreateDrives = $canCreateDrives;
  }
  /**
   * @return bool
   */
  public function getCanCreateDrives()
  {
    return $this->canCreateDrives;
  }
  /**
   * Deprecated: Use `canCreateDrives` instead.
   *
   * @deprecated
   * @param bool $canCreateTeamDrives
   */
  public function setCanCreateTeamDrives($canCreateTeamDrives)
  {
    $this->canCreateTeamDrives = $canCreateTeamDrives;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCanCreateTeamDrives()
  {
    return $this->canCreateTeamDrives;
  }
  /**
   * A list of themes that are supported for shared drives.
   *
   * @param AboutDriveThemes[] $driveThemes
   */
  public function setDriveThemes($driveThemes)
  {
    $this->driveThemes = $driveThemes;
  }
  /**
   * @return AboutDriveThemes[]
   */
  public function getDriveThemes()
  {
    return $this->driveThemes;
  }
  /**
   * A map of source MIME type to possible targets for all supported exports.
   *
   * @param string[] $exportFormats
   */
  public function setExportFormats($exportFormats)
  {
    $this->exportFormats = $exportFormats;
  }
  /**
   * @return string[]
   */
  public function getExportFormats()
  {
    return $this->exportFormats;
  }
  /**
   * The currently supported folder colors as RGB hex strings.
   *
   * @param string[] $folderColorPalette
   */
  public function setFolderColorPalette($folderColorPalette)
  {
    $this->folderColorPalette = $folderColorPalette;
  }
  /**
   * @return string[]
   */
  public function getFolderColorPalette()
  {
    return $this->folderColorPalette;
  }
  /**
   * A map of source MIME type to possible targets for all supported imports.
   *
   * @param string[] $importFormats
   */
  public function setImportFormats($importFormats)
  {
    $this->importFormats = $importFormats;
  }
  /**
   * @return string[]
   */
  public function getImportFormats()
  {
    return $this->importFormats;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#about"`.
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
   * A map of maximum import sizes by MIME type, in bytes.
   *
   * @param string[] $maxImportSizes
   */
  public function setMaxImportSizes($maxImportSizes)
  {
    $this->maxImportSizes = $maxImportSizes;
  }
  /**
   * @return string[]
   */
  public function getMaxImportSizes()
  {
    return $this->maxImportSizes;
  }
  /**
   * The maximum upload size in bytes.
   *
   * @param string $maxUploadSize
   */
  public function setMaxUploadSize($maxUploadSize)
  {
    $this->maxUploadSize = $maxUploadSize;
  }
  /**
   * @return string
   */
  public function getMaxUploadSize()
  {
    return $this->maxUploadSize;
  }
  /**
   * The user's storage quota limits and usage. For users that are part of an
   * organization with pooled storage, information about the limit and usage
   * across all services is for the organization, rather than the individual
   * user. All fields are measured in bytes.
   *
   * @param AboutStorageQuota $storageQuota
   */
  public function setStorageQuota(AboutStorageQuota $storageQuota)
  {
    $this->storageQuota = $storageQuota;
  }
  /**
   * @return AboutStorageQuota
   */
  public function getStorageQuota()
  {
    return $this->storageQuota;
  }
  /**
   * Deprecated: Use `driveThemes` instead.
   *
   * @deprecated
   * @param AboutTeamDriveThemes[] $teamDriveThemes
   */
  public function setTeamDriveThemes($teamDriveThemes)
  {
    $this->teamDriveThemes = $teamDriveThemes;
  }
  /**
   * @deprecated
   * @return AboutTeamDriveThemes[]
   */
  public function getTeamDriveThemes()
  {
    return $this->teamDriveThemes;
  }
  /**
   * The authenticated user.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(About::class, 'Google_Service_Drive_About');
