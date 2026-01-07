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

namespace Google\Service\YouTube;

class ImageSettings extends \Google\Model
{
  protected $backgroundImageUrlType = LocalizedProperty::class;
  protected $backgroundImageUrlDataType = '';
  /**
   * This is generated when a ChannelBanner.Insert request has succeeded for the
   * given channel.
   *
   * @var string
   */
  public $bannerExternalUrl;
  /**
   * Banner image. Desktop size (1060x175).
   *
   * @deprecated
   * @var string
   */
  public $bannerImageUrl;
  /**
   * Banner image. Mobile size high resolution (1440x395).
   *
   * @deprecated
   * @var string
   */
  public $bannerMobileExtraHdImageUrl;
  /**
   * Banner image. Mobile size high resolution (1280x360).
   *
   * @deprecated
   * @var string
   */
  public $bannerMobileHdImageUrl;
  /**
   * Banner image. Mobile size (640x175).
   *
   * @deprecated
   * @var string
   */
  public $bannerMobileImageUrl;
  /**
   * Banner image. Mobile size low resolution (320x88).
   *
   * @deprecated
   * @var string
   */
  public $bannerMobileLowImageUrl;
  /**
   * Banner image. Mobile size medium/high resolution (960x263).
   *
   * @deprecated
   * @var string
   */
  public $bannerMobileMediumHdImageUrl;
  /**
   * Banner image. Tablet size extra high resolution (2560x424).
   *
   * @deprecated
   * @var string
   */
  public $bannerTabletExtraHdImageUrl;
  /**
   * Banner image. Tablet size high resolution (2276x377).
   *
   * @deprecated
   * @var string
   */
  public $bannerTabletHdImageUrl;
  /**
   * Banner image. Tablet size (1707x283).
   *
   * @deprecated
   * @var string
   */
  public $bannerTabletImageUrl;
  /**
   * Banner image. Tablet size low resolution (1138x188).
   *
   * @deprecated
   * @var string
   */
  public $bannerTabletLowImageUrl;
  /**
   * Banner image. TV size high resolution (1920x1080).
   *
   * @deprecated
   * @var string
   */
  public $bannerTvHighImageUrl;
  /**
   * Banner image. TV size extra high resolution (2120x1192).
   *
   * @deprecated
   * @var string
   */
  public $bannerTvImageUrl;
  /**
   * Banner image. TV size low resolution (854x480).
   *
   * @deprecated
   * @var string
   */
  public $bannerTvLowImageUrl;
  /**
   * Banner image. TV size medium resolution (1280x720).
   *
   * @deprecated
   * @var string
   */
  public $bannerTvMediumImageUrl;
  protected $largeBrandedBannerImageImapScriptType = LocalizedProperty::class;
  protected $largeBrandedBannerImageImapScriptDataType = '';
  protected $largeBrandedBannerImageUrlType = LocalizedProperty::class;
  protected $largeBrandedBannerImageUrlDataType = '';
  protected $smallBrandedBannerImageImapScriptType = LocalizedProperty::class;
  protected $smallBrandedBannerImageImapScriptDataType = '';
  protected $smallBrandedBannerImageUrlType = LocalizedProperty::class;
  protected $smallBrandedBannerImageUrlDataType = '';
  /**
   * The URL for a 1px by 1px tracking pixel that can be used to collect
   * statistics for views of the channel or video pages.
   *
   * @deprecated
   * @var string
   */
  public $trackingImageUrl;
  /**
   * @deprecated
   * @var string
   */
  public $watchIconImageUrl;

  /**
   * The URL for the background image shown on the video watch page. The image
   * should be 1200px by 615px, with a maximum file size of 128k.
   *
   * @deprecated
   * @param LocalizedProperty $backgroundImageUrl
   */
  public function setBackgroundImageUrl(LocalizedProperty $backgroundImageUrl)
  {
    $this->backgroundImageUrl = $backgroundImageUrl;
  }
  /**
   * @deprecated
   * @return LocalizedProperty
   */
  public function getBackgroundImageUrl()
  {
    return $this->backgroundImageUrl;
  }
  /**
   * This is generated when a ChannelBanner.Insert request has succeeded for the
   * given channel.
   *
   * @param string $bannerExternalUrl
   */
  public function setBannerExternalUrl($bannerExternalUrl)
  {
    $this->bannerExternalUrl = $bannerExternalUrl;
  }
  /**
   * @return string
   */
  public function getBannerExternalUrl()
  {
    return $this->bannerExternalUrl;
  }
  /**
   * Banner image. Desktop size (1060x175).
   *
   * @deprecated
   * @param string $bannerImageUrl
   */
  public function setBannerImageUrl($bannerImageUrl)
  {
    $this->bannerImageUrl = $bannerImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerImageUrl()
  {
    return $this->bannerImageUrl;
  }
  /**
   * Banner image. Mobile size high resolution (1440x395).
   *
   * @deprecated
   * @param string $bannerMobileExtraHdImageUrl
   */
  public function setBannerMobileExtraHdImageUrl($bannerMobileExtraHdImageUrl)
  {
    $this->bannerMobileExtraHdImageUrl = $bannerMobileExtraHdImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerMobileExtraHdImageUrl()
  {
    return $this->bannerMobileExtraHdImageUrl;
  }
  /**
   * Banner image. Mobile size high resolution (1280x360).
   *
   * @deprecated
   * @param string $bannerMobileHdImageUrl
   */
  public function setBannerMobileHdImageUrl($bannerMobileHdImageUrl)
  {
    $this->bannerMobileHdImageUrl = $bannerMobileHdImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerMobileHdImageUrl()
  {
    return $this->bannerMobileHdImageUrl;
  }
  /**
   * Banner image. Mobile size (640x175).
   *
   * @deprecated
   * @param string $bannerMobileImageUrl
   */
  public function setBannerMobileImageUrl($bannerMobileImageUrl)
  {
    $this->bannerMobileImageUrl = $bannerMobileImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerMobileImageUrl()
  {
    return $this->bannerMobileImageUrl;
  }
  /**
   * Banner image. Mobile size low resolution (320x88).
   *
   * @deprecated
   * @param string $bannerMobileLowImageUrl
   */
  public function setBannerMobileLowImageUrl($bannerMobileLowImageUrl)
  {
    $this->bannerMobileLowImageUrl = $bannerMobileLowImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerMobileLowImageUrl()
  {
    return $this->bannerMobileLowImageUrl;
  }
  /**
   * Banner image. Mobile size medium/high resolution (960x263).
   *
   * @deprecated
   * @param string $bannerMobileMediumHdImageUrl
   */
  public function setBannerMobileMediumHdImageUrl($bannerMobileMediumHdImageUrl)
  {
    $this->bannerMobileMediumHdImageUrl = $bannerMobileMediumHdImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerMobileMediumHdImageUrl()
  {
    return $this->bannerMobileMediumHdImageUrl;
  }
  /**
   * Banner image. Tablet size extra high resolution (2560x424).
   *
   * @deprecated
   * @param string $bannerTabletExtraHdImageUrl
   */
  public function setBannerTabletExtraHdImageUrl($bannerTabletExtraHdImageUrl)
  {
    $this->bannerTabletExtraHdImageUrl = $bannerTabletExtraHdImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTabletExtraHdImageUrl()
  {
    return $this->bannerTabletExtraHdImageUrl;
  }
  /**
   * Banner image. Tablet size high resolution (2276x377).
   *
   * @deprecated
   * @param string $bannerTabletHdImageUrl
   */
  public function setBannerTabletHdImageUrl($bannerTabletHdImageUrl)
  {
    $this->bannerTabletHdImageUrl = $bannerTabletHdImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTabletHdImageUrl()
  {
    return $this->bannerTabletHdImageUrl;
  }
  /**
   * Banner image. Tablet size (1707x283).
   *
   * @deprecated
   * @param string $bannerTabletImageUrl
   */
  public function setBannerTabletImageUrl($bannerTabletImageUrl)
  {
    $this->bannerTabletImageUrl = $bannerTabletImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTabletImageUrl()
  {
    return $this->bannerTabletImageUrl;
  }
  /**
   * Banner image. Tablet size low resolution (1138x188).
   *
   * @deprecated
   * @param string $bannerTabletLowImageUrl
   */
  public function setBannerTabletLowImageUrl($bannerTabletLowImageUrl)
  {
    $this->bannerTabletLowImageUrl = $bannerTabletLowImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTabletLowImageUrl()
  {
    return $this->bannerTabletLowImageUrl;
  }
  /**
   * Banner image. TV size high resolution (1920x1080).
   *
   * @deprecated
   * @param string $bannerTvHighImageUrl
   */
  public function setBannerTvHighImageUrl($bannerTvHighImageUrl)
  {
    $this->bannerTvHighImageUrl = $bannerTvHighImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTvHighImageUrl()
  {
    return $this->bannerTvHighImageUrl;
  }
  /**
   * Banner image. TV size extra high resolution (2120x1192).
   *
   * @deprecated
   * @param string $bannerTvImageUrl
   */
  public function setBannerTvImageUrl($bannerTvImageUrl)
  {
    $this->bannerTvImageUrl = $bannerTvImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTvImageUrl()
  {
    return $this->bannerTvImageUrl;
  }
  /**
   * Banner image. TV size low resolution (854x480).
   *
   * @deprecated
   * @param string $bannerTvLowImageUrl
   */
  public function setBannerTvLowImageUrl($bannerTvLowImageUrl)
  {
    $this->bannerTvLowImageUrl = $bannerTvLowImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTvLowImageUrl()
  {
    return $this->bannerTvLowImageUrl;
  }
  /**
   * Banner image. TV size medium resolution (1280x720).
   *
   * @deprecated
   * @param string $bannerTvMediumImageUrl
   */
  public function setBannerTvMediumImageUrl($bannerTvMediumImageUrl)
  {
    $this->bannerTvMediumImageUrl = $bannerTvMediumImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getBannerTvMediumImageUrl()
  {
    return $this->bannerTvMediumImageUrl;
  }
  /**
   * The image map script for the large banner image.
   *
   * @deprecated
   * @param LocalizedProperty $largeBrandedBannerImageImapScript
   */
  public function setLargeBrandedBannerImageImapScript(LocalizedProperty $largeBrandedBannerImageImapScript)
  {
    $this->largeBrandedBannerImageImapScript = $largeBrandedBannerImageImapScript;
  }
  /**
   * @deprecated
   * @return LocalizedProperty
   */
  public function getLargeBrandedBannerImageImapScript()
  {
    return $this->largeBrandedBannerImageImapScript;
  }
  /**
   * The URL for the 854px by 70px image that appears below the video player in
   * the expanded video view of the video watch page.
   *
   * @deprecated
   * @param LocalizedProperty $largeBrandedBannerImageUrl
   */
  public function setLargeBrandedBannerImageUrl(LocalizedProperty $largeBrandedBannerImageUrl)
  {
    $this->largeBrandedBannerImageUrl = $largeBrandedBannerImageUrl;
  }
  /**
   * @deprecated
   * @return LocalizedProperty
   */
  public function getLargeBrandedBannerImageUrl()
  {
    return $this->largeBrandedBannerImageUrl;
  }
  /**
   * The image map script for the small banner image.
   *
   * @deprecated
   * @param LocalizedProperty $smallBrandedBannerImageImapScript
   */
  public function setSmallBrandedBannerImageImapScript(LocalizedProperty $smallBrandedBannerImageImapScript)
  {
    $this->smallBrandedBannerImageImapScript = $smallBrandedBannerImageImapScript;
  }
  /**
   * @deprecated
   * @return LocalizedProperty
   */
  public function getSmallBrandedBannerImageImapScript()
  {
    return $this->smallBrandedBannerImageImapScript;
  }
  /**
   * The URL for the 640px by 70px banner image that appears below the video
   * player in the default view of the video watch page. The URL for the image
   * that appears above the top-left corner of the video player. This is a
   * 25-pixel-high image with a flexible width that cannot exceed 170 pixels.
   *
   * @deprecated
   * @param LocalizedProperty $smallBrandedBannerImageUrl
   */
  public function setSmallBrandedBannerImageUrl(LocalizedProperty $smallBrandedBannerImageUrl)
  {
    $this->smallBrandedBannerImageUrl = $smallBrandedBannerImageUrl;
  }
  /**
   * @deprecated
   * @return LocalizedProperty
   */
  public function getSmallBrandedBannerImageUrl()
  {
    return $this->smallBrandedBannerImageUrl;
  }
  /**
   * The URL for a 1px by 1px tracking pixel that can be used to collect
   * statistics for views of the channel or video pages.
   *
   * @deprecated
   * @param string $trackingImageUrl
   */
  public function setTrackingImageUrl($trackingImageUrl)
  {
    $this->trackingImageUrl = $trackingImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTrackingImageUrl()
  {
    return $this->trackingImageUrl;
  }
  /**
   * @deprecated
   * @param string $watchIconImageUrl
   */
  public function setWatchIconImageUrl($watchIconImageUrl)
  {
    $this->watchIconImageUrl = $watchIconImageUrl;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getWatchIconImageUrl()
  {
    return $this->watchIconImageUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageSettings::class, 'Google_Service_YouTube_ImageSettings');
