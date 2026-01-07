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

class ClickTag extends \Google\Model
{
  protected $clickThroughUrlType = CreativeClickThroughUrl::class;
  protected $clickThroughUrlDataType = '';
  /**
   * Advertiser event name associated with the click tag. This field is used by
   * DISPLAY_IMAGE_GALLERY and HTML5_BANNER creatives. Applicable to DISPLAY
   * when the primary asset type is not HTML_IMAGE.
   *
   * @var string
   */
  public $eventName;
  /**
   * Parameter name for the specified click tag. For DISPLAY_IMAGE_GALLERY
   * creative assets, this field must match the value of the creative asset's
   * creativeAssetId.name field.
   *
   * @var string
   */
  public $name;

  /**
   * Parameter value for the specified click tag. This field contains a click-
   * through url.
   *
   * @param CreativeClickThroughUrl $clickThroughUrl
   */
  public function setClickThroughUrl(CreativeClickThroughUrl $clickThroughUrl)
  {
    $this->clickThroughUrl = $clickThroughUrl;
  }
  /**
   * @return CreativeClickThroughUrl
   */
  public function getClickThroughUrl()
  {
    return $this->clickThroughUrl;
  }
  /**
   * Advertiser event name associated with the click tag. This field is used by
   * DISPLAY_IMAGE_GALLERY and HTML5_BANNER creatives. Applicable to DISPLAY
   * when the primary asset type is not HTML_IMAGE.
   *
   * @param string $eventName
   */
  public function setEventName($eventName)
  {
    $this->eventName = $eventName;
  }
  /**
   * @return string
   */
  public function getEventName()
  {
    return $this->eventName;
  }
  /**
   * Parameter name for the specified click tag. For DISPLAY_IMAGE_GALLERY
   * creative assets, this field must match the value of the creative asset's
   * creativeAssetId.name field.
   *
   * @param string $name
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClickTag::class, 'Google_Service_Dfareporting_ClickTag');
