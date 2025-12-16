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

class VideoPerformanceAd extends \Google\Collection
{
  protected $collection_key = 'videos';
  /**
   * The list of text assets shown on the call-to-action button.
   *
   * @var string[]
   */
  public $actionButtonLabels;
  protected $companionBannersType = ImageAsset::class;
  protected $companionBannersDataType = 'array';
  /**
   * The custom parameters to pass custom values to tracking URL template.
   *
   * @var string[]
   */
  public $customParameters;
  /**
   * The list of descriptions shown on the call-to-action banner.
   *
   * @var string[]
   */
  public $descriptions;
  /**
   * The first piece after the domain in the display URL.
   *
   * @var string
   */
  public $displayUrlBreadcrumb1;
  /**
   * The second piece after the domain in the display URL.
   *
   * @var string
   */
  public $displayUrlBreadcrumb2;
  /**
   * The domain of the display URL.
   *
   * @var string
   */
  public $domain;
  /**
   * The URL address of the webpage that people reach after they click the ad.
   *
   * @var string
   */
  public $finalUrl;
  /**
   * The list of headlines shown on the call-to-action banner.
   *
   * @var string[]
   */
  public $headlines;
  /**
   * The list of lone headlines shown on the call-to-action banner.
   *
   * @var string[]
   */
  public $longHeadlines;
  /**
   * The URL address loaded in the background for tracking purposes.
   *
   * @var string
   */
  public $trackingUrl;
  protected $videosType = YoutubeVideoDetails::class;
  protected $videosDataType = 'array';

  /**
   * The list of text assets shown on the call-to-action button.
   *
   * @param string[] $actionButtonLabels
   */
  public function setActionButtonLabels($actionButtonLabels)
  {
    $this->actionButtonLabels = $actionButtonLabels;
  }
  /**
   * @return string[]
   */
  public function getActionButtonLabels()
  {
    return $this->actionButtonLabels;
  }
  /**
   * The list of companion banners used by this ad.
   *
   * @param ImageAsset[] $companionBanners
   */
  public function setCompanionBanners($companionBanners)
  {
    $this->companionBanners = $companionBanners;
  }
  /**
   * @return ImageAsset[]
   */
  public function getCompanionBanners()
  {
    return $this->companionBanners;
  }
  /**
   * The custom parameters to pass custom values to tracking URL template.
   *
   * @param string[] $customParameters
   */
  public function setCustomParameters($customParameters)
  {
    $this->customParameters = $customParameters;
  }
  /**
   * @return string[]
   */
  public function getCustomParameters()
  {
    return $this->customParameters;
  }
  /**
   * The list of descriptions shown on the call-to-action banner.
   *
   * @param string[] $descriptions
   */
  public function setDescriptions($descriptions)
  {
    $this->descriptions = $descriptions;
  }
  /**
   * @return string[]
   */
  public function getDescriptions()
  {
    return $this->descriptions;
  }
  /**
   * The first piece after the domain in the display URL.
   *
   * @param string $displayUrlBreadcrumb1
   */
  public function setDisplayUrlBreadcrumb1($displayUrlBreadcrumb1)
  {
    $this->displayUrlBreadcrumb1 = $displayUrlBreadcrumb1;
  }
  /**
   * @return string
   */
  public function getDisplayUrlBreadcrumb1()
  {
    return $this->displayUrlBreadcrumb1;
  }
  /**
   * The second piece after the domain in the display URL.
   *
   * @param string $displayUrlBreadcrumb2
   */
  public function setDisplayUrlBreadcrumb2($displayUrlBreadcrumb2)
  {
    $this->displayUrlBreadcrumb2 = $displayUrlBreadcrumb2;
  }
  /**
   * @return string
   */
  public function getDisplayUrlBreadcrumb2()
  {
    return $this->displayUrlBreadcrumb2;
  }
  /**
   * The domain of the display URL.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The URL address of the webpage that people reach after they click the ad.
   *
   * @param string $finalUrl
   */
  public function setFinalUrl($finalUrl)
  {
    $this->finalUrl = $finalUrl;
  }
  /**
   * @return string
   */
  public function getFinalUrl()
  {
    return $this->finalUrl;
  }
  /**
   * The list of headlines shown on the call-to-action banner.
   *
   * @param string[] $headlines
   */
  public function setHeadlines($headlines)
  {
    $this->headlines = $headlines;
  }
  /**
   * @return string[]
   */
  public function getHeadlines()
  {
    return $this->headlines;
  }
  /**
   * The list of lone headlines shown on the call-to-action banner.
   *
   * @param string[] $longHeadlines
   */
  public function setLongHeadlines($longHeadlines)
  {
    $this->longHeadlines = $longHeadlines;
  }
  /**
   * @return string[]
   */
  public function getLongHeadlines()
  {
    return $this->longHeadlines;
  }
  /**
   * The URL address loaded in the background for tracking purposes.
   *
   * @param string $trackingUrl
   */
  public function setTrackingUrl($trackingUrl)
  {
    $this->trackingUrl = $trackingUrl;
  }
  /**
   * @return string
   */
  public function getTrackingUrl()
  {
    return $this->trackingUrl;
  }
  /**
   * The list of YouTube video assets used by this ad.
   *
   * @param YoutubeVideoDetails[] $videos
   */
  public function setVideos($videos)
  {
    $this->videos = $videos;
  }
  /**
   * @return YoutubeVideoDetails[]
   */
  public function getVideos()
  {
    return $this->videos;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoPerformanceAd::class, 'Google_Service_DisplayVideo_VideoPerformanceAd');
