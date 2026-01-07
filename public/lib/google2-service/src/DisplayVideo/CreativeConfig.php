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

namespace Google\Service\DisplayVideo;

class CreativeConfig extends \Google\Model
{
  /**
   * Type value is not specified or is unknown in this version.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_UNSPECIFIED = 'CREATIVE_TYPE_UNSPECIFIED';
  /**
   * Standard display creative. Create and update methods are supported for this
   * creative type if the hosting_source is one of the following: *
   * `HOSTING_SOURCE_HOSTED` * `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_STANDARD = 'CREATIVE_TYPE_STANDARD';
  /**
   * Expandable creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_EXPANDABLE = 'CREATIVE_TYPE_EXPANDABLE';
  /**
   * Video creative. Create and update methods are supported for this creative
   * type if the hosting_source is one of the following: *
   * `HOSTING_SOURCE_HOSTED` * `HOSTING_SOURCE_THIRD_PARTY`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_VIDEO = 'CREATIVE_TYPE_VIDEO';
  /**
   * Native creative rendered by publishers with assets from advertiser. Create
   * and update methods are supported for this creative type if the
   * hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE = 'CREATIVE_TYPE_NATIVE';
  /**
   * Templated app install mobile creative (banner). Create and update methods
   * are **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL';
  /**
   * Square native creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_SITE_SQUARE = 'CREATIVE_TYPE_NATIVE_SITE_SQUARE';
  /**
   * Interstitial creative including both display and video. Create and update
   * methods are **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL';
  /**
   * Responsive and expandable Lightbox creative. Create and update methods are
   * **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_LIGHTBOX = 'CREATIVE_TYPE_LIGHTBOX';
  /**
   * Native app install creative. Create and update methods are **not**
   * supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_APP_INSTALL = 'CREATIVE_TYPE_NATIVE_APP_INSTALL';
  /**
   * Square native app install creative. Create and update methods are **not**
   * supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE = 'CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE';
  /**
   * Audio creative. Create and update methods are supported for this creative
   * type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_AUDIO = 'CREATIVE_TYPE_AUDIO';
  /**
   * Publisher hosted creative. Create and update methods are **not** supported
   * for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_PUBLISHER_HOSTED = 'CREATIVE_TYPE_PUBLISHER_HOSTED';
  /**
   * Native video creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_NATIVE_VIDEO = 'CREATIVE_TYPE_NATIVE_VIDEO';
  /**
   * Templated app install mobile video creative. Create and update methods are
   * **not** supported for this creative type.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO = 'CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO';
  /**
   * Asset-based creative. Create and update methods are supported for this
   * creative type if the hosting_source is `HOSTING_SOURCE_HOSTED`.
   */
  public const CREATIVE_TYPE_CREATIVE_TYPE_ASSET_BASED_CREATIVE = 'CREATIVE_TYPE_ASSET_BASED_CREATIVE';
  /**
   * The type of creative that can be assigned to the inventory source. Only the
   * following types are supported: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_VIDEO`
   *
   * @var string
   */
  public $creativeType;
  protected $displayCreativeConfigType = InventorySourceDisplayCreativeConfig::class;
  protected $displayCreativeConfigDataType = '';
  protected $videoCreativeConfigType = InventorySourceVideoCreativeConfig::class;
  protected $videoCreativeConfigDataType = '';

  /**
   * The type of creative that can be assigned to the inventory source. Only the
   * following types are supported: * `CREATIVE_TYPE_STANDARD` *
   * `CREATIVE_TYPE_VIDEO`
   *
   * Accepted values: CREATIVE_TYPE_UNSPECIFIED, CREATIVE_TYPE_STANDARD,
   * CREATIVE_TYPE_EXPANDABLE, CREATIVE_TYPE_VIDEO, CREATIVE_TYPE_NATIVE,
   * CREATIVE_TYPE_TEMPLATED_APP_INSTALL, CREATIVE_TYPE_NATIVE_SITE_SQUARE,
   * CREATIVE_TYPE_TEMPLATED_APP_INSTALL_INTERSTITIAL, CREATIVE_TYPE_LIGHTBOX,
   * CREATIVE_TYPE_NATIVE_APP_INSTALL, CREATIVE_TYPE_NATIVE_APP_INSTALL_SQUARE,
   * CREATIVE_TYPE_AUDIO, CREATIVE_TYPE_PUBLISHER_HOSTED,
   * CREATIVE_TYPE_NATIVE_VIDEO, CREATIVE_TYPE_TEMPLATED_APP_INSTALL_VIDEO,
   * CREATIVE_TYPE_ASSET_BASED_CREATIVE
   *
   * @param self::CREATIVE_TYPE_* $creativeType
   */
  public function setCreativeType($creativeType)
  {
    $this->creativeType = $creativeType;
  }
  /**
   * @return self::CREATIVE_TYPE_*
   */
  public function getCreativeType()
  {
    return $this->creativeType;
  }
  /**
   * The configuration for display creatives. Applicable when creative_type is
   * `CREATIVE_TYPE_STANDARD`.
   *
   * @param InventorySourceDisplayCreativeConfig $displayCreativeConfig
   */
  public function setDisplayCreativeConfig(InventorySourceDisplayCreativeConfig $displayCreativeConfig)
  {
    $this->displayCreativeConfig = $displayCreativeConfig;
  }
  /**
   * @return InventorySourceDisplayCreativeConfig
   */
  public function getDisplayCreativeConfig()
  {
    return $this->displayCreativeConfig;
  }
  /**
   * The configuration for video creatives. Applicable when creative_type is
   * `CREATIVE_TYPE_VIDEO`.
   *
   * @param InventorySourceVideoCreativeConfig $videoCreativeConfig
   */
  public function setVideoCreativeConfig(InventorySourceVideoCreativeConfig $videoCreativeConfig)
  {
    $this->videoCreativeConfig = $videoCreativeConfig;
  }
  /**
   * @return InventorySourceVideoCreativeConfig
   */
  public function getVideoCreativeConfig()
  {
    return $this->videoCreativeConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeConfig::class, 'Google_Service_DisplayVideo_CreativeConfig');
