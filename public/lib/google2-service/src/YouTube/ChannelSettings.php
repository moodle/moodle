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

class ChannelSettings extends \Google\Collection
{
  protected $collection_key = 'featuredChannelsUrls';
  /**
   * The country of the channel.
   *
   * @var string
   */
  public $country;
  /**
   * @var string
   */
  public $defaultLanguage;
  /**
   * Which content tab users should see when viewing the channel.
   *
   * @deprecated
   * @var string
   */
  public $defaultTab;
  /**
   * Specifies the channel description.
   *
   * @var string
   */
  public $description;
  /**
   * Title for the featured channels tab.
   *
   * @deprecated
   * @var string
   */
  public $featuredChannelsTitle;
  /**
   * The list of featured channels.
   *
   * @deprecated
   * @var string[]
   */
  public $featuredChannelsUrls;
  /**
   * Lists keywords associated with the channel, comma-separated.
   *
   * @var string
   */
  public $keywords;
  /**
   * Whether user-submitted comments left on the channel page need to be
   * approved by the channel owner to be publicly visible.
   *
   * @deprecated
   * @var bool
   */
  public $moderateComments;
  /**
   * A prominent color that can be rendered on this channel page.
   *
   * @deprecated
   * @var string
   */
  public $profileColor;
  /**
   * Whether the tab to browse the videos should be displayed.
   *
   * @deprecated
   * @var bool
   */
  public $showBrowseView;
  /**
   * Whether related channels should be proposed.
   *
   * @deprecated
   * @var bool
   */
  public $showRelatedChannels;
  /**
   * Specifies the channel title.
   *
   * @var string
   */
  public $title;
  /**
   * The ID for a Google Analytics account to track and measure traffic to the
   * channels.
   *
   * @var string
   */
  public $trackingAnalyticsAccountId;
  /**
   * The trailer of the channel, for users that are not subscribers.
   *
   * @var string
   */
  public $unsubscribedTrailer;

  /**
   * The country of the channel.
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * Which content tab users should see when viewing the channel.
   *
   * @deprecated
   * @param string $defaultTab
   */
  public function setDefaultTab($defaultTab)
  {
    $this->defaultTab = $defaultTab;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDefaultTab()
  {
    return $this->defaultTab;
  }
  /**
   * Specifies the channel description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Title for the featured channels tab.
   *
   * @deprecated
   * @param string $featuredChannelsTitle
   */
  public function setFeaturedChannelsTitle($featuredChannelsTitle)
  {
    $this->featuredChannelsTitle = $featuredChannelsTitle;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getFeaturedChannelsTitle()
  {
    return $this->featuredChannelsTitle;
  }
  /**
   * The list of featured channels.
   *
   * @deprecated
   * @param string[] $featuredChannelsUrls
   */
  public function setFeaturedChannelsUrls($featuredChannelsUrls)
  {
    $this->featuredChannelsUrls = $featuredChannelsUrls;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getFeaturedChannelsUrls()
  {
    return $this->featuredChannelsUrls;
  }
  /**
   * Lists keywords associated with the channel, comma-separated.
   *
   * @param string $keywords
   */
  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
  }
  /**
   * @return string
   */
  public function getKeywords()
  {
    return $this->keywords;
  }
  /**
   * Whether user-submitted comments left on the channel page need to be
   * approved by the channel owner to be publicly visible.
   *
   * @deprecated
   * @param bool $moderateComments
   */
  public function setModerateComments($moderateComments)
  {
    $this->moderateComments = $moderateComments;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getModerateComments()
  {
    return $this->moderateComments;
  }
  /**
   * A prominent color that can be rendered on this channel page.
   *
   * @deprecated
   * @param string $profileColor
   */
  public function setProfileColor($profileColor)
  {
    $this->profileColor = $profileColor;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProfileColor()
  {
    return $this->profileColor;
  }
  /**
   * Whether the tab to browse the videos should be displayed.
   *
   * @deprecated
   * @param bool $showBrowseView
   */
  public function setShowBrowseView($showBrowseView)
  {
    $this->showBrowseView = $showBrowseView;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getShowBrowseView()
  {
    return $this->showBrowseView;
  }
  /**
   * Whether related channels should be proposed.
   *
   * @deprecated
   * @param bool $showRelatedChannels
   */
  public function setShowRelatedChannels($showRelatedChannels)
  {
    $this->showRelatedChannels = $showRelatedChannels;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getShowRelatedChannels()
  {
    return $this->showRelatedChannels;
  }
  /**
   * Specifies the channel title.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The ID for a Google Analytics account to track and measure traffic to the
   * channels.
   *
   * @param string $trackingAnalyticsAccountId
   */
  public function setTrackingAnalyticsAccountId($trackingAnalyticsAccountId)
  {
    $this->trackingAnalyticsAccountId = $trackingAnalyticsAccountId;
  }
  /**
   * @return string
   */
  public function getTrackingAnalyticsAccountId()
  {
    return $this->trackingAnalyticsAccountId;
  }
  /**
   * The trailer of the channel, for users that are not subscribers.
   *
   * @param string $unsubscribedTrailer
   */
  public function setUnsubscribedTrailer($unsubscribedTrailer)
  {
    $this->unsubscribedTrailer = $unsubscribedTrailer;
  }
  /**
   * @return string
   */
  public function getUnsubscribedTrailer()
  {
    return $this->unsubscribedTrailer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelSettings::class, 'Google_Service_YouTube_ChannelSettings');
