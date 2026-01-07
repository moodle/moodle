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

namespace Google\Service\Dfareporting;

class CreativeCustomEvent extends \Google\Model
{
  public const ADVERTISER_CUSTOM_EVENT_TYPE_ADVERTISER_EVENT_TIMER = 'ADVERTISER_EVENT_TIMER';
  public const ADVERTISER_CUSTOM_EVENT_TYPE_ADVERTISER_EVENT_EXIT = 'ADVERTISER_EVENT_EXIT';
  public const ADVERTISER_CUSTOM_EVENT_TYPE_ADVERTISER_EVENT_COUNTER = 'ADVERTISER_EVENT_COUNTER';
  /**
   * The creative is a Flash creative.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_FLASH = 'ARTWORK_TYPE_FLASH';
  /**
   * The creative is HTML5.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_HTML5 = 'ARTWORK_TYPE_HTML5';
  /**
   * The creative is HTML5 if available, Flash otherwise.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_MIXED = 'ARTWORK_TYPE_MIXED';
  /**
   * The creative is Image.
   */
  public const ARTWORK_TYPE_ARTWORK_TYPE_IMAGE = 'ARTWORK_TYPE_IMAGE';
  /**
   * New tab
   */
  public const TARGET_TYPE_TARGET_BLANK = 'TARGET_BLANK';
  /**
   * Current tab
   */
  public const TARGET_TYPE_TARGET_TOP = 'TARGET_TOP';
  /**
   * Same frame
   */
  public const TARGET_TYPE_TARGET_SELF = 'TARGET_SELF';
  /**
   * Parent frame
   */
  public const TARGET_TYPE_TARGET_PARENT = 'TARGET_PARENT';
  /**
   * New window with properties specified in window_properties
   */
  public const TARGET_TYPE_TARGET_POPUP = 'TARGET_POPUP';
  /**
   * Unique ID of this event used by Reporting and Data Transfer. This is a
   * read-only field.
   *
   * @var string
   */
  public $advertiserCustomEventId;
  /**
   * User-entered name for the event.
   *
   * @var string
   */
  public $advertiserCustomEventName;
  /**
   * Type of the event. This is a read-only field.
   *
   * @var string
   */
  public $advertiserCustomEventType;
  /**
   * Artwork label column, used to link events in Campaign Manager back to
   * events in Studio. This is a required field and should not be modified after
   * insertion.
   *
   * @var string
   */
  public $artworkLabel;
  /**
   * Artwork type used by the creative.This is a read-only field.
   *
   * @var string
   */
  public $artworkType;
  protected $exitClickThroughUrlType = CreativeClickThroughUrl::class;
  protected $exitClickThroughUrlDataType = '';
  /**
   * ID of this event. This is a required field and should not be modified after
   * insertion.
   *
   * @var string
   */
  public $id;
  protected $popupWindowPropertiesType = PopupWindowProperties::class;
  protected $popupWindowPropertiesDataType = '';
  /**
   * Target type used by the event.
   *
   * @var string
   */
  public $targetType;
  /**
   * Video reporting ID, used to differentiate multiple videos in a single
   * creative. This is a read-only field.
   *
   * @var string
   */
  public $videoReportingId;

  /**
   * Unique ID of this event used by Reporting and Data Transfer. This is a
   * read-only field.
   *
   * @param string $advertiserCustomEventId
   */
  public function setAdvertiserCustomEventId($advertiserCustomEventId)
  {
    $this->advertiserCustomEventId = $advertiserCustomEventId;
  }
  /**
   * @return string
   */
  public function getAdvertiserCustomEventId()
  {
    return $this->advertiserCustomEventId;
  }
  /**
   * User-entered name for the event.
   *
   * @param string $advertiserCustomEventName
   */
  public function setAdvertiserCustomEventName($advertiserCustomEventName)
  {
    $this->advertiserCustomEventName = $advertiserCustomEventName;
  }
  /**
   * @return string
   */
  public function getAdvertiserCustomEventName()
  {
    return $this->advertiserCustomEventName;
  }
  /**
   * Type of the event. This is a read-only field.
   *
   * Accepted values: ADVERTISER_EVENT_TIMER, ADVERTISER_EVENT_EXIT,
   * ADVERTISER_EVENT_COUNTER
   *
   * @param self::ADVERTISER_CUSTOM_EVENT_TYPE_* $advertiserCustomEventType
   */
  public function setAdvertiserCustomEventType($advertiserCustomEventType)
  {
    $this->advertiserCustomEventType = $advertiserCustomEventType;
  }
  /**
   * @return self::ADVERTISER_CUSTOM_EVENT_TYPE_*
   */
  public function getAdvertiserCustomEventType()
  {
    return $this->advertiserCustomEventType;
  }
  /**
   * Artwork label column, used to link events in Campaign Manager back to
   * events in Studio. This is a required field and should not be modified after
   * insertion.
   *
   * @param string $artworkLabel
   */
  public function setArtworkLabel($artworkLabel)
  {
    $this->artworkLabel = $artworkLabel;
  }
  /**
   * @return string
   */
  public function getArtworkLabel()
  {
    return $this->artworkLabel;
  }
  /**
   * Artwork type used by the creative.This is a read-only field.
   *
   * Accepted values: ARTWORK_TYPE_FLASH, ARTWORK_TYPE_HTML5,
   * ARTWORK_TYPE_MIXED, ARTWORK_TYPE_IMAGE
   *
   * @param self::ARTWORK_TYPE_* $artworkType
   */
  public function setArtworkType($artworkType)
  {
    $this->artworkType = $artworkType;
  }
  /**
   * @return self::ARTWORK_TYPE_*
   */
  public function getArtworkType()
  {
    return $this->artworkType;
  }
  /**
   * Exit click-through URL for the event. This field is used only for exit
   * events.
   *
   * @param CreativeClickThroughUrl $exitClickThroughUrl
   */
  public function setExitClickThroughUrl(CreativeClickThroughUrl $exitClickThroughUrl)
  {
    $this->exitClickThroughUrl = $exitClickThroughUrl;
  }
  /**
   * @return CreativeClickThroughUrl
   */
  public function getExitClickThroughUrl()
  {
    return $this->exitClickThroughUrl;
  }
  /**
   * ID of this event. This is a required field and should not be modified after
   * insertion.
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
   * Properties for rich media popup windows. This field is used only for exit
   * events.
   *
   * @param PopupWindowProperties $popupWindowProperties
   */
  public function setPopupWindowProperties(PopupWindowProperties $popupWindowProperties)
  {
    $this->popupWindowProperties = $popupWindowProperties;
  }
  /**
   * @return PopupWindowProperties
   */
  public function getPopupWindowProperties()
  {
    return $this->popupWindowProperties;
  }
  /**
   * Target type used by the event.
   *
   * Accepted values: TARGET_BLANK, TARGET_TOP, TARGET_SELF, TARGET_PARENT,
   * TARGET_POPUP
   *
   * @param self::TARGET_TYPE_* $targetType
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;
  }
  /**
   * @return self::TARGET_TYPE_*
   */
  public function getTargetType()
  {
    return $this->targetType;
  }
  /**
   * Video reporting ID, used to differentiate multiple videos in a single
   * creative. This is a read-only field.
   *
   * @param string $videoReportingId
   */
  public function setVideoReportingId($videoReportingId)
  {
    $this->videoReportingId = $videoReportingId;
  }
  /**
   * @return string
   */
  public function getVideoReportingId()
  {
    return $this->videoReportingId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeCustomEvent::class, 'Google_Service_Dfareporting_CreativeCustomEvent');
