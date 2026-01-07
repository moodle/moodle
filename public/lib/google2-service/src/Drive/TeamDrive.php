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

class TeamDrive extends \Google\Model
{
  protected $backgroundImageFileType = TeamDriveBackgroundImageFile::class;
  protected $backgroundImageFileDataType = '';
  /**
   * A short-lived link to this Team Drive's background image.
   *
   * @var string
   */
  public $backgroundImageLink;
  protected $capabilitiesType = TeamDriveCapabilities::class;
  protected $capabilitiesDataType = '';
  /**
   * The color of this Team Drive as an RGB hex string. It can only be set on a
   * `drive.teamdrives.update` request that does not set `themeId`.
   *
   * @var string
   */
  public $colorRgb;
  /**
   * The time at which the Team Drive was created (RFC 3339 date-time).
   *
   * @var string
   */
  public $createdTime;
  /**
   * The ID of this Team Drive which is also the ID of the top level folder of
   * this Team Drive.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#teamDrive"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of this Team Drive.
   *
   * @var string
   */
  public $name;
  /**
   * The organizational unit of this shared drive. This field is only populated
   * on `drives.list` responses when the `useDomainAdminAccess` parameter is set
   * to `true`.
   *
   * @var string
   */
  public $orgUnitId;
  protected $restrictionsType = TeamDriveRestrictions::class;
  protected $restrictionsDataType = '';
  /**
   * The ID of the theme from which the background image and color will be set.
   * The set of possible `teamDriveThemes` can be retrieved from a
   * `drive.about.get` response. When not specified on a
   * `drive.teamdrives.create` request, a random theme is chosen from which the
   * background image and color are set. This is a write-only field; it can only
   * be set on requests that don't set `colorRgb` or `backgroundImageFile`.
   *
   * @var string
   */
  public $themeId;

  /**
   * An image file and cropping parameters from which a background image for
   * this Team Drive is set. This is a write only field; it can only be set on
   * `drive.teamdrives.update` requests that don't set `themeId`. When
   * specified, all fields of the `backgroundImageFile` must be set.
   *
   * @param TeamDriveBackgroundImageFile $backgroundImageFile
   */
  public function setBackgroundImageFile(TeamDriveBackgroundImageFile $backgroundImageFile)
  {
    $this->backgroundImageFile = $backgroundImageFile;
  }
  /**
   * @return TeamDriveBackgroundImageFile
   */
  public function getBackgroundImageFile()
  {
    return $this->backgroundImageFile;
  }
  /**
   * A short-lived link to this Team Drive's background image.
   *
   * @param string $backgroundImageLink
   */
  public function setBackgroundImageLink($backgroundImageLink)
  {
    $this->backgroundImageLink = $backgroundImageLink;
  }
  /**
   * @return string
   */
  public function getBackgroundImageLink()
  {
    return $this->backgroundImageLink;
  }
  /**
   * Capabilities the current user has on this Team Drive.
   *
   * @param TeamDriveCapabilities $capabilities
   */
  public function setCapabilities(TeamDriveCapabilities $capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return TeamDriveCapabilities
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * The color of this Team Drive as an RGB hex string. It can only be set on a
   * `drive.teamdrives.update` request that does not set `themeId`.
   *
   * @param string $colorRgb
   */
  public function setColorRgb($colorRgb)
  {
    $this->colorRgb = $colorRgb;
  }
  /**
   * @return string
   */
  public function getColorRgb()
  {
    return $this->colorRgb;
  }
  /**
   * The time at which the Team Drive was created (RFC 3339 date-time).
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
   * The ID of this Team Drive which is also the ID of the top level folder of
   * this Team Drive.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#teamDrive"`.
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
   * The name of this Team Drive.
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
   * The organizational unit of this shared drive. This field is only populated
   * on `drives.list` responses when the `useDomainAdminAccess` parameter is set
   * to `true`.
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * A set of restrictions that apply to this Team Drive or items inside this
   * Team Drive.
   *
   * @param TeamDriveRestrictions $restrictions
   */
  public function setRestrictions(TeamDriveRestrictions $restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return TeamDriveRestrictions
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
  /**
   * The ID of the theme from which the background image and color will be set.
   * The set of possible `teamDriveThemes` can be retrieved from a
   * `drive.about.get` response. When not specified on a
   * `drive.teamdrives.create` request, a random theme is chosen from which the
   * background image and color are set. This is a write-only field; it can only
   * be set on requests that don't set `colorRgb` or `backgroundImageFile`.
   *
   * @param string $themeId
   */
  public function setThemeId($themeId)
  {
    $this->themeId = $themeId;
  }
  /**
   * @return string
   */
  public function getThemeId()
  {
    return $this->themeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TeamDrive::class, 'Google_Service_Drive_TeamDrive');
