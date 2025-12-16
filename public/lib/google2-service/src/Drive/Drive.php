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

class Drive extends \Google\Model
{
  protected $backgroundImageFileType = DriveBackgroundImageFile::class;
  protected $backgroundImageFileDataType = '';
  /**
   * Output only. A short-lived link to this shared drive's background image.
   *
   * @var string
   */
  public $backgroundImageLink;
  protected $capabilitiesType = DriveCapabilities::class;
  protected $capabilitiesDataType = '';
  /**
   * The color of this shared drive as an RGB hex string. It can only be set on
   * a `drive.drives.update` request that does not set `themeId`.
   *
   * @var string
   */
  public $colorRgb;
  /**
   * The time at which the shared drive was created (RFC 3339 date-time).
   *
   * @var string
   */
  public $createdTime;
  /**
   * Whether the shared drive is hidden from default view.
   *
   * @var bool
   */
  public $hidden;
  /**
   * Output only. The ID of this shared drive which is also the ID of the top
   * level folder of this shared drive.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#drive"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of this shared drive.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The organizational unit of this shared drive. This field is
   * only populated on `drives.list` responses when the `useDomainAdminAccess`
   * parameter is set to `true`.
   *
   * @var string
   */
  public $orgUnitId;
  protected $restrictionsType = DriveRestrictions::class;
  protected $restrictionsDataType = '';
  /**
   * The ID of the theme from which the background image and color will be set.
   * The set of possible `driveThemes` can be retrieved from a `drive.about.get`
   * response. When not specified on a `drive.drives.create` request, a random
   * theme is chosen from which the background image and color are set. This is
   * a write-only field; it can only be set on requests that don't set
   * `colorRgb` or `backgroundImageFile`.
   *
   * @var string
   */
  public $themeId;

  /**
   * An image file and cropping parameters from which a background image for
   * this shared drive is set. This is a write only field; it can only be set on
   * `drive.drives.update` requests that don't set `themeId`. When specified,
   * all fields of the `backgroundImageFile` must be set.
   *
   * @param DriveBackgroundImageFile $backgroundImageFile
   */
  public function setBackgroundImageFile(DriveBackgroundImageFile $backgroundImageFile)
  {
    $this->backgroundImageFile = $backgroundImageFile;
  }
  /**
   * @return DriveBackgroundImageFile
   */
  public function getBackgroundImageFile()
  {
    return $this->backgroundImageFile;
  }
  /**
   * Output only. A short-lived link to this shared drive's background image.
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
   * Output only. Capabilities the current user has on this shared drive.
   *
   * @param DriveCapabilities $capabilities
   */
  public function setCapabilities(DriveCapabilities $capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return DriveCapabilities
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * The color of this shared drive as an RGB hex string. It can only be set on
   * a `drive.drives.update` request that does not set `themeId`.
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
   * The time at which the shared drive was created (RFC 3339 date-time).
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
   * Whether the shared drive is hidden from default view.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * Output only. The ID of this shared drive which is also the ID of the top
   * level folder of this shared drive.
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
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string `"drive#drive"`.
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
   * The name of this shared drive.
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
   * Output only. The organizational unit of this shared drive. This field is
   * only populated on `drives.list` responses when the `useDomainAdminAccess`
   * parameter is set to `true`.
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
   * A set of restrictions that apply to this shared drive or items inside this
   * shared drive. Note that restrictions can't be set when creating a shared
   * drive. To add a restriction, first create a shared drive and then use
   * `drives.update` to add restrictions.
   *
   * @param DriveRestrictions $restrictions
   */
  public function setRestrictions(DriveRestrictions $restrictions)
  {
    $this->restrictions = $restrictions;
  }
  /**
   * @return DriveRestrictions
   */
  public function getRestrictions()
  {
    return $this->restrictions;
  }
  /**
   * The ID of the theme from which the background image and color will be set.
   * The set of possible `driveThemes` can be retrieved from a `drive.about.get`
   * response. When not specified on a `drive.drives.create` request, a random
   * theme is chosen from which the background image and color are set. This is
   * a write-only field; it can only be set on requests that don't set
   * `colorRgb` or `backgroundImageFile`.
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
class_alias(Drive::class, 'Google_Service_Drive_Drive');
