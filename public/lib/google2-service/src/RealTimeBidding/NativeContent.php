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

namespace Google\Service\RealTimeBidding;

class NativeContent extends \Google\Model
{
  /**
   * The name of the advertiser or sponsor, to be displayed in the ad creative.
   *
   * @var string
   */
  public $advertiserName;
  protected $appIconType = Image::class;
  protected $appIconDataType = '';
  /**
   * A long description of the ad.
   *
   * @var string
   */
  public $body;
  /**
   * A label for the button that the user is supposed to click.
   *
   * @var string
   */
  public $callToAction;
  /**
   * The URL that the browser/SDK will load when the user clicks the ad.
   *
   * @var string
   */
  public $clickLinkUrl;
  /**
   * The URL to use for click tracking.
   *
   * @var string
   */
  public $clickTrackingUrl;
  /**
   * A short title for the ad.
   *
   * @var string
   */
  public $headline;
  protected $imageType = Image::class;
  protected $imageDataType = '';
  protected $logoType = Image::class;
  protected $logoDataType = '';
  /**
   * The price of the promoted app including currency info.
   *
   * @var string
   */
  public $priceDisplayText;
  /**
   * The app rating in the app store. Must be in the range [0-5].
   *
   * @var 
   */
  public $starRating;
  /**
   * The URL to fetch a native video ad.
   *
   * @var string
   */
  public $videoUrl;
  /**
   * The contents of a VAST document for a native video ad.
   *
   * @var string
   */
  public $videoVastXml;

  /**
   * The name of the advertiser or sponsor, to be displayed in the ad creative.
   *
   * @param string $advertiserName
   */
  public function setAdvertiserName($advertiserName)
  {
    $this->advertiserName = $advertiserName;
  }
  /**
   * @return string
   */
  public function getAdvertiserName()
  {
    return $this->advertiserName;
  }
  /**
   * The app icon, for app download ads.
   *
   * @param Image $appIcon
   */
  public function setAppIcon(Image $appIcon)
  {
    $this->appIcon = $appIcon;
  }
  /**
   * @return Image
   */
  public function getAppIcon()
  {
    return $this->appIcon;
  }
  /**
   * A long description of the ad.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * A label for the button that the user is supposed to click.
   *
   * @param string $callToAction
   */
  public function setCallToAction($callToAction)
  {
    $this->callToAction = $callToAction;
  }
  /**
   * @return string
   */
  public function getCallToAction()
  {
    return $this->callToAction;
  }
  /**
   * The URL that the browser/SDK will load when the user clicks the ad.
   *
   * @param string $clickLinkUrl
   */
  public function setClickLinkUrl($clickLinkUrl)
  {
    $this->clickLinkUrl = $clickLinkUrl;
  }
  /**
   * @return string
   */
  public function getClickLinkUrl()
  {
    return $this->clickLinkUrl;
  }
  /**
   * The URL to use for click tracking.
   *
   * @param string $clickTrackingUrl
   */
  public function setClickTrackingUrl($clickTrackingUrl)
  {
    $this->clickTrackingUrl = $clickTrackingUrl;
  }
  /**
   * @return string
   */
  public function getClickTrackingUrl()
  {
    return $this->clickTrackingUrl;
  }
  /**
   * A short title for the ad.
   *
   * @param string $headline
   */
  public function setHeadline($headline)
  {
    $this->headline = $headline;
  }
  /**
   * @return string
   */
  public function getHeadline()
  {
    return $this->headline;
  }
  /**
   * A large image.
   *
   * @param Image $image
   */
  public function setImage(Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * A smaller image, for the advertiser's logo.
   *
   * @param Image $logo
   */
  public function setLogo(Image $logo)
  {
    $this->logo = $logo;
  }
  /**
   * @return Image
   */
  public function getLogo()
  {
    return $this->logo;
  }
  /**
   * The price of the promoted app including currency info.
   *
   * @param string $priceDisplayText
   */
  public function setPriceDisplayText($priceDisplayText)
  {
    $this->priceDisplayText = $priceDisplayText;
  }
  /**
   * @return string
   */
  public function getPriceDisplayText()
  {
    return $this->priceDisplayText;
  }
  public function setStarRating($starRating)
  {
    $this->starRating = $starRating;
  }
  public function getStarRating()
  {
    return $this->starRating;
  }
  /**
   * The URL to fetch a native video ad.
   *
   * @param string $videoUrl
   */
  public function setVideoUrl($videoUrl)
  {
    $this->videoUrl = $videoUrl;
  }
  /**
   * @return string
   */
  public function getVideoUrl()
  {
    return $this->videoUrl;
  }
  /**
   * The contents of a VAST document for a native video ad.
   *
   * @param string $videoVastXml
   */
  public function setVideoVastXml($videoVastXml)
  {
    $this->videoVastXml = $videoVastXml;
  }
  /**
   * @return string
   */
  public function getVideoVastXml()
  {
    return $this->videoVastXml;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NativeContent::class, 'Google_Service_RealTimeBidding_NativeContent');
