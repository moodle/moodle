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

class ActivityContentDetailsPromotedItem extends \Google\Collection
{
  public const CTA_TYPE_ctaTypeUnspecified = 'ctaTypeUnspecified';
  public const CTA_TYPE_visitAdvertiserSite = 'visitAdvertiserSite';
  protected $collection_key = 'impressionUrl';
  /**
   * The URL the client should fetch to request a promoted item.
   *
   * @var string
   */
  public $adTag;
  /**
   * The URL the client should ping to indicate that the user clicked through on
   * this promoted item.
   *
   * @var string
   */
  public $clickTrackingUrl;
  /**
   * The URL the client should ping to indicate that the user was shown this
   * promoted item.
   *
   * @var string
   */
  public $creativeViewUrl;
  /**
   * The type of call-to-action, a message to the user indicating action that
   * can be taken.
   *
   * @var string
   */
  public $ctaType;
  /**
   * The custom call-to-action button text. If specified, it will override the
   * default button text for the cta_type.
   *
   * @var string
   */
  public $customCtaButtonText;
  /**
   * The text description to accompany the promoted item.
   *
   * @var string
   */
  public $descriptionText;
  /**
   * The URL the client should direct the user to, if the user chooses to visit
   * the advertiser's website.
   *
   * @var string
   */
  public $destinationUrl;
  /**
   * The list of forecasting URLs. The client should ping all of these URLs when
   * a promoted item is not available, to indicate that a promoted item could
   * have been shown.
   *
   * @var string[]
   */
  public $forecastingUrl;
  /**
   * The list of impression URLs. The client should ping all of these URLs to
   * indicate that the user was shown this promoted item.
   *
   * @var string[]
   */
  public $impressionUrl;
  /**
   * The ID that YouTube uses to uniquely identify the promoted video.
   *
   * @var string
   */
  public $videoId;

  /**
   * The URL the client should fetch to request a promoted item.
   *
   * @param string $adTag
   */
  public function setAdTag($adTag)
  {
    $this->adTag = $adTag;
  }
  /**
   * @return string
   */
  public function getAdTag()
  {
    return $this->adTag;
  }
  /**
   * The URL the client should ping to indicate that the user clicked through on
   * this promoted item.
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
   * The URL the client should ping to indicate that the user was shown this
   * promoted item.
   *
   * @param string $creativeViewUrl
   */
  public function setCreativeViewUrl($creativeViewUrl)
  {
    $this->creativeViewUrl = $creativeViewUrl;
  }
  /**
   * @return string
   */
  public function getCreativeViewUrl()
  {
    return $this->creativeViewUrl;
  }
  /**
   * The type of call-to-action, a message to the user indicating action that
   * can be taken.
   *
   * Accepted values: ctaTypeUnspecified, visitAdvertiserSite
   *
   * @param self::CTA_TYPE_* $ctaType
   */
  public function setCtaType($ctaType)
  {
    $this->ctaType = $ctaType;
  }
  /**
   * @return self::CTA_TYPE_*
   */
  public function getCtaType()
  {
    return $this->ctaType;
  }
  /**
   * The custom call-to-action button text. If specified, it will override the
   * default button text for the cta_type.
   *
   * @param string $customCtaButtonText
   */
  public function setCustomCtaButtonText($customCtaButtonText)
  {
    $this->customCtaButtonText = $customCtaButtonText;
  }
  /**
   * @return string
   */
  public function getCustomCtaButtonText()
  {
    return $this->customCtaButtonText;
  }
  /**
   * The text description to accompany the promoted item.
   *
   * @param string $descriptionText
   */
  public function setDescriptionText($descriptionText)
  {
    $this->descriptionText = $descriptionText;
  }
  /**
   * @return string
   */
  public function getDescriptionText()
  {
    return $this->descriptionText;
  }
  /**
   * The URL the client should direct the user to, if the user chooses to visit
   * the advertiser's website.
   *
   * @param string $destinationUrl
   */
  public function setDestinationUrl($destinationUrl)
  {
    $this->destinationUrl = $destinationUrl;
  }
  /**
   * @return string
   */
  public function getDestinationUrl()
  {
    return $this->destinationUrl;
  }
  /**
   * The list of forecasting URLs. The client should ping all of these URLs when
   * a promoted item is not available, to indicate that a promoted item could
   * have been shown.
   *
   * @param string[] $forecastingUrl
   */
  public function setForecastingUrl($forecastingUrl)
  {
    $this->forecastingUrl = $forecastingUrl;
  }
  /**
   * @return string[]
   */
  public function getForecastingUrl()
  {
    return $this->forecastingUrl;
  }
  /**
   * The list of impression URLs. The client should ping all of these URLs to
   * indicate that the user was shown this promoted item.
   *
   * @param string[] $impressionUrl
   */
  public function setImpressionUrl($impressionUrl)
  {
    $this->impressionUrl = $impressionUrl;
  }
  /**
   * @return string[]
   */
  public function getImpressionUrl()
  {
    return $this->impressionUrl;
  }
  /**
   * The ID that YouTube uses to uniquely identify the promoted video.
   *
   * @param string $videoId
   */
  public function setVideoId($videoId)
  {
    $this->videoId = $videoId;
  }
  /**
   * @return string
   */
  public function getVideoId()
  {
    return $this->videoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ActivityContentDetailsPromotedItem::class, 'Google_Service_YouTube_ActivityContentDetailsPromotedItem');
