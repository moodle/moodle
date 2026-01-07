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

class GoogleAppsCardV1CardHeader extends \Google\Model
{
  /**
   * Default value. Applies a square mask to the image. For example, a 4x3 image
   * becomes 3x3.
   */
  public const IMAGE_TYPE_SQUARE = 'SQUARE';
  /**
   * Applies a circular mask to the image. For example, a 4x3 image becomes a
   * circle with a diameter of 3.
   */
  public const IMAGE_TYPE_CIRCLE = 'CIRCLE';
  /**
   * The alternative text of this image that's used for accessibility.
   *
   * @var string
   */
  public $imageAltText;
  /**
   * The shape used to crop the image. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * @var string
   */
  public $imageType;
  /**
   * The HTTPS URL of the image in the card header.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * The subtitle of the card header. If specified, appears on its own line
   * below the `title`.
   *
   * @var string
   */
  public $subtitle;
  /**
   * Required. The title of the card header. The header has a fixed height: if
   * both a title and subtitle are specified, each takes up one line. If only
   * the title is specified, it takes up both lines.
   *
   * @var string
   */
  public $title;

  /**
   * The alternative text of this image that's used for accessibility.
   *
   * @param string $imageAltText
   */
  public function setImageAltText($imageAltText)
  {
    $this->imageAltText = $imageAltText;
  }
  /**
   * @return string
   */
  public function getImageAltText()
  {
    return $this->imageAltText;
  }
  /**
   * The shape used to crop the image. [Google Workspace add-ons and Chat
   * apps](https://developers.google.com/workspace/extend):
   *
   * Accepted values: SQUARE, CIRCLE
   *
   * @param self::IMAGE_TYPE_* $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return self::IMAGE_TYPE_*
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * The HTTPS URL of the image in the card header.
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
   * The subtitle of the card header. If specified, appears on its own line
   * below the `title`.
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
   * Required. The title of the card header. The header has a fixed height: if
   * both a title and subtitle are specified, each takes up one line. If only
   * the title is specified, it takes up both lines.
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
class_alias(GoogleAppsCardV1CardHeader::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1CardHeader');
