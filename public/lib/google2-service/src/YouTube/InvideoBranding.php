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

class InvideoBranding extends \Google\Model
{
  /**
   * The bytes the uploaded image. Only used in api to youtube communication.
   *
   * @var string
   */
  public $imageBytes;
  /**
   * The url of the uploaded image. Only used in apiary to api communication.
   *
   * @var string
   */
  public $imageUrl;
  protected $positionType = InvideoPosition::class;
  protected $positionDataType = '';
  /**
   * The channel to which this branding links. If not present it defaults to the
   * current channel.
   *
   * @var string
   */
  public $targetChannelId;
  protected $timingType = InvideoTiming::class;
  protected $timingDataType = '';

  /**
   * The bytes the uploaded image. Only used in api to youtube communication.
   *
   * @param string $imageBytes
   */
  public function setImageBytes($imageBytes)
  {
    $this->imageBytes = $imageBytes;
  }
  /**
   * @return string
   */
  public function getImageBytes()
  {
    return $this->imageBytes;
  }
  /**
   * The url of the uploaded image. Only used in apiary to api communication.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * The spatial position within the video where the branding watermark will be
   * displayed.
   *
   * @deprecated
   * @param InvideoPosition $position
   */
  public function setPosition(InvideoPosition $position)
  {
    $this->position = $position;
  }
  /**
   * @deprecated
   * @return InvideoPosition
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * The channel to which this branding links. If not present it defaults to the
   * current channel.
   *
   * @param string $targetChannelId
   */
  public function setTargetChannelId($targetChannelId)
  {
    $this->targetChannelId = $targetChannelId;
  }
  /**
   * @return string
   */
  public function getTargetChannelId()
  {
    return $this->targetChannelId;
  }
  /**
   * The temporal position within the video where watermark will be displayed.
   *
   * @param InvideoTiming $timing
   */
  public function setTiming(InvideoTiming $timing)
  {
    $this->timing = $timing;
  }
  /**
   * @return InvideoTiming
   */
  public function getTiming()
  {
    return $this->timing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InvideoBranding::class, 'Google_Service_YouTube_InvideoBranding');
