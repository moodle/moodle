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

class GoogleAppsCardV1GridItem extends \Google\Model
{
  /**
   * Don't use. Unspecified.
   */
  public const LAYOUT_GRID_ITEM_LAYOUT_UNSPECIFIED = 'GRID_ITEM_LAYOUT_UNSPECIFIED';
  /**
   * The title and subtitle are shown below the grid item's image.
   */
  public const LAYOUT_TEXT_BELOW = 'TEXT_BELOW';
  /**
   * The title and subtitle are shown above the grid item's image.
   */
  public const LAYOUT_TEXT_ABOVE = 'TEXT_ABOVE';
  /**
   * A user-specified identifier for this grid item. This identifier is returned
   * in the parent grid's `onClick` callback parameters.
   *
   * @var string
   */
  public $id;
  protected $imageType = GoogleAppsCardV1ImageComponent::class;
  protected $imageDataType = '';
  /**
   * The layout to use for the grid item.
   *
   * @var string
   */
  public $layout;
  /**
   * The grid item's subtitle.
   *
   * @var string
   */
  public $subtitle;
  /**
   * The grid item's title.
   *
   * @var string
   */
  public $title;

  /**
   * A user-specified identifier for this grid item. This identifier is returned
   * in the parent grid's `onClick` callback parameters.
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
   * The image that displays in the grid item.
   *
   * @param GoogleAppsCardV1ImageComponent $image
   */
  public function setImage(GoogleAppsCardV1ImageComponent $image)
  {
    $this->image = $image;
  }
  /**
   * @return GoogleAppsCardV1ImageComponent
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * The layout to use for the grid item.
   *
   * Accepted values: GRID_ITEM_LAYOUT_UNSPECIFIED, TEXT_BELOW, TEXT_ABOVE
   *
   * @param self::LAYOUT_* $layout
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }
  /**
   * @return self::LAYOUT_*
   */
  public function getLayout()
  {
    return $this->layout;
  }
  /**
   * The grid item's subtitle.
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
   * The grid item's title.
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
class_alias(GoogleAppsCardV1GridItem::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1GridItem');
