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

namespace Google\Service\FirebaseDynamicLinks;

class IosInfo extends \Google\Model
{
  /**
   * iOS App Store ID.
   *
   * @var string
   */
  public $iosAppStoreId;
  /**
   * iOS bundle ID of the app.
   *
   * @var string
   */
  public $iosBundleId;
  /**
   * Custom (destination) scheme to use for iOS. By default, we’ll use the
   * bundle ID as the custom scheme. Developer can override this behavior using
   * this param.
   *
   * @var string
   */
  public $iosCustomScheme;
  /**
   * Link to open on iOS if the app is not installed.
   *
   * @var string
   */
  public $iosFallbackLink;
  /**
   * iPad bundle ID of the app.
   *
   * @var string
   */
  public $iosIpadBundleId;
  /**
   * If specified, this overrides the ios_fallback_link value on iPads.
   *
   * @var string
   */
  public $iosIpadFallbackLink;
  /**
   * iOS minimum version.
   *
   * @var string
   */
  public $iosMinimumVersion;

  /**
   * iOS App Store ID.
   *
   * @param string $iosAppStoreId
   */
  public function setIosAppStoreId($iosAppStoreId)
  {
    $this->iosAppStoreId = $iosAppStoreId;
  }
  /**
   * @return string
   */
  public function getIosAppStoreId()
  {
    return $this->iosAppStoreId;
  }
  /**
   * iOS bundle ID of the app.
   *
   * @param string $iosBundleId
   */
  public function setIosBundleId($iosBundleId)
  {
    $this->iosBundleId = $iosBundleId;
  }
  /**
   * @return string
   */
  public function getIosBundleId()
  {
    return $this->iosBundleId;
  }
  /**
   * Custom (destination) scheme to use for iOS. By default, we’ll use the
   * bundle ID as the custom scheme. Developer can override this behavior using
   * this param.
   *
   * @param string $iosCustomScheme
   */
  public function setIosCustomScheme($iosCustomScheme)
  {
    $this->iosCustomScheme = $iosCustomScheme;
  }
  /**
   * @return string
   */
  public function getIosCustomScheme()
  {
    return $this->iosCustomScheme;
  }
  /**
   * Link to open on iOS if the app is not installed.
   *
   * @param string $iosFallbackLink
   */
  public function setIosFallbackLink($iosFallbackLink)
  {
    $this->iosFallbackLink = $iosFallbackLink;
  }
  /**
   * @return string
   */
  public function getIosFallbackLink()
  {
    return $this->iosFallbackLink;
  }
  /**
   * iPad bundle ID of the app.
   *
   * @param string $iosIpadBundleId
   */
  public function setIosIpadBundleId($iosIpadBundleId)
  {
    $this->iosIpadBundleId = $iosIpadBundleId;
  }
  /**
   * @return string
   */
  public function getIosIpadBundleId()
  {
    return $this->iosIpadBundleId;
  }
  /**
   * If specified, this overrides the ios_fallback_link value on iPads.
   *
   * @param string $iosIpadFallbackLink
   */
  public function setIosIpadFallbackLink($iosIpadFallbackLink)
  {
    $this->iosIpadFallbackLink = $iosIpadFallbackLink;
  }
  /**
   * @return string
   */
  public function getIosIpadFallbackLink()
  {
    return $this->iosIpadFallbackLink;
  }
  /**
   * iOS minimum version.
   *
   * @param string $iosMinimumVersion
   */
  public function setIosMinimumVersion($iosMinimumVersion)
  {
    $this->iosMinimumVersion = $iosMinimumVersion;
  }
  /**
   * @return string
   */
  public function getIosMinimumVersion()
  {
    return $this->iosMinimumVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosInfo::class, 'Google_Service_FirebaseDynamicLinks_IosInfo');
