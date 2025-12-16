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

class YoutubeAdGroupAd extends \Google\Collection
{
  protected $collection_key = 'adUrls';
  /**
   * @var string
   */
  public $adGroupAdId;
  /**
   * @var string
   */
  public $adGroupId;
  protected $adUrlsType = AdUrl::class;
  protected $adUrlsDataType = 'array';
  /**
   * @var string
   */
  public $advertiserId;
  protected $audioAdType = AudioAd::class;
  protected $audioAdDataType = '';
  protected $bumperAdType = BumperAd::class;
  protected $bumperAdDataType = '';
  /**
   * @var string
   */
  public $displayName;
  protected $displayVideoSourceAdType = DisplayVideoSourceAd::class;
  protected $displayVideoSourceAdDataType = '';
  /**
   * @var string
   */
  public $entityStatus;
  protected $inStreamAdType = InStreamAd::class;
  protected $inStreamAdDataType = '';
  protected $mastheadAdType = MastheadAd::class;
  protected $mastheadAdDataType = '';
  /**
   * @var string
   */
  public $name;
  protected $nonSkippableAdType = NonSkippableAd::class;
  protected $nonSkippableAdDataType = '';
  protected $videoDiscoverAdType = VideoDiscoveryAd::class;
  protected $videoDiscoverAdDataType = '';
  protected $videoPerformanceAdType = VideoPerformanceAd::class;
  protected $videoPerformanceAdDataType = '';

  /**
   * @param string
   */
  public function setAdGroupAdId($adGroupAdId)
  {
    $this->adGroupAdId = $adGroupAdId;
  }
  /**
   * @return string
   */
  public function getAdGroupAdId()
  {
    return $this->adGroupAdId;
  }
  /**
   * @param string
   */
  public function setAdGroupId($adGroupId)
  {
    $this->adGroupId = $adGroupId;
  }
  /**
   * @return string
   */
  public function getAdGroupId()
  {
    return $this->adGroupId;
  }
  /**
   * @param AdUrl[]
   */
  public function setAdUrls($adUrls)
  {
    $this->adUrls = $adUrls;
  }
  /**
   * @return AdUrl[]
   */
  public function getAdUrls()
  {
    return $this->adUrls;
  }
  /**
   * @param string
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * @param AudioAd
   */
  public function setAudioAd(AudioAd $audioAd)
  {
    $this->audioAd = $audioAd;
  }
  /**
   * @return AudioAd
   */
  public function getAudioAd()
  {
    return $this->audioAd;
  }
  /**
   * @param BumperAd
   */
  public function setBumperAd(BumperAd $bumperAd)
  {
    $this->bumperAd = $bumperAd;
  }
  /**
   * @return BumperAd
   */
  public function getBumperAd()
  {
    return $this->bumperAd;
  }
  /**
   * @param string
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * @param DisplayVideoSourceAd
   */
  public function setDisplayVideoSourceAd(DisplayVideoSourceAd $displayVideoSourceAd)
  {
    $this->displayVideoSourceAd = $displayVideoSourceAd;
  }
  /**
   * @return DisplayVideoSourceAd
   */
  public function getDisplayVideoSourceAd()
  {
    return $this->displayVideoSourceAd;
  }
  /**
   * @param string
   */
  public function setEntityStatus($entityStatus)
  {
    $this->entityStatus = $entityStatus;
  }
  /**
   * @return string
   */
  public function getEntityStatus()
  {
    return $this->entityStatus;
  }
  /**
   * @param InStreamAd
   */
  public function setInStreamAd(InStreamAd $inStreamAd)
  {
    $this->inStreamAd = $inStreamAd;
  }
  /**
   * @return InStreamAd
   */
  public function getInStreamAd()
  {
    return $this->inStreamAd;
  }
  /**
   * @param MastheadAd
   */
  public function setMastheadAd(MastheadAd $mastheadAd)
  {
    $this->mastheadAd = $mastheadAd;
  }
  /**
   * @return MastheadAd
   */
  public function getMastheadAd()
  {
    return $this->mastheadAd;
  }
  /**
   * @param string
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
   * @param NonSkippableAd
   */
  public function setNonSkippableAd(NonSkippableAd $nonSkippableAd)
  {
    $this->nonSkippableAd = $nonSkippableAd;
  }
  /**
   * @return NonSkippableAd
   */
  public function getNonSkippableAd()
  {
    return $this->nonSkippableAd;
  }
  /**
   * @param VideoDiscoveryAd
   */
  public function setVideoDiscoverAd(VideoDiscoveryAd $videoDiscoverAd)
  {
    $this->videoDiscoverAd = $videoDiscoverAd;
  }
  /**
   * @return VideoDiscoveryAd
   */
  public function getVideoDiscoverAd()
  {
    return $this->videoDiscoverAd;
  }
  /**
   * @param VideoPerformanceAd
   */
  public function setVideoPerformanceAd(VideoPerformanceAd $videoPerformanceAd)
  {
    $this->videoPerformanceAd = $videoPerformanceAd;
  }
  /**
   * @return VideoPerformanceAd
   */
  public function getVideoPerformanceAd()
  {
    return $this->videoPerformanceAd;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAdGroupAd::class, 'Google_Service_DisplayVideo_YoutubeAdGroupAd');
