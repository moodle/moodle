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

class AssetAssociation extends \Google\Model
{
  /**
   * Asset role is not specified or is unknown in this version.
   */
  public const ROLE_ASSET_ROLE_UNSPECIFIED = 'ASSET_ROLE_UNSPECIFIED';
  /**
   * The asset is the main asset of the creative.
   */
  public const ROLE_ASSET_ROLE_MAIN = 'ASSET_ROLE_MAIN';
  /**
   * The asset is a backup asset of the creative.
   */
  public const ROLE_ASSET_ROLE_BACKUP = 'ASSET_ROLE_BACKUP';
  /**
   * The asset is a polite load asset of the creative.
   */
  public const ROLE_ASSET_ROLE_POLITE_LOAD = 'ASSET_ROLE_POLITE_LOAD';
  /**
   * Headline of a native creative. The content must be UTF-8 encoded with a
   * length of no more than 25 characters. This role is only supported in the
   * following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_HEADLINE = 'ASSET_ROLE_HEADLINE';
  /**
   * Long headline of a native creative. The content must be UTF-8 encoded with
   * a length of no more than 50 characters. This role is only supported in the
   * following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_LONG_HEADLINE = 'ASSET_ROLE_LONG_HEADLINE';
  /**
   * Body text of a native creative. The content must be UTF-8 encoded with a
   * length of no more than 90 characters. This role is only supported in the
   * following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_BODY = 'ASSET_ROLE_BODY';
  /**
   * Long body text of a native creative. The content must be UTF-8 encoded with
   * a length of no more than 150 characters. This role is only supported in the
   * following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_LONG_BODY = 'ASSET_ROLE_LONG_BODY';
  /**
   * A short, friendly version of the landing page URL to show in the creative.
   * This URL gives people an idea of where they'll arrive after they click on
   * the creative. The content must be UTF-8 encoded with a length of no more
   * than 30 characters. For example, if the landing page URL is
   * 'http://www.example.com/page', the caption URL can be 'example.com'. The
   * protocol (http://) is optional, but the URL can't contain spaces or special
   * characters. This role is only supported in the following creative_type: *
   * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_CAPTION_URL = 'ASSET_ROLE_CAPTION_URL';
  /**
   * The text to use on the call-to-action button of a native creative. The
   * content must be UTF-8 encoded with a length of no more than 15 characters.
   * This role is only supported in the following creative_type: *
   * `CREATIVE_TYPE_NATIVE` * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` *
   * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_CALL_TO_ACTION = 'ASSET_ROLE_CALL_TO_ACTION';
  /**
   * The text that identifies the advertiser or brand name. The content must be
   * UTF-8 encoded with a length of no more than 25 characters. This role is
   * only supported in the following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE` * `CREATIVE_TYPE_NATIVE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_ADVERTISER_NAME = 'ASSET_ROLE_ADVERTISER_NAME';
  /**
   * The purchase price of your app in the Google play store or iOS app store
   * (for example, $5.99). Note that this value is not automatically synced with
   * the actual value listed in the store. It will always be the one provided
   * when save the creative. The content must be UTF-8 encoded with a length of
   * no more than 15 characters. Assets of this role are read-only.
   */
  public const ROLE_ASSET_ROLE_PRICE = 'ASSET_ROLE_PRICE';
  /**
   * The ID of an Android app in the Google play store. You can find this ID in
   * the App’s Google Play Store URL after ‘id’. For example, in
   * `https://play.google.com/store/apps/details?id=com.company.appname` the
   * identifier is com.company.appname. Assets of this role are read-only.
   */
  public const ROLE_ASSET_ROLE_ANDROID_APP_ID = 'ASSET_ROLE_ANDROID_APP_ID';
  /**
   * The ID of an iOS app in the Apple app store. This ID number can be found in
   * the Apple App Store URL as the string of numbers directly after "id". For
   * example, in `https://apps.apple.com/us/app/gmail-email-by-
   * google/id422689480` the ID is 422689480. Assets of this role are read-only.
   */
  public const ROLE_ASSET_ROLE_IOS_APP_ID = 'ASSET_ROLE_IOS_APP_ID';
  /**
   * The rating of an app in the Google play store or iOS app store. Note that
   * this value is not automatically synced with the actual rating in the store.
   * It will always be the one provided when save the creative. Assets of this
   * role are read-only.
   */
  public const ROLE_ASSET_ROLE_RATING = 'ASSET_ROLE_RATING';
  /**
   * The icon of a creative. This role is only supported and required in the
   * following creative_type: * `CREATIVE_TYPE_NATIVE` *
   * `CREATIVE_TYPE_NATIVE_SITE_SQUARE`
   */
  public const ROLE_ASSET_ROLE_ICON = 'ASSET_ROLE_ICON';
  /**
   * The cover image of a native video creative. This role is only supported and
   * required in the following creative_type: * `CREATIVE_TYPE_VIDEO`
   */
  public const ROLE_ASSET_ROLE_COVER_IMAGE = 'ASSET_ROLE_COVER_IMAGE';
  /**
   * The main color to use in a creative. This role is only supported and
   * required in the following creative_type: *
   * `CREATIVE_TYPE_ASSET_BASED_CREATIVE`
   */
  public const ROLE_ASSET_ROLE_BACKGROUND_COLOR = 'ASSET_ROLE_BACKGROUND_COLOR';
  /**
   * The accent color to use in a creative. This role is only supported and
   * required in the following creative_type: *
   * `CREATIVE_TYPE_ASSET_BASED_CREATIVE`
   */
  public const ROLE_ASSET_ROLE_ACCENT_COLOR = 'ASSET_ROLE_ACCENT_COLOR';
  /**
   * Whether the creative must use a logo asset. This role is only supported and
   * required in the following creative_type: *
   * `CREATIVE_TYPE_ASSET_BASED_CREATIVE`
   */
  public const ROLE_ASSET_ROLE_REQUIRE_LOGO = 'ASSET_ROLE_REQUIRE_LOGO';
  /**
   * Whether the creative must use an image asset. This role is only supported
   * and required in the following creative_type: *
   * `CREATIVE_TYPE_ASSET_BASED_CREATIVE`
   */
  public const ROLE_ASSET_ROLE_REQUIRE_IMAGE = 'ASSET_ROLE_REQUIRE_IMAGE';
  /**
   * Whether asset enhancements can be applied to the creative. This role is
   * only supported and required in the following creative_type: *
   * `CREATIVE_TYPE_ASSET_BASED_CREATIVE`
   */
  public const ROLE_ASSET_ROLE_ENABLE_ASSET_ENHANCEMENTS = 'ASSET_ROLE_ENABLE_ASSET_ENHANCEMENTS';
  protected $assetType = Asset::class;
  protected $assetDataType = '';
  /**
   * Optional. The role of this asset for the creative.
   *
   * @var string
   */
  public $role;

  /**
   * Optional. The associated asset.
   *
   * @param Asset $asset
   */
  public function setAsset(Asset $asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return Asset
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * Optional. The role of this asset for the creative.
   *
   * Accepted values: ASSET_ROLE_UNSPECIFIED, ASSET_ROLE_MAIN,
   * ASSET_ROLE_BACKUP, ASSET_ROLE_POLITE_LOAD, ASSET_ROLE_HEADLINE,
   * ASSET_ROLE_LONG_HEADLINE, ASSET_ROLE_BODY, ASSET_ROLE_LONG_BODY,
   * ASSET_ROLE_CAPTION_URL, ASSET_ROLE_CALL_TO_ACTION,
   * ASSET_ROLE_ADVERTISER_NAME, ASSET_ROLE_PRICE, ASSET_ROLE_ANDROID_APP_ID,
   * ASSET_ROLE_IOS_APP_ID, ASSET_ROLE_RATING, ASSET_ROLE_ICON,
   * ASSET_ROLE_COVER_IMAGE, ASSET_ROLE_BACKGROUND_COLOR,
   * ASSET_ROLE_ACCENT_COLOR, ASSET_ROLE_REQUIRE_LOGO, ASSET_ROLE_REQUIRE_IMAGE,
   * ASSET_ROLE_ENABLE_ASSET_ENHANCEMENTS
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetAssociation::class, 'Google_Service_DisplayVideo_AssetAssociation');
