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

namespace Google\Service\HangoutsChat;

class CardHeader extends \Google\Model
{
  public const IMAGE_STYLE_IMAGE_STYLE_UNSPECIFIED = 'IMAGE_STYLE_UNSPECIFIED';
  /**
   * Square border.
   */
  public const IMAGE_STYLE_IMAGE = 'IMAGE';
  /**
   * Circular border.
   */
  public const IMAGE_STYLE_AVATAR = 'AVATAR';
  /**
   * The image's type (for example, square border or circular border).
   *
   * @var string
   */
  public $imageStyle;
  /**
   * The URL of the image in the card header.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * The subtitle of the card header.
   *
   * @var string
   */
  public $subtitle;
  /**
   * The title must be specified. The header has a fixed height: if both a title
   * and subtitle is specified, each takes up one line. If only the title is
   * specified, it takes up both lines.
   *
   * @var string
   */
  public $title;

  /**
   * The image's type (for example, square border or circular border).
   *
   * Accepted values: IMAGE_STYLE_UNSPECIFIED, IMAGE, AVATAR
   *
   * @param self::IMAGE_STYLE_* $imageStyle
   */
  public function setImageStyle($imageStyle)
  {
    $this->imageStyle = $imageStyle;
  }
  /**
   * @return self::IMAGE_STYLE_*
   */
  public function getImageStyle()
  {
    return $this->imageStyle;
  }
  /**
   * The URL of the image in the card header.
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
   * The subtitle of the card header.
   *
   * @param string $subtitle
   */
  public function setSubtitle($subtitle)
  {
    $this->subtitle = $subtitle;
  }
  /**
   * @return string
   */
  public function getSubtitle()
  {
    return $this->subtitle;
  }
  /**
   * The title must be specified. The header has a fixed height: if both a title
   * and subtitle is specified, each takes up one line. If only the title is
   * specified, it takes up both lines.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CardHeader::class, 'Google_Service_HangoutsChat_CardHeader');
